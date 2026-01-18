<?php
/**
 * 브랜드 관리 API
 *
 * GET: 브랜드 단일 조회
 * POST: 브랜드 등록
 * PUT: 브랜드 수정
 * DELETE: 브랜드 삭제
 */

function processGet() {
    global $ret, $parameters;

    $idx = $parameters['idx'] ?? null;

    if (!$idx) {
        header("HTTP/1.1 400 Bad Request");
        $ret['result'] = "FAILED";
        $ret['message'] = __('브랜드 IDX가 필요합니다.', 'api');
        return;
    }

    $brand = \AriRent\Rent::getBrand($idx);

    if (!$brand) {
        header("HTTP/1.1 404 Not Found");
        $ret['result'] = "FAILED";
        $ret['message'] = __('브랜드를 찾을 수 없습니다.', 'api');
        return;
    }

    // 해당 브랜드의 모델 수 조회
    $brand->model_count = \AriRent\Rent::getModelCount(['brand_idx' => $idx]);

    $ret['data'] = $brand;
}

function processPost() {
    global $ret, $parameters;

    checkAdmin();

    // 필수 파라미터 확인
    if (empty($parameters['brand_name'])) {
        header("HTTP/1.1 400 Bad Request");
        $ret['result'] = "FAILED";
        $ret['message'] = __('브랜드명은 필수입니다.', 'api');
        return;
    }

    $data = [
        'brand_name' => trim($parameters['brand_name']),
        'brand_name_en' => trim($parameters['brand_name_en'] ?? ''),
        'country_code' => trim($parameters['country_code'] ?? 'KR'),
        'logo_url' => trim($parameters['logo_url'] ?? ''),
        'sort_order' => intval($parameters['sort_order'] ?? 0),
        'is_active' => isset($parameters['is_active']) ? intval($parameters['is_active']) : 1
    ];

    try {
        $newIdx = \AriRent\Rent::setBrand($data);
        $ret['data'] = ['idx' => $newIdx];
        $ret['message'] = __('브랜드가 등록되었습니다.', 'api');
    } catch (\Exception $e) {
        header("HTTP/1.1 400 Bad Request");
        $ret['result'] = "FAILED";
        $ret['message'] = $e->getMessage();
    }
}

function processPut() {
    global $ret, $parameters;

    checkAdmin();

    if (empty($parameters['idx'])) {
        header("HTTP/1.1 400 Bad Request");
        $ret['result'] = "FAILED";
        $ret['message'] = __('브랜드 IDX가 필요합니다.', 'api');
        return;
    }

    $idx = $parameters['idx'];

    // 기존 브랜드 확인
    $existing = \AriRent\Rent::getBrand($idx);
    if (!$existing) {
        header("HTTP/1.1 404 Not Found");
        $ret['result'] = "FAILED";
        $ret['message'] = __('브랜드를 찾을 수 없습니다.', 'api');
        return;
    }

    $data = ['idx' => $idx];

    // 수정 가능한 필드들
    $allowedFields = ['brand_name', 'brand_name_en', 'country_code', 'logo_url', 'sort_order', 'is_active'];
    foreach ($allowedFields as $field) {
        if (isset($parameters[$field])) {
            $data[$field] = $parameters[$field];
        }
    }

    try {
        \AriRent\Rent::setBrand($data);
        $ret['message'] = __('브랜드 정보가 수정되었습니다.', 'api');
    } catch (\Exception $e) {
        header("HTTP/1.1 400 Bad Request");
        $ret['result'] = "FAILED";
        $ret['message'] = $e->getMessage();
    }
}

function processDelete() {
    global $ret, $parameters;

    checkAdmin();

    if (empty($parameters['idx'])) {
        header("HTTP/1.1 400 Bad Request");
        $ret['result'] = "FAILED";
        $ret['message'] = __('브랜드 IDX가 필요합니다.', 'api');
        return;
    }

    $idx = $parameters['idx'];

    // 기존 브랜드 확인
    $existing = \AriRent\Rent::getBrand($idx);
    if (!$existing) {
        header("HTTP/1.1 404 Not Found");
        $ret['result'] = "FAILED";
        $ret['message'] = __('브랜드를 찾을 수 없습니다.', 'api');
        return;
    }

    // 해당 브랜드의 모델이 있는지 확인
    $modelCount = \AriRent\Rent::getModelCount(['brand_idx' => $idx]);
    if ($modelCount > 0) {
        header("HTTP/1.1 400 Bad Request");
        $ret['result'] = "FAILED";
        $ret['message'] = __('해당 브랜드에 모델이 등록되어 있어 삭제할 수 없습니다.', 'api');
        return;
    }

    $sql = "DELETE FROM " . DB_PREFIX . "rent_brand WHERE idx = :idx";
    \ExpertNote\DB::query($sql, ['idx' => $idx]);

    $ret['message'] = __('브랜드가 삭제되었습니다.', 'api');
}
