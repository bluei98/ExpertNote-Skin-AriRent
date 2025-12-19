<?php
/**
 * YouTube 영상 목록 페이지
 * arirent 스킨용
 */

// 페이지 메타 설정
$pageTitle = __('YouTube 영상', 'skin');
$pageDescription = __('유용한 영상 콘텐츠를 확인하세요.', 'skin');

ExpertNote\Core::setPageTitle($pageTitle);
ExpertNote\Core::setPageDescription($pageDescription);

// Open Graph 메타 태그
\ExpertNote\Core::addMetaTag('og:type', ["property"=>"og:type", "content"=>"website"]);
\ExpertNote\Core::addMetaTag('og:title', ["property"=>"og:title", "content"=>$pageTitle]);
\ExpertNote\Core::addMetaTag('og:description', ["property"=>"og:description", "content"=>$pageDescription]);
\ExpertNote\Core::addMetaTag('og:url', ["property"=>"og:url", "content"=>ExpertNote\Core::getBaseUrl() . "/youtube"]);

// 페이지네이션 설정
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 12;
$offset = ($page - 1) * $perPage;

// 검색 조건
$where = ["status = :status"];
$params = ['status' => 'PUBLISHED'];

// 검색어가 있는 경우
$searchQuery = '';
if (!empty($_GET['q'])) {
    $searchQuery = trim($_GET['q']);
    // 제목 또는 채널명 검색 (로케일 테이블 조인된 상태에서)
    $where[] = "(COALESCE(l1.title, l2.title, l3.title) LIKE :search OR channel_title LIKE :search)";
    $params['search'] = '%' . $searchQuery . '%';
}

// 전체 개수 조회
$totalCount = \ExpertNote\Youtube::getVideoCount(['status = :status'], ['status' => 'PUBLISHED']);

// 검색어가 있으면 별도로 카운트 (검색 조건 포함 시 직접 쿼리)
if (!empty($searchQuery)) {
    $countSql = "SELECT COUNT(*) as cnt
    FROM " . DB_PREFIX . "youtube y
    LEFT JOIN " . DB_PREFIX . "youtubeLocale l1
        ON y.idx = l1.youtube_idx
        AND l1.locale = SUBSTRING(y.default_audio_language, 1, 2)
    LEFT JOIN " . DB_PREFIX . "youtubeLocale l2
        ON y.idx = l2.youtube_idx
        AND l2.locale = 'en'
    LEFT JOIN (
        SELECT youtube_idx, title, locale
        FROM " . DB_PREFIX . "youtubeLocale
        WHERE (youtube_idx, idx) IN (
            SELECT youtube_idx, MIN(idx)
            FROM " . DB_PREFIX . "youtubeLocale
            GROUP BY youtube_idx
        )
    ) l3 ON y.idx = l3.youtube_idx
    WHERE y.status = :status
    AND (COALESCE(l1.title, l2.title, l3.title) LIKE :search OR y.channel_title LIKE :search)";

    $countResult = \ExpertNote\DB::getRow($countSql, $params);
    $totalCount = $countResult->cnt ?? 0;
}

$totalPages = ceil($totalCount / $perPage);

// 영상 목록 조회 - ExpertNote\Youtube 클래스 사용
if (!empty($searchQuery)) {
    // 검색어가 있는 경우 직접 쿼리
    $listSql = "SELECT y.*,
        COALESCE(l1.title, l2.title, l3.title, y.channel_title) as title,
        COALESCE(l1.description, l2.description) as description,
        COALESCE(l1.summary, l2.summary) as summary
    FROM " . DB_PREFIX . "youtube y
    LEFT JOIN " . DB_PREFIX . "youtubeLocale l1
        ON y.idx = l1.youtube_idx
        AND l1.locale = SUBSTRING(y.default_audio_language, 1, 2)
    LEFT JOIN " . DB_PREFIX . "youtubeLocale l2
        ON y.idx = l2.youtube_idx
        AND l2.locale = 'en'
    LEFT JOIN (
        SELECT youtube_idx, title, description, summary, locale
        FROM " . DB_PREFIX . "youtubeLocale
        WHERE (youtube_idx, idx) IN (
            SELECT youtube_idx, MIN(idx)
            FROM " . DB_PREFIX . "youtubeLocale
            GROUP BY youtube_idx
        )
    ) l3 ON y.idx = l3.youtube_idx
    WHERE y.status = :status
    AND (COALESCE(l1.title, l2.title, l3.title) LIKE :search OR y.channel_title LIKE :search)
    ORDER BY y.published_at DESC
    LIMIT {$offset}, {$perPage}";

    $videos = \ExpertNote\DB::getRows($listSql, $params);
} else {
    // 검색어가 없는 경우 Youtube 클래스 메서드 사용
    $videos = \ExpertNote\Youtube::getVideos(
        ['status = :status'],
        ['published_at DESC'],
        [$offset, $perPage],
        ['status' => 'PUBLISHED']
    );
}

/**
 * 초를 시:분:초 형식으로 변환
 */
function formatVideoDuration($seconds) {
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
function formatVideoViewCount($num) {
    if ($num >= 100000000) {
        return number_format($num / 100000000, 1) . __('억', 'skin');
    } else if ($num >= 10000) {
        return number_format($num / 10000, 1) . __('만', 'skin');
    } else if ($num >= 1000) {
        return number_format($num / 1000, 1) . __('천', 'skin');
    }
    return number_format($num);
}
?>

<!-- 페이지 헤더 -->
<section class="bg-primary text-white py-5">
    <div class="container">
        <h1 class="fw-bold mb-3"><?php echo __('YouTube 영상', 'skin') ?></h1>
        <p class="lead mb-0"><?php echo __('유용한 영상 콘텐츠를 확인하세요.', 'skin') ?></p>
    </div>
</section>

<!-- 검색 및 필터 -->
<section class="py-4 bg-light">
    <div class="container">
        <form action="" method="GET" class="row g-3 align-items-center">
            <div class="col-md-8 col-lg-6">
                <div class="input-group">
                    <input type="text" class="form-control" name="q" placeholder="<?php echo __('영상 검색...', 'skin') ?>"
                        value="<?php echo htmlspecialchars($_GET['q'] ?? '') ?>">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-search"></i> <?php echo __('검색', 'skin') ?>
                    </button>
                </div>
            </div>
            <div class="col-md-4 col-lg-6 text-md-end">
                <span class="text-muted">
                    <?php echo __('총', 'skin') ?> <strong><?php echo number_format($totalCount) ?></strong><?php echo __('개의 영상', 'skin') ?>
                </span>
            </div>
        </form>
    </div>
</section>

<!-- 영상 목록 -->
<section class="py-5">
    <div class="container">
        <?php if ($videos && count($videos) > 0): ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
            <?php foreach ($videos as $video): ?>
            <div class="col">
                <div class="card h-100 shadow-sm border-0 video-card">
                    <!-- 썸네일 -->
                    <a href="/video/<?php echo $video->idx ?>/<?php echo \ExpertNote\Utils::getPermaLink($video->title, true) ?>" class="position-relative">
                        <img src="<?php echo $video->thumbnail_medium ?: $video->thumbnail_default ?>"
                            class="card-img-top"
                            alt="<?php echo htmlspecialchars($video->title) ?>"
                            loading="lazy"
                            style="aspect-ratio: 16/9; object-fit: cover;">
                        <!-- 재생 시간 -->
                        <?php if ($video->duration): ?>
                        <span class="position-absolute bottom-0 end-0 bg-dark text-white px-2 py-1 m-2 rounded" style="font-size: 12px;">
                            <?php echo formatVideoDuration($video->duration) ?>
                        </span>
                        <?php endif; ?>
                        <!-- 재생 아이콘 오버레이 -->
                        <div class="play-overlay position-absolute top-50 start-50 translate-middle">
                            <i class="bi bi-play-circle-fill text-white" style="font-size: 48px; opacity: 0.9;"></i>
                        </div>
                    </a>

                    <!-- 카드 바디 -->
                    <div class="card-body">
                        <h5 class="card-title fw-bold text-truncate-2" title="<?php echo htmlspecialchars($video->title) ?>">
                            <a href="/video/<?php echo $video->idx ?>/<?php echo \ExpertNote\Utils::getPermaLink($video->title, true) ?>" class="text-decoration-none text-dark">
                                <?php echo htmlspecialchars($video->title) ?>
                            </a>
                        </h5>
                        <p class="card-text text-muted small mb-2">
                            <a href="https://www.youtube.com/channel/<?php echo $video->channel_id ?>" target="_blank" class="text-decoration-none text-muted">
                                <?php echo htmlspecialchars($video->channel_title) ?>
                            </a>
                        </p>
                        <p class="card-text text-muted small">
                            <span><?php echo formatVideoViewCount($video->view_count) ?><?php echo __('회', 'skin') ?></span>
                            <span class="mx-1">·</span>
                            <span><?php echo date('Y.m.d', strtotime($video->published_at)) ?></span>
                        </p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- 페이지네이션 -->
        <?php if ($totalPages > 1): ?>
        <nav class="mt-5" aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <!-- 이전 페이지 -->
                <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $page - 1 ?><?php echo !empty($_GET['q']) ? '&q=' . urlencode($_GET['q']) : '' ?>">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
                <?php endif; ?>

                <!-- 페이지 번호 -->
                <?php
                $startPage = max(1, $page - 2);
                $endPage = min($totalPages, $page + 2);

                if ($startPage > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=1<?php echo !empty($_GET['q']) ? '&q=' . urlencode($_GET['q']) : '' ?>">1</a>
                </li>
                <?php if ($startPage > 2): ?>
                <li class="page-item disabled"><span class="page-link">...</span></li>
                <?php endif; ?>
                <?php endif; ?>

                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                <li class="page-item <?php echo $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?php echo $i ?><?php echo !empty($_GET['q']) ? '&q=' . urlencode($_GET['q']) : '' ?>"><?php echo $i ?></a>
                </li>
                <?php endfor; ?>

                <?php if ($endPage < $totalPages): ?>
                <?php if ($endPage < $totalPages - 1): ?>
                <li class="page-item disabled"><span class="page-link">...</span></li>
                <?php endif; ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $totalPages ?><?php echo !empty($_GET['q']) ? '&q=' . urlencode($_GET['q']) : '' ?>"><?php echo $totalPages ?></a>
                </li>
                <?php endif; ?>

                <!-- 다음 페이지 -->
                <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $page + 1 ?><?php echo !empty($_GET['q']) ? '&q=' . urlencode($_GET['q']) : '' ?>">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>

        <?php else: ?>
        <!-- 영상이 없는 경우 -->
        <div class="text-center py-5">
            <i class="bi bi-youtube text-muted" style="font-size: 64px;"></i>
            <h4 class="mt-3 text-muted"><?php echo __('등록된 영상이 없습니다.', 'skin') ?></h4>
            <?php if (!empty($_GET['q'])): ?>
            <p class="text-muted"><?php echo __('다른 검색어로 다시 시도해보세요.', 'skin') ?></p>
            <a href="/youtube" class="btn btn-outline-primary mt-2">
                <i class="bi bi-arrow-left me-1"></i><?php echo __('전체 목록 보기', 'skin') ?>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<style>
/* 비디오 카드 스타일 */
.video-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.video-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
}

.video-card .play-overlay {
    opacity: 0;
    transition: opacity 0.3s ease;
}

.video-card:hover .play-overlay {
    opacity: 1;
}

.video-card .card-img-top {
    transition: transform 0.3s ease;
}

.video-card:hover .card-img-top {
    transform: scale(1.05);
}

.video-card a {
    overflow: hidden;
    display: block;
}

/* 텍스트 2줄 말줄임 */
.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.4;
    max-height: 2.8em;
}

/* 페이지네이션 스타일 */
.pagination .page-link {
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 3px;
    border: none;
    color: #333;
}

.pagination .page-item.active .page-link {
    background-color: var(--bs-primary);
    color: #fff;
}

.pagination .page-item:not(.active) .page-link:hover {
    background-color: #f0f0f0;
}
</style>
