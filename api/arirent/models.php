<?php
/**
 * 모델 목록 API
 *
 * GET: 모델 목록 조회
 */

function processGet() {
    global $ret, $parameters;

    $limit = intval($parameters['limit'] ?? 100);
    $offset = intval($parameters['offset'] ?? 0);
    $brandIdx = $parameters['brand_idx'] ?? null;
    $segment = $parameters['segment'] ?? null;
    $isActive = $parameters['is_active'] ?? null;
    $search = $parameters['search'] ?? null;
    $countryCode = $parameters['country_code'] ?? null;

    $where = [];

    if ($brandIdx) {
        $where['brand_idx'] = intval($brandIdx);
    }

    if ($segment) {
        $where['segment'] = $segment;
    }

    if ($isActive !== null) {
        $where['is_active'] = intval($isActive);
    }

    if ($countryCode) {
        $where['b.country_code'] = $countryCode;
    }

    if ($search) {
        $where['m.model_name LIKE'] = "%{$search}%";
    }

    // 정렬 기준
    $sortBy = $parameters['sort_by'] ?? 'sort_order';
    $sortOrder = strtoupper($parameters['sort_order'] ?? 'ASC') === 'DESC' ? 'DESC' : 'ASC';

    $orderby = [$sortBy => $sortOrder];

    // 목록 조회
    $models = \AriRent\Rent::getModels($where, $orderby, ['offset' => $offset, 'count' => $limit]);

    // 전체 개수 조회
    $totalCount = \AriRent\Rent::getModelCount($where);

    $ret['data'] = [
        'items' => $models,
        'total_count' => $totalCount,
        'limit' => $limit,
        'offset' => $offset
    ];
}
