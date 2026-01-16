<?php
/**
 * 출고후기 상세 페이지
 * Bootstrap 그리드 레이아웃 (좌측 col-lg-9 본문 + 우측 col-lg-3 사이드바)
 */
ExpertNote\Core::setLayout("v2");
$listParams = [
    "/forum",
    htmlspecialchars($article->forum_code),
];
$listQueryParams = [];
if($_GET['category']) {
    $listParams[] = "category";
    $listParams[] = ExpertNote\Forum\Thread::slugify($_GET['category']);
}
if(isset($_GET['q'])) $listQueryParams['q'] = $_GET['q'];
if(isset($_GET['page'])) $listQueryParams['page'] = $_GET['page'];
$listPathStr = implode("/", $listParams);
$listPathQueryStr = count($listQueryParams) > 0 ? "?".http_build_query($listQueryParams) : "";

// 본문에서 이미지 추출
preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $article->contents, $imageMatches);
$contentImages = $imageMatches[1] ?? [];

// 연관 포스트 검색 (현재 글 제목 기반)
$sql = "SELECT f.idx, f.forum_code, f.title,
        MATCH(f.title, f.contents) AGAINST(:search_term IN NATURAL LANGUAGE MODE) AS score
        FROM expertnote_forum f
        WHERE f.forum_code = :forum_code
            AND f.locale = :locale
            AND f.status = 'PUBLISHED'
            AND f.idx != :current_idx
            AND MATCH(f.title, f.contents) AGAINST(:search_term IN NATURAL LANGUAGE MODE)
        ORDER BY score DESC
        LIMIT 4";

$relatedThreads = ExpertNote\DB::getRows($sql, [
    'search_term' => $article->title,
    'forum_code' => $article->forum_code,
    'locale' => $article->locale,
    'current_idx' => $article->idx
]);

// 제목에서 FULLTEXT 검색어 추출 (신차, 중고차 검색용)
$searchTermForCars = '';
$carKeywords = [];
if (!empty($article->title)) {
    // 불필요한 단어 제거
    $cleanTitle = preg_replace('/출고후기|출고|후기|\d+년|\d+월|신차|중고|렌트|리스|계약|인수/u', ' ', $article->title);
    $carKeywords = array_filter(array_map('trim', explode(' ', $cleanTitle)), function($kw) {
        return mb_strlen($kw) >= 2;
    });
    // FULLTEXT 검색어 생성 (각 키워드를 OR로 연결)
    $searchTermForCars = implode(' ', $carKeywords);
}

// 연관 신차 검색 (FULLTEXT 검색)
$relatedNewCars = [];
if (!empty($searchTermForCars)) {
    $sqlNewCars = "SELECT r.idx, r.model, r.brand, r.monthly_price, r.image, MATCH(r.brand, r.model) AGAINST(:search IN NATURAL LANGUAGE MODE) as relevance FROM " . DB_PREFIX . "rent r WHERE r.dealer_idx = 1 AND r.status = 'active' AND r.car_type = 'NEW' AND MATCH(r.brand, r.model) AGAINST(:search IN NATURAL LANGUAGE MODE) ORDER BY relevance DESC LIMIT 4";
    $relatedNewCars = ExpertNote\DB::getRows($sqlNewCars, ['search' => $searchTermForCars]) ?: [];
}

// 연관 중고차 검색 (FULLTEXT 검색)
$relatedUsedCars = [];
if (!empty($searchTermForCars)) {
    $sqlUsedCars = "SELECT r.idx, r.model, r.brand, r.monthly_price, r.image, MATCH(r.brand, r.model) AGAINST(:search IN NATURAL LANGUAGE MODE) as relevance FROM " . DB_PREFIX . "rent r WHERE r.dealer_idx = 1 AND r.status = 'active' AND r.car_type = 'USED' AND MATCH(r.brand, r.model) AGAINST(:search IN NATURAL LANGUAGE MODE) ORDER BY relevance DESC LIMIT 4";
    $relatedUsedCars = ExpertNote\DB::getRows($sqlUsedCars, ['search' => $searchTermForCars]) ?: [];
}

// 연관 영상 검색 (키워드 OR 검색)
$relatedVideos = [];
if (!empty($carKeywords)) {
    $likeConditions = [];
    $params = [];
    $i = 0;
    foreach ($carKeywords as $keyword) {
        $likeConditions[] = "COALESCE(l1.title, l2.title, y.channel_title) LIKE :kw{$i}";
        $params["kw{$i}"] = '%' . $keyword . '%';
        $i++;
    }
    $whereClause = implode(' OR ', $likeConditions);
    $sqlVideos = "SELECT y.idx, y.youtube_video_id, y.thumbnail_medium, y.thumbnail_default, COALESCE(l1.title, l2.title, y.channel_title) as title FROM " . DB_PREFIX . "youtube y LEFT JOIN " . DB_PREFIX . "youtubeLocale l1 ON y.idx = l1.youtube_idx AND l1.locale = SUBSTRING(y.default_audio_language, 1, 2) LEFT JOIN " . DB_PREFIX . "youtubeLocale l2 ON y.idx = l2.youtube_idx AND l2.locale = 'en' WHERE y.status = 'PUBLISHED' AND ({$whereClause}) ORDER BY y.published_at DESC LIMIT 4";
    $relatedVideos = ExpertNote\DB::getRows($sqlVideos, $params) ?: [];
}
?>
<script type="application/ld+json"><?php echo json_encode($structuredData, JSON_PRETTY_PRINT)?></script>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1 class="page-title" data-aos="fade-up"><?php echo $forumConfig->forum_title?></h1>
            <p class="page-desc" data-aos="fade-up" data-aos-delay="100"><?php echo $forumConfig->short_desc?></p>
        </div>
    </section>
    
    <!-- Forum View Section -->
    <section class="forum-view-section">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-8">
                    <!-- Post Header -->
                    <div class="post-header" data-aos="fade-up">
                        <div class="post-category">
                            <span class="badge bg-primary"><?php echo htmlspecialchars($article->category) ?></span>
                        </div>
                        <h2 class="post-title"><?php echo htmlspecialchars($article->title) ?></h2>
                        <div class="d-flex justify-content-between gap-3">
                            <div class="post-meta">
                                <span class="meta-item">
                                    <i class="bi bi-person-circle"></i>
                                    <?php echo htmlspecialchars($article->nickname ?: $article->username) ?>
                                </span>
                                <span class="meta-item">
                                    <i class="bi bi-calendar3"></i>
                                    <?php echo date('Y.m.d H:i', strtotime($article->write_time)) ?>
                                </span>
                                <span class="meta-item">
                                    <i class="bi bi-eye"></i>
                                    조회 <?php echo number_format($article->cnt_view) ?>
                                </span>
                                <span class="meta-item">
                                    <i class="bi bi-chat-dots"></i>
                                    댓글 <?php echo number_format($article->cnt_comments) ?>
                                </span>
                            </div>
                            <div>
                                <div class="text-center mt-3">
                                    <button class="share-btn d-inline-block" onclick="shareToFacebook()" title="Facebook">
                                        <i class="bi bi-facebook"></i>
                                    </button>
                                    <button class="share-btn d-inline-block" onclick="shareToTwitter()" title="Twitter">
                                        <i class="bi bi-twitter-x"></i>
                                    </button>
                                    <button class="share-btn d-inline-block" onclick="copyUrl()" title="<?php echo __('URL 복사', 'skin') ?>">
                                        <i class="bi bi-link-45deg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Post Content -->
                    <div class="post-content" data-aos="fade-up" data-aos-delay="100">
                        <div class="content-body">
                            <?php echo $article->contents; ?>
                        </div>

        <?php if($article->cnt_files > 0): ?>
                        <!-- Attachments -->
                        <!-- <div class="post-attachments">
                            <h5><i class="bi bi-paperclip"></i> 첨부파일</h5>
                            <ul class="attachment-list">
                                <li>
                                    <a href="#" class="attachment-item">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                        <span class="attachment-name">2024_신년_프로모션_안내.pdf</span>
                                        <span class="attachment-size">(2.5MB)</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="attachment-item">
                                        <i class="bi bi-file-earmark-image"></i>
                                        <span class="attachment-name">프로모션_이벤트_상세_이미지.jpg</span>
                                        <span class="attachment-size">(1.8MB)</span>
                                    </a>
                                </li>
                            </ul>
                        </div> -->
        <?php endif; ?>
                    </div>

                    <!-- Post Actions -->
                    <div class="post-actions" data-aos="fade-up" data-aos-delay="200">
                        <div class="action-left">
                            <button class="btn btn-outline-danger rounded-1" id="likeBtn" onclick="toggleLike(<?php echo $idx ?>, 'LIKE')">
                                <i class="bi bi-heart-fill me-2"></i>
                                <span><?php echo __('좋아요', 'skin') ?></span>
                                <strong class="ms-1" id="like-count-<?php echo $idx ?>"><?php echo number_format($article->cnt_like) ?></strong>
                            </button>
                            <button class="btn btn-outline-secondary rounded-1" id="dislikeBtn" onclick="toggleLike(<?php echo $idx ?>, 'DISLIKE')">
                                <i class="bi bi-hand-thumbs-down me-2"></i>
                                <span><?php echo __('싫어요', 'skin') ?></span>
                                <strong class="ms-1" id="dislike-count-<?php echo $idx ?>"><?php echo number_format($article->cnt_dislike) ?></strong>
                            </button>
                        </div>
                        <div class="action-right">
                            <a href="<?php echo $listPathStr . $listPathQueryStr ?>"  class="btn btn-primary rounded-1">
                                <i class="bi bi-list me-1"></i> <?php echo __('목록으로', 'skin') ?>
                            </a>
                            <?php if ($isAuthor || $isAdmin): ?>
                            <a href="/forum/<?php echo urlencode($article->forum_code) ?>/edit/<?php echo $article->idx ?>" class="btn btn-outline-primary rounded-1">
                                <i class="bi bi-pencil me-1"></i> <?php echo __('수정', 'skin') ?>
                            </a>
                            <button onclick="deletePost(<?php echo $article->idx ?>)" class="btn btn-outline-danger rounded-1">
                                <i class="bi bi-trash me-1"></i> <?php echo __('삭제', 'skin') ?>
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Post Navigation -->
                    <!-- <div class="post-navigation" data-aos="fade-up" data-aos-delay="300">
                        <a href="#" class="nav-item nav-prev">
                            <div class="nav-direction">
                                <i class="bi bi-chevron-up"></i> 이전글
                            </div>
                            <div class="nav-title">홈페이지 리뉴얼 및 신규 기능 추가 안내</div>
                        </a>
                        <a href="#" class="nav-item nav-next">
                            <div class="nav-direction">
                                <i class="bi bi-chevron-down"></i> 다음글
                            </div>
                            <div class="nav-title">시스템 정기 점검 안내 (1월 15일)</div>
                        </a>
                    </div> -->

                    <!-- Comments Section -->
                    <div class="comments-section" data-aos="fade-up" data-aos-delay="400">
                        <h3 class="comments-title">
                            <i class="bi bi-chat-dots"></i> 댓글 <span class="comment-count">24</span>
                        </h3>

                        <!-- Comment Write -->
                        <div class="comment-write">
                            <form id="commentForm">
                                <div class="comment-input-group">
                                    <textarea class="form-control" rows="4" placeholder="댓글을 입력하세요." required></textarea>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-send"></i> 댓글 등록
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
                <div class="col-lg-4">
                    <?php if(count($relatedNewCars) > 0): ?>
                    <!-- 연관 신차 -->
                    <div class="sidebar-card">
                        <h4 class="sidebar-title">
                            <i class="bi bi-car-front text-primary"></i> <?php echo __('연관 신차', 'skin') ?>
                        </h4>
                        <div class="row row-cols-2 g-2">
                            <?php foreach($relatedNewCars as $car): ?>
                            <div class="col">
                                <a href="/car/<?php echo $car->idx ?>/<?php echo \ExpertNote\Utils::getPermaLink($car->brand . ' ' . $car->model, true) ?>" class="sidebar-car-card">
                                    <div class="sidebar-car-thumb">
                                        <?php if($car->image): ?>
                                        <img src="<?php echo htmlspecialchars($car->image) ?>" alt="<?php echo htmlspecialchars($car->model) ?>">
                                        <?php else: ?>
                                        <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                                            <i class="bi bi-car-front text-muted"></i>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="sidebar-car-info">
                                        <div class="sidebar-car-brand"><?php echo htmlspecialchars($car->brand) ?></div>
                                        <div class="sidebar-car-model"><?php echo htmlspecialchars($car->model) ?></div>
                                        <?php if($car->monthly_price): ?>
                                        <div class="sidebar-car-price"><?php echo __('월', 'skin') ?> <?php echo number_format($car->monthly_price) ?><?php echo __('원', 'skin') ?></div>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <a href="/new-car" class="btn btn-outline-primary btn-sm w-100 mt-3">
                            <?php echo __('신차 더보기', 'skin') ?> <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                    <?php endif; ?>

                    <?php if(count($relatedUsedCars) > 0): ?>
                    <!-- 연관 중고차 -->
                    <div class="sidebar-card">
                        <h4 class="sidebar-title">
                            <i class="bi bi-car-front-fill text-success"></i> <?php echo __('연관 중고차', 'skin') ?>
                        </h4>
                        <div class="row row-cols-2 g-2">
                            <?php foreach($relatedUsedCars as $car): ?>
                            <div class="col">
                                <a href="/car/<?php echo $car->idx ?>/<?php echo \ExpertNote\Utils::getPermaLink($car->brand . ' ' . $car->model, true) ?>" class="sidebar-car-card">
                                    <div class="sidebar-car-thumb">
                                        <?php if($car->image): ?>
                                        <img src="<?php echo htmlspecialchars($car->image) ?>" alt="<?php echo htmlspecialchars($car->model) ?>">
                                        <?php else: ?>
                                        <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                                            <i class="bi bi-car-front-fill text-muted"></i>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="sidebar-car-info">
                                        <div class="sidebar-car-brand"><?php echo htmlspecialchars($car->brand) ?></div>
                                        <div class="sidebar-car-model"><?php echo htmlspecialchars($car->model) ?></div>
                                        <?php if($car->monthly_price): ?>
                                        <div class="sidebar-car-price"><?php echo __('월', 'skin') ?> <?php echo number_format($car->monthly_price) ?><?php echo __('원', 'skin') ?></div>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <a href="/used-car" class="btn btn-outline-success btn-sm w-100 mt-3">
                            <?php echo __('중고차 더보기', 'skin') ?> <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                    <?php endif; ?>

                    <?php if(count($relatedVideos) > 0): ?>
                    <!-- 연관 영상 -->

                    <div class="related-videos">
                        <h3 class="section-title">관련 영상</h3>
                        <div class="row row-cols-2 g-2">
                            <?php foreach($relatedVideos as $video):?>
                            <div class="col">
                                <a href="/video/<?php echo $video->idx ?>/<?php echo \ExpertNote\Utils::getPermaLink($video->title, true) ?>" target="_blank" class="video-card">
                                    <div class="video-thumbnail">
                                        <img src="<?php echo htmlspecialchars($video->thumbnail_medium ?: $video->thumbnail_default) ?>" alt="영상 썸네일">
                                        <div class="play-overlay">
                                            <i class="bi bi-play-circle-fill"></i>
                                        </div>
                                        <!-- <span class="video-duration">10:25</span> -->
                                    </div>
                                    <div class="video-info">
                                        <h4 class="video-title"><?php echo htmlspecialchars($video->title) ?></h4>
                                        <!-- <p class="video-channel">드림카렌트 TV</p> -->
                                    </div>
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <a href="/youtube" class="btn btn-outline-danger btn-sm w-100 mt-3">
                            <?php echo __('영상 더보기', 'skin') ?> <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                    <?php endif; ?>

                    <!-- 렌트 상담 CTA (스티키) -->
                    <div class="sidebar-sticky">
                        <div class="sidebar-card cta-sticky-card">
                            <div class="cta-buttons">
                                <a href="tel:010-4299-3772" class="btn cta-btn cta-btn-phone">
                                    <i class="bi bi-telephone-fill"></i>
                                    <span><?php echo __('전화로 무심사/저신용 상담 받기', 'skin') ?></span>
                                </a>
                                <a href="/kakaolink" target="_blank" class="btn cta-btn cta-btn-kakao">
                                    <i class="bi bi-chat-fill"></i>
                                    <span><?php echo __('카카오톡으로 무심사/저신용 상담 받기', 'skin') ?></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



