<?php
/**
 * 차량 이미지 ZIP 다운로드 API
 *
 * GET: 차량의 모든 이미지를 ZIP으로 압축하여 다운로드
 */

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

        // 이미지 다운로드
        $imageContent = @file_get_contents($imageUrl);

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
