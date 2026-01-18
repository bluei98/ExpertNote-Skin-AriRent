<?php
/**
 * 차량 제목 파싱 API (OpenAI 기반)
 *
 * 차량 title 문자열을 받아 DB 테이블 기준으로 brand/model을 식별하고
 * OpenAI를 통해 grade를 정규화하여 JSON을 반환한다.
 *
 * POST: 차량 제목 파싱
 */

function processPost() {
    global $ret, $parameters;

    $title = trim($parameters['title'] ?? '');

    if (empty($title)) {
        header("HTTP/1.1 400 Bad Request");
        $ret['result'] = "FAILED";
        $ret['message'] = __('차량 제목이 필요합니다.', 'api');
        return;
    }

    $ret['data'] = parseCarTitle($title);
}

/**
 * 차량 제목을 파싱하여 브랜드, 모델, 등급 정보를 추출
 */
function parseCarTitle(string $title): array {
    $result = [
        'brand_idx'  => null,
        'brand_name' => null,
        'model_idx'  => null,
        'model_name' => null,
        'grade'      => null,
        'color'      => null
    ];

    // 1. 모델 & 브랜드 DB 조회
    $modelInfo = findModel($title);

    if (!$modelInfo) {
        return $result;
    }

    $result['brand_idx']  = (int)$modelInfo->brand_idx;
    $result['brand_name'] = $modelInfo->brand_name;
    $result['model_idx']  = (int)$modelInfo->model_idx;
    $result['model_name'] = $modelInfo->model_name;

    // 2. OpenAI로 grade 파싱
    $result['grade'] = parseGradeWithAI($title, $modelInfo->model_name, $modelInfo->brand_name);

    return $result;
}

/**
 * DB에서 모델 찾기
 */
function findModel(string $title): ?object {
    $sql = "
        SELECT
            m.idx AS model_idx,
            m.model_name,
            b.idx AS brand_idx,
            b.brand_name
        FROM " . DB_PREFIX . "rent_model m
        JOIN " . DB_PREFIX . "rent_brand b ON b.idx = m.brand_idx
        WHERE :title LIKE CONCAT('%', m.model_name, '%')
          AND m.is_active = 1
          AND b.is_active = 1
        ORDER BY LENGTH(m.model_name) DESC
        LIMIT 1
    ";

    $result = \ExpertNote\DB::getRow($sql, ['title' => $title]);

    return $result ?: null;
}

/**
 * OpenAI를 사용하여 grade 파싱
 */
function parseGradeWithAI(string $title, string $modelName, string $brandName): ?string {
    // OpenAI API 설정 확인
    $openapi = \ExpertNote\SiteMeta::get("openapi");
    if (!isset($openapi["openai"]["api_key"])) {
        // API 키가 없으면 기본 파싱 사용
        return parseGradeFallback($title, $modelName);
    }

    $client = \OpenAI::client($openapi["openai"]["api_key"]);

    $systemPrompt = <<<PROMPT
당신은 차량 등급(grade) 추출 전문가입니다.

규칙:
1. 브랜드명, 모델명 제거
2. 접두사 제거: "더뉴", "올뉴", "뉴", "더 뉴", "올 뉴" 등
3. 세대 정보 제거: "(N세대)", "N세대" 형식
4. 괄호 안 내용 제거: 시트 색상, 옵션 정보 등
5. 내부 코드 제거: V1, V2, S8 등 모델 내부 버전 코드
6. 남은 정보로 grade 구성: 엔진/배기량, 구동방식, 트림명

반환: grade 문자열만 (없으면 빈 문자열)
PROMPT;

    $userPrompt = "차량명: {$title}\n브랜드: {$brandName}\n모델: {$modelName}\n\ngrade를 추출하세요.";

    try {
        $response = $client->chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt]
            ],
            'temperature' => 0.1,
            'max_tokens' => 100
        ]);

        $grade = trim($response->choices[0]->message->content);

        // 빈 문자열이나 "없음" 같은 응답 처리
        if (empty($grade) || $grade === '없음' || $grade === 'null' || $grade === '-') {
            return null;
        }

        return $grade;
    } catch (\Exception $e) {
        // 에러 시 기본 파싱 사용
        return parseGradeFallback($title, $modelName);
    }
}

/**
 * 기본 grade 파싱 (OpenAI 실패 시 폴백)
 */
function parseGradeFallback(string $title, string $modelName): ?string {
    // 괄호 제거
    $clean = preg_replace('/\([^)]*\)/u', '', $title);

    // 모델명 제거
    $clean = str_replace($modelName, '', $clean);

    // 접두사 제거
    $prefixes = ['더 뉴 ', '더뉴 ', '올 뉴 ', '올뉴 ', '뉴 ', '더 뉴', '더뉴', '올 뉴', '올뉴', '뉴'];
    foreach ($prefixes as $prefix) {
        $clean = str_replace($prefix, '', $clean);
    }

    // 세대 정보 제거
    $clean = preg_replace('/\d세대/u', '', $clean);

    // 정리
    $clean = trim(preg_replace('/\s+/', ' ', $clean));

    return !empty($clean) ? $clean : null;
}
