<?php
/**
 * 포럼 게시글 삭제 페이지 - 처리 로직
 */

$forumCode = $_GET['code'] ?? '';
$idx = isset($_GET['idx']) ? (int)$_GET['idx'] : 0;

if (empty($forumCode) || $idx <= 0) {
    header("Location: /");
    exit;
}

// 로그인 체크
if (!ExpertNote\User\User::isLogin()) {
    header("Location: /login?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// 포럼 설정 조회
$forumConfig = ExpertNote\Forum\Forum::getForumConfig($forumCode, $i18n->locale);
if (!$forumConfig) {
    header("Location: /404");
    exit;
}

// 게시글 조회
$article = ExpertNote\Forum\Thread::getThread($idx);
if (!$article || $article->forum_code !== $forumCode) {
    header("Location: /404");
    exit;
}

// 작성자 또는 관리자만 삭제 가능
$isAuthor = ($_SESSION['user_id'] === $article->user_id);
$isAdmin = ExpertNote\User\User::isAdmin();

if (!$isAuthor && !$isAdmin) {
    die(__('삭제 권한이 없습니다.', 'skin'));
}

// 페이지 타이틀 설정
ExpertNote\Core::setPageTitle(__('게시글 삭제', 'skin') . ' - ' . $forumConfig->forum_title);
ExpertNote\Core::setLayout('default');

// 스킨 파일 로드
if (!$forumConfig->skin) $forumConfig->skin = 'default';
include SKINPATH."/forum/skins/{$forumConfig->skin}/delete.php";
