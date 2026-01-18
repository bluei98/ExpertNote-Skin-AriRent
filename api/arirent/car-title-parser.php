<?php
/**
 * 차량 제목 파싱 API
 *
 * 차량 title 문자열을 받아 DB 테이블 기준으로 brand/model을 식별하고
 * 정규화된 JSON을 반환한다.
 *
 * POST: 차량 제목 파싱
 */

// 브랜드별 등급 형식 정의
// ENGINE_FIRST: 엔진/배기량이 먼저 오는 브랜드 (수입차 위주)
const ENGINE_FIRST_BRANDS = ['제네시스', '벤츠', '아우디', 'BMW'];

// 트림 키워드 목록
const TRIM_KEYWORDS = [
    '프레스티지', '노블레스', '익스클루시브',
    '시그니처', '캘리그래피', '모던', '트렌디'
];

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
        'color'      => null  // 항상 null (시트 컬러는 색상이 아님)
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

    // 2. 등급 파싱
    $result['grade'] = parseGrade($title, $result['brand_name']);

    return $result;
}

/**
 * DB에서 모델 찾기
 *
 * title에 포함된 model_name을 찾는다.
 * 길이가 긴 모델명을 우선 매칭하여 정확도를 높인다.
 * 예: "그랜저IG" > "그랜저", "K5 DL3" > "K5"
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
 * 등급(grade) 파싱
 *
 * 브랜드에 따라 다른 형식:
 * - ENGINE_FIRST (제네시스, 벤츠, BMW, 아우디): "{엔진} {구동방식}"
 * - TRIM_FIRST (현대, 기아 등): "{트림} {배기량}"
 */
function parseGrade(string $title, string $brandName): ?string {
    // 괄호 제거 (시트, 옵션 정보 제거)
    $clean = preg_replace('/\([^)]*\)/u', '', $title);

    // 엔진/배기량 추출: 1.6, 2.0, 2.5T, 3.5 터보 등
    preg_match('/\b(\d\.\d(T)?|\d\.\d 터보)\b/u', $clean, $engine);

    // 구동방식 추출: 2WD, AWD, 4WD
    preg_match('/\b(2WD|AWD|4WD)\b/u', $clean, $drive);

    // 트림 추출
    $trim = null;
    foreach (TRIM_KEYWORDS as $t) {
        if (mb_strpos($clean, $t) !== false) {
            $trim = $t;
            break;
        }
    }

    // 브랜드별 조합 규칙
    if (in_array($brandName, ENGINE_FIRST_BRANDS)) {
        // ENGINE_FIRST: {엔진} {구동방식}
        $parts = array_filter([
            $engine[0] ?? null,
            $drive[0] ?? null
        ]);
        return !empty($parts) ? implode(' ', $parts) : null;
    }

    // TRIM_FIRST: {트림} {배기량}
    $parts = array_filter([
        $trim,
        $engine[0] ?? null
    ]);

    return !empty($parts) ? implode(' ', $parts) : null;
}
