<?php
/**
 * 빠른 상담 신청 API
 * arirent 스킨용
 *
 * POST /api/arirent/consult
 *
 * 파라미터:
 *   - name: 이름 (필수)
 *   - phone: 연락처 (필수)
 *   - region: 지역 (필수)
 *   - car_type: 차종 (필수)
 *
 * 반환:
 *   - result: SUCCESS|FAILED
 *   - message: 메시지
 */

// JSON 응답 헤더
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');

// 응답 초기화
$ret = [
    'result' => 'FAILED',
    'result_code' => 0,
    'message' => '',
    'data' => null
];

$startTime = microtime(true);

// 디스코드 웹훅 URL
$discordWebhookUrl = 'https://discordapp.com/api/webhooks/1439930770943901848/BwO0WGZ0kavQHGVn7F_LCt2zGJrC0dqTYtJWKpP4KUON9t61t6BWBjYowWPQ1HRMKZv8';

try {
    // POST 요청만 허용
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception(__('POST 요청만 허용됩니다.', 'api'), 405);
    }

    // JSON 바디 파싱
    $input = json_decode(file_get_contents('php://input'), true);

    $name = trim($input['name'] ?? $_POST['name'] ?? '');
    $phone = trim($input['phone'] ?? $_POST['phone'] ?? '');
    $region = trim($input['region'] ?? $_POST['region'] ?? '');
    $carType = trim($input['car_type'] ?? $_POST['car_type'] ?? '');

    // 필수 파라미터 검증
    if (empty($name)) {
        throw new Exception(__('이름을 입력해주세요.', 'api'), 400);
    }
    if (empty($phone)) {
        throw new Exception(__('연락처를 입력해주세요.', 'api'), 400);
    }
    if (empty($region)) {
        throw new Exception(__('지역을 선택해주세요.', 'api'), 400);
    }
    if (empty($carType)) {
        throw new Exception(__('차종을 선택해주세요.', 'api'), 400);
    }

    // 연락처 형식 검증 (간단한 검증)
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (strlen($phone) < 10 || strlen($phone) > 11) {
        throw new Exception(__('올바른 연락처를 입력해주세요.', 'api'), 400);
    }

    // 디스코드 메시지 생성
    $discordMessage = [
        'embeds' => [
            [
                'title' => '🚗 새로운 상담 신청',
                'color' => 0x5865F2, // 디스코드 블루
                'fields' => [
                    [
                        'name' => '👤 이름',
                        'value' => $name,
                        'inline' => true
                    ],
                    [
                        'name' => '📞 연락처',
                        'value' => $phone,
                        'inline' => true
                    ],
                    [
                        'name' => '📍 지역',
                        'value' => $region,
                        'inline' => true
                    ],
                    [
                        'name' => '🚙 차종',
                        'value' => $carType,
                        'inline' => true
                    ],
                    [
                        'name' => '🌐 IP 주소',
                        'value' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
                        'inline' => true
                    ]
                ],
                'timestamp' => date('c'),
                'footer' => [
                    'text' => 'AriRent 상담 신청'
                ]
            ]
        ]
    ];

    // 디스코드 웹훅 전송
    $ch = curl_init($discordWebhookUrl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($discordMessage));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    // 디스코드 응답 확인 (204 No Content가 성공)
    if ($httpCode !== 204 && $httpCode !== 200) {
        throw new Exception(__('상담 신청 전송에 실패했습니다. 잠시 후 다시 시도해주세요.', 'api'), 500);
    }

    $ret['result'] = 'SUCCESS';
    $ret['message'] = __('상담 신청이 완료되었습니다. 빠른 시간 내에 연락드리겠습니다.', 'api');

} catch (Exception $e) {
    $code = $e->getCode() ?: 500;
    http_response_code($code);
    $ret['result'] = 'FAILED';
    $ret['result_code'] = $code;
    $ret['message'] = $e->getMessage();
}

$ret['elapsed_time'] = number_format(microtime(true) - $startTime, 4);
echo json_encode($ret, JSON_UNESCAPED_UNICODE);
exit;
