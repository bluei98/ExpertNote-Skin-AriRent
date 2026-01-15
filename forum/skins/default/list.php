<?php
ExpertNote\Core::setLayout("v2");

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

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1 class="page-title" data-aos="fade-up"><?php echo $forumConfig->forum_title?></h1>
            <p class="page-desc" data-aos="fade-up" data-aos-delay="100"><?php echo $forumConfig->short_desc?></p>
        </div>
    </section>
    
    <!-- Forum Section -->
    <section class="forum-section">
        <div class="container">
            <!-- Category Tabs -->
            <?php if(!empty($categories) && count($categories) > 0): ?>
            <div class="category-tabs" data-aos="fade-up">
                <ul class="nav nav-tabs">
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
            <?php endif; ?>

            <!-- Search & Filter -->
            <div class="forum-controls" data-aos="fade-up">
                <div class="row g-3 align-items-end">
                    <!-- <div class="col-md-4">
                        <label class="form-label">검색 조건</label>
                        <select class="form-select">
                            <option value="title">제목</option>
                            <option value="content">내용</option>
                            <option value="title_content">제목+내용</option>
                        </select>
                    </div> -->
                    <div class="col-md-12">
                        <label class="form-label">검색어</label>
                        
                        <form method="get" action="/forum/<?php echo urlencode($forumConfig->forum_code) ?>" class="">
                            <?php if(isset($_GET['category'])): ?>
                            <input type="hidden" name="category" value="<?php echo htmlspecialchars($_GET['category']) ?>">
                            <?php endif; ?>
                            <div class="input-group">
                                <input type="text" name="q" value="<?php echo htmlspecialchars($_GET['q'] ?? '') ?>" class="form-control" placeholder="검색어를 입력하세요">
                                <button class="btn btn-primary" type="button">
                                    <i class="bi bi-search"></i> 검색
                                </button>
                            </div>
                        </form>
                        
                    </div>
                </div>
            </div>

            <!-- Forum Stats -->
            <!-- <div class="forum-stats" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-item">
                    <i class="bi bi-file-text"></i>
                    <span>총 <strong>152</strong>건</span>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-item">
                    <i class="bi bi-eye"></i>
                    <span>조회 <strong>15,234</strong>회</span>
                </div>
            </div> -->

            <!-- Forum List (Desktop) -->
            <div class="forum-list d-none d-md-block" data-aos="fade-up" data-aos-delay="200">
                <div class="forum-table">
                    <div class="forum-header">
                        <div class="col-num">번호</div>
                        <div class="col-category">카테고리</div>
                        <div class="col-title">제목</div>
                        <div class="col-date">작성일</div>
                        <div class="col-views">조회</div>
                    </div>
<?php
if(!empty($threads) && count($threads) > 0):
    foreach($threads as $thread):
        $title = htmlspecialchars($thread->title ?? '');

        if(isset($_GET['q']) && $_GET['q']) {
            $searchTerm = htmlspecialchars($_GET['q']);
            $pattern = '/' . preg_quote($searchTerm, '/') . '/i';
            $title = preg_replace($pattern, '<mark>$0</mark>', $title);
        }
?>
                
                    <div class="forum-item<?php echo ($thread->use_sticky == "Y" || $thread->use_all_sticky == "Y") ? ' notice' : '' ; ?>">
                        <div class="col-num">
                            <?php if($thread->use_sticky == "Y" || $thread->use_all_sticky == "Y"): ?>
                                <i class="bi bi-megaphone-fill"></i>
                            <?php else:?>
                                <?php echo $thread->idx; ?>
                            <?php endif;?>
                        </div>
                        <div class="col-category"><span class="badge bg-danger"><?php echo htmlspecialchars($thread->category ?? '')?></span></div>
                        <div class="col-title">
                            <a href="forum-view-default.html" class="title-link">
                                <?php if(($thread->use_sticky == "Y" || $thread->use_all_sticky == "Y")):?><i class="bi bi-pin-fill text-danger me-1"></i><?php endif;?>
                                <a href="/forum/<?php echo $thread->forum_code?>/<?php echo ExpertNote\Forum\Thread::getPermalink($thread->idx, $thread->title)?><?php echo $queryParamStr?>" class="text-dark text-decoration-none d-inline-block"><?php echo $title?></a>
                                <?php if($thread->cnt_comments > 0):?><span class="comment-count"><i class="bi bi-chat-dots"></i> <?php echo count($thread->cnt_comments); ?></span><?php endif;?>
                                <?php if($thread->cnt_files > 0):?><span class="file-count"><i class="bi bi-file-earmark"></i> <?php echo count($thread->cnt_files); ?></span><?php endif;?>
                            </a>
                        </div>
                        <div class="col-date"><?php echo date('Y.m.d', strtotime($thread->publish_time))?></div>
                        <div class="col-views"><?php echo number_format($thread->cnt_view)?></div>
                    </div>
<?php
        endforeach;
else:
?>
                    <div>
                        <div class="col-12 text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
                            <h4 class="mt-3 text-muted"><?php echo __('게시글이 없습니다.', 'skin')?></h4>
                        </div>
                    </div>
<?php
endif;
?>
                </div>
            </div>

            <!-- Forum List (Mobile) -->
            <div class="forum-list-mobile d-md-none" data-aos="fade-up" data-aos-delay="200">
                <!-- Notice Posts -->
<?php
if(!empty($threads) && count($threads) > 0):
    foreach($threads as $thread):
        $title = htmlspecialchars($thread->title ?? '');

        if(isset($_GET['q']) && $_GET['q']) {
            $searchTerm = htmlspecialchars($_GET['q']);
            $pattern = '/' . preg_quote($searchTerm, '/') . '/i';
            $title = preg_replace($pattern, '<mark>$0</mark>', $title);
        }
?>
            <a href="/forum/<?php echo $thread->forum_code?>/<?php echo ExpertNote\Forum\Thread::getPermalink($thread->idx, $thread->title)?><?php echo $queryParamStr?>" class="text-decoration-none">
                <div class="forum-card<?php echo ($thread->use_sticky == "Y" || $thread->use_all_sticky == "Y") ? ' notice' : '' ; ?>">
                    <div class="card-header">
                        <span class="badge bg-danger"><?php echo htmlspecialchars($thread->category ?? '')?></span>
                        <span class="date"><?php echo date('Y.m.d', strtotime($thread->publish_time))?></span>
                    </div>
                    <h3 class="card-title">
                        <i class="bi bi-pin-fill text-danger me-1"></i>
                        <?php echo $title?>
                    </h3>
                    <div class="card-footer">
                        <span class="views"><i class="bi bi-eye"></i> <?php echo number_format($thread->cnt_view)?></span>
                    </div>
                </div>
            </a>
<?php
        endforeach;
else:
?>
                <div class="forum-card">
                    <div class="text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
                        <h4 class="mt-3 text-muted"><?php echo __('게시글이 없습니다.', 'skin')?></h4>
                    </div>
                </div>
<?php
endif;
?>
            </div>

            <!-- Pagination -->
            <?php if(isset($totalPages) && $totalPages > 1): ?>
            <nav aria-label="Page navigation" data-aos="fade-up" data-aos-delay="300">
                <ul class="pagination justify-content-center">
                    <!-- 이전 페이지 -->
                    <?php if($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" aria-label="Previous">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                    <?php else: ?>
                    <li class="page-item disabled">
                        <a class="page-link" href="#" aria-label="Previous">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                    <?php endif; ?>

                    <!-- 페이지 번호 -->
                    <?php
                    $startPage = max(1, $page - 2);
                    $endPage = min($totalPages, $page + 2);

                    if($startPage > 1):
                    ?>
                    <li class="page-item">
                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => 1])); ?>">1</a>
                    </li>
                    <?php if($startPage > 2): ?>
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php endif; ?>
                    <?php endif; ?>

                    <?php for($i = $startPage; $i <= $endPage; $i++): ?>
                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                    </li>
                    <?php endfor; ?>

                    <?php if($endPage < $totalPages): ?>
                    <?php if($endPage < $totalPages - 1): ?>
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php endif; ?>
                    <li class="page-item">
                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $totalPages])); ?>"><?php echo $totalPages; ?></a>
                    </li>
                    <?php endif; ?>

                    <!-- 다음 페이지 -->
                    <?php if($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" aria-label="Next">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                    <?php else: ?>
                    <li class="page-item disabled">
                        <a class="page-link" href="#" aria-label="Next">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </section>