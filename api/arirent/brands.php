<?php
/**
 * 브랜드 목록 API
 *
 * GET: 브랜드 목록 조회
 */

function processGet() {
    global $ret, $parameters;

    $limit = intval($parameters['limit'] ?? 100);
    $offset = intval($parameters['offset'] ?? 0);
    $countryCode = $parameters['country_code'] ?? null;
    $isActive = $parameters['is_active'] ?? null;
    $search = $parameters['search'] ?? null;

    $where = [];

    if ($countryCode) {
        $where['country_code'] = $countryCode;
    }

    if ($isActive !== null) {
        $where['is_active'] = intval($isActive);
    }

    if ($search) {
        $where['brand_name LIKE'] = "%{$search}%";
    }

    // 정렬 기준
    $sortBy = $parameters['sort_by'] ?? 'sort_order';
    $sortOrder = strtoupper($parameters['sort_order'] ?? 'ASC') === 'DESC' ? 'DESC' : 'ASC';

    $orderby = [$sortBy => $sortOrder];

    // 목록 조회
    $brands = \AriRent\Rent::getBrands($where, $orderby, ['offset' => $offset, 'count' => $limit]);

    // 각 브랜드의 모델 수 조회
    foreach ($brands as $brand) {
        $brand->model_count = \AriRent\Rent::getModelCount(['brand_idx' => $brand->idx]);
    }

    // 전체 개수 조회
    $totalCount = \AriRent\Rent::getBrandCount($where);

    $ret['data'] = [
        'items' => $brands,
        'total_count' => $totalCount,
        'limit' => $limit,
        'offset' => $offset
    ];
}
