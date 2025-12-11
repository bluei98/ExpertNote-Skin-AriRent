<?php
/**
 * YouTube 영상 상세 페이지
 * arirent 스킨용
 */

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

ExpertNote\Core::setPageTitle($pageTitle);
ExpertNote\Core::setPageDescription($pageDescription);

// Open Graph 메타 태그
\ExpertNote\Core::addMetaTag('og:type', ["property"=>"og:type", "content"=>"video.other"]);
\ExpertNote\Core::addMetaTag('og:title', ["property"=>"og:title", "content"=>$pageTitle]);
\ExpertNote\Core::addMetaTag('og:description', ["property"=>"og:description", "content"=>$pageDescription]);
\ExpertNote\Core::addMetaTag('og:url', ["property"=>"og:url", "content"=>ExpertNote\Core::getBaseUrl() . "/video/" . $video->idx]);
\ExpertNote\Core::addMetaTag('og:image', ["property"=>"og:image", "content"=>$video->thumbnail_high ?: $video->thumbnail_medium]);
\ExpertNote\Core::addMetaTag('og:video', ["property"=>"og:video", "content"=>"https://www.youtube.com/embed/" . $video->youtube_video_id]);

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

<!-- 구조화된 데이터 (Schema.org JSON-LD) -->
<script type="application/ld+json">
<?php echo json_encode($schemaData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<!-- 페이지 헤더 -->
<section class="bg-dark text-white py-3">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/" class="text-white-50"><?php echo __('홈', 'skin'); ?></a></li>
                <li class="breadcrumb-item"><a href="/youtube" class="text-white-50"><?php echo __('YouTube 영상', 'skin'); ?></a></li>
                <li class="breadcrumb-item active text-white" aria-current="page"><?php echo htmlspecialchars(mb_substr($video->title, 0, 30)) . (mb_strlen($video->title) > 30 ? '...' : ''); ?></li>
            </ol>
        </nav>
    </div>
</section>

<!-- 메인 콘텐츠 -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="row">
            <!-- 영상 플레이어 -->
            <div class="col-lg-8">
                <!-- YouTube 임베드 플레이어 -->
                <div class="video-player-wrapper mb-4">
                    <div class="ratio ratio-16x9 rounded overflow-hidden shadow">
                        <iframe
                            src="https://www.youtube.com/embed/<?php echo htmlspecialchars($video->youtube_video_id); ?>?rel=0&modestbranding=1"
                            title="<?php echo htmlspecialchars($video->title); ?>"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen>
                        </iframe>
                    </div>
                </div>

                <!-- 영상 정보 -->
                <div class="video-info bg-white rounded shadow-sm p-4 mb-4">
                    <h1 class="h4 fw-bold mb-3"><?php echo htmlspecialchars($video->title); ?></h1>

                    <div class="d-flex flex-wrap align-items-center gap-3 mb-3 text-muted">
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
                    <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                        <a href="https://www.youtube.com/channel/<?php echo htmlspecialchars($video->channel_id); ?>"
                           target="_blank"
                           class="text-decoration-none">
                            <div class="d-flex align-items-center gap-2">
                                <div class="channel-avatar bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-youtube"></i>
                                </div>
                                <div>
                                    <span class="fw-semibold text-dark"><?php echo htmlspecialchars($video->channel_title); ?></span>
                                </div>
                            </div>
                        </a>
                        <a href="https://www.youtube.com/watch?v=<?php echo htmlspecialchars($video->youtube_video_id); ?>"
                           target="_blank"
                           class="btn btn-danger btn-sm ms-auto d-none d-md-inline-block">
                            <i class="bi bi-youtube"></i> <?php echo __('YouTube에서 보기', 'skin'); ?>
                        </a>
                    </div>

                    <!-- 요약 -->
                    <div class="video-summary mt-4" id="summarySection">
                        <h6 class="fw-bold mb-2"><i class="bi bi-card-text"></i> <?php echo __('요약', 'skin'); ?></h6>
                        
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
                <div class="share-buttons bg-white rounded shadow-sm p-3 mb-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-share"></i> <?php echo __('공유하기', 'skin'); ?></h6>
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-outline-secondary btn-sm" onclick="copyLink()">
                            <i class="bi bi-link-45deg"></i> <?php echo __('링크 복사', 'skin'); ?>
                        </button>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(ExpertNote\Core::getBaseUrl() . '/video/' . $video->idx); ?>"
                           target="_blank"
                           class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-facebook"></i> Facebook
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(ExpertNote\Core::getBaseUrl() . '/video/' . $video->idx); ?>&text=<?php echo urlencode($video->title); ?>"
                           target="_blank"
                           class="btn btn-outline-info btn-sm">
                            <i class="bi bi-twitter-x"></i> X
                        </a>
                        <a href="https://api.whatsapp.com/send?text=<?php echo urlencode($video->title . ' ' . ExpertNote\Core::getBaseUrl() . '/video/' . $video->idx); ?>"
                           target="_blank"
                           class="btn btn-outline-success btn-sm">
                            <i class="bi bi-whatsapp"></i> WhatsApp
                        </a>
                    </div>
                </div>

                <!-- 연관 신차 렌트 차량 -->
                <?php if (!empty($relatedNewCars)): ?>
                <div class="related-cars-section bg-white rounded shadow-sm p-4 mb-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-car-front text-primary"></i> <?php echo __('연관 신차 렌트', 'skin'); ?></h5>
                    <div class="row row-cols-2 row-cols-md-4 g-3">
                        <?php foreach ($relatedNewCars as $car): ?>
                        <?php
                            $carImages = \AriRent\Rent::getImages($car->idx);
                            $carImage = !empty($carImages) ? $carImages[0]->image_url : '/assets/images/no-image.png';
                        ?>
                        <div class="col">
                            <a href="/item/<?php echo $car->idx; ?>" class="card h-100 text-decoration-none rent-car-card">
                                <div class="card-img-wrapper">
                                    <img src="<?php echo htmlspecialchars($carImage); ?>"
                                         class="card-img-top"
                                         alt="<?php echo htmlspecialchars($car->title); ?>"
                                         loading="lazy"
                                         style="aspect-ratio: 16/10; object-fit: cover;">
                                    <span class="badge bg-primary position-absolute top-0 start-0 m-2"><?php echo __('신차', 'skin'); ?></span>
                                </div>
                                <div class="card-body p-2">
                                    <h6 class="card-title text-dark mb-1 text-truncate" style="font-size: 13px;"><?php echo htmlspecialchars($car->title); ?></h6>
                                    <?php if ($car->min_price): ?>
                                    <p class="card-text text-primary fw-bold mb-0" style="font-size: 12px;">
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

                <!-- 연관 중고차 렌트 차량 -->
                <?php if (!empty($relatedUsedCars)): ?>
                <div class="related-cars-section bg-white rounded shadow-sm p-4 mb-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-car-front text-success"></i> <?php echo __('연관 중고차 렌트', 'skin'); ?></h5>
                    <div class="row row-cols-2 row-cols-md-4 g-3">
                        <?php foreach ($relatedUsedCars as $car): ?>
                        <?php
                            $carImages = \AriRent\Rent::getImages($car->idx);
                            $carImage = !empty($carImages) ? $carImages[0]->image_url : '/assets/images/no-image.png';
                        ?>
                        <div class="col">
                            <a href="/item/<?php echo $car->idx; ?>" class="card h-100 text-decoration-none rent-car-card">
                                <div class="card-img-wrapper">
                                    <img src="<?php echo htmlspecialchars($carImage); ?>"
                                         class="card-img-top"
                                         alt="<?php echo htmlspecialchars($car->title); ?>"
                                         loading="lazy"
                                         style="aspect-ratio: 16/10; object-fit: cover;">
                                    <span class="badge bg-success position-absolute top-0 start-0 m-2"><?php echo __('중고차', 'skin'); ?></span>
                                </div>
                                <div class="card-body p-2">
                                    <h6 class="card-title text-dark mb-1 text-truncate" style="font-size: 13px;"><?php echo htmlspecialchars($car->title); ?></h6>
                                    <?php if ($car->min_price): ?>
                                    <p class="card-text text-success fw-bold mb-0" style="font-size: 12px;">
                                        <?php echo __('월', 'skin'); ?> <?php echo number_format($car->min_price); ?><?php echo __('원~', 'skin'); ?>
                                    </p>
                                    <?php endif; ?>
                                    <?php if ($car->mileage_km): ?>
                                    <p class="card-text text-muted mb-0" style="font-size: 11px;">
                                        <?php echo number_format($car->mileage_km); ?>km
                                    </p>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- 사이드바: 관련 영상 -->
            <div class="col-lg-4">
                <div class="related-videos-sidebar">
                    <h5 class="fw-bold mb-3"><i class="bi bi-collection-play"></i> <?php echo __('관련 영상', 'skin'); ?></h5>

                    <?php if (!empty($relatedVideos)): ?>
                    <div class="related-videos-list">
                        <?php foreach ($relatedVideos as $related): ?>
                        <a href="/video/<?php echo $related->idx; ?>/<?php echo \ExpertNote\Utils::getPermaLink($related->title, true); ?>" class="related-video-item d-flex gap-3 mb-3 text-decoration-none">
                            <div class="related-thumbnail position-relative flex-shrink-0">
                                <img src="<?php echo $related->thumbnail_medium ?: $related->thumbnail_default; ?>"
                                     alt="<?php echo htmlspecialchars($related->title); ?>"
                                     class="rounded"
                                     loading="lazy"
                                     style="width: 168px; height: 94px; object-fit: cover;">
                                <?php if ($related->duration): ?>
                                <span class="duration-badge position-absolute bottom-0 end-0 bg-dark text-white px-1 m-1 rounded" style="font-size: 11px;">
                                    <?php echo formatDuration($related->duration); ?>
                                </span>
                                <?php endif; ?>
                            </div>
                            <div class="related-info flex-grow-1">
                                <h6 class="related-title text-dark mb-1"><?php echo htmlspecialchars($related->title); ?></h6>
                                <p class="text-muted small mb-1"><?php echo htmlspecialchars($related->channel_title); ?></p>
                                <p class="text-muted small mb-0">
                                    <?php echo formatCount($related->view_count); ?><?php echo __('회', 'skin'); ?> · <?php echo timeAgo($related->published_at); ?>
                                </p>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <p class="text-muted"><?php echo __('관련 영상이 없습니다.', 'skin'); ?></p>
                    <?php endif; ?>

                    <!-- 전체 영상 보기 버튼 -->
                    <a href="/youtube" class="btn btn-outline-primary w-100 mt-3">
                        <i class="bi bi-grid-3x3-gap"></i> <?php echo __('전체 영상 보기', 'skin'); ?>
                    </a>

                    <!-- 렌트 상담 버튼 -->
                    <div class="rent-consultation-buttons mt-4 pt-4 border-top">
                        <h6 class="fw-bold mb-3"><i class="bi bi-headset"></i> <?php echo __('렌트 상담', 'skin'); ?></h6>
                        <a href="tel:<?php echo \ExpertNote\SiteMeta::get('company_phone') ?: '1588-0000'; ?>" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-telephone-fill"></i> <?php echo __('영상의 자동차 렌트! 즉시 전화 상담', 'skin'); ?>
                        </a>
                        <a href="<?php echo \ExpertNote\SiteMeta::get('kakao_channel_url') ?: 'https://pf.kakao.com/_example'; ?>" target="_blank" class="btn w-100" style="background-color: #FEE500; color: #000;">
                            <i class="bi bi-chat-fill"></i> <?php echo __('영상의 자동차 렌트! 즉시 카카오톡 상담', 'skin'); ?>
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

<style>
/* 비디오 플레이어 래퍼 */
.video-player-wrapper {
    background-color: #000;
    border-radius: 12px;
}

/* 관련 영상 사이드바 */
.related-videos-sidebar {
    position: sticky;
    top: 20px;
}

.related-video-item {
    transition: background-color 0.2s ease;
    padding: 8px;
    border-radius: 8px;
}

.related-video-item:hover {
    background-color: #f8f9fa;
}

.related-title {
    font-size: 14px;
    font-weight: 600;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.4;
}

/* 모바일 반응형 */
@media (max-width: 991.98px) {
    .related-videos-sidebar {
        position: static;
        margin-top: 2rem;
    }

    .related-thumbnail img {
        width: 120px !important;
        height: 68px !important;
    }
}

@media (max-width: 575.98px) {
    .related-video-item {
        flex-direction: column;
    }

    .related-thumbnail img {
        width: 100% !important;
        height: auto !important;
        aspect-ratio: 16/9;
    }
}

/* 설명 영역 */
.description-content {
    max-height: 200px;
    overflow-y: auto;
}

/* 요약 영역 */
.summary-content {
    border-left: 4px solid var(--bs-primary);
}

/* 연관 렌트 차량 카드 */
.rent-car-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border: 1px solid #e9ecef;
    overflow: hidden;
}

.rent-car-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.rent-car-card .card-img-wrapper {
    position: relative;
    overflow: hidden;
}

.rent-car-card .card-img-top {
    transition: transform 0.3s ease;
}

.rent-car-card:hover .card-img-top {
    transform: scale(1.05);
}

.related-cars-section .card-title {
    line-height: 1.3;
}
</style>
