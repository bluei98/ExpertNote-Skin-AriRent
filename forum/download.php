<?php
/**
 * 포럼 파일 다운로드 페이지
 */

// 파일 인덱스 받기
$file_idx = isset($_GET['file_idx']) ? (int)$_GET['file_idx'] : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

if (!$file_idx) {
    die(__('잘못된 요청입니다.', 'skin'));
}

// 실제 다운로드 처리
if ($action === 'download') {
    $file = ExpertNote\Forum\Thread::downloadThreadFile($file_idx, true);

    if (!$file) {
        die(__('파일을 찾을 수 없습니다.', 'skin'));
    }

    // 게스트 다운로드 권한 체크
    if ($file->permit_guest_download !== 'Y' && !ExpertNote\User\User::isLogin()) {
        die(__('로그인이 필요합니다.', 'skin'));
    }

    // 파일 다운로드 처리
    if ($file->store_method === 'LOCAL') {
        $filepath = $file->path;

        if (!file_exists($filepath)) {
            die(__('파일이 존재하지 않습니다.', 'skin'));
        }

        // 파일 다운로드 헤더 설정
        header('Content-Type: ' . $file->mime);
        header('Content-Disposition: attachment; filename="' . $file->real_name . '"');
        header('Content-Length: ' . $file->size);
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: public');

        // 파일 전송
        readfile($filepath);
        exit;
    }
    elseif ($file->store_method === 'S3' || $file->store_method === 'URL') {
        // S3 또는 URL인 경우 리다이렉트
        header('Location: ' . $file->url);
        exit;
    }

    die(__('다운로드할 수 없는 파일입니다.', 'skin'));
}

// 파일 정보 조회 (카운트 증가 없이)
$file = ExpertNote\Forum\Thread::downloadThreadFile($file_idx, false);
if (!$file) {
    die(__('파일을 찾을 수 없습니다.', 'skin'));
}

$article = ExpertNote\Forum\Thread::getThread($file->forum_idx);
$i18n->setlocale($article->locale);

$pageTitle = $article->title." (".sprintf(__('%s 파일 다운로드', 'skin'), $file->real_name).")";

// 페이지 메타 설정
\ExpertNote\Core::setPageTitle($pageTitle);
\ExpertNote\Core::setPageDescription(strip_tags(mb_substr($article->contents, 0, 120)));
\ExpertNote\Core::setPageKeywords($article->tags ?? '');

// Open Graph 메타 태그
\ExpertNote\Core::addMetaTag('og:type', ["property"=>"og:type", "content"=>"article"]);
\ExpertNote\Core::addMetaTag('og:title', ["property"=>"og:title", "content"=>$pageTitle]);
\ExpertNote\Core::addMetaTag('og:description', ["property"=>"og:description", "content"=>strip_tags(substr($article->content, 0, 120))]);
\ExpertNote\Core::addMetaTag('og:url', ["property"=>"og:url", "content"=>ExpertNote\Core::getBaseUrl()."/forum/".$article->forum_code."/".ExpertNote\Forum\Thread::getPermalink($article->idx, $article->title)]);
// \ExpertNote\Core::addMetaTag('og:site_name', ["property"=>"og:type", "content"=>$article->title]);

// // 트위터 카드 메타 태그
\ExpertNote\Core::addMetaTag('twitter:card', ["name"=>"twitter:card", "content"=>"summary_large_image"]);
\ExpertNote\Core::addMetaTag('twitter:title', ["name"=>"twitter:title", "content"=>$pageTitle]);
\ExpertNote\Core::addMetaTag('twitter:description', ["name"=>"twitter:description", "content"=>strip_tags(substr($article->content, 0, 120))]);
\ExpertNote\Core::addMetaTag('twitter:url', ["name"=>"twitter:url", "content"=>ExpertNote\Core::getBaseUrl()."/forum/".$article->forum_code."/".ExpertNote\Forum\Thread::getPermalink($article->idx, $article->title)]);


// 게스트 다운로드 권한 체크
$need_login = ($file->permit_guest_download !== 'Y' && !ExpertNote\User\User::isLogin());

// 파일 크기 포맷팅 함수
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

// 템플릿 파일 포함
include __DIR__ . '/skins/default/download.php';
