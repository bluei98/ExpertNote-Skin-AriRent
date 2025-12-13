<?php
/**
 * 렌트 대리점 관리 API
 *
 * GET: 대리점 조회 (단일/목록)
 * POST: 대리점 등록
 * PUT: 대리점 수정
 * DELETE: 대리점 삭제
 */

function processGet() {
    global $ret, $parameters;

    $idx = $parameters['idx'] ?? null;

    if ($idx) {
        // 단일 대리점 조회
        $sql = "SELECT d.*,
                (SELECT COUNT(*) FROM " . DB_PREFIX . "rent WHERE dealer_idx = d.idx) as car_count
                FROM " . DB_PREFIX . "rent_dealer d WHERE d.idx = :idx";
        $dealer = \ExpertNote\DB::getRow($sql, ['idx' => $idx]);

        if (!$dealer) {
            header("HTTP/1.1 404 Not Found");
            $ret['result'] = "FAILED";
            $ret['message'] = __('대리점을 찾을 수 없습니다.', 'api');
            return;
        }

        // 보험 정보 조회
        $insuranceSql = "SELECT * FROM " . DB_PREFIX . "rent_insurance WHERE dealer_idx = :dealer_idx";
        $dealer->insurance = \ExpertNote\DB::getRow($insuranceSql, ['dealer_idx' => $idx]);

        $ret['data'] = $dealer;
    } else {
        // 대리점 목록 조회
        $limit = intval($parameters['limit'] ?? 100);
        $offset = intval($parameters['offset'] ?? 0);
        $status = $parameters['status'] ?? null;

        $wheres = [];
        $params = [];

        if ($status) {
            $wheres[] = "d.status = :status";
            $params['status'] = $status;
        }

        $sql = "SELECT d.*,
                (SELECT COUNT(*) FROM " . DB_PREFIX . "rent WHERE dealer_idx = d.idx) as car_count
                FROM " . DB_PREFIX . "rent_dealer d";

        if (count($wheres) > 0) {
            $sql .= " WHERE " . implode(" AND ", $wheres);
        }

        $sql .= " ORDER BY d.dealer_name ASC LIMIT {$offset}, {$limit}";

        $dealers = \ExpertNote\DB::getRows($sql, $params);
        $ret['data'] = $dealers;
    }
}

function processPost() {
    global $ret, $parameters;

    checkAdmin();

    // 필수 파라미터 확인
    if (empty($parameters['dealer_code']) || empty($parameters['dealer_name'])) {
        header("HTTP/1.1 400 Bad Request");
        $ret['result'] = "FAILED";
        $ret['message'] = __('대리점 코드와 이름은 필수입니다.', 'api');
        return;
    }

    // 대리점 코드 중복 확인
    $checkSql = "SELECT idx FROM " . DB_PREFIX . "rent_dealer WHERE dealer_code = :dealer_code";
    $existing = \ExpertNote\DB::getRow($checkSql, ['dealer_code' => $parameters['dealer_code']]);

    if ($existing) {
        header("HTTP/1.1 400 Bad Request");
        $ret['result'] = "FAILED";
        $ret['message'] = __('이미 존재하는 대리점 코드입니다.', 'api');
        return;
    }

    $data = [
        'dealer_code' => trim($parameters['dealer_code']),
        'dealer_name' => trim($parameters['dealer_name']),
        'business_number' => trim($parameters['business_number'] ?? ''),
        'representative' => trim($parameters['representative'] ?? ''),
        'phone' => trim($parameters['phone'] ?? ''),
        'email' => trim($parameters['email'] ?? ''),
        'address' => trim($parameters['address'] ?? ''),
        'status' => $parameters['status'] ?? 'active'
    ];

    $columns = implode(', ', array_keys($data));
    $placeholders = ':' . implode(', :', array_keys($data));
    $sql = "INSERT INTO " . DB_PREFIX . "rent_dealer ({$columns}) VALUES ({$placeholders})";
    \ExpertNote\DB::query($sql, $data);

    $newIdx = \ExpertNote\DB::getLastInsertId();

    $ret['data'] = ['idx' => $newIdx];
    $ret['message'] = __('대리점이 등록되었습니다.', 'api');
}

function processPut() {
    global $ret, $parameters;

    checkAdmin();

    if (empty($parameters['idx'])) {
        header("HTTP/1.1 400 Bad Request");
        $ret['result'] = "FAILED";
        $ret['message'] = __('대리점 IDX가 필요합니다.', 'api');
        return;
    }

    $idx = $parameters['idx'];

    // 기존 대리점 확인
    $checkSql = "SELECT idx FROM " . DB_PREFIX . "rent_dealer WHERE idx = :idx";
    $existing = \ExpertNote\DB::getRow($checkSql, ['idx' => $idx]);

    if (!$existing) {
        header("HTTP/1.1 404 Not Found");
        $ret['result'] = "FAILED";
        $ret['message'] = __('대리점을 찾을 수 없습니다.', 'api');
        return;
    }

    // 업데이트할 필드 수집
    $allowedFields = [
        'dealer_code', 'dealer_name', 'business_number', 'representative',
        'phone', 'email', 'address', 'status'
    ];

    $sets = [];
    $params = ['idx' => $idx];

    foreach ($allowedFields as $field) {
        if (isset($parameters[$field])) {
            $sets[] = "{$field} = :{$field}";
            $params[$field] = $parameters[$field];
        }
    }

    if (count($sets) === 0) {
        header("HTTP/1.1 400 Bad Request");
        $ret['result'] = "FAILED";
        $ret['message'] = __('업데이트할 필드가 없습니다.', 'api');
        return;
    }

    // 대리점 코드 중복 확인 (변경 시)
    if (isset($parameters['dealer_code'])) {
        $dupSql = "SELECT idx FROM " . DB_PREFIX . "rent_dealer WHERE dealer_code = :dealer_code AND idx != :idx";
        $dup = \ExpertNote\DB::getRow($dupSql, ['dealer_code' => $parameters['dealer_code'], 'idx' => $idx]);
        if ($dup) {
            header("HTTP/1.1 400 Bad Request");
            $ret['result'] = "FAILED";
            $ret['message'] = __('이미 존재하는 대리점 코드입니다.', 'api');
            return;
        }
    }

    $sql = "UPDATE " . DB_PREFIX . "rent_dealer SET " . implode(', ', $sets) . " WHERE idx = :idx";
    \ExpertNote\DB::query($sql, $params);

    $ret['message'] = __('대리점 정보가 수정되었습니다.', 'api');
}

function processDelete() {
    global $ret, $parameters;

    checkAdmin();

    if (empty($parameters['idx'])) {
        header("HTTP/1.1 400 Bad Request");
        $ret['result'] = "FAILED";
        $ret['message'] = __('대리점 IDX가 필요합니다.', 'api');
        return;
    }

    $idx = $parameters['idx'];

    // 기존 대리점 확인
    $checkSql = "SELECT idx FROM " . DB_PREFIX . "rent_dealer WHERE idx = :idx";
    $existing = \ExpertNote\DB::getRow($checkSql, ['idx' => $idx]);

    if (!$existing) {
        header("HTTP/1.1 404 Not Found");
        $ret['result'] = "FAILED";
        $ret['message'] = __('대리점을 찾을 수 없습니다.', 'api');
        return;
    }

    // 소속 차량 확인
    $carCountSql = "SELECT COUNT(*) as cnt FROM " . DB_PREFIX . "rent WHERE dealer_idx = :idx";
    $carCount = \ExpertNote\DB::getRow($carCountSql, ['idx' => $idx]);

    if ($carCount->cnt > 0) {
        header("HTTP/1.1 400 Bad Request");
        $ret['result'] = "FAILED";
        $ret['message'] = __('소속 차량이 있는 대리점은 삭제할 수 없습니다. 먼저 차량을 삭제하거나 다른 대리점으로 이동해주세요.', 'api');
        return;
    }

    // 보험 정보 삭제
    \ExpertNote\DB::query("DELETE FROM " . DB_PREFIX . "rent_insurance WHERE dealer_idx = :idx", ['idx' => $idx]);

    // 대리점 삭제
    \ExpertNote\DB::query("DELETE FROM " . DB_PREFIX . "rent_dealer WHERE idx = :idx", ['idx' => $idx]);

    $ret['message'] = __('대리점이 삭제되었습니다.', 'api');
}
