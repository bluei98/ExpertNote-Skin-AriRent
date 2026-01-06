<?php
// AriRent 클래스 로드
require_once __DIR__ . '/vendor/autoload.php';

// 페이지 메타 설정
$pageTitle = '아리렌트';
$pageSuffix = '무심사 저신용 신차 • 중고차 장기렌트';
$pageDescription = '아리렌트 무심사 장기렌트 - 저신용 6~10등급, 개인회생, 신용불량자도 신차•중고차 전액할부 OK! 초기비용 0원, 당일출고, 무보증 렌트카 전문업체. 현대•기아•제네시스•수입차 최저가 보장.';
$pageKeywords = implode(",", [
    '아리렌트',
    '저신용 신차 장기렌트',
    '무심사 신차 할부',
    '저신용 중고차 장기렌트',
    '무심사 중고차 할부',
    '저신용 렌트카',
    '신용불량자 무보증 장기렌트카'
]);

ExpertNote\Core::setPageTitle($pageTitle);
ExpertNote\Core::setPageSuffix($pageSuffix);
ExpertNote\Core::setPageDescription($pageDescription);
ExpertNote\Core::setPageKeywords($pageKeywords);

// Open Graph 메타 태그
\ExpertNote\Core::addMetaTag('og:type', ["property"=>"og:type", "content"=>"website"]);
\ExpertNote\Core::addMetaTag('og:title', ["property"=>"og:title", "content"=>$pageTitle . " - " . $pageSuffix]);
\ExpertNote\Core::addMetaTag('og:description', ["property"=>"og:description", "content"=>$pageDescription]);
\ExpertNote\Core::addMetaTag('og:url', ["property"=>"og:url", "content"=>ExpertNote\Core::getBaseUrl()]);
\ExpertNote\Core::addMetaTag('og:site_name', ["property"=>"og:site_name", "content"=>"아리렌트"]);

// 대표 이미지 (있는 경우)
// $ogImage = ExpertNote\Core::getBaseUrl() . "/skins/arirent/assets/images/og-image.jpg"; // 실제 이미지 경로로 변경 필요
// \ExpertNote\Core::addMetaTag('og:image', ["property"=>"og:image", "content"=>$ogImage]);
// \ExpertNote\Core::addMetaTag('og:image:width', ["property"=>"og:image:width", "content"=>"1200"]);
// \ExpertNote\Core::addMetaTag('og:image:height', ["property"=>"og:image:height", "content"=>"630"]);

// 트위터 카드 메타 태그
\ExpertNote\Core::addMetaTag('twitter:card', ["name"=>"twitter:card", "content"=>"summary_large_image"]);
\ExpertNote\Core::addMetaTag('twitter:title', ["name"=>"twitter:title", "content"=>$pageTitle . " - " . $pageSuffix]);
\ExpertNote\Core::addMetaTag('twitter:description', ["name"=>"twitter:description", "content"=>$pageDescription]);
\ExpertNote\Core::addMetaTag('twitter:url', ["name"=>"twitter:url", "content"=>ExpertNote\Core::getBaseUrl()]);
// \ExpertNote\Core::addMetaTag('twitter:image', ["name"=>"twitter:image", "content"=>$ogImage]);
?>
    <!-- Hero Carousel -->
    <!-- <div id="heroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active carousel-item-color-1">
                <div class="d-flex justify-content-center align-items-center h-100">
                    <div class="text-center text-white p-4">
                        <h2 class="display-3 fw-bold mb-3">제네시스 GV80</h2>
                        <p class="fs-4 mb-4">럭셔리 SUV의 정점</p>
                        <div class="fs-1 fw-bold mb-4">월 425,000원~</div>
                        <button class="btn btn-lg px-5 py-3 fw-bold" style="background: var(--accent-color); color: #fff;">상담 신청하기</button>
                    </div>
                </div>
            </div>
            <div class="carousel-item carousel-item-color-2">
                <div class="d-flex justify-content-center align-items-center h-100">
                    <div class="text-center text-white p-4">
                        <h2 class="display-3 fw-bold mb-3">현대 팰리세이드</h2>
                        <p class="fs-4 mb-4">가족을 위한 최고의 선택</p>
                        <div class="fs-1 fw-bold mb-4">월 380,000원~</div>
                        <button class="btn btn-lg px-5 py-3 fw-bold" style="background: var(--accent-color); color: #fff;">상담 신청하기</button>
                    </div>
                </div>
            </div>
            <div class="carousel-item carousel-item-color-3">
                <div class="d-flex justify-content-center align-items-center h-100">
                    <div class="text-center text-white p-4">
                        <h2 class="display-3 fw-bold mb-3">기아 카니발</h2>
                        <p class="fs-4 mb-4">넓고 편안한 프리미엄 미니밴</p>
                        <div class="fs-1 fw-bold mb-4">월 350,000원~</div>
                        <button class="btn btn-lg px-5 py-3 fw-bold" style="background: var(--accent-color); color: #fff;">상담 신청하기</button>
                    </div>
                </div>
            </div>
            <div class="carousel-item carousel-item-color-4">
                <div class="d-flex justify-content-center align-items-center h-100">
                    <div class="text-center text-white p-4">
                        <h2 class="display-3 fw-bold mb-3">현대 아반떼</h2>
                        <p class="fs-4 mb-4">경제적이고 실용적인 준중형 세단</p>
                        <div class="fs-1 fw-bold mb-4">월 280,000원~</div>
                        <button class="btn btn-lg px-5 py-3 fw-bold" style="background: var(--accent-color); color: #fff;">상담 신청하기</button>
                    </div>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div> -->

    <!-- Brand Filter -->
    <!-- <section class="container my-5" style="margin-top: -50px !important; position: relative; z-index: 100;">
        <div class="bg-white shadow-lg p-4">
            <div class="d-flex flex-wrap justify-content-center gap-2">
                <button class="btn btn-outline-secondary px-4 filter-btn active" data-brand="all">전체</button>
                <button class="btn btn-outline-secondary px-4 filter-btn" data-brand="hyundai">현대</button>
                <button class="btn btn-outline-secondary px-4 filter-btn" data-brand="kia">기아</button>
                <button class="btn btn-outline-secondary px-4 filter-btn" data-brand="genesis">제네시스</button>
                <button class="btn btn-outline-secondary px-4 filter-btn" data-brand="renault">르노</button>
                <button class="btn btn-outline-secondary px-4 filter-btn" data-brand="chevrolet">쉐보레</button>
                <button class="btn btn-outline-secondary px-4 filter-btn" data-brand="import">수입차</button>
            </div>
        </div>
    </section> -->

    <!-- Quick Consultation (Desktop) -->
    <aside class="card shadow-lg quick-consult d-none d-xl-block">
        <div class="card-body p-4">
            <h3 class="text-primary text-center mb-4">빠른 상담 신청</h3>
            <form id="consultForm">
                <div class="mb-3">
                    <input type="text" name="name" class="form-control" placeholder="이름" required>
                </div>
                <div class="mb-3">
                    <input type="tel" name="phone" class="form-control" placeholder="연락처" required>
                </div>
                <div class="mb-3">
                    <select name="region" class="form-select" required>
                        <option value="">지역 선택</option>
                        <option value="서울">서울</option>
                        <option value="인천">인천</option>
                        <option value="경기">경기</option>
                        <option value="부산">부산</option>
                        <option value="대구">대구</option>
                        <option value="대전">대전</option>
                        <option value="광주">광주</option>
                        <option value="울산">울산</option>
                        <option value="충청남도">충청남도</option>
                        <option value="충청북도">충청북도</option>
                        <option value="경상남도">경상남도</option>
                        <option value="경상북도">경상북도</option>
                        <option value="전라남도">전라남도</option>
                        <option value="전라북도">전라북도</option>
                        <option value="제주도">제주도</option>
                        <option value="기타">기타</option>
                    </select>
                </div>
                <div class="mb-3">
                    <select name="car_type" class="form-select" required>
                        <option value="">차종 선택</option>
                        <option value="경차/소형">경차/소형</option>
                        <option value="준중형/중형">준중형/중형</option>
                        <option value="대형">대형</option>
                        <option value="SUV">SUV</option>
                        <option value="수입차">수입차</option>
                    </select>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="privacy" required>
                    <label class="form-check-label small" for="privacy">
                        개인정보 수집 및 이용 동의
                    </label>
                </div>
                <button type="submit" class="btn w-100 fw-bold" style="background: var(--accent-color); color: #fff;">상담 신청하기</button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="container my-5 py-5">
        <!-- 신차 장기렌트 인기 차량 -->
        <section id="new-rental" class="mb-5">
            <h2 class="text-center fw-bold mb-3"><?php echo __('신차 장기렌트 인기 차량', 'skin'); ?></h2>
            <p class="text-center text-muted mb-5">
                <?php echo __('아리렌트에서 가장 인기 있는 신차를 만나보세요', 'skin'); ?>
            </p>

            <div class="row row-cols-2 row-cols-md-4 row-cols-lg-4 row-cols-xl-4 g-4">
<?php
$newCars = AriRent\Rent::getRents(["r.car_type" =>"NEW", "r.status"=>"active"], ["r.idx" => "DESC"], ["offset"=>0, "count"=>8]);
foreach($newCars as $item):
    include SKINPATH."/modules/car-item.php";
endforeach;
?>
            </div>

            <!-- 더보기 버튼 -->
            <div class="text-center mt-4">
                <a href="/car-list?car_type=NEW" class="btn btn-outline-primary btn-lg px-5">
                    <i class="bi bi-arrow-right me-2"></i><?php echo __('신차 더보기', 'skin'); ?>
                </a>
            </div>
        </section>

        <!-- 중고차 장기렌트 인기 차량 -->
        <section id="used-rental">
            <h2 class="text-center fw-bold mb-3"><?php echo __('중고차 장기렌트 인기 차량', 'skin'); ?></h2>
            <p class="text-center text-muted mb-5">
                <?php echo __('합리적인 가격의 중고차 장기렌트를 확인하세요', 'skin'); ?>
            </p>

            <div class="row row-cols-2 row-cols-md-4 row-cols-lg-4 row-cols-xl-4 g-4">
<?php
$usedCars = AriRent\Rent::getRents(["r.car_type" =>"USED", "r.status"=>"active"], ["r.idx" => "DESC"], ["offset"=>0, "count"=>8]);
foreach($newCars as $item):
    include SKINPATH."/modules/car-item.php";
endforeach;
?>
            </div>

            <!-- 더보기 버튼 -->
            <div class="text-center mt-4">
                <a href="/car-list?car_type=USED" class="btn btn-outline-primary btn-lg px-5">
                    <i class="bi bi-arrow-right me-2"></i><?php echo __('중고차 더보기', 'skin'); ?>
                </a>
            </div>
        </section>
    </main>

    <!-- Reviews Section -->
    <section class="bg-light py-5" id="reviews">
        <div class="container">
            <h2 class="text-center fw-bold mb-3"><?php echo __('믿을 수 있는 아리렌트 출고 후기', 'skin') ?></h2>
            <p class="text-center text-muted mb-5">
                <?php echo __('실제 고객님들의 생생한 후기를 확인하세요', 'skin') ?>
            </p>

<?php
// 포럼 리뷰 게시판에서 최근 후기 가져오기
$reviewThreads = ExpertNote\Forum\Thread::getThreads(
    ["f.forum_code = :forum_code", "f.status = 'PUBLISHED'", "f.parent_idx = 0"],
    ["f.publish_time DESC"],
    [0, 8],
    ['forum_code' => 'review']
);

// 본문에서 첫 번째 이미지 추출 함수
function getFirstImageFromContent($contents) {
    if (preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $contents, $match)) {
        return $match[1];
    }
    return null;
}

// 작성자 이름 마스킹 함수
function maskAuthorName($name) {
    if (mb_strlen($name) <= 1) return $name . '*';
    return mb_substr($name, 0, 1) . str_repeat('*', mb_strlen($name) - 1);
}

if (!empty($reviewThreads)):
?>
            <!-- 리뷰 그리드 -->
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
<?php foreach($reviewThreads as $review):
    $reviewImage = getFirstImageFromContent($review->contents);
    $authorName = !empty($review->nickname) ? $review->nickname : $review->username;
    $maskedName = maskAuthorName($authorName);
    $reviewUrl = "/forum/review/" . $review->idx;
?>
                <div class="col">
                    <a href="<?php echo $reviewUrl ?>" class="text-decoration-none">
                        <div class="card shadow-sm border-0 h-100 review-card-item">
                            <div class="review-thumb">
                                <?php if ($reviewImage): ?>
                                <img src="<?php echo htmlspecialchars($reviewImage) ?>" alt="<?php echo htmlspecialchars($review->title) ?>" loading="lazy">
                                <?php else: ?>
                                <div class="review-thumb-placeholder">
                                    <i class="bi bi-car-front-fill"></i>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title fw-bold review-title"><?php echo htmlspecialchars($review->title) ?></h5>
                                <div class="d-flex align-items-center mt-3">
                                    <div class="review-author-badge me-2">
                                        <strong><?php echo htmlspecialchars($maskedName) ?></strong>
                                    </div>
                                    <div class="review-stars">
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                    </div>
                                </div>
                                <p class="card-text text-muted small mt-2">
                                    <?php echo date('Y.m.d', strtotime($review->publish_time)) ?>
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
<?php endforeach; ?>
            </div>

            <!-- 더보기 버튼 -->
            <div class="text-center mt-5">
                <a href="/forum/review" class="btn btn-outline-primary btn-lg px-5">
                    <i class="bi bi-list-ul me-2"></i><?php echo __('출고 후기 더보기', 'skin') ?>
                </a>
            </div>
<?php else: ?>
            <!-- 후기가 없는 경우 기본 메시지 -->
            <div class="text-center py-5">
                <i class="bi bi-chat-square-text text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-3"><?php echo __('아직 등록된 후기가 없습니다.', 'skin') ?></p>
            </div>
<?php endif; ?>
        </div>
    </section>

    <!-- YouTube Videos Section -->
    <section class="container my-5 py-5" id="youtube-videos">
        <h2 class="text-center fw-bold mb-3"><?php echo __('유튜브 영상', 'skin') ?></h2>
        <p class="text-center text-muted mb-5">
            <?php echo __('장기렌트 관련 유용한 정보를 영상으로 확인하세요', 'skin') ?>
        </p>

<?php
// YouTube 영상 조회 (PUBLISHED 상태, 최신 8개)
$youtubeVideos = \ExpertNote\Youtube::getVideos(
    ['status = :status'],
    ['published_at DESC'],
    [0, 8],
    ['status' => 'PUBLISHED']
);

// 초를 시:분:초 형식으로 변환
function formatYoutubeDuration($seconds) {
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $secs = $seconds % 60;

    if ($hours > 0) {
        return sprintf("%d:%02d:%02d", $hours, $minutes, $secs);
    }
    return sprintf("%d:%02d", $minutes, $secs);
}

// 숫자 포맷팅 (조회수 등)
function formatYoutubeViewCount($num) {
    if ($num >= 100000000) {
        return number_format($num / 100000000, 1) . __('억', 'skin');
    } else if ($num >= 10000) {
        return number_format($num / 10000, 1) . __('만', 'skin');
    } else if ($num >= 1000) {
        return number_format($num / 1000, 1) . __('천', 'skin');
    }
    return number_format($num);
}

if (!empty($youtubeVideos)):
?>
        <div class="row row-cols-2 row-cols-md-2 row-cols-lg-4 g-4">
<?php foreach($youtubeVideos as $video):
    $videoUrl = "/video/{$video->idx}/" . \ExpertNote\Utils::getPermaLink($video->title, true);
?>
            <div class="col">
                <a href="<?php echo $videoUrl ?>" class="text-decoration-none">
                    <div class="card shadow-sm border-0 h-100 youtube-card-item">
                        <div class="youtube-thumb position-relative">
                            <img src="<?php echo $video->thumbnail_medium ?: $video->thumbnail_default ?>"
                                alt="<?php echo htmlspecialchars($video->title) ?>" loading="lazy">
                            <?php if ($video->duration): ?>
                            <span class="youtube-duration"><?php echo formatYoutubeDuration($video->duration) ?></span>
                            <?php endif; ?>
                            <div class="youtube-play-overlay">
                                <i class="bi bi-play-circle-fill"></i>
                            </div>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title youtube-title"><?php echo htmlspecialchars($video->title) ?></h5>
                            <p class="card-text text-muted small mb-0">
                                <?php echo htmlspecialchars($video->channel_title) ?>
                            </p>
                            <p class="card-text text-muted small">
                                <span><?php echo formatYoutubeViewCount($video->view_count) ?><?php echo __('회', 'skin') ?></span>
                                <span class="mx-1">·</span>
                                <span><?php echo date('Y.m.d', strtotime($video->published_at)) ?></span>
                            </p>
                        </div>
                    </div>
                </a>
            </div>
<?php endforeach; ?>
        </div>

        <!-- 더보기 버튼 -->
        <div class="text-center mt-5">
            <a href="/youtube" class="btn btn-outline-danger btn-lg px-5">
                <i class="bi bi-youtube me-2"></i><?php echo __('영상 더보기', 'skin') ?>
            </a>
        </div>
<?php else: ?>
        <!-- 영상이 없는 경우 -->
        <div class="text-center py-5">
            <i class="bi bi-youtube text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mt-3"><?php echo __('등록된 영상이 없습니다.', 'skin') ?></p>
        </div>
<?php endif; ?>
    </section>

    <style>
    /* YouTube 카드 스타일 */
    .youtube-card-item {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
    }
    .youtube-card-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.15) !important;
    }
    .youtube-thumb {
        overflow: hidden;
        aspect-ratio: 16/9;
        background: #000;
    }
    .youtube-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    .youtube-card-item:hover .youtube-thumb img {
        transform: scale(1.05);
    }
    .youtube-duration {
        position: absolute;
        bottom: 8px;
        right: 8px;
        background: rgba(0,0,0,0.8);
        color: #fff;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    .youtube-play-overlay {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .youtube-play-overlay i {
        font-size: 3rem;
        color: rgba(255,255,255,0.9);
        text-shadow: 0 2px 8px rgba(0,0,0,0.5);
    }
    .youtube-card-item:hover .youtube-play-overlay {
        opacity: 1;
    }
    .youtube-title {
        font-size: 0.9rem;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        color: #1a1a1a;
    }

    /* 리뷰 카드 스타일 */
    .review-card-item {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
    }
    .review-card-item:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.15) !important;
    }
    .review-thumb {
        height: 180px;
        overflow: hidden;
        background: #f5f5f5;
    }
    .review-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    .review-card-item:hover .review-thumb img {
        transform: scale(1.05);
    }
    .review-thumb-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        color: rgba(255,255,255,0.5);
        font-size: 3rem;
    }
    .review-title {
        font-size: 0.95rem;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        color: #1a1a1a;
    }
    .review-author-badge {
        background: var(--primary-color);
        color: #fff;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.75rem;
    }
    .review-stars {
        color: #fae100;
        font-size: 0.8rem;
    }
    </style>

<script>
// 빠른 상담 신청 폼 처리
document.getElementById('consultForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const form = e.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;

    // 폼 데이터 수집
    const formData = {
        name: form.querySelector('[name="name"]').value.trim(),
        phone: form.querySelector('[name="phone"]').value.trim(),
        region: form.querySelector('[name="region"]').value,
        car_type: form.querySelector('[name="car_type"]').value
    };

    // 개인정보 동의 체크
    if (!form.querySelector('#privacy').checked) {
        ExpertNote.Util.showMessage(
            '<?php echo __('개인정보 수집 및 이용에 동의해주세요.', 'skin'); ?>',
            '<?php echo __('알림', 'skin'); ?>',
            [{ title: '<?php echo __('확인', 'skin'); ?>', class: 'btn btn-primary', dismiss: true }]
        );
        return;
    }

    // 버튼 로딩 상태
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span><?php echo __('신청 중...', 'skin'); ?>';

    try {
        const response = await fetch('/api/arirent/consult', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });

        const data = await response.json();

        if (data.result === 'SUCCESS') {
            ExpertNote.Util.showMessage(
                data.message || '<?php echo __('상담 신청이 완료되었습니다.', 'skin'); ?>',
                '<?php echo __('신청 완료', 'skin'); ?>',
                [{ title: '<?php echo __('확인', 'skin'); ?>', class: 'btn btn-primary', dismiss: true }]
            );
            form.reset();
        } else {
            throw new Error(data.message || '<?php echo __('상담 신청에 실패했습니다.', 'skin'); ?>');
        }
    } catch (error) {
        ExpertNote.Util.showMessage(
            error.message || '<?php echo __('오류가 발생했습니다. 다시 시도해주세요.', 'skin'); ?>',
            '<?php echo __('오류', 'skin'); ?>',
            [{ title: '<?php echo __('확인', 'skin'); ?>', class: 'btn btn-secondary', dismiss: true }]
        );
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
});
</script>