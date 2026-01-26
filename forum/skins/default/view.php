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
                    <?php
                    // 댓글 권한 체크
                    $canComment = false;
                    if ($isGuest && $forumConfig->permit_guest_comment === 'Y') {
                        $canComment = true;
                    } elseif ($isMember && $forumConfig->permit_member_comment === 'Y') {
                        $canComment = true;
                    } elseif (ExpertNote\User\User::isAdmin()) {
                        $canComment = true;
                    }
                    ?>
                    <?php if ($forumConfig->use_comment === 'Y'): ?>
                    <div class="comments-section" data-aos="fade-up" data-aos-delay="400">
                        <h3 class="comments-title">
                            <i class="bi bi-chat-dots"></i> <?php echo __('댓글', 'skin') ?> <span class="comment-count" id="comment-count">0</span><?php echo __('개', 'skin') ?>
                        </h3>

                        <?php if ($canComment): ?>
                        <!-- Comment Write -->
                        <div class="comment-write">
                            <form id="commentForm" onsubmit="return submitComment(event)">
                                <div class="comment-input-group">
                                    <textarea class="form-control" name="content" id="comment-editor" rows="4" placeholder="<?php echo __('댓글을 입력하세요...', 'skin') ?>" required></textarea>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-send"></i> <?php echo __('댓글 등록', 'skin') ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                        <?php endif; ?>

                        <!-- Comment List -->
                        <div class="comment-list" id="comments-list"></div>

                        <!-- Load More Comments -->
                        <div id="load-more-container" class="text-center mt-4" style="display: none;">
                            <button id="load-more-comments" class="btn btn-outline-secondary" onclick="loadMoreComments()">
                                <i class="bi bi-arrow-down-circle"></i> <?php echo __('댓글 더보기', 'skin') ?>
                            </button>
                        </div>
                        <!-- Empty Comments Message -->
                        <div class="comment-empty text-center py-5" id="comments-empty" style="display: none;">
                            <i class="bi bi-chat-square-text text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3 mb-0"><?php echo __('등록된 댓글이 없습니다.', 'skin') ?></p>
                            <?php if ($canComment): ?>
                            <p class="text-muted small"><?php echo __('첫 번째 댓글을 작성해보세요!', 'skin') ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
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
                                <a href="tel:1666-5623" class="btn cta-btn cta-btn-phone">
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





<style>
/* 공유 버튼 */
.share-btn {
    width: 38px;
    height: 38px;
    border: 1px solid #e0e0e0;
    background: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 1rem;
    color: #666;
}

.share-btn:hover {
    background: #f8f9fa;
    border-color: #D85D4E;
    color: #D85D4E;
}

/* 사이드바 */
.sidebar-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    padding: 1.25rem;
    margin-bottom: 1rem;
}

.sidebar-title {
    font-size: 1rem;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #f0f0f0;
}

/* 사이드바 차량 카드 (2열 그리드) */
.sidebar-car-card {
    display: block;
    text-decoration: none;
    background: #f8f9fa;
    border-radius: 10px;
    overflow: hidden;
    transition: all 0.2s ease;
}

.sidebar-car-card:hover {
    background: #f0f0f0;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.sidebar-car-thumb {
    aspect-ratio: 16/10;
    overflow: hidden;
    background: #e9ecef;
}

.sidebar-car-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.sidebar-car-card:hover .sidebar-car-thumb img {
    transform: scale(1.05);
}

.sidebar-car-info {
    padding: 0.5rem;
}

.sidebar-car-brand {
    font-size: 0.65rem;
    color: #888;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.sidebar-car-model {
    font-size: 0.75rem;
    font-weight: 600;
    color: #1a1a1a;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    margin-bottom: 0.25rem;
}

.sidebar-car-price {
    font-size: 0.7rem;
    color: #D85D4E;
    font-weight: 600;
}

/* 렌트 상담 CTA 카드 */
.cta-card {
    background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
    border: 2px solid #D85D4E;
    text-align: center;
}

.cta-header {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
}

.cta-logo {
    height: 28px;
    width: auto;
}

.cta-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #D85D4E;
    margin: 0;
}

.cta-desc {
    font-size: 0.85rem;
    color: #666;
    margin-bottom: 1rem;
}

.cta-buttons {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.cta-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
}

.cta-btn i {
    font-size: 1rem;
}

.cta-btn span {
    flex: 1;
    text-align: center;
}

.cta-btn-phone {
    background: #D85D4E;
    color: #fff;
    border: none;
}

.cta-btn-phone:hover {
    background: #c04d3f;
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(216, 93, 78, 0.3);
}

.cta-btn-kakao {
    background: #FEE500;
    color: #3C1E1E;
    border: none;
}

.cta-btn-kakao:hover {
    background: #F5DC00;
    color: #3C1E1E;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(254, 229, 0, 0.4);
}

/* 사이드바 스티키 (스크롤 따라다니기) */
.sidebar-sticky {
    position: sticky;
    top: 5rem;
}

.cta-sticky-card {
    background: linear-gradient(135deg, #fff 0%, #fef9f8 100%);
    border: 2px solid #D85D4E;
    box-shadow: 0 4px 20px rgba(216, 93, 78, 0.15);
}

/* 댓글 섹션 */
.comment-form {
    margin-bottom: 1.5rem;
}

.comment-form textarea {
    width: 100%;
    min-height: 100px;
    padding: 1rem;
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    font-size: 0.95rem;
    resize: vertical;
    transition: border-color 0.2s;
}

.comment-form textarea:focus {
    outline: none;
    border-color: #D85D4E;
}

.comment-form .btn-primary {
    margin-top: 0.75rem;
    padding: 0.6rem 1.5rem;
    border-radius: 8px;
    font-weight: 500;
}

.comment-item {
    padding: 1rem 0;
    border-bottom: 1px solid #f0f0f0;
}

.comment-item:last-child {
    border-bottom: none;
}

.comment-item.depth-1 { padding-left: 1.5rem; background: #fafafa; margin-left: 3rem; border-radius: 8px; }
.comment-item.depth-2 { padding-left: 2rem; background: #f5f5f5; margin-left: 6rem; border-radius: 8px; }
.comment-item.depth-3 { padding-left: 2.5rem; background: #f0f0f0; margin-left: 9rem; border-radius: 8px; }

.comment-author {
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 0.25rem;
}

.comment-meta {
    font-size: 0.8rem;
    color: #999;
    margin-bottom: 0.5rem;
}

.comment-content {
    color: #333;
    line-height: 1.6;
}

.comment-actions {
    margin-top: 0.5rem;
    display: flex;
    gap: 0.5rem;
}

.comment-actions .btn {
    padding: 0.25rem 0.75rem;
    font-size: 0.8rem;
}

.comment-edit-form,
.comment-reply-form {
    margin-top: 0.75rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.comment-edit-form textarea,
.comment-reply-form textarea {
    width: 100%;
    min-height: 80px;
    padding: 0.75rem;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

#load-more-container {
    padding: 1rem 0;
    text-align: center;
}

#load-more-comments {
    border-radius: 50px;
    padding: 0.5rem 2rem;
}

/* 반응형 */
@media (max-width: 768px) {
    .sidebar-card {
        padding: 1rem;
        border-radius: 12px;
    }
}
</style>

<?php /* 이전 디자인 - v2 디자인으로 대체됨 */ if(false): ?>
<div class="review-detail container-xl">
    <div class="row">
        <!-- 좌측: 본문 영역 (col-lg-9) -->
        <div class="col-lg-8">
            <!-- 헤더 카드 -->
            <div class="review-card">
                <h1 class="review-title"><?php echo htmlspecialchars($article->title) ?></h1>

                <div class="review-meta">
                    <div class="review-author">
                        <div class="author-avatar">
                            <?php echo mb_substr($article->nickname ?: $article->username, 0, 1) ?>
                        </div>
                        <div class="author-info">
                            <span class="author-name"><?php echo htmlspecialchars($article->nickname ?: $article->username) ?></span>
                            <span class="review-date"><?php echo date('Y년 m월 d일', strtotime($article->write_time)) ?></span>
                        </div>
                    </div>

                    <div class="review-stats">
                        <span class="stat-item"><i class="bi bi-eye"></i> <?php echo number_format($article->cnt_view) ?></span>
                        <span class="stat-item"><i class="bi bi-heart"></i> <?php echo number_format($article->cnt_like) ?></span>
                        <span class="stat-item"><i class="bi bi-chat"></i> <?php echo number_format($article->cnt_comments) ?></span>
                    </div>
                </div>
            </div>

            <!-- 이미지 갤러리 -->
            <?php if(count($contentImages) > 0 && $forumConfig->skin == "card"): ?>
            <div class="review-card review-gallery">
                <div class="gallery-main">
                    <img src="<?php echo htmlspecialchars($contentImages[0]) ?>" alt="<?php echo htmlspecialchars($article->title) ?>" id="mainGalleryImage" onclick="openLightbox(0)">
                </div>
                <?php if(count($contentImages) > 1): ?>
                <div class="gallery-thumbs">
                    <?php foreach($contentImages as $i => $img): ?>
                    <div class="gallery-thumb <?php echo $i === 0 ? 'active' : '' ?>" onclick="changeMainImage(<?php echo $i ?>, '<?php echo htmlspecialchars($img) ?>')">
                        <img src="<?php echo htmlspecialchars($img) ?>" alt="<?php echo __('이미지', 'skin') ?> <?php echo $i + 1 ?>">
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="gallery-counter">
                    <span id="currentImageNum">1</span> / <?php echo count($contentImages) ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>



            <!-- 본문 카드 -->
            <div class="review-card">
                <div class="review-content">
                    <?php echo $article->contents ?>
                </div>

                <?php if(count($files) > 0): ?>
                <div class="review-attachments">
                    <div class="attachment-title">
                        <i class="bi bi-paperclip"></i> <?php echo __('첨부파일', 'skin') ?>
                    </div>
                    <?php foreach ($files as $file): ?>
                    <div class="attachment-item">
                        <i class="bi bi-file-earmark"></i>
                        <a href="/forum/download/<?php echo $file->idx ?>">
                            <?php echo htmlspecialchars($file->real_name) ?>
                        </a>
                        <span class="attachment-size">
                            (<?php echo ExpertNote\Utils::convertFileSize($file->size) ?>)
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- 좋아요/싫어요 및 공유 -->
            <div class="review-card">
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <button class="btn btn-outline-danger w-100 py-3" id="likeBtn" onclick="toggleLike(<?php echo $idx ?>, 'LIKE')">
                            <i class="bi bi-heart-fill me-2"></i>
                            <span><?php echo __('좋아요', 'skin') ?></span>
                            <strong class="ms-1" id="like-count-<?php echo $idx ?>"><?php echo number_format($article->cnt_like) ?></strong>
                        </button>
                    </div>
                    <div class="col-6">
                        <button class="btn btn-outline-secondary w-100 py-3" id="dislikeBtn" onclick="toggleLike(<?php echo $idx ?>, 'DISLIKE')">
                            <i class="bi bi-hand-thumbs-down me-2"></i>
                            <span><?php echo __('별로예요', 'skin') ?></span>
                            <strong class="ms-1" id="dislike-count-<?php echo $idx ?>"><?php echo number_format($article->cnt_dislike) ?></strong>
                        </button>
                    </div>
                </div>
                <div class="share-buttons">
                    <button class="share-btn" onclick="shareToFacebook()" title="Facebook">
                        <i class="bi bi-facebook"></i>
                    </button>
                    <button class="share-btn" onclick="shareToTwitter()" title="Twitter">
                        <i class="bi bi-twitter-x"></i>
                    </button>
                    <button class="share-btn" onclick="copyUrl()" title="<?php echo __('URL 복사', 'skin') ?>">
                        <i class="bi bi-link-45deg"></i>
                    </button>
                </div>
            </div>

            <!-- 푸터 네비게이션 -->
            <div class="review-card">
                <div class="review-footer">
                    <div class="footer-nav">
                        <a href="<?php echo $listPathStr . $listPathQueryStr ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-list me-1"></i> <?php echo __('목록으로', 'skin') ?>
                        </a>
                        <?php if ($isAuthor || $isAdmin): ?>
                        <a href="/forum/<?php echo urlencode($article->forum_code) ?>/edit/<?php echo $article->idx ?>" class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-1"></i> <?php echo __('수정', 'skin') ?>
                        </a>
                        <button onclick="deletePost(<?php echo $article->idx ?>)" class="btn btn-outline-danger">
                            <i class="bi bi-trash me-1"></i> <?php echo __('삭제', 'skin') ?>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if(count($relatedThreads) > 0): ?>
            <!-- 관련 후기 -->
            <div class="review-card">
                <h3 class="sidebar-title">
                    <i class="bi bi-collection"></i> <?php echo __('관련 출고후기', 'skin') ?>
                </h3>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-3">
                    <?php foreach($relatedThreads as $row):
                        // 본문에서 첫 번째 이미지 추출
                        $threadContent = ExpertNote\DB::getVar("SELECT contents FROM expertnote_forum WHERE idx = :idx", ['idx' => $row->idx]);
                        preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $threadContent, $thumbMatch);
                        $thumbUrl = $thumbMatch[1] ?? '';
                    ?>
                    <div class="col">
                        <a href="<?php echo "/forum/{$row->forum_code}/".ExpertNote\Forum\Thread::getPermalink($row->idx, $row->title) ?>" class="card h-100 text-decoration-none border-0 shadow-sm related-thread-card">
                            <div class="card-img-top related-thread-thumb">
                                <?php if($thumbUrl): ?>
                                <img src="<?php echo htmlspecialchars($thumbUrl) ?>" alt="<?php echo htmlspecialchars($row->title) ?>">
                                <?php else: ?>
                                <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                                    <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="card-body p-2">
                                <p class="card-text small fw-semibold text-dark mb-0 related-thread-title"><?php echo htmlspecialchars($row->title) ?></p>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- 댓글 섹션 -->
            <?php if ($forumConfig->use_comment === 'Y'): ?>
            <div class="review-card">
                <div class="comments-header">
                    <i class="bi bi-chat-dots"></i>
                    <?php echo __('댓글', 'skin') ?>
                    <span id="comment-count">0</span><?php echo __('개', 'skin') ?>
                </div>

                <?php
                $canComment = false;
                if ($isGuest && $forumConfig->permit_guest_comment === 'Y') {
                    $canComment = true;
                } elseif ($isMember && $forumConfig->permit_member_comment === 'Y') {
                    $canComment = true;
                } elseif (ExpertNote\User\User::isAdmin()) {
                    $canComment = true;
                }

                if ($canComment): ?>
                <form class="comment-form" onsubmit="return submitComment(event)">
                    <textarea name="content" id="comment-editor" placeholder="<?php echo __('댓글을 입력하세요...', 'skin') ?>" required></textarea>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-1"></i> <?php echo __('댓글 작성', 'skin') ?>
                    </button>
                </form>
                <?php endif; ?>

                <div id="comments-list"></div>

                <div id="load-more-container" style="display: none;">
                    <button id="load-more-comments" class="btn btn-outline-secondary" onclick="loadMoreComments()">
                        <i class="bi bi-plus-circle me-1"></i> <?php echo __('댓글 더 보기', 'skin') ?>
                    </button>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- 우측: 사이드바 (col-lg-4) -->
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
            <div class="sidebar-card">
                <h4 class="sidebar-title">
                    <i class="bi bi-youtube text-danger"></i> <?php echo __('연관 영상', 'skin') ?>
                </h4>
                <div class="row row-cols-2 g-2">
                    <?php foreach($relatedVideos as $video): ?>
                    <div class="col">
                        <a href="/video/<?php echo $video->idx ?>/<?php echo \ExpertNote\Utils::getPermaLink($video->title, true) ?>" class="sidebar-video-card">
                            <div class="sidebar-video-thumb">
                                <img src="<?php echo htmlspecialchars($video->thumbnail_medium ?: $video->thumbnail_default) ?>" alt="<?php echo htmlspecialchars($video->title) ?>">
                                <div class="sidebar-video-play"><i class="bi bi-play-fill"></i></div>
                            </div>
                            <div class="sidebar-video-title"><?php echo htmlspecialchars($video->title) ?></div>
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
                        <a href="tel:1666-5623" class="btn cta-btn cta-btn-phone">
                            <i class="bi bi-telephone-fill"></i>
                            <span><?php echo __('전화로 무심사/저신용 상담 받기', 'skin') ?></span>
                        </a>
                        <a href="http://pf.kakao.com/_ugtHn/chat" target="_blank" class="btn cta-btn cta-btn-kakao">
                            <i class="bi bi-chat-fill"></i>
                            <span><?php echo __('카카오톡으로 무심사/저신용 상담 받기', 'skin') ?></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; /* 이전 디자인 끝 */ ?>

<script>
// 이미지 갤러리 기능
const contentImages = <?php echo json_encode($contentImages) ?>;
let currentImageIndex = 0;

function changeMainImage(index, src) {
    document.getElementById('mainGalleryImage').src = src;
    currentImageIndex = index;

    // 썸네일 active 상태 변경
    document.querySelectorAll('.gallery-thumb').forEach((thumb, i) => {
        thumb.classList.toggle('active', i === index);
    });

    // 이미지 카운터 업데이트
    const counterEl = document.getElementById('currentImageNum');
    if (counterEl) {
        counterEl.textContent = index + 1;
    }
}

function openLightbox(index) {
    window.open(contentImages[index], '_blank');
}

// 공유 기능
function getShareUrl() {
    return window.location.origin + '/forum/<?php echo urlencode($article->forum_code) ?>/<?php echo \ExpertNote\Forum\Thread::getPermalink($idx, $article->title) ?>';
}

function getShareTitle() {
    return document.querySelector('.review-title').textContent;
}

function shareToFacebook() {
    const url = encodeURIComponent(getShareUrl());
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, 'facebook-share', 'width=600,height=400');
}

function shareToTwitter() {
    const url = encodeURIComponent(getShareUrl());
    const text = encodeURIComponent(getShareTitle());
    window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, 'twitter-share', 'width=600,height=400');
}

function copyUrl() {
    const url = getShareUrl();
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(url).then(() => {
            ExpertNote.Util.showMessage('<?php echo __('URL이 복사되었습니다.', 'skin') ?>', '<?php echo __('복사 완료', 'skin') ?>');
        });
    } else {
        const textarea = document.createElement('textarea');
        textarea.value = url;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        ExpertNote.Util.showMessage('<?php echo __('URL이 복사되었습니다.', 'skin') ?>', '<?php echo __('복사 완료', 'skin') ?>');
    }
}

// 좋아요/싫어요
function toggleLike(idx, actionType) {
    fetch('/api/v1/forum/like', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ forum_idx: idx, action_type: actionType })
    })
    .then(res => res.json())
    .then(data => {
        if (data.result === 'SUCCESS' && data.data) {
            document.getElementById(`like-count-${idx}`).textContent = data.data.cnt_like || 0;
            document.getElementById(`dislike-count-${idx}`).textContent = data.data.cnt_dislike || 0;

            if (actionType === 'LIKE') {
                const likeBtn = document.getElementById('likeBtn');
                likeBtn.classList.toggle('btn-outline-danger');
                likeBtn.classList.toggle('btn-danger');
            } else {
                const dislikeBtn = document.getElementById('dislikeBtn');
                dislikeBtn.classList.toggle('btn-outline-secondary');
                dislikeBtn.classList.toggle('btn-secondary');
            }
        } else {
            ExpertNote.Util.showMessage(data.message || '<?php echo __('처리에 실패했습니다.', 'skin') ?>', '<?php echo __('오류', 'skin') ?>');
        }
    })
    .catch(err => {
        console.error(err);
        ExpertNote.Util.showMessage('<?php echo __('오류가 발생했습니다.', 'skin') ?>', '<?php echo __('오류', 'skin') ?>');
    });
}

function deletePost(idx) {
    if (!confirm('<?php echo __('정말 삭제하시겠습니까?', 'skin') ?>')) return;
    location.href = `/forum/<?php echo urlencode($article->forum_code) ?>/delete/${idx}`;
}

// 댓글 관련 함수들
let currentPage = 1;
let totalComments = 0;
let isLoading = false;

function loadComments(page = 1) {
    if (isLoading) return;
    isLoading = true;

    const emptyEl = document.getElementById('comments-empty');

    fetch(`/api/v1/forum/comments?idx=<?php echo $idx ?>&page=${page}&limit=20`)
        .then(res => res.json())
        .then(data => {
            if (data.result === 'SUCCESS' || data.data) {
                totalComments = data.total || 0;
                document.getElementById('comment-count').textContent = totalComments;

                if (data.data && data.data.length > 0) {
                    const commentsList = document.getElementById('comments-list');
                    data.data.forEach(comment => {
                        commentsList.appendChild(renderComment(comment));
                    });

                    if (data.currentPage < data.totalPage) {
                        document.getElementById('load-more-container').style.display = 'block';
                    } else {
                        document.getElementById('load-more-container').style.display = 'none';
                    }

                    // 댓글이 있으면 빈 상태 메시지 숨김
                    if (emptyEl) emptyEl.style.display = 'none';
                } else {
                    // 댓글이 없으면 빈 상태 메시지 표시
                    if (emptyEl) emptyEl.style.display = 'block';
                }
            }
            isLoading = false;
        })
        .catch(err => {
            console.error('댓글 로드 오류:', err);
            isLoading = false;
        });
}

function renderComment(comment) {
    const div = document.createElement('div');
    div.className = `comment-item depth-${Math.min(comment.depth || 0, 3)}`;
    div.id = `comment-${comment.idx}`;

    const isAuthor = <?php echo ExpertNote\User\User::isLogin() ? "'" . $_SESSION['user_id'] . "'" : 'null' ?> === comment.user_id;
    const isAdmin = <?php echo ExpertNote\User\User::isAdmin() ? 'true' : 'false' ?>;
    const canReply = <?php echo $canComment ? 'true' : 'false' ?>;
    const authorName = comment.nickname || comment.username;
    const isAdminUser = comment.user_level >= 9;

    let actionsHtml = '';
    if (canReply && (comment.depth || 0) < 3) {
        actionsHtml += `<button class="btn-action" onclick="replyComment(${comment.idx}, '${authorName}')"><i class="bi bi-reply"></i> <?php echo __('답글', 'skin') ?></button>`;
    }
    if (isAuthor || isAdmin) {
        actionsHtml += `<button class="btn-action" onclick="editComment(${comment.idx}, '${(comment.contents || '').replace(/'/g, "\\'").replace(/\n/g, '\\n')}')"><?php echo __('수정', 'skin') ?></button>`;
        actionsHtml += `<button class="btn-action text-danger" onclick="deleteComment(${comment.idx})"><?php echo __('삭제', 'skin') ?></button>`;
    }

    div.innerHTML = `
        <div class="comment-avatar">
            <i class="bi bi-person-circle"></i>
        </div>
        <div class="comment-content">
            <div class="comment-header">
                <span class="comment-author${isAdminUser ? ' admin' : ''}">${authorName}</span>
                <span class="comment-date">${new Date(comment.write_time).toLocaleString('ko-KR')}</span>
            </div>
            <div class="comment-body" id="comment-content-${comment.idx}">${(comment.contents || '').replace(/\n/g, '<br>')}</div>
            <div class="comment-actions">${actionsHtml}</div>
        </div>
    `;

    return div;
}

function loadMoreComments() {
    currentPage++;
    loadComments(currentPage);
}

function submitComment(e) {
    e.preventDefault();
    const form = e.target;
    const contents = form.content.value;

    if (!contents || !contents.trim()) {
        ExpertNote.Util.showMessage('<?php echo __('댓글 내용을 입력하세요.', 'skin') ?>', '<?php echo __('입력 오류', 'skin') ?>');
        return false;
    }

    fetch('/api/v1/forum/comment', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ parent_idx: <?php echo $idx ?>, reply_idx: 0, contents: contents })
    })
    .then(res => res.json())
    .then(data => {
        if (data.result === 'SUCCESS') {
            ExpertNote.Util.showMessage('<?php echo __('댓글이 작성되었습니다.', 'skin') ?>', '<?php echo __('작성 완료', 'skin') ?>');
            form.reset();
            document.getElementById('comments-list').innerHTML = '';
            currentPage = 1;
            loadComments(1);
        } else {
            ExpertNote.Util.showMessage(data.message || '<?php echo __('댓글 작성에 실패했습니다.', 'skin') ?>', '<?php echo __('오류', 'skin') ?>');
        }
    })
    .catch(err => {
        console.error(err);
        ExpertNote.Util.showMessage('<?php echo __('오류가 발생했습니다.', 'skin') ?>', '<?php echo __('오류', 'skin') ?>');
    });

    return false;
}

function editComment(commentIdx, currentContent) {
    const contentDiv = document.getElementById('comment-content-' + commentIdx);
    const commentItem = document.getElementById('comment-' + commentIdx);
    if (commentItem.querySelector('.comment-edit-form')) return;

    const editForm = document.createElement('div');
    editForm.className = 'comment-edit-form mt-2';
    editForm.innerHTML = `
        <textarea class="form-control mb-2" id="edit-textarea-${commentIdx}" rows="3">${currentContent}</textarea>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-primary" onclick="saveComment(${commentIdx})"><?php echo __('저장', 'skin') ?></button>
            <button class="btn btn-sm btn-secondary" onclick="cancelEdit(${commentIdx})"><?php echo __('취소', 'skin') ?></button>
        </div>
    `;
    contentDiv.style.display = 'none';
    contentDiv.parentNode.insertBefore(editForm, contentDiv.nextSibling);
}

function cancelEdit(commentIdx) {
    const contentDiv = document.getElementById('comment-content-' + commentIdx);
    const commentItem = document.getElementById('comment-' + commentIdx);
    const editForm = commentItem.querySelector('.comment-edit-form');
    if (editForm) editForm.remove();
    contentDiv.style.display = 'block';
}

function saveComment(commentIdx) {
    const textarea = document.getElementById('edit-textarea-' + commentIdx);
    const contents = textarea.value;
    if (!contents.trim()) {
        ExpertNote.Util.showMessage('<?php echo __('댓글 내용을 입력하세요.', 'skin') ?>', '<?php echo __('입력 오류', 'skin') ?>');
        return;
    }

    fetch('/api/v1/forum/comment', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ idx: commentIdx, contents: contents })
    })
    .then(res => res.json())
    .then(data => {
        if (data.result === 'SUCCESS') {
            ExpertNote.Util.showMessage('<?php echo __('댓글이 수정되었습니다.', 'skin') ?>', '<?php echo __('수정 완료', 'skin') ?>');
            document.getElementById('comments-list').innerHTML = '';
            currentPage = 1;
            loadComments(1);
        } else {
            ExpertNote.Util.showMessage(data.message || '<?php echo __('댓글 수정에 실패했습니다.', 'skin') ?>', '<?php echo __('오류', 'skin') ?>');
        }
    });
}

function deleteComment(commentIdx) {
    if (!confirm('<?php echo __('정말 삭제하시겠습니까?', 'skin') ?>')) return;

    fetch('/api/v1/forum/comment', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ idx: commentIdx })
    })
    .then(res => res.json())
    .then(data => {
        if (data.result === 'SUCCESS') {
            ExpertNote.Util.showMessage('<?php echo __('댓글이 삭제되었습니다.', 'skin') ?>', '<?php echo __('삭제 완료', 'skin') ?>');
            document.getElementById('comments-list').innerHTML = '';
            currentPage = 1;
            loadComments(1);
        } else {
            ExpertNote.Util.showMessage(data.message || '<?php echo __('댓글 삭제에 실패했습니다.', 'skin') ?>', '<?php echo __('오류', 'skin') ?>');
        }
    });
}

function replyComment(commentIdx, authorName) {
    const commentItem = document.getElementById('comment-' + commentIdx);
    if (commentItem.querySelector('.comment-reply-form')) return;
    document.querySelectorAll('.comment-reply-form').forEach(form => form.remove());

    const replyForm = document.createElement('div');
    replyForm.className = 'comment-reply-form mt-3 p-3 bg-light rounded';
    replyForm.innerHTML = `
        <div class="mb-2 text-muted small">
            <i class="bi bi-reply"></i> <strong>${authorName}</strong><?php echo __('님에게 답글', 'skin') ?>
        </div>
        <textarea class="form-control mb-2" id="reply-textarea-${commentIdx}" rows="3" placeholder="<?php echo __('답글을 입력하세요', 'skin') ?>"></textarea>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-primary" onclick="submitReply(${commentIdx})"><?php echo __('답글 작성', 'skin') ?></button>
            <button class="btn btn-sm btn-secondary" onclick="cancelReply(${commentIdx})"><?php echo __('취소', 'skin') ?></button>
        </div>
    `;
    commentItem.appendChild(replyForm);
    document.getElementById('reply-textarea-' + commentIdx).focus();
}

function cancelReply(commentIdx) {
    const commentItem = document.getElementById('comment-' + commentIdx);
    const replyForm = commentItem.querySelector('.comment-reply-form');
    if (replyForm) replyForm.remove();
}

function submitReply(replyToIdx) {
    const textarea = document.getElementById('reply-textarea-' + replyToIdx);
    const contents = textarea.value;
    if (!contents.trim()) {
        ExpertNote.Util.showMessage('<?php echo __('답글 내용을 입력하세요.', 'skin') ?>', '<?php echo __('입력 오류', 'skin') ?>');
        return;
    }

    fetch('/api/v1/forum/comment', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ parent_idx: <?php echo $idx ?>, reply_idx: replyToIdx, contents: contents })
    })
    .then(res => res.json())
    .then(data => {
        if (data.result === 'SUCCESS') {
            ExpertNote.Util.showMessage('<?php echo __('답글이 작성되었습니다.', 'skin') ?>', '<?php echo __('작성 완료', 'skin') ?>');
            document.getElementById('comments-list').innerHTML = '';
            currentPage = 1;
            loadComments(1);
        } else {
            ExpertNote.Util.showMessage(data.message || '<?php echo __('답글 작성에 실패했습니다.', 'skin') ?>', '<?php echo __('오류', 'skin') ?>');
        }
    });
}

// 페이지 로드 시 댓글 불러오기
document.addEventListener('DOMContentLoaded', function() {
    <?php if ($forumConfig->use_comment === 'Y'): ?>
    loadComments(1);
    <?php endif; ?>
});
</script>
