<?php
/**
 * 포럼 게시글 보기 페이지 - 처리 로직
 */

// URI가 입력된 경우 게시물 IDX 체크
if ($_GET['slug']) {
    $tmp = explode("-", $_GET['slug']);
    $idx = $tmp[count($tmp)-1];
}
else if ($_GET['idx']) {
    $idx = $_GET['idx'];
}
else {
    header("Location: /");
    exit;
}

// 게시물 조회
$article = \ExpertNote\Forum\Thread::getThread($idx);
if (!$article) {
    \ExpertNote\Core::setMessage('존재하지 않는 게시물입니다.');
    \ExpertNote\Core::redirect('/');
    exit;
}

// 게시물 언어로 강제 변환
$i18n->setlocale($article->locale);
setcookie('siteLocale', strtolower($article->locale), time() + (86400 * 365), "/");

// 포럼 조회수 카운팅
\ExpertNote\Forum\Thread::setThreadView($article->idx);

// 포럼 설정 조회
$forumConfig = \ExpertNote\Forum\Forum::getForumConfig($article->forum_code, $article->locale);
if (!$forumConfig) {
    \ExpertNote\Core::redirect('/');
    exit;
}
if (!$forumConfig->skin) $forumConfig->skin = 'default';

// 권한 체크
if (!$_SESSION['user_id']) {
    if ($forumConfig->permit_member_read != 'Y') {
        \ExpertNote\Core::redirect('/login?returl=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}


// 비밀글 체크
if ($article->is_secret == 'Y') {
    $canView = false;
    
    if ($_SESSION['user_id']) {
        // 작성자 본인이거나 관리자인 경우
        if ($article->user_id == $_SESSION['user_id'] || in_array($_SESSION['user_level'], ['ADMIN', 'SUPERADMIN'])) {
            $canView = true;
        }
    }
    
    // 비밀번호로 인증된 경우 체크 (세션에 저장된 정보)
    if (isset($_SESSION['forum_secret_'.$idx]) && $_SESSION['forum_secret_'.$idx] === true) {
        $canView = true;
    }
    
    if (!$canView) {
        // 비밀번호 입력 폼으로 리다이렉트 또는 모달 표시
        include SKINPATH . '/forum/skins/'.$forumConfig->skin.'/password.php';
        exit;
    }
}

// 첨부파일 조회
$files = \ExpertNote\Forum\Thread::getThreadFiles($idx, false);

// 댓글 조회
$commentCount = \ExpertNote\Forum\Comment::getThreadCommentCount($idx);

// 좋아요/싫어요 정보
$likes = \ExpertNote\Forum\Thread::getThreadLike($idx);
// $userVote = \ExpertNote\Forum\Thread::getUserVote($idx);

// 이전/다음 게시물
// $navigation = \ExpertNote\Forum\Thread::getForumPreviousNextLink($idx, $article->category_idx);

// 페이지 메타 설정
\ExpertNote\Core::setPageTitle($article->title);
\ExpertNote\Core::setPageDescription(strip_tags(mb_substr($article->contents, 0, 120)));
\ExpertNote\Core::setPageKeywords($article->tags ?? '');

// Open Graph 메타 태그
\ExpertNote\Core::addMetaTag('og:type', ["property"=>"og:type", "content"=>"article"]);
\ExpertNote\Core::addMetaTag('og:title', ["property"=>"og:title", "content"=>$article->title]);
\ExpertNote\Core::addMetaTag('og:description', ["property"=>"og:description", "content"=>strip_tags(substr($article->content, 0, 120))]);
\ExpertNote\Core::addMetaTag('og:url', ["property"=>"og:url", "content"=>ExpertNote\Core::getBaseUrl()."/forum/".$article->forum_code."/".ExpertNote\Forum\Thread::getPermalink($article->idx, $article->title)]);
// \ExpertNote\Core::addMetaTag('og:site_name', ["property"=>"og:type", "content"=>$article->title]);

// // 트위터 카드 메타 태그
\ExpertNote\Core::addMetaTag('twitter:card', ["name"=>"twitter:card", "content"=>"summary_large_image"]);
\ExpertNote\Core::addMetaTag('twitter:title', ["name"=>"twitter:title", "content"=>$article->title]);
\ExpertNote\Core::addMetaTag('twitter:description', ["name"=>"twitter:description", "content"=>strip_tags(substr($article->content, 0, 120))]);
\ExpertNote\Core::addMetaTag('twitter:url', ["name"=>"twitter:url", "content"=>ExpertNote\Core::getBaseUrl()."/forum/".$article->forum_code."/".ExpertNote\Forum\Thread::getPermalink($article->idx, $article->title)]);

if ($article->featured_image) {
    \ExpertNote\Core::addMetaTag('og:image', ["property"=>"og:image", "content"=>$article->featured_image]);
    \ExpertNote\Core::addMetaTag('twitter:image', ["name"=>"twitter:image", "content"=>$article->featured_image]);
}

// 구조화된 데이터 (JSON-LD)
$structuredData = [
    "@context" => "https://schema.org",
    "@type" => "Article",
    "mainEntityOfPage" => [
        "@type" => "WebPage",
        "@id" => $article->url ?? "https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}"
    ],
    "headline" => $article->title,
    "description" => strip_tags(mb_substr($article->content, 0, 160)), // 160자 요약
    "author" => [
        "@type" => "Person",
        "name" => $article->nickname ?? $article->username
    ],
    "publisher" => [
        "@type" => "Organization",
        "name" => \ExpertNote\SiteMeta::get("site_title")[$i18n->locale],
        "logo" => [
            "@type" => "ImageObject",
            "url" => \ExpertNote\SiteMeta::get("site_logo_default") ?? "https://{$_SERVER['HTTP_HOST']}/assets/images/logo_light.png"
        ]
    ],
    "articleSection" => implode(" ", [$forumConfig->forum_title, $article->category]),
    "datePublished" => date('c', strtotime($article->publish_time)),
    "dateModified" => date('c', strtotime($article->modified_time)),
    "url" => $article->url ?? "https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}",
    "inLanguage" => $article->locale
];

if ($article->featured_image) $structuredData["image"] = $article->featured_image;


\ExpertNote\Core::addScript('structured-data', '<script type="application/ld+json">' . json_encode($structuredData, JSON_UNESCAPED_UNICODE) . '</script>');

include SKINPATH."/forum/skins/{$forumConfig->skin}/view.php";
?>