<?php
/**
 * 차량 이미지 ZIP 다운로드 API
 *
 * GET: 차량의 모든 이미지를 ZIP으로 압축하여 다운로드
 */

/**
 * cURL을 사용한 이미지 다운로드
 *
 * @param string $url 이미지 URL
 * @param int $timeout 타임아웃 (초)
 * @return string|false 이미지 데이터 또는 실패 시 false
 */
function downloadImage($url, $timeout = 10) {
    // 사이트 도메인 가져오기 (S3 Referer 정책 우회용)
    $referer = defined('SITE_URL') ? SITE_URL : 'https://arirent.co.kr';

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 3,
        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        CURLOPT_REFERER => $referer,
    ]);

    $content = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($httpCode !== 200 || $content === false || empty($content)) {
        \ExpertNote\Log::setLog('car-image-download', 'WARNING', '이미지 다운로드 실패', [
            'url' => $url,
            'http_code' => $httpCode,
            'error' => $error
        ]);
        return false;
    }

    return $content;
}

function processGet() {
    global $ret, $parameters;

    // 관리자 권한 체크
    if (!\ExpertNote\User\User::isAdmin()) {
        header("HTTP/1.1 403 Forbidden");
        $ret['result'] = "FAILED";
        $ret['message'] = __('관리자 권한이 필요합니다.', 'api');
        return;
    }

    // car-idx 확인
    $carIdx = $parameters['car-idx'] ?? $parameters['car_idx'] ?? null;
    if (!$carIdx) {
        header("HTTP/1.1 400 Bad Request");
        $ret['result'] = "FAILED";
        $ret['message'] = __('차량 정보가 없습니다.', 'api');
        return;
    }

    // 차량 정보 조회
    $carSql = "SELECT idx, car_number, title FROM " . DB_PREFIX . "rent WHERE idx = :idx";
    $car = \ExpertNote\DB::getRow($carSql, ['idx' => $carIdx]);

    if (!$car) {
        header("HTTP/1.1 404 Not Found");
        $ret['result'] = "FAILED";
        $ret['message'] = __('차량을 찾을 수 없습니다.', 'api');
        return;
    }

    // 이미지 목록 조회
    $images = \AriRent\Rent::getImages($carIdx);

    if (empty($images)) {
        header("HTTP/1.1 404 Not Found");
        $ret['result'] = "FAILED";
        $ret['message'] = __('다운로드할 이미지가 없습니다.', 'api');
        return;
    }

    // 임시 ZIP 파일 생성
    $zipFileName = 'car_' . $carIdx . '_' . preg_replace('/[^a-zA-Z0-9가-힣]/', '_', $car->car_number) . '_' . date('Ymd_His') . '.zip';
    $zipFilePath = sys_get_temp_dir() . '/' . $zipFileName;

    $zip = new ZipArchive();
    if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        header("HTTP/1.1 500 Internal Server Error");
        $ret['result'] = "FAILED";
        $ret['message'] = __('ZIP 파일 생성에 실패했습니다.', 'api');
        return;
    }

    $addedCount = 0;
    foreach ($images as $index => $image) {
        $imageUrl = $image->image_url;

        // URL에서 파일 확장자 추출
        $urlPath = parse_url($imageUrl, PHP_URL_PATH);
        $ext = pathinfo($urlPath, PATHINFO_EXTENSION) ?: 'jpg';

        // 이미지 다운로드 (cURL 사용)
        $imageContent = downloadImage($imageUrl);

        if ($imageContent !== false) {
            // ZIP에 추가 (순서_차량번호_인덱스.확장자)
            $fileName = sprintf('%02d_%s_%d.%s', $index + 1, preg_replace('/[^a-zA-Z0-9가-힣]/', '_', $car->car_number), $image->idx, $ext);
            $zip->addFromString($fileName, $imageContent);
            $addedCount++;
        }
    }

    $zip->close();

    if ($addedCount === 0) {
        @unlink($zipFilePath);
        header("HTTP/1.1 500 Internal Server Error");
        $ret['result'] = "FAILED";
        $ret['message'] = __('이미지 다운로드에 실패했습니다.', 'api');
        return;
    }

    // ZIP 파일 다운로드
    if (file_exists($zipFilePath)) {
        // 기존 출력 버퍼 정리
        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $zipFileName . '"');
        header('Content-Length: ' . filesize($zipFilePath));
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: public');

        readfile($zipFilePath);

        // 임시 파일 삭제
        @unlink($zipFilePath);

        exit;
    } else {
        header("HTTP/1.1 500 Internal Server Error");
        $ret['result'] = "FAILED";
        $ret['message'] = __('ZIP 파일을 찾을 수 없습니다.', 'api');
        return;
    }
}

function processPost() {
    global $ret;
    $ret['result'] = "FAILED";
    $ret['message'] = __('GET 메서드만 지원합니다.', 'api');
}

function processDelete() {
    global $ret;
    $ret['result'] = "FAILED";
    $ret['message'] = __('GET 메서드만 지원합니다.', 'api');
}
