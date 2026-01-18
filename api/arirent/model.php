<?php
/**
 * 모델 관리 API
 *
 * GET: 모델 단일 조회
 * POST: 모델 등록
 * PUT: 모델 수정
 * DELETE: 모델 삭제
 */

function processGet() {
    global $ret, $parameters;

    $idx = $parameters['idx'] ?? null;

    if (!$idx) {
        header("HTTP/1.1 400 Bad Request");
        $ret['result'] = "FAILED";
        $ret['message'] = __('모델 IDX가 필요합니다.', 'api');
        return;
    }

    $model = \AriRent\Rent::getModel($idx);

    if (!$model) {
        header("HTTP/1.1 404 Not Found");
        $ret['result'] = "FAILED";
        $ret['message'] = __('모델을 찾을 수 없습니다.', 'api');
        return;
    }

    $ret['data'] = $model;
}

function processPost() {
    global $ret, $parameters;

    checkAdmin();

    // 필수 파라미터 확인
    if (empty($parameters['brand_idx']) || empty($parameters['model_name'])) {
        header("HTTP/1.1 400 Bad Request");
        $ret['result'] = "FAILED";
        $ret['message'] = __('브랜드 IDX와 모델명은 필수입니다.', 'api');
        return;
    }

    // 브랜드 존재 여부 확인
    $brand = \AriRent\Rent::getBrand($parameters['brand_idx']);
    if (!$brand) {
        header("HTTP/1.1 400 Bad Request");
        $ret['result'] = "FAILED";
        $ret['message'] = __('존재하지 않는 브랜드입니다.', 'api');
        return;
    }

    $data = [
        'brand_idx' => intval($parameters['brand_idx']),
        'model_name' => trim($parameters['model_name']),
        'model_name_en' => trim($parameters['model_name_en'] ?? ''),
        'segment' => trim($parameters['segment'] ?? ''),
        'sort_order' => intval($parameters['sort_order'] ?? 0),
        'is_active' => isset($parameters['is_active']) ? intval($parameters['is_active']) : 1
    ];

    try {
        $newIdx = \AriRent\Rent::setModel($data);
        $ret['data'] = ['idx' => $newIdx];
        $ret['message'] = __('모델이 등록되었습니다.', 'api');
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
        $ret['message'] = __('모델 IDX가 필요합니다.', 'api');
        return;
    }

    $idx = $parameters['idx'];

    // 기존 모델 확인
    $existing = \AriRent\Rent::getModel($idx);
    if (!$existing) {
        header("HTTP/1.1 404 Not Found");
        $ret['result'] = "FAILED";
        $ret['message'] = __('모델을 찾을 수 없습니다.', 'api');
        return;
    }

    $data = ['idx' => $idx];

    // 수정 가능한 필드들
    $allowedFields = ['brand_idx', 'model_name', 'model_name_en', 'segment', 'sort_order', 'is_active'];
    foreach ($allowedFields as $field) {
        if (isset($parameters[$field])) {
            $data[$field] = $parameters[$field];
        }
    }

    // brand_idx가 변경되면 브랜드 존재 여부 확인
    if (isset($data['brand_idx'])) {
        $brand = \AriRent\Rent::getBrand($data['brand_idx']);
        if (!$brand) {
            header("HTTP/1.1 400 Bad Request");
            $ret['result'] = "FAILED";
            $ret['message'] = __('존재하지 않는 브랜드입니다.', 'api');
            return;
        }
    }

    try {
        \AriRent\Rent::setModel($data);
        $ret['message'] = __('모델 정보가 수정되었습니다.', 'api');
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
        $ret['message'] = __('모델 IDX가 필요합니다.', 'api');
        return;
    }

    $idx = $parameters['idx'];

    // 기존 모델 확인
    $existing = \AriRent\Rent::getModel($idx);
    if (!$existing) {
        header("HTTP/1.1 404 Not Found");
        $ret['result'] = "FAILED";
        $ret['message'] = __('모델을 찾을 수 없습니다.', 'api');
        return;
    }

    $sql = "DELETE FROM " . DB_PREFIX . "rent_model WHERE idx = :idx";
    \ExpertNote\DB::query($sql, ['idx' => $idx]);

    $ret['message'] = __('모델이 삭제되었습니다.', 'api');
}
