<?php
/**
 * 찜하기(위시리스트) API
 *
 * GET: 찜하기 상태 확인
 * POST: 찜하기 추가
 * DELETE: 찜하기 제거
 */

function processGet() {
    global $parameters;

    // IP 주소 가져오기
    $ipAddress = $_SERVER['REMOTE_ADDR'];

    // 차량 IDX 확인
    $rentIdx = isset($parameters['rent_idx']) ? intval($parameters['rent_idx']) : 0;

    if (!$rentIdx) {
        header("HTTP/1.1 400 Bad Request");
        $ret['result'] = 'FAILED';
        $ret['result_code'] = 400;
        $ret['message'] = '차량 정보가 필요합니다.';
        echo json_encode($ret, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 찜하기 상태 확인
    $sql = "SELECT COUNT(*) as cnt FROM " . DB_PREFIX . "rent_wishlist
            WHERE ip_address = :ip_address AND rent_idx = :rent_idx";
    $result = \ExpertNote\DB::getRow($sql, [
        'ip_address' => $ipAddress,
        'rent_idx' => $rentIdx
    ]);

    $ret['result'] = 'SUCCESS';
    $ret['result_code'] = 0;
    $ret['success'] = true;
    $ret['data'] = [
        'is_wishlisted' => $result->cnt > 0
    ];

    echo json_encode($ret, JSON_UNESCAPED_UNICODE);
    exit;
}

function processPost() {
    global $parameters;

    // IP 주소 가져오기
    $ipAddress = $_SERVER['REMOTE_ADDR'];

    // 로그인된 경우 user_id도 함께 저장
    $userId = null;
    if (\ExpertNote\User\User::isLogin()) {
        $userId = $_SESSION['user_id'];
    }

    // 차량 IDX 확인
    $rentIdx = isset($parameters['rent_idx']) ? intval($parameters['rent_idx']) : 0;

    if (!$rentIdx) {
        header("HTTP/1.1 400 Bad Request");
        $ret['result'] = 'FAILED';
        $ret['result_code'] = 400;
        $ret['message'] = '차량 정보가 필요합니다.';
        echo json_encode($ret, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 중복 체크
    $sql = "SELECT COUNT(*) as cnt FROM " . DB_PREFIX . "rent_wishlist
            WHERE ip_address = :ip_address AND rent_idx = :rent_idx";
    $exists = \ExpertNote\DB::getRow($sql, [
        'ip_address' => $ipAddress,
        'rent_idx' => $rentIdx
    ]);

    if ($exists->cnt > 0) {
        $ret['result'] = 'SUCCESS';
        $ret['result_code'] = 0;
        $ret['success'] = true;
        $ret['message'] = '이미 찜한 차량입니다.';
        $ret['data'] = ['is_wishlisted' => true];
    } else {
        // 찜하기 추가
        $sql = "INSERT INTO " . DB_PREFIX . "rent_wishlist (user_id, ip_address, rent_idx)
                VALUES (:user_id, :ip_address, :rent_idx)";
        \ExpertNote\DB::query($sql, [
            'user_id' => $userId,
            'ip_address' => $ipAddress,
            'rent_idx' => $rentIdx
        ]);

        // expertnote_rent 테이블의 wish_count 증가
        $sql = "UPDATE " . DB_PREFIX . "rent SET wish_count = wish_count + 1 WHERE idx = :rent_idx";
        \ExpertNote\DB::query($sql, ['rent_idx' => $rentIdx]);

        $ret['result'] = 'SUCCESS';
        $ret['result_code'] = 0;
        $ret['success'] = true;
        $ret['message'] = '찜하기에 추가되었습니다.';
        $ret['data'] = ['is_wishlisted' => true];
    }

    echo json_encode($ret, JSON_UNESCAPED_UNICODE);
    exit;
}

function processPut() {
    // PUT은 POST와 동일하게 처리
    processPost();
}

function processDelete() {
    global $parameters;

    // IP 주소 가져오기
    $ipAddress = $_SERVER['REMOTE_ADDR'];

    // 차량 IDX 확인
    $rentIdx = isset($parameters['rent_idx']) ? intval($parameters['rent_idx']) : 0;

    if (!$rentIdx) {
        header("HTTP/1.1 400 Bad Request");
        $ret['result'] = 'FAILED';
        $ret['result_code'] = 400;
        $ret['message'] = '차량 정보가 필요합니다.';
        echo json_encode($ret, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 찜하기 제거
    $sql = "DELETE FROM " . DB_PREFIX . "rent_wishlist
            WHERE ip_address = :ip_address AND rent_idx = :rent_idx";
    \ExpertNote\DB::query($sql, [
        'ip_address' => $ipAddress,
        'rent_idx' => $rentIdx
    ]);

    // expertnote_rent 테이블의 wish_count 감소 (0 이하로 내려가지 않도록)
    $sql = "UPDATE " . DB_PREFIX . "rent
            SET wish_count = GREATEST(0, wish_count - 1)
            WHERE idx = :rent_idx";
    \ExpertNote\DB::query($sql, ['rent_idx' => $rentIdx]);

    $ret['result'] = 'SUCCESS';
    $ret['result_code'] = 0;
    $ret['success'] = true;
    $ret['message'] = '찜하기에서 제거되었습니다.';
    $ret['data'] = ['is_wishlisted' => false];

    echo json_encode($ret, JSON_UNESCAPED_UNICODE);
    exit;
}
