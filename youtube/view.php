<?php
/**
 * YouTube 영상 상세 페이지
 * arirent 스킨용 - v2 레이아웃
 */

ExpertNote\Core::setLayout("v2");

// 영상 idx 조회
$idx = $_GET['idx'] ?? 0;
if (!$idx) {
    header("Location: /youtube");
    exit;
}

// 영상 정보 조회
$video = \ExpertNote\Youtube::getVideo($idx);
if (!$video || $video->status !== 'PUBLISHED') {
    header("Location: /youtube");
    exit;
}

// 조회수 증가
\ExpertNote\Youtube::increaseViewCount($idx);

// 관련 영상 검색 (현재 영상 제목으로 검색, 자기 자신 제외)
$relatedVideos = \ExpertNote\Youtube::searchRelatedVideos($video->title, 5);
$relatedVideos = array_filter($relatedVideos, function($v) use ($video) {
    return $v->idx != $video->idx;
});
$relatedVideos = array_slice($relatedVideos, 0, 4);

// 연관 렌트 차량 검색 (영상 제목으로 FULLTEXT 검색)
$relatedNewCars = \AriRent\Rent::searchRelatedRents($video->title, 'NEW', 8);
$relatedUsedCars = \AriRent\Rent::searchRelatedRents($video->title, 'USED', 8);

// 연관 게시물 검색 (영상 제목으로 FULLTEXT 검색)
$relatedPosts = \ExpertNote\Forum\Forum::searchRelatedThreads($video->title, 6);

/**
 * 초를 시:분:초 형식으로 변환
 */
function formatDuration($seconds) {
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $secs = $seconds % 60;

    if ($hours > 0) {
        return sprintf("%d:%02d:%02d", $hours, $minutes, $secs);
    }
    return sprintf("%d:%02d", $minutes, $secs);
}

/**
 * 숫자 포맷팅 (조회수 등)
 */
function formatCount($num) {
    if ($num >= 100000000) {
        return number_format($num / 100000000, 1) . __('억', 'skin');
    } else if ($num >= 10000) {
        return number_format($num / 10000, 1) . __('만', 'skin');
    } else if ($num >= 1000) {
        return number_format($num / 1000, 1) . __('천', 'skin');
    }
    return number_format($num);
}

/**
 * 상대 시간 계산
 */
function timeAgo($datetime) {
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    if ($diff->y > 0) {
        return $diff->y . __('년 전', 'skin');
    } else if ($diff->m > 0) {
        return $diff->m . __('개월 전', 'skin');
    } else if ($diff->d > 0) {
        return $diff->d . __('일 전', 'skin');
    } else if ($diff->h > 0) {
        return $diff->h . __('시간 전', 'skin');
    } else if ($diff->i > 0) {
        return $diff->i . __('분 전', 'skin');
    }
    return __('방금 전', 'skin');
}

// 페이지 메타 설정
$pageTitle = $video->title;
$pageDescription = $video->description
    ? mb_substr(trim(preg_replace('/\s+/', ' ', strip_tags($video->description))), 0, 160)
    : $video->title;

ExpertNote\Core::setPageTitle($pageTitle . " - 아리렌트");
ExpertNote\Core::setPageDescription($pageDescription);
ExpertNote\Core::setPageKeywords("아리렌트, 유튜브, " . $video->channel_title . ", 장기렌트, 자동차");

// Open Graph 메타 태그
\ExpertNote\Core::addMetaTag('og:type', ["property"=>"og:type", "content"=>"video.other"]);
\ExpertNote\Core::addMetaTag('og:title', ["property"=>"og:title", "content"=>$pageTitle]);
\ExpertNote\Core::addMetaTag('og:description', ["property"=>"og:description", "content"=>$pageDescription]);
\ExpertNote\Core::addMetaTag('og:url', ["property"=>"og:url", "content"=>ExpertNote\Core::getBaseUrl() . "/video/" . $video->idx]);
\ExpertNote\Core::addMetaTag('og:image', ["property"=>"og:image", "content"=>$video->thumbnail_high ?: $video->thumbnail_medium]);
\ExpertNote\Core::addMetaTag('og:video', ["property"=>"og:video", "content"=>"https://www.youtube.com/embed/" . $video->youtube_video_id]);
\ExpertNote\Core::addMetaTag('og:site_name', ["property"=>"og:site_name", "content"=>"아리렌트"]);

// Twitter Card
\ExpertNote\Core::addMetaTag('twitter:card', ["name"=>"twitter:card", "content"=>"player"]);
\ExpertNote\Core::addMetaTag('twitter:title', ["name"=>"twitter:title", "content"=>$pageTitle]);
\ExpertNote\Core::addMetaTag('twitter:description', ["name"=>"twitter:description", "content"=>$pageDescription]);
\ExpertNote\Core::addMetaTag('twitter:image', ["name"=>"twitter:image", "content"=>$video->thumbnail_high ?: $video->thumbnail_medium]);
\ExpertNote\Core::addMetaTag('twitter:player', ["name"=>"twitter:player", "content"=>"https://www.youtube.com/embed/" . $video->youtube_video_id]);

// 구조화된 데이터 (Schema.org JSON-LD)
$schemaData = [
    "@context" => "https://schema.org",
    "@type" => "VideoObject",
    "name" => $video->title,
    "description" => $video->description ?: $video->title,
    "thumbnailUrl" => [
        $video->thumbnail_high ?: $video->thumbnail_medium,
        $video->thumbnail_medium ?: $video->thumbnail_default,
        $video->thumbnail_default
    ],
    "uploadDate" => date('c', strtotime($video->published_at)),
    "duration" => $video->duration ? 'PT' . $video->duration . 'S' : null,
    "contentUrl" => "https://www.youtube.com/watch?v=" . $video->youtube_video_id,
    "embedUrl" => "https://www.youtube.com/embed/" . $video->youtube_video_id,
    "interactionStatistic" => [
        [
            "@type" => "InteractionCounter",
            "interactionType" => ["@type" => "WatchAction"],
            "userInteractionCount" => $video->view_count ?: 0
        ]
    ],
    "author" => [
        "@type" => "Person",
        "name" => $video->channel_title,
        "url" => "https://www.youtube.com/channel/" . $video->channel_id
    ],
    "publisher" => [
        "@type" => "Organization",
        "name" => \ExpertNote\SiteMeta::get('site_name') ?: 'AriRent',
        "logo" => [
            "@type" => "ImageObject",
            "url" => ExpertNote\Core::getBaseUrl() . "/assets/images/logo.png"
        ]
    ]
];

// 좋아요 수가 있으면 추가
if ($video->like_count) {
    $schemaData["interactionStatistic"][] = [
        "@type" => "InteractionCounter",
        "interactionType" => ["@type" => "LikeAction"],
        "userInteractionCount" => $video->like_count
    ];
}

// 댓글 수가 있으면 추가
if ($video->comment_count) {
    $schemaData["interactionStatistic"][] = [
        "@type" => "InteractionCounter",
        "interactionType" => ["@type" => "CommentAction"],
        "userInteractionCount" => $video->comment_count
    ];
}

// null 값 제거
$schemaData = array_filter($schemaData, function($v) { return $v !== null; });
?>

<style>
:root {
    --primary-color: #0066FF;
    --secondary-color: #00D4AA;
    --dark-color: #1a1a2e;
    --light-bg: #f8fafc;
}

/* 브레드크럼 섹션 */
.breadcrumb-section {
    background: linear-gradient(135deg, var(--dark-color) 0%, #16213e 100%);
    padding: 20px 0;
}

.breadcrumb-section .breadcrumb {
    margin-bottom: 0;
    background: transparent;
}

.breadcrumb-section .breadcrumb-item a {
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    transition: color 0.3s ease;
}

.breadcrumb-section .breadcrumb-item a:hover {
    color: #fff;
}

.breadcrumb-section .breadcrumb-item.active {
    color: rgba(255, 255, 255, 0.9);
}

.breadcrumb-section .breadcrumb-item + .breadcrumb-item::before {
    color: rgba(255, 255, 255, 0.5);
}

/* 메인 컨텐츠 섹션 */
.video-view-section {
    background: var(--light-bg);
    padding: 40px 0 60px;
}

/* 비디오 플레이어 */
.video-player-wrapper {
    background: #000;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
}

/* 영상 정보 카드 */
.video-info-card {
    background: #fff;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.video-title {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--dark-color);
    line-height: 1.4;
    margin-bottom: 16px;
}

.video-stats {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    color: #6c757d;
    font-size: 0.9rem;
}

.video-stats span {
    display: flex;
    align-items: center;
    gap: 6px;
}

.video-stats i {
    font-size: 1rem;
}

/* 채널 정보 */
.channel-info {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 0;
    border-bottom: 1px solid #e9ecef;
    margin-bottom: 20px;
}

.channel-link {
    display: flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
}

.channel-avatar {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #FF0000, #cc0000);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1.25rem;
}

.channel-name {
    font-weight: 700;
    color: var(--dark-color);
    font-size: 1rem;
}

.btn-youtube {
    background: #FF0000;
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 50px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-youtube:hover {
    background: #cc0000;
    color: #fff;
    transform: scale(1.05);
}

/* 요약 섹션 */
.summary-section {
    background: linear-gradient(135deg, #f0f7ff 0%, #e8f4f8 100%);
    border-radius: 12px;
    padding: 20px;
    border-left: 4px solid var(--primary-color);
}

.summary-section h6 {
    color: var(--primary-color);
    font-weight: 700;
    margin-bottom: 12px;
}

.summary-section p {
    color: #495057;
    line-height: 1.8;
}

/* 공유 버튼 카드 */
.share-card {
    background: #fff;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.share-card h6 {
    font-weight: 700;
    color: var(--dark-color);
    margin-bottom: 16px;
}

.share-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.share-buttons .btn {
    border-radius: 50px;
    padding: 8px 16px;
    font-weight: 600;
    font-size: 0.85rem;
}

/* 연관 차량 섹션 */
.related-section {
    background: #fff;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    margin-bottom: 24px;
}

.related-section h5 {
    font-weight: 700;
    color: var(--dark-color);
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* 차량 카드 */
.car-card {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
    height: 100%;
}

.car-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    border-color: transparent;
}

.car-card .card-img-wrapper {
    position: relative;
    overflow: hidden;
}

.car-card .card-img-wrapper img {
    width: 100%;
    aspect-ratio: 16/10;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.car-card:hover .card-img-wrapper img {
    transform: scale(1.08);
}

.car-card .badge {
    position: absolute;
    top: 10px;
    left: 10px;
    font-size: 0.7rem;
    padding: 4px 10px;
    border-radius: 50px;
}

.car-card .card-body {
    padding: 12px;
}

.car-card .card-title {
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--dark-color);
    margin-bottom: 6px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.car-card .price {
    font-size: 0.8rem;
    font-weight: 700;
}

.car-card .mileage {
    font-size: 0.75rem;
    color: #6c757d;
}

/* 사이드바 */
.sidebar-sticky {
    position: sticky;
    top: 100px;
}

/* 관련 영상 카드 */
.related-videos-card {
    background: #fff;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    margin-bottom: 24px;
}

.related-videos-card h5 {
    font-weight: 700;
    color: var(--dark-color);
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.related-video-item {
    display: flex;
    gap: 12px;
    padding: 12px;
    border-radius: 12px;
    transition: background 0.3s ease;
    text-decoration: none;
}

.related-video-item:hover {
    background: var(--light-bg);
}

.related-video-item .thumbnail {
    position: relative;
    flex-shrink: 0;
    width: 140px;
    border-radius: 10px;
    overflow: hidden;
}

.related-video-item .thumbnail img {
    width: 100%;
    height: 80px;
    object-fit: cover;
}

.related-video-item .duration {
    position: absolute;
    bottom: 6px;
    right: 6px;
    background: rgba(0, 0, 0, 0.85);
    color: #fff;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 600;
}

.related-video-item .info {
    flex: 1;
    min-width: 0;
}

.related-video-item .title {
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--dark-color);
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.4;
    margin-bottom: 6px;
}

.related-video-item .channel {
    font-size: 0.75rem;
    color: #6c757d;
    margin-bottom: 4px;
}

.related-video-item .meta {
    font-size: 0.7rem;
    color: #adb5bd;
}

/* 상담 카드 */
.consult-card {
    background: linear-gradient(135deg, var(--dark-color) 0%, #16213e 100%);
    border-radius: 16px;
    padding: 24px;
    color: #fff;
}

.consult-card h5 {
    font-weight: 700;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.consult-card .btn-consult {
    width: 100%;
    padding: 14px;
    border-radius: 12px;
    font-weight: 700;
    font-size: 0.9rem;
    margin-bottom: 10px;
    transition: all 0.3s ease;
}

.consult-card .btn-phone {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border: none;
    color: #fff;
}

.consult-card .btn-phone:hover {
    transform: scale(1.02);
    box-shadow: 0 4px 15px rgba(0, 102, 255, 0.4);
}

.consult-card .btn-kakao {
    background: #FEE500;
    border: none;
    color: #000;
}

.consult-card .btn-kakao:hover {
    background: #e6cf00;
    transform: scale(1.02);
}

/* 연관 게시물 */
.post-card {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
    height: 100%;
}

.post-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
}

.post-card .post-thumbnail {
    height: 120px;
    overflow: hidden;
}

.post-card .post-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.post-card:hover .post-thumbnail img {
    transform: scale(1.08);
}

.post-card .post-placeholder {
    height: 120px;
    background: var(--light-bg);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #adb5bd;
    font-size: 2rem;
}

.post-card .card-body {
    padding: 12px;
}

.post-card .post-title {
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--dark-color);
    margin-bottom: 6px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.post-card .post-meta {
    font-size: 0.75rem;
    color: #6c757d;
}

/* 반응형 */
@media (max-width: 991.98px) {
    .video-view-section {
        padding: 24px 0 40px;
    }

    .video-title {
        font-size: 1.25rem;
    }

    .sidebar-sticky {
        position: static;
    }

    .related-video-item .thumbnail {
        width: 120px;
    }

    .related-video-item .thumbnail img {
        height: 68px;
    }
}

@media (max-width: 767.98px) {
    .video-info-card {
        padding: 16px;
    }

    .video-title {
        font-size: 1.1rem;
    }

    .video-stats {
        gap: 12px;
        font-size: 0.8rem;
    }

    .channel-info {
        flex-direction: column;
        gap: 16px;
        align-items: flex-start;
    }

    .btn-youtube {
        width: 100%;
        text-align: center;
    }

    .related-video-item {
        flex-direction: column;
    }

    .related-video-item .thumbnail {
        width: 100%;
    }

    .related-video-item .thumbnail img {
        height: auto;
        aspect-ratio: 16/9;
    }
}
</style>

<!-- 구조화된 데이터 (Schema.org JSON-LD) -->
<script type="application/ld+json">
<?php echo json_encode($schemaData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<!-- 브레드크럼 -->
<section class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><?php echo __('홈', 'skin'); ?></a></li>
                <li class="breadcrumb-item"><a href="/youtube"><?php echo __('YouTube 영상', 'skin'); ?></a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars(mb_substr($video->title, 0, 30)) . (mb_strlen($video->title) > 30 ? '...' : ''); ?></li>
            </ol>
        </nav>
    </div>
</section>

<!-- 메인 컨텐츠 -->
<section class="video-view-section">
    <div class="container">
        <div class="row g-4">
            <!-- 왼쪽: 영상 플레이어 & 정보 -->
            <div class="col-lg-8">
                <!-- YouTube 임베드 플레이어 -->
                <div class="video-player-wrapper mb-4">
                    <div class="ratio ratio-16x9">
                        <iframe
                            src="https://www.youtube.com/embed/<?php echo htmlspecialchars($video->youtube_video_id); ?>?rel=0&modestbranding=1"
                            title="<?php echo htmlspecialchars($video->title); ?>"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen>
                        </iframe>
                    </div>
                </div>

                <!-- 영상 정보 -->
                <div class="video-info-card mb-4">
                    <h1 class="video-title"><?php echo htmlspecialchars($video->title); ?></h1>

                    <div class="video-stats mb-3">
                        <span><i class="bi bi-eye"></i> <?php echo formatCount($video->view_count); ?><?php echo __('회', 'skin'); ?></span>
                        <?php if ($video->like_count): ?>
                        <span><i class="bi bi-hand-thumbs-up"></i> <?php echo formatCount($video->like_count); ?></span>
                        <?php endif; ?>
                        <?php if ($video->comment_count): ?>
                        <span><i class="bi bi-chat"></i> <?php echo formatCount($video->comment_count); ?></span>
                        <?php endif; ?>
                        <span><i class="bi bi-calendar3"></i> <?php echo date('Y.m.d', strtotime($video->published_at)); ?></span>
                        <?php if ($video->duration): ?>
                        <span><i class="bi bi-clock"></i> <?php echo formatDuration($video->duration); ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- 채널 정보 -->
                    <div class="channel-info">
                        <a href="https://www.youtube.com/channel/<?php echo htmlspecialchars($video->channel_id); ?>"
                           target="_blank"
                           class="channel-link">
                            <div class="channel-avatar">
                                <i class="bi bi-youtube"></i>
                            </div>
                            <span class="channel-name"><?php echo htmlspecialchars($video->channel_title); ?></span>
                        </a>
                        <a href="https://www.youtube.com/watch?v=<?php echo htmlspecialchars($video->youtube_video_id); ?>"
                           target="_blank"
                           class="btn btn-youtube d-none d-md-inline-flex align-items-center gap-2">
                            <i class="bi bi-youtube"></i> <?php echo __('YouTube에서 보기', 'skin'); ?>
                        </a>
                    </div>

                    <!-- 요약 -->
                    <div class="summary-section" id="summarySection">
                        <h6><i class="bi bi-card-text"></i> <?php echo __('AI 요약', 'skin'); ?></h6>
                        <?php if ($video->summary): ?>
                        <p class="mb-0" style="white-space: pre-line;" id="summaryText"><?php echo nl2br(htmlspecialchars($video->summary)); ?></p>
                        <?php else: ?>
                        <div id="summaryLoading" class="text-center py-3">
                            <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <span class="text-muted"><?php echo __('AI가 영상을 요약하고 있습니다...', 'skin'); ?></span>
                        </div>
                        <p class="mb-0" style="white-space: pre-line; display: none;" id="summaryText"></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 공유 버튼 -->
                <div class="share-card mb-4">
                    <h6><i class="bi bi-share"></i> <?php echo __('공유하기', 'skin'); ?></h6>
                    <div class="share-buttons">
                        <button class="btn btn-outline-secondary" onclick="copyLink()">
                            <i class="bi bi-link-45deg"></i> <?php echo __('링크 복사', 'skin'); ?>
                        </button>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(ExpertNote\Core::getBaseUrl() . '/video/' . $video->idx); ?>"
                           target="_blank"
                           class="btn btn-outline-primary">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(ExpertNote\Core::getBaseUrl() . '/video/' . $video->idx); ?>&text=<?php echo urlencode($video->title); ?>"
                           target="_blank"
                           class="btn btn-outline-dark">
                            <i class="bi bi-twitter-x"></i>
                        </a>
                        <a href="https://api.whatsapp.com/send?text=<?php echo urlencode($video->title . ' ' . ExpertNote\Core::getBaseUrl() . '/video/' . $video->idx); ?>"
                           target="_blank"
                           class="btn btn-outline-success">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                    </div>
                </div>

                <!-- 연관 신차 렌트 -->
                <?php if (!empty($relatedNewCars)): ?>
                <div class="related-section">
                    <h5><i class="bi bi-car-front text-primary"></i> <?php echo __('연관 신차 렌트', 'skin'); ?></h5>
                    <div class="row g-3">
                        <?php foreach ($relatedNewCars as $car): ?>
                        <?php
                            $carImages = \AriRent\Rent::getImages($car->idx);
                            $carImage = !empty($carImages) ? $carImages[0]->image_url : '/assets/images/no-image.png';
                        ?>
                        <div class="col-6 col-md-3">
                            <a href="/item/<?php echo $car->idx; ?>" class="car-card d-block text-decoration-none">
                                <div class="card-img-wrapper">
                                    <img src="<?php echo htmlspecialchars($carImage); ?>"
                                         alt="<?php echo htmlspecialchars($car->title); ?>"
                                         loading="lazy">
                                    <span class="badge bg-primary"><?php echo __('신차', 'skin'); ?></span>
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title"><?php echo htmlspecialchars($car->title); ?></h6>
                                    <?php if ($car->min_price): ?>
                                    <p class="price text-primary mb-0">
                                        <?php echo __('월', 'skin'); ?> <?php echo number_format($car->min_price); ?><?php echo __('원~', 'skin'); ?>
                                    </p>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- 연관 중고차 렌트 -->
                <?php if (!empty($relatedUsedCars)): ?>
                <div class="related-section">
                    <h5><i class="bi bi-car-front text-success"></i> <?php echo __('연관 중고차 렌트', 'skin'); ?></h5>
                    <div class="row g-3">
                        <?php foreach ($relatedUsedCars as $car): ?>
                        <?php
                            $carImages = \AriRent\Rent::getImages($car->idx);
                            $carImage = !empty($carImages) ? $carImages[0]->image_url : '/assets/images/no-image.png';
                        ?>
                        <div class="col-6 col-md-3">
                            <a href="/item/<?php echo $car->idx; ?>" class="car-card d-block text-decoration-none">
                                <div class="card-img-wrapper">
                                    <img src="<?php echo htmlspecialchars($carImage); ?>"
                                         alt="<?php echo htmlspecialchars($car->title); ?>"
                                         loading="lazy">
                                    <span class="badge bg-success"><?php echo __('중고차', 'skin'); ?></span>
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title"><?php echo htmlspecialchars($car->title); ?></h6>
                                    <?php if ($car->min_price): ?>
                                    <p class="price text-success mb-0">
                                        <?php echo __('월', 'skin'); ?> <?php echo number_format($car->min_price); ?><?php echo __('원~', 'skin'); ?>
                                    </p>
                                    <?php endif; ?>
                                    <?php if ($car->mileage_km): ?>
                                    <p class="mileage mb-0"><?php echo number_format($car->mileage_km); ?>km</p>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- 연관 게시물 -->
                <?php if (!empty($relatedPosts)): ?>
                <div class="related-section">
                    <h5><i class="bi bi-file-text"></i> <?php echo __('연관 게시물', 'skin'); ?></h5>
                    <div class="row g-3">
                        <?php foreach ($relatedPosts as $post): ?>
                        <div class="col-6 col-md-4">
                            <a href="/forum/<?php echo $post->idx; ?>/<?php echo \ExpertNote\Utils::getPermaLink($post->title, true)."-".$post->idx; ?>" class="post-card d-block text-decoration-none">
                                <?php if ($post->thumbnail): ?>
                                <div class="post-thumbnail">
                                    <img src="<?php echo $post->thumbnail; ?>" alt="<?php echo htmlspecialchars($post->title); ?>">
                                </div>
                                <?php else: ?>
                                <div class="post-placeholder">
                                    <i class="bi bi-file-text"></i>
                                </div>
                                <?php endif; ?>
                                <div class="card-body">
                                    <h6 class="post-title"><?php echo htmlspecialchars($post->title); ?></h6>
                                    <p class="post-meta mb-0">
                                        <i class="bi bi-eye"></i> <?php echo number_format($post->view_count); ?>
                                        · <?php echo date('Y.m.d', strtotime($post->created_at)); ?>
                                    </p>
                                </div>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- 오른쪽: 사이드바 -->
            <div class="col-lg-4">
                <div class="sidebar-sticky">
                    <!-- 관련 영상 -->
                    <div class="related-videos-card">
                        <h5><i class="bi bi-collection-play text-danger"></i> <?php echo __('관련 영상', 'skin'); ?></h5>

                        <?php if (!empty($relatedVideos)): ?>
                        <div class="related-videos-list">
                            <?php foreach ($relatedVideos as $related): ?>
                            <a href="/video/<?php echo $related->idx; ?>/<?php echo \ExpertNote\Utils::getPermaLink($related->title, true); ?>" class="related-video-item">
                                <div class="thumbnail">
                                    <img src="<?php echo $related->thumbnail_medium ?: $related->thumbnail_default; ?>"
                                         alt="<?php echo htmlspecialchars($related->title); ?>"
                                         loading="lazy">
                                    <?php if ($related->duration): ?>
                                    <span class="duration"><?php echo formatDuration($related->duration); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="info">
                                    <h6 class="title"><?php echo htmlspecialchars($related->title); ?></h6>
                                    <p class="channel"><?php echo htmlspecialchars($related->channel_title); ?></p>
                                    <p class="meta">
                                        <?php echo formatCount($related->view_count); ?><?php echo __('회', 'skin'); ?> · <?php echo timeAgo($related->published_at); ?>
                                    </p>
                                </div>
                            </a>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <p class="text-muted text-center py-3"><?php echo __('관련 영상이 없습니다.', 'skin'); ?></p>
                        <?php endif; ?>

                        <a href="/youtube" class="btn btn-outline-primary w-100 mt-3">
                            <i class="bi bi-grid-3x3-gap"></i> <?php echo __('전체 영상 보기', 'skin'); ?>
                        </a>
                    </div>

                    <!-- 렌트 상담 -->
                    <div class="consult-card">
                        <h5><i class="bi bi-headset"></i> <?php echo __('렌트 상담', 'skin'); ?></h5>
                        <a href="tel:<?php echo \ExpertNote\SiteMeta::get('company_phone') ?: '1666-5623'; ?>" class="btn btn-consult btn-phone">
                            <i class="bi bi-telephone-fill me-2"></i> <?php echo __('즉시 전화 상담', 'skin'); ?>
                        </a>
                        <a href="/kakaolink" target="_blank" class="btn btn-consult btn-kakao">
                            <i class="bi bi-chat-fill me-2"></i> <?php echo __('카카오톡 상담', 'skin'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
const videoId = '<?php echo $video->youtube_video_id; ?>';
const videoIdx = <?php echo $video->idx; ?>;
const videoLang = '<?php echo $video->default_audio_language ? substr($video->default_audio_language, 0, 2) : "en"; ?>';
const hasSummary = <?php echo $video->summary ? 'true' : 'false'; ?>;

// 링크 복사 기능
function copyLink() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        ExpertNote.Util.showMessage(
            '<?php echo __('링크가 클립보드에 복사되었습니다.', 'skin'); ?>',
            '<?php echo __('복사 완료', 'skin'); ?>',
            [{ title: '<?php echo __('확인', 'skin'); ?>', class: 'btn btn-primary', dismiss: true }]
        );
    }).catch(() => {
        // 폴백
        const textarea = document.createElement('textarea');
        textarea.value = url;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        ExpertNote.Util.showMessage(
            '<?php echo __('링크가 클립보드에 복사되었습니다.', 'skin'); ?>',
            '<?php echo __('복사 완료', 'skin'); ?>',
            [{ title: '<?php echo __('확인', 'skin'); ?>', class: 'btn btn-primary', dismiss: true }]
        );
    });
}

// AI 요약 생성 (로그인 불필요한 스킨 전용 API 사용)
async function generateSummary() {
    const loadingEl = document.getElementById('summaryLoading');
    const textEl = document.getElementById('summaryText');

    if (!loadingEl || !textEl) return;

    try {
        // 스킨 전용 요약 API 호출 (로그인 불필요)
        const response = await fetch('/api/arirent/youtube-summary', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ idx: videoIdx })
        });
        const data = await response.json();

        if (data.result === 'SUCCESS' && data.data && data.data.summary) {
            // 요약 표시
            textEl.textContent = data.data.summary;
            textEl.style.display = 'block';
            loadingEl.style.display = 'none';
        } else {
            throw new Error(data.message || 'Summary generation failed');
        }
    } catch (error) {
        console.error('Summary error:', error);
        // 오류 시 요약 섹션 숨기기
        document.getElementById('summarySection').style.display = 'none';
    }
}

// 페이지 로드 시 요약이 없으면 생성
document.addEventListener('DOMContentLoaded', function() {
    if (!hasSummary) {
        generateSummary();
    }
});
</script>
