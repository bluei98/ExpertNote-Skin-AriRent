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
// TRIM_FIRST: 트림명이 먼저 오는 브랜드 (국산차 위주)
const ENGINE_FIRST_BRANDS = ['제네시스', 'Genesis', '벤츠', 'Mercedes-Benz', 'BMW', '아우디', 'Audi'];
const TRIM_FIRST_BRANDS = ['현대', 'Hyundai', '기아', 'Kia', '르노코리아', 'Renault Korea', 'KG모빌리티', 'KG Mobility'];

// 트림 키워드 (우선순위 순)
const TRIM_KEYWORDS = [
    '캘리그래피', '시그니처', '익스클루시브', '프레스티지', '노블레스',
    '인스퍼레이션', '센세이션', '프리미엄', '럭셔리', '모던', '트렌디',
    '그래비티', 'GT', 'N Line', 'N라인'
];

// 엔진/배기량 패턴 (정규식용)
const ENGINE_PATTERNS = [
    '3\.5T', '2\.5T', '1\.6T', '2\.0T', '3\.0T',  // 터보 (T 붙은 것 먼저)
    '3\.5', '2\.5', '2\.0', '1\.6', '1\.5', '3\.0', '2\.2',  // 일반 배기량
    '터보'
];

// 구동방식 패턴
const DRIVE_PATTERNS = ['AWD', '4WD', '2WD', 'RWD', 'FWD'];

function processPost() {
    global $ret, $parameters;

    $title = trim($parameters['title'] ?? '');

    if (empty($title)) {
        header("HTTP/1.1 400 Bad Request");
        $ret['result'] = "FAILED";
        $ret['message'] = __('차량 제목이 필요합니다.', 'api');
        return;
    }

    // 파싱 실행
    $result = parseCarTitle($title);
    $ret['data'] = $result;
}

/**
 * 차량 제목을 파싱하여 브랜드, 모델, 등급 정보를 추출
 *
 * @param string $title 원본 차량 제목
 * @return array 파싱 결과
 */
function parseCarTitle(string $title): array {
    // 기본 응답 구조 (실패 시 반환값)
    $result = [
        'brand_idx' => null,
        'brand_name' => null,
        'model_idx' => null,
        'model_name' => null,
        'grade' => null,
        'color' => null  // 항상 null (시트 컬러는 색상이 아님)
    ];

    // 1. 괄호 안 내용 제거 (시트 컬러 등 불필요한 정보 제거)
    // 예: "K5 2.0 프레스티지(블랙시트)" → "K5 2.0 프레스티지"
    $cleanTitle = preg_replace('/\([^)]*\)/', '', $title);
    $cleanTitle = trim($cleanTitle);

    // 2. 모델 찾기 (가장 중요한 단계)
    // brand는 model을 통해서만 찾는다 (brand 단독 매칭 금지)
    $model = findModel($cleanTitle);

    if (!$model) {
        // 모델을 찾지 못하면 빈 응답 반환
        return $result;
    }

    $result['model_idx'] = (int)$model->idx;
    $result['model_name'] = $model->model_name;
    $result['brand_idx'] = (int)$model->brand_idx;
    $result['brand_name'] = $model->brand_name;

    // 3. 등급(grade) 파싱
    $result['grade'] = parseGrade($cleanTitle, $model->model_name, $model->brand_name);

    return $result;
}

/**
 * DB에서 모델 찾기
 *
 * title에 포함된 model_name을 찾는다.
 * 길이가 긴 모델명을 우선 매칭하여 정확도를 높인다.
 * 예: "쏘렌토"와 "쏘렌토 MQ4"가 있을 때 "쏘렌토 MQ4"를 먼저 매칭
 *
 * @param string $title 정제된 차량 제목
 * @return object|null 모델 정보 (brand 정보 포함)
 */
function findModel(string $title): ?object {
    // 모델명 길이 내림차순 정렬로 가장 긴 매칭을 찾음
    // 이렇게 하는 이유: "아반떼 N"이 "아반떼"보다 먼저 매칭되어야 함
    $sql = "SELECT m.idx, m.brand_idx, m.model_name, b.brand_name
            FROM " . DB_PREFIX . "rent_model m
            INNER JOIN " . DB_PREFIX . "rent_brand b ON m.brand_idx = b.idx
            WHERE m.is_active = 1
              AND b.is_active = 1
              AND :title LIKE CONCAT('%', m.model_name, '%')
            ORDER BY CHAR_LENGTH(m.model_name) DESC
            LIMIT 1";

    $result = \ExpertNote\DB::getRow($sql, ['title' => $title]);

    // DB::getRow()는 결과가 없을 때 false를 반환하므로 null로 변환
    return $result ?: null;
}

/**
 * 등급(grade) 문자열 생성
 *
 * 브랜드에 따라 다른 형식으로 등급을 생성한다:
 * - ENGINE_FIRST (제네시스, 벤츠, BMW, 아우디): "{엔진} {구동방식}" 예: "2.5T AWD"
 * - TRIM_FIRST (현대, 기아, 르노): "{트림} {배기량}" 예: "프레스티지 2.0"
 *
 * @param string $title 정제된 차량 제목
 * @param string $modelName 찾은 모델명
 * @param string $brandName 브랜드명
 * @return string|null 생성된 등급 문자열
 */
function parseGrade(string $title, string $modelName, string $brandName): ?string {
    // 모델명을 제외한 나머지 부분에서 등급 정보 추출
    $gradeStr = str_replace($modelName, '', $title);
    $gradeStr = trim($gradeStr);

    if (empty($gradeStr)) {
        return null;
    }

    // 엔진/배기량 추출
    $engine = extractEngine($gradeStr);

    // 구동방식 추출
    $drive = extractDrive($gradeStr);

    // 트림 추출
    $trim = extractTrim($gradeStr);

    // 브랜드 유형에 따른 등급 형식 결정
    $isEngineFirst = isEngineFirstBrand($brandName);

    if ($isEngineFirst) {
        // ENGINE_FIRST: "{엔진} {구동방식}"
        // 수입차는 엔진 스펙이 더 중요한 식별자
        $parts = array_filter([$engine, $drive]);
        return !empty($parts) ? implode(' ', $parts) : $trim;
    } else {
        // TRIM_FIRST: "{트림} {배기량}"
        // 국산차는 트림명이 더 중요한 식별자
        $parts = array_filter([$trim, $engine]);
        return !empty($parts) ? implode(' ', $parts) : null;
    }
}

/**
 * 엔진/배기량 정보 추출
 *
 * @param string $str 검색 대상 문자열
 * @return string|null 추출된 엔진 정보
 */
function extractEngine(string $str): ?string {
    // 패턴을 정규식으로 변환하여 검색
    $pattern = '/(' . implode('|', ENGINE_PATTERNS) . ')/i';

    if (preg_match($pattern, $str, $matches)) {
        return $matches[1];
    }

    return null;
}

/**
 * 구동방식 추출
 *
 * @param string $str 검색 대상 문자열
 * @return string|null 추출된 구동방식
 */
function extractDrive(string $str): ?string {
    foreach (DRIVE_PATTERNS as $drive) {
        if (stripos($str, $drive) !== false) {
            return strtoupper($drive);
        }
    }

    return null;
}

/**
 * 트림명 추출
 *
 * @param string $str 검색 대상 문자열
 * @return string|null 추출된 트림명
 */
function extractTrim(string $str): ?string {
    foreach (TRIM_KEYWORDS as $trim) {
        if (stripos($str, $trim) !== false) {
            return $trim;
        }
    }

    return null;
}

/**
 * ENGINE_FIRST 브랜드인지 확인
 *
 * @param string $brandName 브랜드명
 * @return bool ENGINE_FIRST 브랜드 여부
 */
function isEngineFirstBrand(string $brandName): bool {
    foreach (ENGINE_FIRST_BRANDS as $brand) {
        if (stripos($brandName, $brand) !== false) {
            return true;
        }
    }

    return false;
}
