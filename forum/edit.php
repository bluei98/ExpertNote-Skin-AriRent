<?php
/**
 * 포럼 게시글 작성/수정 페이지 - 비즈니스 로직
 */

$forumCode = $_GET['code'] ?? '';
$idx = isset($_GET['idx']) ? (int)$_GET['idx'] : 0;

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

// 권한 체크
$isEdit = $idx > 0;
if ($isEdit) {
    // 수정 모드
    $forum = ExpertNote\Forum\Forum::getForumsByIdx($idx);
    if (!$forum || $forum->forum_code !== $forumCode) {
        header("Location: /404");
        exit;
    }

    // 작성자 또는 관리자만 수정 가능
    $isAuthor = (ExpertNote\User\User::isLogin() && $_SESSION['user_id'] === $forum->user_id);
    $isAdmin = ExpertNote\User\User::isAdmin();

    if (!$isAuthor && !$isAdmin) {
        die(__('수정 권한이 없습니다.', 'skin'));
    }
} else {
    // 작성 모드
    $isGuest = !ExpertNote\User\User::isLogin();
    $isMember = ExpertNote\User\User::isLogin();

    // 작성 권한 체크 (permit_member_edit, permit_guest_edit 사용)
    $canWrite = false;
    if ($isGuest && $forumConfig->permit_guest_edit === 'Y') {
        $canWrite = true;
    } elseif ($isMember && $forumConfig->permit_member_edit === 'Y') {
        $canWrite = true;
    } elseif (ExpertNote\User\User::isAdmin()) {
        $canWrite = true;
    }

    if (!$canWrite) {
        header("Location: /login?redirect=" . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }

    $forum = (object)[
        'title' => '',
        'contents' => '',
        'use_sticky' => 'N',
        'category' => ''
    ];
}

// 카테고리 목록 조회 (categories 필드에서 쉼표 구분으로 가져옴)
$categories = [];
if (!empty($forumConfig->categories)) {
    $categories = explode(',', $forumConfig->categories);
    $categories = array_map('trim', $categories);
}

// 게시물 파일 정보 로드
$articleFiles = [];
if ($isEdit) {
    // V1의 getThreadFiles 메소드가 있다면 사용
    // $articleFiles = $en->getThreadFiles($forum->idx);
}

// Edit Key
$edit_key = \ExpertNote\Security::encrypt(time() . "," . $_SESSION['user_id']);

ExpertNote\Core::setPageTitle(($isEdit ? __('게시글 수정', 'skin') : __('게시글 작성', 'skin')) . ' - ' . $forumConfig->forum_title);
ExpertNote\Core::setLayout('default');

// 스킨 템플릿 로드
include SKINPATH . "/forum/skins/{$forumConfig->skin}/edit.php";
