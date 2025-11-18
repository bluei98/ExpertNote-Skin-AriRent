<?php
/**
 * 포럼 목록 페이지 - 처리 로직
 */

// echo "<pre>";print_r($_GET);echo "</pre>";

// 포럼 코드 가져오기
$forumCode = $_GET['code'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$category = $_GET['category'] ?? '';
$searchKeyword = $_GET['q'] ?? '';

if (empty($forumCode)) {
    header("Location: /");
    exit;
}

// 포럼 설정 조회
$forumConfig = ExpertNote\Forum\Forum::getForumConfig($forumCode, $i18n->locale);
if (!$forumConfig) {
    header("Location: /404");
    exit;
}

// 언어 체크 및 리다이렉션 (V1과 동일)
// if ($forumConfig->locale != $i18n->locale) {
//     // 포럼 언어와 현재 언어가 다른 경우
//     header("Location: /{$forumConfig->locale}/forum/{$forumCode}");
//     exit;
// }
// 게시물 언어로 강제 변환
$i18n->setlocale($forumConfig->locale);
setcookie('siteLocale', strtolower($forumConfig->locale), time() + (86400 * 365), "/");

// 포럼이 개설상태가 아닌경우 메인페이지로 리다이렉트
if ($forumConfig->status != 'PUBLISHED' && !ExpertNote\User\User::isAdmin()) {
    header("Location: /");
    exit;
}

// 권한 체크 (permit_member_list, permit_guest_list 사용)
$isGuest = !ExpertNote\User\User::isLogin();
$isMember = ExpertNote\User\User::isLogin();

if ($isGuest && $forumConfig->permit_guest_list === 'N') {
    header("Location: /login?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}
if ($isMember && $forumConfig->permit_member_list === 'N' && !ExpertNote\User\User::isAdmin()) {
    die(__('접근 권한이 없습니다.', 'skin'));
}

// 페이징 설정
$postsPerPage = $forumConfig->cnt_article ?: 20;
$offset = ($page - 1) * $postsPerPage;

// 게시글 목록 조회
$wheres = [
    "f.forum_code = :forum_code",
    "f.locale = :locale",
    "f.status = 'PUBLISHED'"
];
$params = [
    'forum_code' => $forumCode,
    'locale' => $i18n->locale,
];

// 관리자가 아닌 경우 PUBLISHED만 보이도록
// if (!ExpertNote\User\User::isAdmin()) {
//     $wheres[] = "f.status = 'PUBLISHED'";
// }

// 카테고리 필터
if ($category) {
    $wheres[] = 'f.category = :category';
    $params['category'] = $category;
}

// 검색어 필터
if ($searchKeyword) {
    $wheres[] = '(f.title LIKE :keyword OR f.contents LIKE :keyword OR f.tags LIKE :keyword)';
    $params['keyword'] = '%' . $searchKeyword . '%';
}

$orderby = ["f.publish_time DESC"];

// 전체 게시물 수 (필터링 전)
$threadTotalCount = ExpertNote\Forum\Thread::getThreadCount($wheres, $params);
// 필터링된 게시물 수
$totalCount = ExpertNote\Forum\Thread::getThreadCount($wheres, $params);

// 게시물 목록 조회
$threads = ExpertNote\Forum\Thread::getThreads($wheres, $orderby, [$offset, $postsPerPage], $params);

// 페이지 정보 설정
$totalPages = ceil($totalCount / $postsPerPage);

// 카테고리 목록 조회
$categories = [];
if (!empty($forumConfig->categories)) {
    $categoryNames = explode(',', $forumConfig->categories);
    $categoryNames = array_map('trim', $categoryNames);

    foreach($categoryNames as $category) {
        $categories[] = [
            "category" => $category,
            "cnt" => $cnt,
        ];
    }
}

// 페이지 메타 설정
ExpertNote\Core::setPageTitle($forumConfig->forum_title);
ExpertNote\Core::setLayout($forumConfig->skin);

// 스킨 파일 로드
if (!$forumConfig->skin) $forumConfig->skin = 'default';
include SKINPATH."/forum/skins/{$forumConfig->skin}/list.php";
?>
