<?php
/**
 * 블로그 스타일 포럼 목록 스킨
 * 전통적인 블로그 레이아웃 (가로 형태)
 */

// 쿼리 파라미터 구성
$queryParams = [];
$listPaths = [
    "/forum",
    $forumConfig->forum_code
];
if(isset($_GET['category'])) {
    $listPaths[] = "category";
    $listPaths[] = ExpertNote\Forum\Thread::slugify($_GET['category']);
    $queryParams['category'] = $_GET['category'];
};
if(isset($_GET['q'])) $queryParams['q'] = $_GET['q'];
if(isset($_GET['page'])) $queryParams['page'] = $_GET['page'];
$queryParamStr = count($queryParams) > 0 ? "?".http_build_query($queryParams) : "";
$listPathStr = implode("/", $listPaths);
?>

<div class="container py-5">
    <!-- 헤더 영역 -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="fw-bold mb-1"><?php echo htmlspecialchars($forumConfig->forum_title) ?></h1>
                    <?php if($forumConfig->forum_description): ?>
                    <p class="text-muted mb-0"><?php echo htmlspecialchars($forumConfig->forum_description) ?></p>
                    <?php endif; ?>
                </div>
                <?php if((!ExpertNote\User\User::isLogin() && $forumConfig->permit_guest_edit == "Y") ||
                         (ExpertNote\User\User::isLogin() && $forumConfig->permit_member_edit == "Y") ||
                         ExpertNote\User\User::isAdmin()): ?>
                <a href="/forum/<?php echo urlencode($forumConfig->forum_code) ?>/edit" class="btn btn-primary">
                    <i class="bi bi-pencil-square"></i> <?php echo __('새 글 쓰기', 'skin') ?>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- 카테고리 탭 -->
    <?php if(!empty($categories) && count($categories) > 0): ?>
    <div class="row mb-4">
        <div class="col-12">
            <ul class="nav nav-pills">
                <!-- 전체 탭 -->
                <li class="nav-item">
                    <a class="nav-link <?php if(!isset($_GET['category'])): ?>active<?php endif; ?>"
                       href="/forum/<?php echo urlencode($forumConfig->forum_code) ?>">
                        <?php echo __('전체', 'skin') ?>
                    </a>
                </li>
                <!-- 카테고리 탭 -->
                <?php foreach($categories as $category): ?>
                <li class="nav-item">
                    <a class="nav-link <?php if(isset($_GET['category']) && $_GET['category'] == $category["category"]): ?>active<?php endif; ?>"
                       href="/forum/<?php echo urlencode($forumConfig->forum_code) ?>?category=<?php echo urlencode($category["category"]) ?>">
                        <?php echo htmlspecialchars($category["category"]) ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>

    <!-- 블로그 목록 -->
    <div class="row">
        <div class="col-12">
            <?php if(!empty($threads) && count($threads) > 0):
                    foreach($threads as $thread):
                        $title = htmlspecialchars($thread->title);

                        if(isset($_GET['q']) && $_GET['q']) {
                            $searchTerm = htmlspecialchars($_GET['q']);
                            $pattern = '/' . preg_quote($searchTerm, '/') . '/i';
                            $title = preg_replace($pattern, '<mark>$0</mark>', $title);
                        }

                        // 본문 미리보기 (HTML 태그 제거, 200자 제한)
                        $preview = strip_tags($thread->contents);
                        $preview = mb_substr($preview, 0, 200);
                        if(mb_strlen(strip_tags($thread->contents)) > 200) {
                            $preview .= '...';
                        }
            ?>
            <article class="blog-post mb-4 pb-4 border-bottom">
                <a href="/forum/<?php echo $thread->forum_code?>/<?php echo ExpertNote\Forum\Thread::getPermalink($thread->idx, $thread->title)?><?php echo $queryParamStr?>" class="text-decoration-none">
                    <div class="row g-4">
                        <!-- 썸네일 이미지 -->
                        <div class="col-md-4 col-lg-3">
                            <?php if($thread->featured_image): ?>
                            <div class="blog-thumbnail rounded overflow-hidden" style="aspect-ratio: 16/10;">
                                <img src="<?php echo $thread->featured_image?>"
                                     alt="<?php echo htmlspecialchars($thread->title)?>"
                                     class="w-100 h-100"
                                     style="object-fit: cover;">
                            </div>
                            <?php else: ?>
                            <div class="blog-thumbnail rounded bg-light d-flex align-items-center justify-content-center" style="aspect-ratio: 16/10;">
                                <i class="bi bi-file-text text-muted" style="font-size: 3rem;"></i>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- 콘텐츠 -->
                        <div class="col-md-8 col-lg-9">
                            <div class="d-flex flex-column h-100">
                                <!-- 메타 정보 -->
                                <div class="mb-2">
                                    <?php if($thread->category): ?>
                                    <span class="badge bg-primary me-2"><?php echo htmlspecialchars($thread->category)?></span>
                                    <?php endif; ?>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar3 me-1"></i><?php echo date('Y년 m월 d일', strtotime($thread->publish_time))?>
                                    </small>
                                </div>

                                <!-- 제목 -->
                                <h2 class="blog-title h5 fw-bold text-dark mb-2">
                                    <?php if($thread->use_sticky == 'Y' || $thread->use_all_sticky == 'Y'): ?>
                                    <span class="badge bg-danger me-1"><?php echo __('공지', 'skin')?></span>
                                    <?php endif; ?>
                                    <?php echo $title?>
                                </h2>

                                <!-- 미리보기 -->
                                <p class="blog-excerpt text-muted mb-3 flex-grow-1">
                                    <?php echo htmlspecialchars($preview)?>
                                </p>

                                <!-- 하단 정보 -->
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="blog-meta">
                                        <?php if($thread->user_id): ?>
                                        <small class="text-muted me-3">
                                            <i class="bi bi-person me-1"></i><?php echo htmlspecialchars($thread->username ?: $thread->user_id)?>
                                        </small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="blog-stats">
                                        <?php if($thread->cnt_view > 0): ?>
                                        <small class="text-muted me-2">
                                            <i class="bi bi-eye"></i> <?php echo number_format($thread->cnt_view)?>
                                        </small>
                                        <?php endif; ?>
                                        <?php if($thread->cnt_like > 0): ?>
                                        <small class="text-danger me-2">
                                            <i class="bi bi-heart-fill"></i> <?php echo number_format($thread->cnt_like)?>
                                        </small>
                                        <?php endif; ?>
                                        <?php if($thread->cnt_comment > 0): ?>
                                        <small class="text-primary">
                                            <i class="bi bi-chat-dots"></i> <?php echo number_format($thread->cnt_comment)?>
                                        </small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </article>
            <?php
                    endforeach;
                else:
            ?>
            <div class="text-center py-5">
                <div class="text-muted mb-3">
                    <i class="bi bi-journal-text" style="font-size: 4rem;"></i>
                </div>
                <p class="mb-3"><?php echo __('아직 작성된 글이 없습니다.', 'skin') ?></p>
                <?php if((!ExpertNote\User\User::isLogin() && $forumConfig->permit_guest_edit == "Y") ||
                         (ExpertNote\User\User::isLogin() && $forumConfig->permit_member_edit == "Y") ||
                         ExpertNote\User\User::isAdmin()): ?>
                <a href="/forum/<?php echo urlencode($forumConfig->forum_code) ?>/edit" class="btn btn-primary">
                    <i class="bi bi-pencil-square"></i> <?php echo __('첫 글 작성하기', 'skin') ?>
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- 하단 버튼 및 검색 -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <?php if((!ExpertNote\User\User::isLogin() && $forumConfig->permit_guest_edit == "Y") ||
                             (ExpertNote\User\User::isLogin() && $forumConfig->permit_member_edit == "Y") ||
                             ExpertNote\User\User::isAdmin()): ?>
                    <a href="/forum/<?php echo urlencode($forumConfig->forum_code) ?>/edit" class="btn btn-primary">
                        <i class="bi bi-pencil-square"></i> <?php echo __('글 작성', 'skin') ?>
                    </a>
                    <?php endif; ?>
                </div>

                <div>
                    <form method="get" action="/forum/<?php echo urlencode($forumConfig->forum_code) ?>" class="d-flex gap-2">
                        <?php if(isset($_GET['category'])): ?>
                        <input type="hidden" name="category" value="<?php echo htmlspecialchars($_GET['category']) ?>">
                        <?php endif; ?>

                        <div class="input-group" style="width: 300px;">
                            <input type="text"
                                   name="q"
                                   class="form-control"
                                   value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>"
                                   placeholder="<?php echo __('검색어를 입력하세요', 'skin') ?>">
                            <button type="submit" class="btn btn-outline-secondary">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- 페이지 네비게이션 -->
    <?php if($totalPages > 1):
        $pvQueryParams = $queryParams;
        unset($pvQueryParams["category"]);
        unset($pvQueryParams["page"]);
        $pvQueryParamStr = count($pvQueryParams) > 0 ? "?".http_build_query($pvQueryParams) : "";
    ?>
    <div class="row mt-4">
        <div class="col-12">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <?php
                    // 페이지 범위 계산
                    $startPage = max(1, $page - 5);
                    $endPage = min($totalPages, $page + 5);
                    ?>

                    <?php if($page > 1): ?>
                    <!-- 처음 -->
                    <li class="page-item">
                        <a class="page-link" href="<?php echo $listPathStr?><?php echo $pvQueryParamStr ? $pvQueryParamStr."&page=1" : "?page=1"?>">
                            <?php echo __('처음', 'skin') ?>
                        </a>
                    </li>
                    <!-- 이전 -->
                    <li class="page-item">
                        <a class="page-link" href="<?php echo $listPathStr?><?php echo $pvQueryParamStr ? $pvQueryParamStr."&page=".($page-1) : "?page=".($page-1)?>">
                            <?php echo __('이전', 'skin') ?>
                        </a>
                    </li>
                    <?php endif; ?>

                    <!-- 페이지 번호들 -->
                    <?php for($i = $startPage; $i <= $endPage; $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="<?php echo $listPathStr?><?php echo $pvQueryParamStr ? $pvQueryParamStr."&page=".($i) : "?page=".($i)?>">
                            <?php echo $i ?>
                        </a>
                    </li>
                    <?php endfor; ?>

                    <?php if($page < $totalPages): ?>
                    <!-- 다음 -->
                    <li class="page-item">
                        <a class="page-link" href="<?php echo $listPathStr?><?php echo $pvQueryParamStr ? $pvQueryParamStr."&page=".($page+1) : "?page=".($page+1)?>">
                            <?php echo __('다음', 'skin') ?>
                        </a>
                    </li>
                    <!-- 마지막 -->
                    <li class="page-item">
                        <a class="page-link" href="<?php echo $listPathStr?><?php echo $pvQueryParamStr ? $pvQueryParamStr."&page=".($totalPages) : "?page=".($totalPages)?>">
                            <?php echo __('마지막', 'skin') ?>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.blog-post:hover .blog-title {
    color: var(--bs-primary) !important;
}
.blog-thumbnail {
    transition: transform 0.3s ease;
}
.blog-post:hover .blog-thumbnail img {
    transform: scale(1.05);
}
.blog-thumbnail {
    overflow: hidden;
}
</style>
