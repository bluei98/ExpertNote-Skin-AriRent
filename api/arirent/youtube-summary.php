<?php
/**
 * YouTube 영상 AI 요약 API
 *
 * 로그인 없이 영상 요약을 생성하는 공개 API
 *
 * POST /skins/arirent/api/v1/youtube-summary.php
 *
 * 파라미터:
 *   - idx: YouTube 영상 IDX (필수)
 *
 * 반환:
 *   - result: SUCCESS|FAILED
 *   - message: 메시지
 *   - data.summary: 생성된 요약 텍스트
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

try {
    // POST 요청만 허용
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception(__('POST 요청만 허용됩니다.', 'api'), 405);
    }

    // JSON 바디 파싱
    $input = json_decode(file_get_contents('php://input'), true);
    $idx = $input['idx'] ?? $_POST['idx'] ?? null;

    if (!$idx) {
        throw new Exception(__('영상 IDX가 필요합니다.', 'api'), 400);
    }

    // 영상 정보 조회
    $video = \ExpertNote\Youtube::getVideo($idx);
    if (!$video) {
        throw new Exception(__('영상을 찾을 수 없습니다.', 'api'), 404);
    }

    // 이미 요약이 있는지 확인
    $locale = $video->default_audio_language ? substr($video->default_audio_language, 0, 2) : 'en';
    $localeData = \ExpertNote\Youtube::getLocale($idx, $locale);

    if ($localeData && !empty($localeData->summary)) {
        // 이미 요약이 있으면 반환
        $ret['result'] = 'SUCCESS';
        $ret['message'] = __('기존 요약을 반환합니다.', 'api');
        $ret['data'] = [
            'summary' => $localeData->summary,
            'cached' => true
        ];
    } else {
        // 자막 가져오기
        $transcriptText = getTranscript($video->youtube_video_id, $locale);

        if (empty($transcriptText) || strlen($transcriptText) < 50) {
            throw new Exception(__('자막을 가져올 수 없습니다.', 'api'), 400);
        }

        // OpenAI로 요약 생성
        $summary = generateSummary($transcriptText, $locale);

        if (empty($summary)) {
            throw new Exception(__('요약 생성에 실패했습니다.', 'api'), 500);
        }

        // DB에 저장
        \ExpertNote\Youtube::setLocale([
            'youtube_idx' => $idx,
            'locale' => $locale,
            'summary' => $summary
        ]);

        $ret['result'] = 'SUCCESS';
        $ret['message'] = __('요약이 생성되었습니다.', 'api');
        $ret['data'] = [
            'summary' => $summary,
            'cached' => false
        ];
    }

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

/**
 * YouTube 자막 가져오기
 *
 * @param string $videoId YouTube 영상 ID
 * @param string $lang 언어 코드
 * @return string 자막 텍스트
 */
function getTranscript($videoId, $lang = '') {
    $apiUrl = 'https://api.codesand.co.kr/youtube/transcript';

    // 쿼리 파라미터 구성
    $params = ['video_id' => $videoId];
    if (!empty($lang)) {
        $params['lang'] = $lang;
    }

    $url = $apiUrl . '?' . http_build_query($params);

    // cURL 요청
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false || $httpCode !== 200) {
        return '';
    }

    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return '';
    }

    // API 응답 형식에 따라 데이터 추출
    $transcript = [];
    if (isset($data['transcript'])) {
        $transcript = $data['transcript'];
    } elseif (is_array($data)) {
        $transcript = $data;
    }

    if (empty($transcript) || !is_array($transcript)) {
        return '';
    }

    // 자막 텍스트만 추출하여 연결
    $texts = array_column($transcript, 'text');
    return implode(' ', $texts);
}

/**
 * OpenAI로 요약 생성
 *
 * @param string $transcriptText 자막 텍스트
 * @param string $lang 언어 코드
 * @return string 요약 텍스트
 */
function generateSummary($transcriptText, $lang = 'en') {
    // OpenAI API 키 가져오기
    $openaiConfig = \ExpertNote\SiteMeta::get("openapi")["openai"] ?? null;
    $apiKey = $openaiConfig['api_key'] ?? null;

    if (!$apiKey) {
        throw new Exception(__('OpenAI API Key가 설정되지 않았습니다.', 'api'), 500);
    }

    $targetLang = strtoupper($lang);
    $langMap = [
        'KO' => 'Korean',
        'EN' => 'English',
        'JA' => 'Japanese',
        'ZH' => 'Chinese',
        'ES' => 'Spanish',
        'FR' => 'French',
        'DE' => 'German'
    ];
    $langName = $langMap[$targetLang] ?? 'English';

    $prompt = "You are a professional content summarizer. Summarize the following video transcript in {$langName}.

Requirements:
- Write a concise summary (3-5 sentences)
- Capture the main topic and key points
- Use natural {$langName} language
- If the content is about trading/finance, use proper trading terminology
- If the content is about cars/vehicles, use proper automotive terminology
- Output ONLY the summary, no additional text

Transcript:
" . mb_substr($transcriptText, 0, 8000);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'model' => 'gpt-4o-mini',
        'messages' => [
            ['role' => 'user', 'content' => $prompt]
        ],
        'max_tokens' => 500,
        'temperature' => 0.7
    ]));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200 || !$response) {
        return '';
    }

    $data = json_decode($response, true);

    return $data['choices'][0]['message']['content'] ?? '';
}
