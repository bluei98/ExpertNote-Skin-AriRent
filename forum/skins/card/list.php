<?php
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
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="fw-bold mb-0"><?php echo htmlspecialchars($forumConfig->forum_title) ?></h1>
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

    <?php if(!empty($categories) && count($categories) > 0): ?>
    <div class="row mb-4">
        <div class="col-12">
            <ul class="nav nav-pills">
                <?php foreach($categories as $no=>$category): ?>
                <li class="nav-item">
                    <a class="nav-link <?php if((isset($_GET['category']) && $_GET['category'] == $category["category"]) || (!isset($_GET['category']) && $no==0)): ?>active<?php endif; ?>"
                       href="/forum/<?php echo urlencode($forumConfig->forum_code) ?><?php if($no > 0): ?>?category=<?php echo urlencode($category["category"]) ?><?php endif; ?>">
                        <?php echo $no == 0 ? __('전체', 'skin') : htmlspecialchars($category["category"]) ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>

    <div class="row g-4">
        <?php if(!empty($threads) && count($threads) > 0):
                foreach($threads as $thread):
                    $title = htmlspecialchars($thread->title);

                    if(isset($_GET['q']) && $_GET['q']) {
                        $searchTerm = htmlspecialchars($_GET['q']);
                        $pattern = '/' . preg_quote($searchTerm, '/') . '/i';
                        $title = preg_replace($pattern, '<mark>$0</mark>', $title);
                    }
        ?>
        <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <a href="/forum/<?php echo $thread->forum_code?>/<?php echo ExpertNote\Forum\Thread::getPermalink($thread->idx, $thread->title)?><?php echo $queryParamStr?>" class="text-decoration-none">
                    <?php if($thread->featured_image): ?>
                    <img src="<?php echo $thread->featured_image?>" class="card-img-top" alt="<?php echo htmlspecialchars($thread->title)?>" style="height: 200px; object-fit: cover;">
                    <?php else: ?>
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                        <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                    </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <?php if($thread->category): ?>
                        <span class="badge bg-primary mb-2"><?php echo htmlspecialchars($thread->category)?></span>
                        <?php endif; ?>
                        <h5 class="card-title text-dark fw-semibold mb-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            <?php echo $title?>
                        </h5>
                        <p class="card-text text-muted small mb-3" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            <?php echo htmlspecialchars(strip_tags(mb_substr($thread->contents, 0, 100)))?>
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="bi bi-clock me-1"></i><?php echo date('Y.m.d', strtotime($thread->publish_time))?>
                            </small>
                            <div>
                                <?php if($thread->cnt_view > 0): ?>
                                <small class="text-muted me-2"><i class="bi bi-eye"></i> <?php echo number_format($thread->cnt_view)?></small>
                                <?php endif; ?>
                                <?php if($thread->cnt_like > 0): ?>
                                <small class="text-danger"><i class="bi bi-heart-fill"></i> <?php echo number_format($thread->cnt_like)?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <?php
                endforeach;
            else:
        ?>
        <div class="col-12">
            <div class="text-center py-5">
                <div class="text-muted mb-3">
                    <i class="bi bi-inbox" style="font-size: 4rem;"></i>
                </div>
                <p class="mb-3"><?php echo __('게시글이 없습니다.', 'skin') ?></p>
                <?php if((!ExpertNote\User\User::isLogin() && $forumConfig->permit_guest_edit == "Y") ||
                         (ExpertNote\User\User::isLogin() && $forumConfig->permit_member_edit == "Y") ||
                         ExpertNote\User\User::isAdmin()): ?>
                <a href="/forum/<?php echo urlencode($forumConfig->forum_code) ?>/edit" class="btn btn-primary">
                    <i class="bi bi-pencil-square"></i> <?php echo __('첫 글 작성하기', 'skin') ?>
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="row mt-5">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
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
