<?php
/**
 * YouTube 영상 목록 페이지
 * arirent 스킨용 - v2 레이아웃
 */

ExpertNote\Core::setLayout("v2");

// 페이지 메타 설정
$pageTitle = __('YouTube 영상', 'skin');
$pageDescription = __('유용한 영상 콘텐츠를 확인하세요.', 'skin');

ExpertNote\Core::setPageTitle($pageTitle . " - 아리렌트");
ExpertNote\Core::setPageDescription($pageDescription);
ExpertNote\Core::setPageKeywords("아리렌트, 유튜브, 영상, 장기렌트, 자동차");

// Open Graph 메타 태그
\ExpertNote\Core::addMetaTag('og:type', ["property"=>"og:type", "content"=>"website"]);
\ExpertNote\Core::addMetaTag('og:title', ["property"=>"og:title", "content"=>$pageTitle . " - 아리렌트"]);
\ExpertNote\Core::addMetaTag('og:description', ["property"=>"og:description", "content"=>$pageDescription]);
\ExpertNote\Core::addMetaTag('og:url', ["property"=>"og:url", "content"=>ExpertNote\Core::getBaseUrl() . "/youtube"]);
\ExpertNote\Core::addMetaTag('og:site_name', ["property"=>"og:site_name", "content"=>"아리렌트"]);

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

<style>
:root {
    --primary-color: #0066FF;
    --secondary-color: #00D4AA;
    --dark-color: #1a1a2e;
    --light-bg: #f8fafc;
}

/* 페이지 헤더 */
.page-header {
    background: linear-gradient(135deg, var(--dark-color) 0%, #16213e 100%);
    padding: 60px 0;
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    opacity: 0.5;
}

.page-header-content {
    position: relative;
    z-index: 1;
}

.page-header .breadcrumb {
    background: transparent;
    padding: 0;
    margin-bottom: 16px;
}

.page-header .breadcrumb-item a {
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    transition: color 0.3s ease;
}

.page-header .breadcrumb-item a:hover {
    color: #fff;
}

.page-header .breadcrumb-item.active {
    color: rgba(255, 255, 255, 0.9);
}

.page-header .breadcrumb-item + .breadcrumb-item::before {
    color: rgba(255, 255, 255, 0.5);
}

.page-title {
    font-size: 2.5rem;
    font-weight: 800;
    color: #fff;
    margin-bottom: 12px;
}

.page-desc {
    font-size: 1.1rem;
    color: rgba(255, 255, 255, 0.8);
    margin-bottom: 0;
}

/* 검색 섹션 */
.search-section {
    background: var(--light-bg);
    padding: 30px 0;
    border-bottom: 1px solid #e9ecef;
}

.search-form {
    background: #fff;
    border-radius: 50px;
    padding: 8px 8px 8px 24px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    display: flex;
    align-items: center;
    max-width: 600px;
}

.search-form input {
    border: none;
    outline: none;
    flex: 1;
    font-size: 1rem;
    padding: 8px 0;
}

.search-form input::placeholder {
    color: #adb5bd;
}

.search-form .btn-search {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border: none;
    border-radius: 50px;
    padding: 12px 28px;
    color: #fff;
    font-weight: 600;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.search-form .btn-search:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 15px rgba(0, 102, 255, 0.4);
}

.search-result-info {
    color: #6c757d;
    font-size: 0.95rem;
}

.search-result-info strong {
    color: var(--primary-color);
    font-weight: 700;
}

/* 영상 목록 섹션 */
.video-list-section {
    padding: 60px 0;
    background: #fff;
}

/* 비디오 카드 */
.video-card {
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.video-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.video-thumbnail {
    position: relative;
    overflow: hidden;
    aspect-ratio: 16/9;
}

.video-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.video-card:hover .video-thumbnail img {
    transform: scale(1.08);
}

.play-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.video-card:hover .play-overlay {
    opacity: 1;
}

.play-overlay i {
    font-size: 60px;
    color: #fff;
    text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    transition: transform 0.3s ease;
}

.video-card:hover .play-overlay i {
    transform: scale(1.1);
}

.video-duration {
    position: absolute;
    bottom: 10px;
    right: 10px;
    background: rgba(0, 0, 0, 0.85);
    color: #fff;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
}

.video-info {
    padding: 20px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.video-title {
    font-size: 1rem;
    font-weight: 700;
    color: #1a1a2e;
    margin-bottom: 10px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.5;
    min-height: 3em;
}

.video-title a {
    color: inherit;
    text-decoration: none;
    transition: color 0.3s ease;
}

.video-title a:hover {
    color: var(--primary-color);
}

.video-channel {
    font-size: 0.85rem;
    color: #6c757d;
    margin-bottom: 8px;
}

.video-channel a {
    color: inherit;
    text-decoration: none;
    transition: color 0.3s ease;
}

.video-channel a:hover {
    color: var(--primary-color);
}

.video-meta {
    font-size: 0.8rem;
    color: #adb5bd;
    margin-top: auto;
}

.video-meta span + span::before {
    content: '·';
    margin: 0 6px;
}

/* 페이지네이션 */
.pagination-wrapper {
    margin-top: 50px;
}

.pagination {
    gap: 8px;
}

.pagination .page-link {
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    border: 1px solid #e9ecef;
    color: #495057;
    font-weight: 600;
    transition: all 0.3s ease;
}

.pagination .page-link:hover {
    background: var(--light-bg);
    border-color: var(--primary-color);
    color: var(--primary-color);
}

.pagination .page-item.active .page-link {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border-color: transparent;
    color: #fff;
}

.pagination .page-item.disabled .page-link {
    background: transparent;
    color: #adb5bd;
}

/* 빈 상태 */
.empty-state {
    text-align: center;
    padding: 80px 20px;
}

.empty-state i {
    font-size: 80px;
    color: #dee2e6;
    margin-bottom: 24px;
}

.empty-state h4 {
    color: #495057;
    font-weight: 700;
    margin-bottom: 12px;
}

.empty-state p {
    color: #6c757d;
    margin-bottom: 24px;
}

.empty-state .btn {
    padding: 12px 32px;
    border-radius: 50px;
    font-weight: 600;
}

/* 반응형 */
@media (max-width: 991.98px) {
    .page-title {
        font-size: 2rem;
    }

    .video-list-section {
        padding: 40px 0;
    }
}

@media (max-width: 767.98px) {
    .page-header {
        padding: 40px 0;
    }

    .page-title {
        font-size: 1.75rem;
    }

    .page-desc {
        font-size: 1rem;
    }

    .search-section {
        padding: 20px 0;
    }

    .search-form {
        padding: 6px 6px 6px 16px;
    }

    .search-form .btn-search {
        padding: 10px 20px;
        font-size: 0.9rem;
    }

    .video-list-section {
        padding: 30px 0;
    }

    .video-info {
        padding: 16px;
    }

    .video-title {
        font-size: 0.95rem;
    }

    .pagination .page-link {
        width: 38px;
        height: 38px;
        font-size: 0.9rem;
    }
}
</style>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/"><?php echo __('홈', 'skin')?></a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo __('YouTube 영상', 'skin')?></li>
                </ol>
            </nav>
            <h1 class="page-title"><i class="bi bi-youtube text-danger"></i> <?php echo __('YouTube 영상', 'skin')?></h1>
            <p class="page-desc"><?php echo __('유용한 영상 콘텐츠를 확인하세요.', 'skin')?></p>
        </div>
    </div>
</section>

<!-- Search Section -->
<section class="search-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <form action="" method="GET" class="search-form">
                    <input type="text" name="q" placeholder="<?php echo __('영상 검색...', 'skin')?>"
                        value="<?php echo htmlspecialchars($_GET['q'] ?? '')?>">
                    <button type="submit" class="btn btn-search">
                        <i class="bi bi-search me-1"></i> 
                    </button>
                </form>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <span class="search-result-info">
                    <?php echo __('총', 'skin')?> <strong><?php echo number_format($totalCount)?></strong><?php echo __('개의 영상', 'skin')?>
                </span>
            </div>
        </div>
    </div>
</section>

<!-- Video List Section -->
<section class="video-list-section">
    <div class="container">
        <?php if ($videos && count($videos) > 0): ?>
        <div class="row g-4">
            <?php foreach ($videos as $index => $video): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <a href="/video/<?php echo $video->idx?>/<?php echo \ExpertNote\Utils::getPermaLink($video->title, true)?>" class="video-card d-block text-decoration-none">
                    <!-- 썸네일 -->
                    <div class="video-thumbnail">
                        <img src="<?php echo $video->thumbnail_medium ?: $video->thumbnail_default?>"
                            alt="<?php echo htmlspecialchars($video->title)?>"
                            loading="lazy">
                        <div class="play-overlay">
                            <i class="bi bi-play-circle-fill"></i>
                        </div>
                        <?php if ($video->duration): ?>
                        <span class="video-duration"><?php echo formatVideoDuration($video->duration)?></span>
                        <?php endif; ?>
                    </div>

                    <!-- 정보 -->
                    <div class="video-info">
                        <h3 class="video-title"><?php echo htmlspecialchars($video->title)?></h3>
                        <p class="video-channel"><?php echo htmlspecialchars($video->channel_title)?></p>
                        <div class="video-meta">
                            <span><?php echo __('조회수', 'skin')?> <?php echo formatVideoViewCount($video->view_count)?></span>
                            <span><?php echo date('Y.m.d', strtotime($video->published_at))?></span>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- 페이지네이션 -->
        <?php if ($totalPages > 1): ?>
        <div class="pagination-wrapper">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <!-- 이전 페이지 -->
                    <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page - 1?><?php echo !empty($_GET['q']) ? '&q=' . urlencode($_GET['q']) : ''?>">
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
                        <a class="page-link" href="?page=1<?php echo !empty($_GET['q']) ? '&q=' . urlencode($_GET['q']) : ''?>">1</a>
                    </li>
                    <?php if ($startPage > 2): ?>
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''?>">
                        <a class="page-link" href="?page=<?php echo $i?><?php echo !empty($_GET['q']) ? '&q=' . urlencode($_GET['q']) : ''?>"><?php echo $i?></a>
                    </li>
                    <?php endfor; ?>

                    <?php if ($endPage < $totalPages): ?>
                    <?php if ($endPage < $totalPages - 1): ?>
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php endif; ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $totalPages?><?php echo !empty($_GET['q']) ? '&q=' . urlencode($_GET['q']) : ''?>"><?php echo $totalPages?></a>
                    </li>
                    <?php endif; ?>

                    <!-- 다음 페이지 -->
                    <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page + 1?><?php echo !empty($_GET['q']) ? '&q=' . urlencode($_GET['q']) : ''?>">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <!-- 영상이 없는 경우 -->
        <div class="empty-state">
            <i class="bi bi-youtube"></i>
            <h4><?php echo __('등록된 영상이 없습니다.', 'skin')?></h4>
            <?php if (!empty($_GET['q'])): ?>
            <p><?php echo __('다른 검색어로 다시 시도해보세요.', 'skin')?></p>
            <a href="/youtube" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left me-1"></i><?php echo __('전체 목록 보기', 'skin')?>
            </a>
            <?php else: ?>
            <p><?php echo __('곧 새로운 영상이 업로드됩니다.', 'skin')?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
