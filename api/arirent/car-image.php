<?php
/**
 * 차량 이미지 업로드 API
 *
 * POST: 이미지 업로드 및 DB 저장
 */

function processPost() {
    global $ret, $parameters;

    checkAdmin();

    // rent_idx 확인
    $rentIdx = $parameters['rent_idx'] ?? null;
    if (!$rentIdx) {
        header("HTTP/1.1 400 Bad Request");
        $ret['result'] = "FAILED";
        $ret['message'] = __('차량 정보가 없습니다.', 'api');
        return;
    }

    // 파일 업로드 확인
    if (empty($_FILES['file'])) {
        header("HTTP/1.1 400 Bad Request");
        $ret['result'] = "FAILED";
        $ret['message'] = __('업로드할 파일이 없습니다.', 'api');
        return;
    }

    $file = $_FILES['file'];

    // 에러 체크
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'php.ini의 upload_max_filesize 초과',
            UPLOAD_ERR_FORM_SIZE => '폼의 MAX_FILE_SIZE 초과',
            UPLOAD_ERR_PARTIAL => '파일이 일부만 업로드됨',
            UPLOAD_ERR_NO_FILE => '업로드된 파일 없음',
            UPLOAD_ERR_NO_TMP_DIR => '임시 폴더 없음',
            UPLOAD_ERR_CANT_WRITE => '디스크에 쓰기 실패',
            UPLOAD_ERR_EXTENSION => 'PHP 확장에 의해 업로드 중단',
        ];
        header("HTTP/1.1 400 Bad Request");
        $ret['result'] = "FAILED";
        $ret['message'] = $errorMessages[$file['error']] ?? __('파일 업로드 중 오류가 발생했습니다.', 'api') . " (code: {$file['error']})";
        return;
    }

    // 허용된 이미지 타입 확인
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, $allowedTypes)) {
        header("HTTP/1.1 400 Bad Request");
        $ret['result'] = "FAILED";
        $ret['message'] = __('허용되지 않는 파일 형식입니다. (jpg, png, gif, webp만 허용)', 'api');
        return;
    }

    // 파일 크기 제한 (10MB)
    $maxSize = 10 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        header("HTTP/1.1 400 Bad Request");
        $ret['result'] = "FAILED";
        $ret['message'] = __('파일 크기가 10MB를 초과합니다.', 'api');
        return;
    }

    // 파일 정보 생성
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $serverName = time() . '_' . uniqid() . '.' . $ext;
    $fileDate = date("Ym");

    $objFile = (object)[
        'tmp_name' => $file['tmp_name'],
        'server_name' => $serverName,
        'real_name' => $file['name'],
        'mime' => $mimeType,
        'size' => $file['size'],
        'service_folder' => 'rent',
        'path' => '/'.$fileDate.'/'
    ];

    // Store 클래스를 사용하여 파일 저장
    $result = \ExpertNote\Store::uploadFile($objFile);

    if ($result->result === 'SUCCESS') {
        // 현재 최대 image_order 조회
        $maxOrder = \ExpertNote\DB::getVar(
            "SELECT COALESCE(MAX(image_order), 0) FROM " . DB_PREFIX . "rent_images WHERE rent_idx = :rent_idx",
            ['rent_idx' => $rentIdx]
        );

        // Rent 클래스를 사용하여 DB에 이미지 정보 저장
        $imageIdx = \AriRent\Rent::addImage([
            'rent_idx' => $rentIdx,
            'image_url' => $result->url,
            'image_order' => $maxOrder + 1,
            'file_size' => $result->size
        ]);

        $ret['data'] = [
            'idx' => $imageIdx,
            'url' => $result->url,
            'filename' => $result->server_name,
            'original_name' => $file['name'],
            'size' => $result->size,
            'mime' => $result->mime
        ];
        $ret['message'] = __('파일이 업로드되었습니다.', 'api');
    } else {
        header("HTTP/1.1 500 Internal Server Error");
        $ret['result'] = "FAILED";
        $ret['message'] = $result->message ?? __('파일 저장 중 오류가 발생했습니다.', 'api');
    }
}

function processGet() {
    global $ret;
    $ret['message'] = __('POST 메서드만 지원합니다.', 'api');
}

function processDelete() {
    global $ret;

    checkAdmin();

    // idx 확인 (DELETE 요청은 query string에서 가져옴)
    $idx = $_GET['idx'] ?? null;
    if (!$idx) {
        header("HTTP/1.1 400 Bad Request");
        $ret['result'] = "FAILED";
        $ret['message'] = __('이미지 정보가 없습니다.', 'api');
        return;
    }

    // 이미지 정보 조회
    $sql = "SELECT * FROM " . DB_PREFIX . "rent_images WHERE idx = :idx";
    $image = \ExpertNote\DB::getRow($sql, ['idx' => $idx]);

    if (!$image) {
        header("HTTP/1.1 404 Not Found");
        $ret['result'] = "FAILED";
        $ret['message'] = __('이미지를 찾을 수 없습니다.', 'api');
        return;
    }

    // S3/LOCAL에서 파일 삭제
    $imageUrl = $image->image_url;
    $storeMethod = \ExpertNote\SiteMeta::get('store_method') ?? 'LOCAL';

    // URL에서 S3 키 추출
    // S3 URL 형식: https://bucket.s3.region.amazonaws.com/rent/202501/filename.jpg
    // CDN URL 형식: https://cdn.example.com/rent/202501/filename.jpg
    $urlPath = parse_url($imageUrl, PHP_URL_PATH);
    $s3Key = ltrim($urlPath, '/'); // rent/202501/filename.jpg

    // 'rent/'로 시작하는 경우 해당 부분부터 사용
    if (strpos($s3Key, 'rent/') !== false) {
        $s3Key = substr($s3Key, strpos($s3Key, 'rent/'));
    }

    if ($s3Key && $storeMethod !== 'LOCAL') {
        // S3에서 직접 삭제
        try {
            $deleteResult = \ExpertNote\Store::deleteFromS3($s3Key);
            if (!$deleteResult || $deleteResult->result !== 'SUCCESS') {
                \ExpertNote\Log::setLog('S3_DELETE', 'FAILED', null, [
                    'image_url' => $imageUrl,
                    's3_key' => $s3Key,
                    'message' => $deleteResult->message ?? 'Unknown error'
                ]);
            }
        } catch (\Exception $e) {
            \ExpertNote\Log::setLog('S3_DELETE', 'ERROR', null, [
                'image_url' => $imageUrl,
                's3_key' => $s3Key,
                'error' => $e->getMessage()
            ]);
        }
    } elseif ($s3Key && $storeMethod === 'LOCAL') {
        // LOCAL 파일 삭제
        $pathParts = explode('/', $s3Key);
        if (count($pathParts) >= 3) {
            $serviceFolder = $pathParts[0];
            $datePath = '/' . $pathParts[1] . '/';
            $serverName = $pathParts[2];
            \ExpertNote\Store::deleteFile('LOCAL', $serviceFolder, $datePath, $serverName);
        }
    }

    // DB에서 삭제
    $deleted = \AriRent\Rent::deleteImage($idx);

    if ($deleted) {
        $ret['message'] = __('이미지가 삭제되었습니다.', 'api');
    } else {
        header("HTTP/1.1 500 Internal Server Error");
        $ret['result'] = "FAILED";
        $ret['message'] = __('이미지 삭제 중 오류가 발생했습니다.', 'api');
    }
}
