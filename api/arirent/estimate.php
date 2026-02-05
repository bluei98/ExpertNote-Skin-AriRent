<?php
/**
 * 견적 조회 API
 *
 * GET: 브랜드/모델/차량 목록 조회 (활성 차량이 있는 항목만)
 *
 * 파라미터:
 *   - brand_idx: 브랜드 IDX (선택) - 지정 시 해당 브랜드의 모델+차량 반환
 *   - model_idx: 모델 IDX (선택) - 지정 시 해당 모델의 차량만 반환
 *   - car_type: 차종 (선택) - NEW 또는 USED
 */

function processGet() {
    global $ret, $parameters;

    $brandIdx = intval($parameters['brand_idx'] ?? 0);
    $modelIdx = intval($parameters['model_idx'] ?? 0);
    $carType = isset($parameters['car_type']) ? strtoupper($parameters['car_type']) : '';

    // 브랜드 목록 조회 (활성 차량이 있는 브랜드만)
    $allBrands = \AriRent\Rent::getBrands(['is_active' => 1], ['sort_order' => 'ASC']);
    $brands = [];
    foreach ($allBrands as $b) {
        $cnt = \AriRent\Rent::getRentCount(['r.status' => 'active', 'r.brand_idx' => $b->idx]);
        if ($cnt > 0) {
            $b->vehicle_count = $cnt;
            $brands[] = $b;
        }
    }

    $data = ['brands' => $brands];

    // 브랜드 선택 시 모델 + 차량 조회
    if ($brandIdx) {
        // 모델 목록 (활성 차량이 있는 모델만)
        $allModels = \AriRent\Rent::getModels(['brand_idx' => $brandIdx, 'is_active' => 1], ['sort_order' => 'ASC']);
        $models = [];
        foreach ($allModels as $m) {
            $cnt = \AriRent\Rent::getRentCount(['r.status' => 'active', 'r.brand_idx' => $brandIdx, 'r.model_idx' => $m->idx]);
            if ($cnt > 0) {
                $m->vehicle_count = $cnt;
                $models[] = $m;
            }
        }
        $data['models'] = $models;

        // 차량 목록 (가격 포함, 최저가순)
        $where = ['r.status' => 'active', 'r.brand_idx' => $brandIdx];
        if ($modelIdx) {
            $where['r.model_idx'] = $modelIdx;
        }
        if ($carType) {
            $where['r.car_type'] = $carType;
        }
        $vehicles = \AriRent\Rent::getRents($where, ['p.monthly_rent_amount' => 'ASC'], [], true);
        if (!is_array($vehicles)) $vehicles = [];

        // 월 렌트료 최저가순 정렬 (is_sticky 무시)
        usort($vehicles, function($a, $b) {
            $priceA = isset($a->min_price) ? (int)$a->min_price : PHP_INT_MAX;
            $priceB = isset($b->min_price) ? (int)$b->min_price : PHP_INT_MAX;
            return $priceA - $priceB;
        });

        $data['vehicles'] = $vehicles;
    }

    $ret['data'] = $data;
}
