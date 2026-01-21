<?php
namespace AriRent;

/**
 * 렌트 차량 관리 클래스
 *
 * expertnote_rent, expertnote_rent_dealer, expertnote_rent_price,
 * expertnote_rent_images, expertnote_rent_insurance 테이블 관리
 */
class Rent {

    /**
     * 차량 조회 (단일)
     * PUBLISHED 상태의 대리점 차량만 조회
     *
     * @param int $idx 차량 IDX
     * @return object|false 차량 정보 객체 또는 false
     */
    public static function getRent($idx) {
        $sql = "SELECT r.*, rb.brand_name, rb.brand_name_en, rm.model_name, rm.model_name_en
                FROM " . DB_PREFIX . "rent r
                INNER JOIN " . DB_PREFIX . "rent_dealer d ON r.dealer_idx = d.idx AND d.status = 'PUBLISHED'
                LEFT JOIN " . DB_PREFIX . "rent_brand rb ON r.brand_idx = rb.idx
                LEFT JOIN " . DB_PREFIX . "rent_model rm ON r.model_idx = rm.idx
                WHERE r.idx = :idx";
        $params = ['idx' => $idx];

        return \ExpertNote\DB::getRow($sql, $params);
    }

    /**
     * 차량 목록 조회 (최저가 포함)
     *
     * @param array $where WHERE 조건 배열
     * @param array $orderby ORDER BY 조건 배열
     * @param array $limit LIMIT 조건 배열
     * @param bool $includePrices 모든 가격 옵션 포함 여부 (일괄 조회로 성능 최적화)
     * @return array 차량 목록 (min_price 컬럼 포함, includePrices=true 시 prices 배열 포함)
     */
    public static function getRents($where = [], $orderby = [], $limit = [], $includePrices = false) {
        $sql = "SELECT r.*, MIN(rp.monthly_rent_amount) as min_price, rd.dealer_name, rd.dealer_code,
                       rb.brand_name, rb.brand_name_en, rm.model_name, rm.model_name_en, rm.segment
                FROM " . DB_PREFIX . "rent r
                LEFT JOIN " . DB_PREFIX . "rent_price rp ON r.idx = rp.rent_idx
                INNER JOIN " . DB_PREFIX . "rent_dealer rd ON r.dealer_idx = rd.idx AND rd.status = 'PUBLISHED'
                LEFT JOIN " . DB_PREFIX . "rent_brand rb ON r.brand_idx = rb.idx
                LEFT JOIN " . DB_PREFIX . "rent_model rm ON r.model_idx = rm.idx";

        $params = [];
        $conditions = [];

        // WHERE 조건 처리
        foreach ($where as $key => $value) {
            // 지원하는 연산자 목록
            $operators = [' >=', ' <=', ' >', ' <', ' LIKE'];
            $hasOperator = false;

            // 연산자가 포함되어 있는지 확인
            foreach ($operators as $op) {
                if (strpos($key, $op) !== false) {
                    $paramKey = str_replace(array_merge($operators, ['.']), '', $key);
                    $conditions[] = $key . " :$paramKey";
                    $params[$paramKey] = $value;
                    $hasOperator = true;
                    break;
                }
            }

            // 연산자가 없으면 = 연산자 사용
            if (!$hasOperator) {
                $paramKey = str_replace('.', '_', $key);
                $conditions[] = "$key = :$paramKey";
                $params[$paramKey] = $value;
            }
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        // GROUP BY 추가 (JOIN 때문에 필요)
        $sql .= " GROUP BY r.idx";

        // ORDER BY 처리
        if (!empty($orderby)) {
            $orderClauses = [];
            foreach ($orderby as $column => $direction) {
                $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
                // min_price 정렬 지원
                if ($column === 'p.monthly_rent_amount') {
                    $orderClauses[] = "min_price $direction";
                } else {
                    $orderClauses[] = "$column $direction";
                }
            }
            $sql .= " ORDER BY " . implode(', ', $orderClauses);
        }

        // LIMIT 처리
        if (!empty($limit)) {
            if (isset($limit['offset']) && isset($limit['count'])) {
                $sql .= " LIMIT " . (int)$limit['offset'] . ", " . (int)$limit['count'];
            } elseif (isset($limit['count'])) {
                $sql .= " LIMIT " . (int)$limit['count'];
            }
        }

        $rents = \ExpertNote\DB::getRows($sql, $params);

        // 모든 가격 옵션 일괄 조회 (N+1 문제 해결)
        if ($includePrices && !empty($rents)) {
            $rentIds = array_map(function($rent) { return $rent->idx; }, $rents);

            // named parameter로 IN 절 구성
            $placeholders = [];
            $priceParams = [];
            foreach ($rentIds as $i => $id) {
                $placeholders[] = ":rent_id_{$i}";
                $priceParams["rent_id_{$i}"] = $id;
            }

            $priceSql = "SELECT * FROM " . DB_PREFIX . "rent_price
                         WHERE rent_idx IN (" . implode(',', $placeholders) . ") AND monthly_rent_amount > 0
                         ORDER BY rent_idx, monthly_rent_amount ASC";
            $allPrices = \ExpertNote\DB::getRows($priceSql, $priceParams);

            // rent_idx별로 그룹화
            $pricesByRent = [];
            foreach ($allPrices as $price) {
                $pricesByRent[$price->rent_idx][] = $price;
            }

            // 각 rent에 prices 할당
            foreach ($rents as $rent) {
                $rent->prices = $pricesByRent[$rent->idx] ?? [];
            }
        }

        return $rents;
    }

    /**
     * 차량 개수 조회
     *
     * @param array $where WHERE 조건 배열
     * @return int 차량 개수
     */
    public static function getRentCount($where = []) {
        $sql = "SELECT COUNT(*) as cnt FROM " . DB_PREFIX . "rent r
                INNER JOIN " . DB_PREFIX . "rent_dealer d ON r.dealer_idx = d.idx AND d.status = 'PUBLISHED'";

        $params = [];
        $conditions = [];

        // WHERE 조건 처리
        foreach ($where as $key => $value) {
            if (strpos($key, ' LIKE') !== false) {
                $paramKey = str_replace([' LIKE', '.'], ['', '_'], $key);
                $conditions[] = $key . " :$paramKey";
                $params[$paramKey] = $value;
            } else {
                $paramKey = str_replace('.', '_', $key);
                $conditions[] = "$key = :$paramKey";
                $params[$paramKey] = $value;
            }
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $result = \ExpertNote\DB::getRow($sql, $params);
        return $result ? $result->cnt : 0;
    }

    /**
     * 차량 검색 (제목, 브랜드, 차번호 등 OR 조건 검색)
     *
     * @param string $searchQuery 검색어
     * @param array $filters 추가 필터 조건 (car_type, brand, fuel_type 등)
     * @param array $orderby ORDER BY 조건 배열
     * @param array $limit LIMIT 조건 배열
     * @return array 차량 목록 (min_price 컬럼 포함)
     */
    public static function searchRents($searchQuery, $filters = [], $orderby = [], $limit = []) {
        $sql = "SELECT r.*, MIN(p.monthly_rent_amount) as min_price, d.dealer_name, d.dealer_code,
                       rb.brand_name, rb.brand_name_en, rm.model_name, rm.model_name_en
                FROM " . DB_PREFIX . "rent r
                LEFT JOIN " . DB_PREFIX . "rent_price p ON r.idx = p.rent_idx
                INNER JOIN " . DB_PREFIX . "rent_dealer d ON r.dealer_idx = d.idx AND d.status = 'PUBLISHED'
                LEFT JOIN " . DB_PREFIX . "rent_brand rb ON r.brand_idx = rb.idx
                LEFT JOIN " . DB_PREFIX . "rent_model rm ON r.model_idx = rm.idx";

        $params = [];
        $conditions = [];

        // 검색어가 있으면 OR 조건으로 title, brand_name, model_name, car_number 검색
        if (!empty($searchQuery)) {
            $searchQuery = trim($searchQuery);
            $conditions[] = "(r.title LIKE :search_title OR rb.brand_name LIKE :search_brand OR rb.brand_name_en LIKE :search_brand_en OR rm.model_name LIKE :search_model OR rm.model_name_en LIKE :search_model_en OR r.car_number LIKE :search_car_number)";
            $params['search_title'] = "%{$searchQuery}%";
            $params['search_brand'] = "%{$searchQuery}%";
            $params['search_brand_en'] = "%{$searchQuery}%";
            $params['search_model'] = "%{$searchQuery}%";
            $params['search_model_en'] = "%{$searchQuery}%";
            $params['search_car_number'] = "%{$searchQuery}%";
        }

        // 추가 필터 처리
        foreach ($filters as $key => $value) {
            if (empty($value)) continue;

            $paramKey = str_replace('.', '_', $key);
            $conditions[] = "$key = :$paramKey";
            $params[$paramKey] = $value;
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        // GROUP BY 추가 (JOIN 때문에 필요)
        $sql .= " GROUP BY r.idx";

        // ORDER BY 처리
        if (!empty($orderby)) {
            $orderClauses = [];
            foreach ($orderby as $column => $direction) {
                $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
                if ($column === 'p.monthly_rent_amount') {
                    $orderClauses[] = "min_price $direction";
                } else {
                    $orderClauses[] = "$column $direction";
                }
            }
            $sql .= " ORDER BY " . implode(', ', $orderClauses);
        }

        // LIMIT 처리
        if (!empty($limit)) {
            if (isset($limit['offset']) && isset($limit['count'])) {
                $sql .= " LIMIT " . (int)$limit['offset'] . ", " . (int)$limit['count'];
            } elseif (isset($limit['count'])) {
                $sql .= " LIMIT " . (int)$limit['count'];
            }
        }

        return \ExpertNote\DB::getRows($sql, $params);
    }

    /**
     * 차량 검색 개수 조회
     *
     * @param string $searchQuery 검색어
     * @param array $filters 추가 필터 조건
     * @return int 차량 개수
     */
    public static function searchRentCount($searchQuery, $filters = []) {
        $sql = "SELECT COUNT(*) as cnt FROM " . DB_PREFIX . "rent r
                INNER JOIN " . DB_PREFIX . "rent_dealer d ON r.dealer_idx = d.idx AND d.status = 'PUBLISHED'
                LEFT JOIN " . DB_PREFIX . "rent_brand rb ON r.brand_idx = rb.idx
                LEFT JOIN " . DB_PREFIX . "rent_model rm ON r.model_idx = rm.idx";

        $params = [];
        $conditions = [];

        // 검색어가 있으면 OR 조건으로 title, brand_name, model_name, car_number 검색
        if (!empty($searchQuery)) {
            $searchQuery = trim($searchQuery);
            $conditions[] = "(r.title LIKE :search_title OR rb.brand_name LIKE :search_brand OR rb.brand_name_en LIKE :search_brand_en OR rm.model_name LIKE :search_model OR rm.model_name_en LIKE :search_model_en OR r.car_number LIKE :search_car_number)";
            $params['search_title'] = "%{$searchQuery}%";
            $params['search_brand'] = "%{$searchQuery}%";
            $params['search_brand_en'] = "%{$searchQuery}%";
            $params['search_model'] = "%{$searchQuery}%";
            $params['search_model_en'] = "%{$searchQuery}%";
            $params['search_car_number'] = "%{$searchQuery}%";
        }

        // 추가 필터 처리
        foreach ($filters as $key => $value) {
            if (empty($value)) continue;

            $paramKey = str_replace('.', '_', $key);
            $conditions[] = "$key = :$paramKey";
            $params[$paramKey] = $value;
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $result = \ExpertNote\DB::getRow($sql, $params);
        return $result ? $result->cnt : 0;
    }

    /**
     * 차량 등록
     *
     * @param array $data 차량 데이터
     * @return int 생성된 차량 IDX
     */
    public static function createRent($data) {
        try {
            \ExpertNote\DB::beginTransaction();

            // 필수 필드 확인
            if (empty($data['dealer_idx'])) {
                throw new \Exception('대리점 IDX는 필수입니다.');
            }

            if (empty($data['car_number'])) {
                throw new \Exception('차량번호는 필수입니다.');
            }

            if (empty($data['title'])) {
                throw new \Exception('차량명은 필수입니다.');
            }

            // 차량 기본 정보 저장
            // driver_range는 대리점(expertnote_rent_dealer)에서 관리됨
            $rentData = [
                'dealer_idx' => $data['dealer_idx'],
                'car_type' => $data['car_type'] ?? 'NEW',
                'car_number' => $data['car_number'],
                'title' => $data['title'],
                'model_year' => $data['model_year'] ?? null,
                'model_month' => $data['model_month'] ?? null,
                'mileage_km' => $data['mileage_km'] ?? null,
                'fuel_type' => $data['fuel_type'] ?? null,
                'option_exterior' => $data['option_exterior'] ?? null,
                'option_safety' => $data['option_safety'] ?? null,
                'option_convenience' => $data['option_convenience'] ?? null,
                'option_seat' => $data['option_seat'] ?? null,
                'contract_terms' => isset($data['contract_terms']) ? json_encode($data['contract_terms']) : null,
                'original_url' => $data['original_url'] ?? null,
                'status' => $data['status'] ?? 'active',
                'crawled_at' => $data['crawled_at'] ?? null
            ];

            $rentIdx = self::insertRent($rentData);

            // 가격 정보가 있으면 저장
            if (!empty($data['prices']) && is_array($data['prices'])) {
                foreach ($data['prices'] as $price) {
                    $price['rent_idx'] = $rentIdx;
                    self::addPrice($price);
                }
            }

            // 이미지 정보가 있으면 저장
            if (!empty($data['images']) && is_array($data['images'])) {
                foreach ($data['images'] as $index => $image) {
                    $imageData = [
                        'rent_idx' => $rentIdx,
                        'image_url' => $image['image_url'] ?? $image,
                        'original_url' => $image['original_url'] ?? null,
                        'image_order' => $image['image_order'] ?? $index,
                        'file_size' => $image['file_size'] ?? null
                    ];
                    self::addImage($imageData);
                }
            }

            \ExpertNote\DB::commit();

            return $rentIdx;

        } catch (\Exception $e) {
            \ExpertNote\DB::rollback();
            throw $e;
        }
    }

    /**
     * 차량 정보 수정
     *
     * @param int $idx 차량 IDX
     * @param array $data 수정할 데이터
     * @return bool 수정 성공 여부
     */
    public static function updateRent($idx, $data) {
        // driver_range는 대리점(expertnote_rent_dealer)에서 관리됨
        $allowedFields = [
            'car_type', 'car_number', 'title', 'model_year', 'model_month',
            'mileage_km', 'fuel_type', 'option_exterior', 'option_safety',
            'option_convenience', 'option_seat', 'contract_terms',
            'view_count', 'wish_count', 'original_url', 'status', 'crawled_at'
        ];

        $updateFields = [];
        $params = ['idx' => $idx];

        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields)) {
                if (in_array($key, ['contract_terms']) && is_array($value)) {
                    $value = json_encode($value);
                }
                $updateFields[] = "`$key` = :$key";
                $params[$key] = $value;
            }
        }

        if (empty($updateFields)) {
            return false;
        }

        $sql = "UPDATE " . DB_PREFIX . "rent SET " . implode(', ', $updateFields) . " WHERE idx = :idx";

        return \ExpertNote\DB::query($sql, $params);
    }

    /**
     * 차량 삭제 (소프트 삭제)
     *
     * @param int $idx 차량 IDX
     * @return bool 삭제 성공 여부
     */
    public static function deleteRent($idx) {
        return self::updateRent($idx, ['status' => 'deleted']);
    }

    /**
     * 차량 완전 삭제 (하드 삭제)
     *
     * @param int $idx 차량 IDX
     * @return bool 삭제 성공 여부
     */
    public static function deleteRentPermanently($idx) {
        $sql = "DELETE FROM " . DB_PREFIX . "rent WHERE idx = :idx";
        return \ExpertNote\DB::query($sql, ['idx' => $idx]);
    }

    /**
     * 차량 기본 정보 삽입 (내부 사용)
     *
     * @param array $data 차량 데이터
     * @return int 생성된 IDX
     */
    private static function insertRent($data) {
        $fields = array_keys($data);
        $placeholders = array_map(function($field) { return ":$field"; }, $fields);

        $sql = "INSERT INTO " . DB_PREFIX . "rent (" . implode(', ', $fields) . ")
                VALUES (" . implode(', ', $placeholders) . ")";

        \ExpertNote\DB::query($sql, $data);

        return \ExpertNote\DB::getLastInsertId();
    }

    /**
     * 조회수 증가
     *
     * @param int $idx 차량 IDX
     * @return bool 성공 여부
     */
    public static function incrementViewCount($idx) {
        $sql = "UPDATE " . DB_PREFIX . "rent SET view_count = view_count + 1 WHERE idx = :idx";
        return \ExpertNote\DB::query($sql, ['idx' => $idx]);
    }

    /**
     * 찜 개수 증가
     *
     * @param int $idx 차량 IDX
     * @return bool 성공 여부
     */
    public static function incrementWishCount($idx) {
        $sql = "UPDATE " . DB_PREFIX . "rent SET wish_count = wish_count + 1 WHERE idx = :idx";
        return \ExpertNote\DB::query($sql, ['idx' => $idx]);
    }

    /**
     * 찜 개수 감소
     *
     * @param int $idx 차량 IDX
     * @return bool 성공 여부
     */
    public static function decrementWishCount($idx) {
        $sql = "UPDATE " . DB_PREFIX . "rent SET wish_count = GREATEST(wish_count - 1, 0) WHERE idx = :idx";
        return \ExpertNote\DB::query($sql, ['idx' => $idx]);
    }

    /**
     * 가격 옵션 추가
     *
     * @param array $data 가격 데이터
     * @return int 생성된 IDX
     */
    public static function addPrice($data) {
        $fields = ['rent_idx', 'deposit_amount', 'rental_period_months',
                   'monthly_rent_amount', 'yearly_mileage_limit'];

        $insertData = [];
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $insertData[$field] = $data[$field];
            }
        }

        $placeholders = array_map(function($field) { return ":$field"; }, array_keys($insertData));

        $sql = "INSERT INTO " . DB_PREFIX . "rent_price (" . implode(', ', array_keys($insertData)) . ")
                VALUES (" . implode(', ', $placeholders) . ")";

        \ExpertNote\DB::query($sql, $insertData);

        return \ExpertNote\DB::getLastInsertId();
    }

    /**
     * 차량의 가격 옵션 목록 조회
     *
     * @param int $rentIdx 차량 IDX
     * @return array 가격 옵션 목록
     */
    public static function getPrices($rentIdx) {
        $sql = "SELECT * FROM " . DB_PREFIX . "rent_price WHERE rent_idx = :rent_idx AND monthly_rent_amount > 0 ORDER BY monthly_rent_amount ASC";
        return \ExpertNote\DB::getRows($sql, ['rent_idx' => $rentIdx]);
    }

    /**
     * 가격 옵션 삭제
     *
     * @param int $idx 가격 옵션 IDX
     * @return bool 삭제 성공 여부
     */
    public static function deletePrice($idx) {
        $sql = "DELETE FROM " . DB_PREFIX . "rent_price WHERE idx = :idx";
        return \ExpertNote\DB::query($sql, ['idx' => $idx]);
    }

    /**
     * 이미지 추가
     *
     * @param array $data 이미지 데이터
     * @return int 생성된 IDX
     */
    public static function addImage($data) {
        $fields = ['rent_idx', 'image_url', 'original_url', 'image_order', 'file_size'];

        $insertData = [];
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $insertData[$field] = $data[$field];
            }
        }

        $placeholders = array_map(function($field) { return ":$field"; }, array_keys($insertData));

        $sql = "INSERT INTO " . DB_PREFIX . "rent_images (" . implode(', ', array_keys($insertData)) . ")
                VALUES (" . implode(', ', $placeholders) . ")";

        \ExpertNote\DB::query($sql, $insertData);

        return \ExpertNote\DB::getLastInsertId();
    }

    /**
     * 차량의 이미지 목록 조회
     *
     * @param int $rentIdx 차량 IDX
     * @return array 이미지 목록
     */
    public static function getImages($rentIdx) {
        $sql = "SELECT * FROM " . DB_PREFIX . "rent_images WHERE rent_idx = :rent_idx ORDER BY image_order ASC";
        return \ExpertNote\DB::getRows($sql, ['rent_idx' => $rentIdx]);
    }

    /**
     * 이미지 삭제
     *
     * @param int $idx 이미지 IDX
     * @return bool 삭제 성공 여부
     */
    public static function deleteImage($idx) {
        $sql = "DELETE FROM " . DB_PREFIX . "rent_images WHERE idx = :idx";
        return \ExpertNote\DB::query($sql, ['idx' => $idx]);
    }

    /**
     * 대리점 정보 조회
     *
     * @param int $idx 대리점 IDX
     * @return object|false 대리점 정보 객체 또는 false
     */
    public static function getDealer($idx) {
        $sql = "SELECT * FROM " . DB_PREFIX . "rent_dealer WHERE idx = :idx";
        return \ExpertNote\DB::getRow($sql, ['idx' => $idx]);
    }

    /**
     * 대리점 코드로 조회
     *
     * @param string $dealerCode 대리점 코드
     * @return object|false 대리점 정보 객체 또는 false
     */
    public static function getDealerByCode($dealerCode) {
        $sql = "SELECT * FROM " . DB_PREFIX . "rent_dealer WHERE dealer_code = :dealer_code";
        return \ExpertNote\DB::getRow($sql, ['dealer_code' => $dealerCode]);
    }

    /**
     * 대리점 목록 조회
     *
     * @param array $where WHERE 조건 배열
     * @param array $orderby ORDER BY 조건 배열
     * @param array $limit LIMIT 조건 배열
     * @return array 대리점 목록
     */
    public static function getDealers($where = [], $orderby = [], $limit = []) {
        $sql = "SELECT * FROM " . DB_PREFIX . "rent_dealer";

        $params = [];
        $conditions = [];

        foreach ($where as $key => $value) {
            $conditions[] = "`$key` = :$key";
            $params[$key] = $value;
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        if (!empty($orderby)) {
            $orderClauses = [];
            foreach ($orderby as $column => $direction) {
                $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
                $orderClauses[] = "`$column` $direction";
            }
            $sql .= " ORDER BY " . implode(', ', $orderClauses);
        }

        if (!empty($limit)) {
            if (isset($limit['offset']) && isset($limit['count'])) {
                $sql .= " LIMIT " . (int)$limit['offset'] . ", " . (int)$limit['count'];
            } elseif (isset($limit['count'])) {
                $sql .= " LIMIT " . (int)$limit['count'];
            }
        }

        return \ExpertNote\DB::getRows($sql, $params);
    }

    /**
     * 대리점 등록
     *
     * @param array $data 대리점 데이터
     * @return int 생성된 대리점 IDX
     */
    public static function createDealer($data) {
        if (empty($data['dealer_code']) || empty($data['dealer_name'])) {
            throw new \Exception('대리점 코드와 이름은 필수입니다.');
        }

        $sql = "INSERT INTO " . DB_PREFIX . "rent_dealer (dealer_code, dealer_name)
                VALUES (:dealer_code, :dealer_name)";

        $params = [
            'dealer_code' => $data['dealer_code'],
            'dealer_name' => $data['dealer_name']
        ];

        \ExpertNote\DB::query($sql, $params);

        return \ExpertNote\DB::getLastInsertId();
    }

    /**
     * 대리점 정보 수정
     *
     * @param int $idx 대리점 IDX
     * @param array $data 수정할 데이터
     * @return bool 수정 성공 여부
     */
    public static function updateDealer($idx, $data) {
        $allowedFields = ['dealer_code', 'dealer_name', 'status', 'driver_range'];

        $updateFields = [];
        $params = ['idx' => $idx];

        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields)) {
                // driver_range가 배열이면 JSON으로 변환
                if ($key === 'driver_range' && is_array($value)) {
                    $value = json_encode($value, JSON_UNESCAPED_UNICODE);
                }
                $updateFields[] = "`$key` = :$key";
                $params[$key] = $value;
            }
        }

        if (empty($updateFields)) {
            return false;
        }

        $sql = "UPDATE " . DB_PREFIX . "rent_dealer SET " . implode(', ', $updateFields) . " WHERE idx = :idx";

        return \ExpertNote\DB::query($sql, $params);
    }

    /**
     * 대리점 삭제
     *
     * @param int $idx 대리점 IDX
     * @return bool 삭제 성공 여부
     */
    public static function deleteDealer($idx) {
        $sql = "DELETE FROM " . DB_PREFIX . "rent_dealer WHERE idx = :idx";
        return \ExpertNote\DB::query($sql, ['idx' => $idx]);
    }

    // ==================== 브랜드 관련 함수 ====================

    /**
     * 브랜드 조회 (단일)
     *
     * @param int $idx 브랜드 IDX
     * @return object|false 브랜드 정보 객체 또는 false
     */
    public static function getBrand($idx) {
        $sql = "SELECT * FROM " . DB_PREFIX . "rent_brand WHERE idx = :idx";
        return \ExpertNote\DB::getRow($sql, ['idx' => $idx]);
    }

    /**
     * 브랜드 목록 조회
     *
     * @param array $where WHERE 조건 배열
     * @param array $orderby ORDER BY 조건 배열
     * @param array $limit LIMIT 조건 배열
     * @return array 브랜드 목록
     */
    public static function getBrands($where = [], $orderby = [], $limit = []) {
        $sql = "SELECT * FROM " . DB_PREFIX . "rent_brand";

        $params = [];
        $conditions = [];

        foreach ($where as $key => $value) {
            if (strpos($key, ' LIKE') !== false) {
                $paramKey = str_replace([' LIKE', '.'], ['', '_'], $key);
                $conditions[] = $key . " :$paramKey";
                $params[$paramKey] = $value;
            } else {
                $paramKey = str_replace('.', '_', $key);
                $conditions[] = "`$key` = :$paramKey";
                $params[$paramKey] = $value;
            }
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        if (!empty($orderby)) {
            $orderClauses = [];
            foreach ($orderby as $column => $direction) {
                $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
                $orderClauses[] = "`$column` $direction";
            }
            $sql .= " ORDER BY " . implode(', ', $orderClauses);
        }

        if (!empty($limit)) {
            if (isset($limit['offset']) && isset($limit['count'])) {
                $sql .= " LIMIT " . (int)$limit['offset'] . ", " . (int)$limit['count'];
            } elseif (isset($limit['count'])) {
                $sql .= " LIMIT " . (int)$limit['count'];
            }
        }

        return \ExpertNote\DB::getRows($sql, $params);
    }

    /**
     * 브랜드 개수 조회
     *
     * @param array $where WHERE 조건 배열
     * @return int 브랜드 개수
     */
    public static function getBrandCount($where = []) {
        $sql = "SELECT COUNT(*) as cnt FROM " . DB_PREFIX . "rent_brand";

        $params = [];
        $conditions = [];

        foreach ($where as $key => $value) {
            if (strpos($key, ' LIKE') !== false) {
                $paramKey = str_replace([' LIKE', '.'], ['', '_'], $key);
                $conditions[] = $key . " :$paramKey";
                $params[$paramKey] = $value;
            } else {
                $paramKey = str_replace('.', '_', $key);
                $conditions[] = "`$key` = :$paramKey";
                $params[$paramKey] = $value;
            }
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $result = \ExpertNote\DB::getRow($sql, $params);
        return $result ? (int)$result->cnt : 0;
    }

    /**
     * 브랜드 등록/수정
     *
     * @param array $data 브랜드 데이터 (idx가 있으면 수정, 없으면 등록)
     * @return int|bool 등록 시 생성된 IDX, 수정 시 true, 실패 시 false
     */
    public static function setBrand($data) {
        $allowedFields = ['brand_name', 'brand_name_en', 'country_code', 'logo_url', 'sort_order', 'is_active'];

        if (!empty($data['idx'])) {
            // 수정
            $updateFields = [];
            $params = ['idx' => $data['idx']];

            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $updateFields[] = "`$field` = :$field";
                    $params[$field] = $data[$field];
                }
            }

            if (empty($updateFields)) {
                return false;
            }

            $sql = "UPDATE " . DB_PREFIX . "rent_brand SET " . implode(', ', $updateFields) . " WHERE idx = :idx";
            return \ExpertNote\DB::query($sql, $params);

        } else {
            // 등록
            if (empty($data['brand_name'])) {
                throw new \Exception('브랜드명은 필수입니다.');
            }

            $insertFields = [];
            $insertData = [];

            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $insertFields[] = $field;
                    $insertData[$field] = $data[$field];
                }
            }

            $placeholders = array_map(function($field) { return ":$field"; }, $insertFields);

            $sql = "INSERT INTO " . DB_PREFIX . "rent_brand (" . implode(', ', $insertFields) . ")
                    VALUES (" . implode(', ', $placeholders) . ")";

            \ExpertNote\DB::query($sql, $insertData);

            return \ExpertNote\DB::getLastInsertId();
        }
    }

    // ==================== 모델 관련 함수 ====================

    /**
     * 모델 조회 (단일)
     *
     * @param int $idx 모델 IDX
     * @return object|false 모델 정보 객체 또는 false
     */
    public static function getModel($idx) {
        $sql = "SELECT m.*, b.brand_name, b.brand_name_en, b.country_code
                FROM " . DB_PREFIX . "rent_model m
                LEFT JOIN " . DB_PREFIX . "rent_brand b ON m.brand_idx = b.idx
                WHERE m.idx = :idx";
        return \ExpertNote\DB::getRow($sql, ['idx' => $idx]);
    }

    /**
     * 모델 목록 조회
     *
     * @param array $where WHERE 조건 배열
     * @param array $orderby ORDER BY 조건 배열
     * @param array $limit LIMIT 조건 배열
     * @return array 모델 목록
     */
    public static function getModels($where = [], $orderby = [], $limit = []) {
        $sql = "SELECT m.*, b.brand_name, b.brand_name_en, b.country_code
                FROM " . DB_PREFIX . "rent_model m
                LEFT JOIN " . DB_PREFIX . "rent_brand b ON m.brand_idx = b.idx";

        $params = [];
        $conditions = [];

        foreach ($where as $key => $value) {
            if (strpos($key, ' LIKE') !== false) {
                $paramKey = str_replace([' LIKE', '.'], ['', '_'], $key);
                $conditions[] = $key . " :$paramKey";
                $params[$paramKey] = $value;
            } else {
                $paramKey = str_replace('.', '_', $key);
                // 테이블 별칭 처리
                if (strpos($key, '.') === false) {
                    $conditions[] = "m.`$key` = :$paramKey";
                } else {
                    $conditions[] = "$key = :$paramKey";
                }
                $params[$paramKey] = $value;
            }
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        if (!empty($orderby)) {
            $orderClauses = [];
            foreach ($orderby as $column => $direction) {
                $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
                // 테이블 별칭 처리
                if (strpos($column, '.') === false) {
                    $orderClauses[] = "m.`$column` $direction";
                } else {
                    $orderClauses[] = "$column $direction";
                }
            }
            $sql .= " ORDER BY " . implode(', ', $orderClauses);
        }

        if (!empty($limit)) {
            if (isset($limit['offset']) && isset($limit['count'])) {
                $sql .= " LIMIT " . (int)$limit['offset'] . ", " . (int)$limit['count'];
            } elseif (isset($limit['count'])) {
                $sql .= " LIMIT " . (int)$limit['count'];
            }
        }

        return \ExpertNote\DB::getRows($sql, $params);
    }

    /**
     * 모델 개수 조회
     *
     * @param array $where WHERE 조건 배열
     * @return int 모델 개수
     */
    public static function getModelCount($where = []) {
        $sql = "SELECT COUNT(*) as cnt FROM " . DB_PREFIX . "rent_model m";

        $params = [];
        $conditions = [];

        foreach ($where as $key => $value) {
            if (strpos($key, ' LIKE') !== false) {
                $paramKey = str_replace([' LIKE', '.'], ['', '_'], $key);
                $conditions[] = $key . " :$paramKey";
                $params[$paramKey] = $value;
            } else {
                $paramKey = str_replace('.', '_', $key);
                if (strpos($key, '.') === false) {
                    $conditions[] = "m.`$key` = :$paramKey";
                } else {
                    $conditions[] = "$key = :$paramKey";
                }
                $params[$paramKey] = $value;
            }
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $result = \ExpertNote\DB::getRow($sql, $params);
        return $result ? (int)$result->cnt : 0;
    }

    /**
     * 모델 등록/수정
     *
     * @param array $data 모델 데이터 (idx가 있으면 수정, 없으면 등록)
     * @return int|bool 등록 시 생성된 IDX, 수정 시 true, 실패 시 false
     */
    public static function setModel($data) {
        $allowedFields = ['brand_idx', 'model_name', 'model_name_en', 'segment', 'sort_order', 'is_active'];

        if (!empty($data['idx'])) {
            // 수정
            $updateFields = [];
            $params = ['idx' => $data['idx']];

            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $updateFields[] = "`$field` = :$field";
                    $params[$field] = $data[$field];
                }
            }

            if (empty($updateFields)) {
                return false;
            }

            $sql = "UPDATE " . DB_PREFIX . "rent_model SET " . implode(', ', $updateFields) . " WHERE idx = :idx";
            return \ExpertNote\DB::query($sql, $params);

        } else {
            // 등록
            if (empty($data['brand_idx']) || empty($data['model_name'])) {
                throw new \Exception('브랜드 IDX와 모델명은 필수입니다.');
            }

            $insertFields = [];
            $insertData = [];

            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $insertFields[] = $field;
                    $insertData[$field] = $data[$field];
                }
            }

            $placeholders = array_map(function($field) { return ":$field"; }, $insertFields);

            $sql = "INSERT INTO " . DB_PREFIX . "rent_model (" . implode(', ', $insertFields) . ")
                    VALUES (" . implode(', ', $placeholders) . ")";

            \ExpertNote\DB::query($sql, $insertData);

            return \ExpertNote\DB::getLastInsertId();
        }
    }

    // ==================== 연관 차량 검색 ====================

    /**
     * 연관 차량 검색 (FULLTEXT 검색)
     *
     * @param string $searchText 검색어
     * @param string $carType 차량 유형 (NEW, USED, 또는 null=전체)
     * @param int $limit 최대 개수
     * @param string $status 차량 상태
     * @return array 차량 목록
     */
    public static function searchRelatedRents($searchText, $carType = null, $limit = 8, $status = 'active') {
        // 검색어 정제 (FULLTEXT용)
        $searchText = trim($searchText);
        if (empty($searchText)) {
            return [];
        }

        // FULLTEXT 검색용 키워드 추출 (공백으로 분리하여 + 붙이기)
        $words = preg_split('/\s+/', $searchText);
        $fulltextQuery = '';
        foreach ($words as $word) {
            if (mb_strlen($word) >= 2) {
                $fulltextQuery .= '+' . $word . '* ';
            }
        }
        $fulltextQuery = trim($fulltextQuery);

        if (empty($fulltextQuery)) {
            return self::searchRelatedRentsLike($searchText, $carType, $limit, $status);
        }

        $params = [
            'status' => $status,
            'search' => $fulltextQuery
        ];

        $carTypeCondition = '';
        if ($carType) {
            $carTypeCondition = ' AND r.car_type = :car_type';
            $params['car_type'] = $carType;
        }

        $sql = "SELECT r.*, MIN(p.monthly_rent_amount) as min_price,
                       MATCH(r.title) AGAINST(:search IN BOOLEAN MODE) as relevance
                FROM " . DB_PREFIX . "rent r
                LEFT JOIN " . DB_PREFIX . "rent_price p ON r.idx = p.rent_idx
                INNER JOIN " . DB_PREFIX . "rent_dealer d ON r.dealer_idx = d.idx AND d.status = 'PUBLISHED'
                WHERE r.status = :status {$carTypeCondition}
                AND MATCH(r.title) AGAINST(:search IN BOOLEAN MODE)
                GROUP BY r.idx
                ORDER BY relevance DESC, r.created_at DESC
                LIMIT " . (int)$limit;

        try {
            $results = \ExpertNote\DB::getRows($sql, $params);
            if (!empty($results)) {
                return $results;
            }
        } catch (\Exception $e) {
            // FULLTEXT 인덱스가 없으면 LIKE 검색으로 폴백
        }

        return self::searchRelatedRentsLike($searchText, $carType, $limit, $status);
    }

    /**
     * 연관 차량 검색 (LIKE 폴백)
     *
     * @param string $searchText 검색어
     * @param string $carType 차량 유형 (NEW, USED, 또는 null=전체)
     * @param int $limit 최대 개수
     * @param string $status 차량 상태
     * @return array 차량 목록
     */
    private static function searchRelatedRentsLike($searchText, $carType = null, $limit = 8, $status = 'active') {
        // 검색어에서 키워드 추출
        $words = preg_split('/\s+/', $searchText);
        $words = array_filter($words, function($word) {
            return mb_strlen($word) >= 2;
        });

        if (empty($words)) {
            return [];
        }

        $params = ['status' => $status];
        $likeConditions = [];

        foreach ($words as $i => $word) {
            $paramName = "word{$i}";
            $likeConditions[] = "r.title LIKE :{$paramName}";
            $params[$paramName] = '%' . $word . '%';
        }

        $carTypeCondition = '';
        if ($carType) {
            $carTypeCondition = ' AND r.car_type = :car_type';
            $params['car_type'] = $carType;
        }

        $sql = "SELECT r.*, MIN(p.monthly_rent_amount) as min_price
                FROM " . DB_PREFIX . "rent r
                LEFT JOIN " . DB_PREFIX . "rent_price p ON r.idx = p.rent_idx
                INNER JOIN " . DB_PREFIX . "rent_dealer d ON r.dealer_idx = d.idx AND d.status = 'PUBLISHED'
                WHERE r.status = :status {$carTypeCondition}
                AND (" . implode(' OR ', $likeConditions) . ")
                GROUP BY r.idx
                ORDER BY r.created_at DESC
                LIMIT " . (int)$limit;

        return \ExpertNote\DB::getRows($sql, $params);
    }

    /**
     * 대리점의 보험 조건 조회
     *
     * @param int $dealerIdx 대리점 IDX
     * @return object|false 보험 조건 객체 또는 false
     */
    public static function getInsurance($dealerIdx) {
        $sql = "SELECT * FROM " . DB_PREFIX . "rent_insurance WHERE dealer_idx = :dealer_idx";
        return \ExpertNote\DB::getRow($sql, ['dealer_idx' => $dealerIdx]);
    }

    /**
     * 보험 조건 저장 (등록 또는 수정)
     *
     * @param int $dealerIdx 대리점 IDX
     * @param array $data 보험 데이터
     * @return bool 성공 여부
     */
    public static function saveInsurance($dealerIdx, $data) {
        $existing = self::getInsurance($dealerIdx);

        $fields = [
            'liability_personal', 'liability_property', 'liability_self_injury',
            'deductible_personal', 'deductible_property', 'deductible_self_injury',
            'deductible_own_car'
        ];

        if ($existing) {
            // 수정
            $updateFields = [];
            $params = ['dealer_idx' => $dealerIdx];

            foreach ($fields as $field) {
                if (isset($data[$field])) {
                    $updateFields[] = "`$field` = :$field";
                    $params[$field] = $data[$field];
                }
            }

            if (empty($updateFields)) {
                return false;
            }

            $sql = "UPDATE " . DB_PREFIX . "rent_insurance SET " . implode(', ', $updateFields) .
                   " WHERE dealer_idx = :dealer_idx";

            return \ExpertNote\DB::query($sql, $params);

        } else {
            // 신규 등록
            $data['dealer_idx'] = $dealerIdx;

            $insertFields = ['dealer_idx'];
            $insertData = ['dealer_idx' => $dealerIdx];

            foreach ($fields as $field) {
                if (isset($data[$field])) {
                    $insertFields[] = $field;
                    $insertData[$field] = $data[$field];
                }
            }

            $placeholders = array_map(function($field) { return ":$field"; }, $insertFields);

            $sql = "INSERT INTO " . DB_PREFIX . "rent_insurance (" . implode(', ', $insertFields) . ")
                    VALUES (" . implode(', ', $placeholders) . ")";

            \ExpertNote\DB::query($sql, $insertData);

            return \ExpertNote\DB::getLastInsertId();
        }
    }
}
