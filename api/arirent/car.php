<?php
/**
 * 렌트 차량 관리 API
 *
 * GET: 차량 조회 (단일/목록)
 * POST: 차량 등록
 * PUT: 차량 수정
 * DELETE: 차량 삭제
 */

function processGet() {
    global $ret, $parameters;

    $idx = $parameters['idx'] ?? null;

    if ($idx) {
        // 단일 차량 조회 (가격, 이미지 포함)
        $sql = "SELECT r.*, d.dealer_name FROM " . DB_PREFIX . "rent r
                LEFT JOIN " . DB_PREFIX . "rent_dealer d ON r.dealer_idx = d.idx
                WHERE r.idx = :idx";
        $car = \ExpertNote\DB::getRow($sql, ['idx' => $idx]);

        if (!$car) {
            header("HTTP/1.1 404 Not Found");
            $ret['result'] = "FAILED";
            $ret['message'] = __('차량을 찾을 수 없습니다.', 'api');
            return;
        }

        // 가격 정보 조회
        $pricesSql = "SELECT * FROM " . DB_PREFIX . "rent_price WHERE rent_idx = :rent_idx ORDER BY deposit_amount, rental_period_months";
        $car->prices = \ExpertNote\DB::getRows($pricesSql, ['rent_idx' => $idx]);

        // 이미지 정보 조회
        $imagesSql = "SELECT * FROM " . DB_PREFIX . "rent_images WHERE rent_idx = :rent_idx ORDER BY sort_order, idx";
        $car->images = \ExpertNote\DB::getRows($imagesSql, ['rent_idx' => $idx]);

        $ret['data'] = $car;
    } else {
        // 차량 목록 조회
        $limit = intval($parameters['limit'] ?? 20);
        $offset = intval($parameters['offset'] ?? 0);
        $status = $parameters['status'] ?? null;
        $dealerIdx = $parameters['dealer_idx'] ?? null;
        $carType = $parameters['car_type'] ?? null;

        $wheres = [];
        $params = [];

        if ($status) {
            $wheres[] = "r.status = :status";
            $params['status'] = $status;
        }

        if ($dealerIdx) {
            $wheres[] = "r.dealer_idx = :dealer_idx";
            $params['dealer_idx'] = $dealerIdx;
        }

        if ($carType) {
            $wheres[] = "r.car_type = :car_type";
            $params['car_type'] = $carType;
        }

        $sql = "SELECT r.*, d.dealer_name,
                (SELECT COUNT(*) FROM " . DB_PREFIX . "rent_images WHERE rent_idx = r.idx) as image_count,
                (SELECT COUNT(*) FROM " . DB_PREFIX . "rent_price WHERE rent_idx = r.idx) as price_count
                FROM " . DB_PREFIX . "rent r
                LEFT JOIN " . DB_PREFIX . "rent_dealer d ON r.dealer_idx = d.idx";

        if (count($wheres) > 0) {
            $sql .= " WHERE " . implode(" AND ", $wheres);
        }

        $sql .= " ORDER BY r.idx DESC LIMIT {$offset}, {$limit}";

        $cars = \ExpertNote\DB::getRows($sql, $params);
        $ret['data'] = $cars;
    }
}

function processPost() {
    global $ret, $parameters;

    checkAdmin();

    // 필수 파라미터 확인
    if (empty($parameters['dealer_idx']) || empty($parameters['car_number']) || empty($parameters['title'])) {
        header("HTTP/1.1 400 Bad Request");
        $ret['result'] = "FAILED";
        $ret['message'] = __('대리점, 차량번호, 차량명은 필수입니다.', 'api');
        return;
    }

    $data = [
        'dealer_idx' => $parameters['dealer_idx'],
        'car_type' => $parameters['car_type'] ?? 'NEW',
        'car_number' => trim($parameters['car_number']),
        'title' => trim($parameters['title']),
        'brand' => trim($parameters['brand'] ?? ''),
        'model' => trim($parameters['model'] ?? ''),
        'image' => trim($parameters['image'] ?? ''),
        'monthly_price' => intval($parameters['monthly_price'] ?? 0) ?: null,
        'model_year' => trim($parameters['model_year'] ?? ''),
        'model_month' => trim($parameters['model_month'] ?? ''),
        'mileage_km' => intval($parameters['mileage_km'] ?? 0) ?: null,
        'fuel_type' => trim($parameters['fuel_type'] ?? ''),
        'status' => $parameters['status'] ?? 'active',
        'option_exterior' => trim($parameters['option_exterior'] ?? ''),
        'option_safety' => trim($parameters['option_safety'] ?? ''),
        'option_convenience' => trim($parameters['option_convenience'] ?? ''),
        'option_seat' => trim($parameters['option_seat'] ?? '')
    ];

    // 차량번호 중복 확인
    $checkSql = "SELECT idx FROM " . DB_PREFIX . "rent WHERE dealer_idx = :dealer_idx AND car_number = :car_number";
    $existing = \ExpertNote\DB::getRow($checkSql, [
        'dealer_idx' => $data['dealer_idx'],
        'car_number' => $data['car_number']
    ]);

    if ($existing) {
        header("HTTP/1.1 400 Bad Request");
        $ret['result'] = "FAILED";
        $ret['message'] = __('해당 대리점에 동일한 차량번호가 이미 등록되어 있습니다.', 'api');
        return;
    }

    $columns = implode(', ', array_keys($data));
    $placeholders = ':' . implode(', :', array_keys($data));
    $sql = "INSERT INTO " . DB_PREFIX . "rent ({$columns}) VALUES ({$placeholders})";
    \ExpertNote\DB::query($sql, $data);

    $newIdx = \ExpertNote\DB::getLastInsertId();

    // 가격 정보 저장
    if (!empty($parameters['prices']) && is_array($parameters['prices'])) {
        foreach ($parameters['prices'] as $price) {
            $priceData = [
                'rent_idx' => $newIdx,
                'deposit_amount' => intval($price['deposit_amount'] ?? 0) ?: null,
                'rental_period_months' => intval($price['rental_period_months'] ?? 0) ?: null,
                'monthly_rent_amount' => intval($price['monthly_rent_amount'] ?? 0) ?: null,
                'yearly_mileage_limit' => intval($price['yearly_mileage_limit'] ?? 0) ?: null
            ];
            $priceSql = "INSERT INTO " . DB_PREFIX . "rent_price (rent_idx, deposit_amount, rental_period_months, monthly_rent_amount, yearly_mileage_limit)
                         VALUES (:rent_idx, :deposit_amount, :rental_period_months, :monthly_rent_amount, :yearly_mileage_limit)";
            \ExpertNote\DB::query($priceSql, $priceData);
        }
    }

    // 이미지 정보 저장
    if (!empty($parameters['images']) && is_array($parameters['images'])) {
        $sortOrder = 0;
        foreach ($parameters['images'] as $image) {
            $imageData = [
                'rent_idx' => $newIdx,
                'image_url' => trim($image['image_url'] ?? ''),
                'image_type' => trim($image['image_type'] ?? 'exterior'),
                'sort_order' => $sortOrder++
            ];
            if (!empty($imageData['image_url'])) {
                $imageSql = "INSERT INTO " . DB_PREFIX . "rent_images (rent_idx, image_url, image_type, sort_order)
                             VALUES (:rent_idx, :image_url, :image_type, :sort_order)";
                \ExpertNote\DB::query($imageSql, $imageData);
            }
        }
    }

    $ret['data'] = ['idx' => $newIdx];
    $ret['message'] = __('차량이 등록되었습니다.', 'api');
}

function processPut() {
    global $ret, $parameters;

    checkAdmin();

    if (empty($parameters['idx'])) {
        header("HTTP/1.1 400 Bad Request");
        $ret['result'] = "FAILED";
        $ret['message'] = __('차량 IDX가 필요합니다.', 'api');
        return;
    }

    $idx = $parameters['idx'];

    // 기존 차량 확인
    $checkSql = "SELECT idx FROM " . DB_PREFIX . "rent WHERE idx = :idx";
    $existing = \ExpertNote\DB::getRow($checkSql, ['idx' => $idx]);

    if (!$existing) {
        header("HTTP/1.1 404 Not Found");
        $ret['result'] = "FAILED";
        $ret['message'] = __('차량을 찾을 수 없습니다.', 'api');
        return;
    }

    // 업데이트할 필드 수집
    $allowedFields = [
        'dealer_idx', 'car_type', 'car_number', 'title', 'brand', 'model',
        'image', 'monthly_price', 'model_year', 'model_month', 'mileage_km',
        'fuel_type', 'status', 'option_exterior', 'option_safety',
        'option_convenience', 'option_seat'
    ];

    $sets = [];
    $params = ['idx' => $idx];

    foreach ($allowedFields as $field) {
        if (isset($parameters[$field])) {
            $sets[] = "{$field} = :{$field}";
            $params[$field] = $parameters[$field];
        }
    }

    if (count($sets) > 0) {
        $sql = "UPDATE " . DB_PREFIX . "rent SET " . implode(', ', $sets) . " WHERE idx = :idx";
        \ExpertNote\DB::query($sql, $params);
    }

    // 가격 정보 업데이트 (전체 교체 방식)
    if (isset($parameters['prices']) && is_array($parameters['prices'])) {
        // 기존 가격 삭제
        \ExpertNote\DB::query("DELETE FROM " . DB_PREFIX . "rent_price WHERE rent_idx = :idx", ['idx' => $idx]);

        // 새 가격 추가
        foreach ($parameters['prices'] as $price) {
            $priceData = [
                'rent_idx' => $idx,
                'deposit_amount' => intval($price['deposit_amount'] ?? 0) ?: null,
                'rental_period_months' => intval($price['rental_period_months'] ?? 0) ?: null,
                'monthly_rent_amount' => intval($price['monthly_rent_amount'] ?? 0) ?: null,
                'yearly_mileage_limit' => intval($price['yearly_mileage_limit'] ?? 0) ?: null
            ];
            $priceSql = "INSERT INTO " . DB_PREFIX . "rent_price (rent_idx, deposit_amount, rental_period_months, monthly_rent_amount, yearly_mileage_limit)
                         VALUES (:rent_idx, :deposit_amount, :rental_period_months, :monthly_rent_amount, :yearly_mileage_limit)";
            \ExpertNote\DB::query($priceSql, $priceData);
        }
    }

    // 이미지 정보 업데이트 (전체 교체 방식)
    if (isset($parameters['images']) && is_array($parameters['images'])) {
        // 기존 이미지 삭제
        \ExpertNote\DB::query("DELETE FROM " . DB_PREFIX . "rent_images WHERE rent_idx = :idx", ['idx' => $idx]);

        // 새 이미지 추가
        $sortOrder = 0;
        foreach ($parameters['images'] as $image) {
            $imageData = [
                'rent_idx' => $idx,
                'image_url' => trim($image['image_url'] ?? ''),
                'image_type' => trim($image['image_type'] ?? 'exterior'),
                'sort_order' => $sortOrder++
            ];
            if (!empty($imageData['image_url'])) {
                $imageSql = "INSERT INTO " . DB_PREFIX . "rent_images (rent_idx, image_url, image_type, sort_order)
                             VALUES (:rent_idx, :image_url, :image_type, :sort_order)";
                \ExpertNote\DB::query($imageSql, $imageData);
            }
        }
    }

    $ret['message'] = __('차량 정보가 수정되었습니다.', 'api');
}

function processDelete() {
    global $ret, $parameters;
    print_r($parameters);

    checkAdmin();

    if (empty($parameters['idx'])) {
        header("HTTP/1.1 400 Bad Request");
        $ret['result'] = "FAILED";
        $ret['message'] = __('차량 IDX가 필요합니다.', 'api');
        return;
    }

    $idx = $parameters['idx'];

    // 기존 차량 확인
    $checkSql = "SELECT idx FROM " . DB_PREFIX . "rent WHERE idx = :idx";
    $existing = \ExpertNote\DB::getRow($checkSql, ['idx' => $idx]);

    if (!$existing) {
        header("HTTP/1.1 404 Not Found");
        $ret['result'] = "FAILED";
        $ret['message'] = __('차량을 찾을 수 없습니다.', 'api');
        return;
    }

    // 관련 데이터 삭제
    \ExpertNote\DB::query("DELETE FROM " . DB_PREFIX . "rent_images WHERE rent_idx = :idx", ['idx' => $idx]);
    \ExpertNote\DB::query("DELETE FROM " . DB_PREFIX . "rent_price WHERE rent_idx = :idx", ['idx' => $idx]);
    \ExpertNote\DB::query("DELETE FROM " . DB_PREFIX . "rent_wishlist WHERE rent_idx = :idx", ['idx' => $idx]);

    // 차량 삭제
    \ExpertNote\DB::query("DELETE FROM " . DB_PREFIX . "rent WHERE idx = :idx", ['idx' => $idx]);

    $ret['message'] = __('차량이 삭제되었습니다.', 'api');
}
