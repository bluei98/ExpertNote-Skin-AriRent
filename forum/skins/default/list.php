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

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="ps-4"><?php echo __('제목', 'skin') ?></th>
                                    <th scope="col" class="text-center" style="width: 120px;"><?php echo __('작성자', 'skin') ?></th>
                                    <th scope="col" class="text-center" style="width: 100px;"><?php echo __('날짜', 'skin') ?></th>
                                    <th scope="col" class="text-center" style="width: 80px;"><?php echo __('조회', 'skin') ?></th>
                                    <th scope="col" class="text-center pe-4" style="width: 80px;"><?php echo __('추천', 'skin') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($threads) && count($threads) > 0): ?>
                                    <?php foreach($threads as $article):
                                        $title = htmlspecialchars($article->title);
                                        $contents = strip_tags($article->contents);

                                        if(isset($_GET['q']) && $_GET['q']) {
                                            $searchTerm = htmlspecialchars($_GET['q']);
                                            $title = str_replace($searchTerm, "<mark>{$searchTerm}</mark>", $title);
                                        }

                                        $stickyClass = '';
                                        if($article->use_sticky == 'Y' || $article->use_all_sticky == 'Y') {
                                            $stickyClass = 'table-primary';
                                        }

                                        $deletedClass = '';
                                        if(preg_match("/^(DEL)/", $article->status)) {
                                            $deletedClass = 'text-muted text-decoration-line-through';
                                        }
                                    ?>
                                    <tr class="<?php echo $stickyClass ?>">
                                        <td class="ps-4">
                                            <div class="d-flex flex-column">
                                                <div class="mb-1">
                                                    <?php if($article->category): ?>
                                                    <a href="/forum/<?php echo $article->forum_code?>/category/<?php echo ExpertNote\Forum\Thread::slugify($article->category)?>"
                                                       class="badge bg-secondary text-decoration-none">
                                                        <?php echo htmlspecialchars($article->category) ?>
                                                    </a>
                                                    <?php endif; ?>

                                                    <?php if($article->use_sticky == 'Y' || $article->use_all_sticky == 'Y'): ?>
                                                    <span class="badge bg-danger"><?php echo __('공지', 'skin') ?></span>
                                                    <?php endif; ?>
                                                </div>
                                                <a href="/forum/<?php echo htmlspecialchars($article->forum_code)?>/<?php echo ExpertNote\Forum\Thread::getPermalink($article->idx, $article->title)?><?php echo $queryParamStr?>"
                                                   class="text-decoration-none text-dark fw-semibold <?php echo $deletedClass ?>">
                                                    <?php echo $title ?>
                                                </a>
                                                <div class="mt-1">
                                                    <?php if($article->cnt_files > 0): ?>
                                                    <i class="bi bi-paperclip text-muted" title="<?php echo __('첨부파일', 'skin') ?>"></i>
                                                    <?php endif; ?>

                                                    <?php if($article->cnt_comments > 0): ?>
                                                    <span class="text-primary small">[<?php echo number_format($article->cnt_comments) ?>]</span>
                                                    <?php endif; ?>

                                                    <?php if($article->cnt_like > 0): ?>
                                                    <span class="text-danger small"><i class="bi bi-heart-fill"></i> <?php echo number_format($article->cnt_like) ?></span>
                                                    <?php endif; ?>

                                                    <?php
                                                    $publishTime = strtotime($article->publish_time);
                                                    $now = time();
                                                    if(($now - $publishTime) < 86400):
                                                    ?>
                                                    <span class="badge bg-success"><?php echo __('새글', 'skin') ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="text-center align-middle">
                                            <small><?php echo htmlspecialchars($article->nickname ?: $article->username) ?></small>
                                        </td>

                                        <td class="text-center align-middle">
                                            <small class="text-muted">
                                                <time datetime="<?php echo date('Y-m-d H:i:s', strtotime($article->publish_time)) ?>">
                                                    <?php
                                                    $articleTime = strtotime($article->publish_time);
                                                    $diffDays = floor(($now - $articleTime) / 86400);

                                                    if($diffDays == 0) {
                                                        echo date('H:i', $articleTime);
                                                    } elseif($diffDays < 7) {
                                                        echo $diffDays . __('일 전', 'skin');
                                                    } else {
                                                        echo date(__('Y.m.d', 'skin'), $articleTime);
                                                    }
                                                    ?>
                                                </time>
                                            </small>
                                        </td>

                                        <td class="text-center align-middle">
                                            <small class="text-muted"><?php echo number_format($article->cnt_view) ?></small>
                                        </td>

                                        <td class="text-center align-middle pe-4">
                                            <small class="text-muted"><?php echo number_format($article->cnt_like) ?></small>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="text-muted mb-3">
                                                <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                            </div>
                                            <p class="mb-3"><?php echo __('게시글이 없습니다.', 'skin') ?></p>
                                            <?php if((!ExpertNote\User\User::isLogin() && $forumConfig->permit_guest_edit == "Y") ||
                                                     (ExpertNote\User\User::isLogin() && $forumConfig->permit_member_edit == "Y") ||
                                                     ExpertNote\User\User::isAdmin()): ?>
                                            <a href="/forum/<?php echo urlencode($forumConfig->forum_code) ?>/edit" class="btn btn-primary">
                                                <i class="bi bi-pencil-square"></i> <?php echo __('첫 글 작성하기', 'skin') ?>
                                            </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
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
