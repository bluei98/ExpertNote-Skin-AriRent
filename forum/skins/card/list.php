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

// 첫 번째 스레드를 Featured로 사용
// $featuredThread = null;
// $regularThreads = [];
// if(!empty($threads) && count($threads) > 0) {
//     $featuredThread = $threads[0];
//     $regularThreads = array_slice($threads, 1);
// }
$regularThreads = $threads;
?>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1 class="page-title" data-aos="fade-up"><?php echo $forumConfig->forum_title?></h1>
            <p class="page-desc" data-aos="fade-up" data-aos-delay="100"><?php echo $forumConfig->short_desc?></p>
        </div>
    </section>

    <!-- Blog Section -->
    <section class="blog-section">
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

            <?php if($featuredThread):
                $featuredTitle = htmlspecialchars($featuredThread->title ?? '');
                $featuredExcerpt = mb_substr(strip_tags($featuredThread->contents ?? ''), 0, 150) . '...';
                $featuredImage = $featuredThread->featured_image ?: 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?w=800&h=500&fit=crop';
                if(isset($_GET['q']) && $_GET['q']) {
                    $searchTerm = htmlspecialchars($_GET['q']);
                    $pattern = '/' . preg_quote($searchTerm, '/') . '/i';
                    $featuredTitle = preg_replace($pattern, '<mark>$0</mark>', $featuredTitle);
                }
            ?>
            <!-- Featured Post -->
            <div class="featured-post" data-aos="fade-up" data-aos-delay="100">
                <div class="row g-0">
                    <div class="col-lg-4">
                        <div class="featured-image">
                            <img src="<?php echo $featuredImage; ?>" alt="<?php echo $featuredTitle; ?>">
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="featured-content">
                            <?php if($featuredThread->category): ?>
                            <span class="blog-category"><?php echo htmlspecialchars($featuredThread->category); ?></span>
                            <?php endif; ?>
                            <h2 class="featured-title"><?php echo $featuredTitle; ?></h2>
                            <p class="featured-excerpt"><?php echo $featuredExcerpt; ?></p>
                            <div class="featured-meta">
                                <span class="meta-item"><i class="bi bi-calendar3"></i> <?php echo date('Y.m.d', strtotime($featuredThread->publish_time)); ?></span>
                                <span class="meta-item"><i class="bi bi-eye"></i> <?php echo number_format($featuredThread->cnt_view); ?></span>
                                <?php if($featuredThread->cnt_comments > 0): ?>
                                <span class="meta-item"><i class="bi bi-chat-dots"></i> <?php echo number_format($featuredThread->cnt_comments); ?></span>
                                <?php endif; ?>
                            </div>
                            <a href="/forum/<?php echo $featuredThread->forum_code?>/<?php echo ExpertNote\Forum\Thread::getPermalink($featuredThread->idx, $featuredThread->title)?><?php echo $queryParamStr?>" class="btn btn-primary mt-3">
                                <?php echo __('자세히 보기', 'skin')?> <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Blog Grid -->
            <div class="row g-4" data-aos="fade-up" data-aos-delay="200">
<?php
if(!empty($regularThreads) && count($regularThreads) > 0):
    foreach($regularThreads as $thread):
        $title = htmlspecialchars($thread->title ?? '');
        $excerpt = mb_substr(strip_tags($thread->contents ?? ''), 0, 100) . '...';

        // 대표 이미지 (없으면 기본 이미지)
        $defaultImages = [
            'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=600&h=400&fit=crop',
            'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=600&h=400&fit=crop',
            'https://images.unsplash.com/photo-1511919884226-fd3cad34687c?w=600&h=400&fit=crop',
            'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=600&h=400&fit=crop',
        ];
        $threadImage = $thread->featured_image ?: $defaultImages[$thread->idx % count($defaultImages)];

        if(isset($_GET['q']) && $_GET['q']) {
            $searchTerm = htmlspecialchars($_GET['q']);
            $pattern = '/' . preg_quote($searchTerm, '/') . '/i';
            $title = preg_replace($pattern, '<mark>$0</mark>', $title);
        }
?>
                <!-- Blog Card -->
                <div class="col-lg-4 col-md-6">
                    <article class="blog-card">
                        <div class="blog-image">
                            <a href="/forum/<?php echo $thread->forum_code?>/<?php echo ExpertNote\Forum\Thread::getPermalink($thread->idx, $thread->title)?>"><img src="<?php echo $threadImage; ?>" alt="<?php echo strip_tags($title); ?>"></a>
                            <?php if($thread->category): ?>
                            <span class="blog-category"><?php echo htmlspecialchars($thread->category); ?></span>
                            <?php endif; ?>
                            <?php if($thread->use_sticky == "Y" || $thread->use_all_sticky == "Y"): ?>
                            <span class="blog-badge-notice"><i class="bi bi-pin-fill"></i></span>
                            <?php endif; ?>
                        </div>
                        <div class="blog-content">
                            <h3 class="blog-title">
                                <a href="/forum/<?php echo $thread->forum_code?>/<?php echo ExpertNote\Forum\Thread::getPermalink($thread->idx, $thread->title)?><?php echo $queryParamStr?>"><?php echo $title; ?></a>
                            </h3>
                            <p class="blog-excerpt"><?php echo $excerpt; ?></p>
                            <div class="blog-meta">
                                <span class="meta-item"><i class="bi bi-calendar3"></i> <?php echo date('Y.m.d', strtotime($thread->publish_time)); ?></span>
                                <span class="meta-item"><i class="bi bi-eye"></i> <?php echo number_format($thread->cnt_view); ?></span>
                            </div>
                            <div class="blog-footer">
                                <?php if($thread->cnt_comments > 0): ?>
                                <span class="blog-comments"><i class="bi bi-chat-dots"></i> <?php echo number_format($thread->cnt_comments); ?></span>
                                <?php else: ?>
                                <span></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                </div>
<?php
    endforeach;
elseif(empty($featuredThread)):
?>
                <!-- 빈 결과 -->
                <div class="col-12">
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
