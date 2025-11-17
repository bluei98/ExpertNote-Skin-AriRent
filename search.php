<?php
/**
 * 차량 검색 결과 페이지
 */

// 검색어 파라미터
$searchQuery = isset($_GET['q']) ? trim($_GET['q']) : '';

// 페이지 파라미터
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 12;
$offset = ($page - 1) * $perPage;

// 필터 파라미터
$carType = isset($_GET['car_type']) ? $_GET['car_type'] : '';
$brand = isset($_GET['brand']) ? $_GET['brand'] : '';
$fuelType = isset($_GET['fuel_type']) ? $_GET['fuel_type'] : '';

// 페이지 메타 정보 설정
$pageTitle = $searchQuery ? "'{$searchQuery}' 검색 결과 - 아리렌트" : "차량 검색 - 아리렌트";
$pageDescription = $searchQuery
    ? "'{$searchQuery}' 검색 결과입니다. 아리렌트에서 원하는 차량을 찾아보세요."
    : "아리렌트에서 원하는 차량을 검색하세요. 다양한 장기렌트 차량을 합리적인 가격으로 제공합니다.";

\ExpertNote\Core::setPageTitle($pageTitle);
\ExpertNote\Core::setPageDescription($pageDescription);

// 동적 키워드 생성
$keywords = ['아리렌트', '장기렌트', '차량 검색'];

if ($searchQuery) {
    $keywords[] = $searchQuery;
    $keywords[] = $searchQuery . ' 장기렌트';
    $keywords[] = $searchQuery . ' 렌트';
}

if ($carType === 'NEW') {
    $keywords[] = '신차 장기렌트';
} elseif ($carType === 'USED') {
    $keywords[] = '중고차 장기렌트';
}

if ($brand) {
    $keywords[] = $brand . ' 장기렌트';
}

$keywordsString = implode(', ', array_unique($keywords));
\ExpertNote\Core::setPageKeywords($keywordsString);

// Open Graph 메타 태그
\ExpertNote\Core::addMetaTag('og:type', ["property"=>"og:type", "content"=>"website"]);
\ExpertNote\Core::addMetaTag('og:title', ["property"=>"og:title", "content"=>$pageTitle]);
\ExpertNote\Core::addMetaTag('og:description', ["property"=>"og:description", "content"=>$pageDescription]);
\ExpertNote\Core::addMetaTag('og:url', ["property"=>"og:url", "content"=>ExpertNote\Core::getBaseUrl() . $_SERVER['REQUEST_URI']]);
\ExpertNote\Core::addMetaTag('og:site_name', ["property"=>"og:site_name", "content"=>"아리렌트"]);

// JSON-LD 구조화된 데이터
$jsonLd = [
    "@context" => "https://schema.org",
    "@type" => "SearchResultsPage",
    "url" => ExpertNote\Core::getBaseUrl() . $_SERVER['REQUEST_URI'],
    "name" => $pageTitle,
    "description" => $pageDescription,
    "potentialAction" => [
        "@type" => "SearchAction",
        "target" => [
            "@type" => "EntryPoint",
            "urlTemplate" => ExpertNote\Core::getBaseUrl() . "/search?q={search_term_string}"
        ],
        "query-input" => "required name=search_term_string"
    ],
    "mainEntity" => [
        "@type" => "ItemList",
        "numberOfItems" => 0,
        "itemListElement" => []
    ]
];

// WHERE 조건 구성
$where = ["r.dealer_idx" => 1];

// 검색어가 있으면 title 또는 brand에서 검색
if ($searchQuery) {
    // 복잡한 OR 조건은 직접 SQL에서 처리해야 하므로,
    // 여기서는 title LIKE만 사용 (Rent.php의 getRents()가 지원)
    $where['r.title LIKE'] = "%{$searchQuery}%";
}

if ($carType) {
    $where['r.car_type'] = $carType;
}

if ($brand) {
    $where['r.brand'] = $brand;
}

if ($fuelType) {
    $where['r.fuel_type'] = $fuelType;
}

// 정렬 조건
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'popular';
$orderby = [];

switch ($sort) {
    case 'price_low':
        $orderby['p.monthly_rent_amount'] = 'ASC';
        break;
    case 'price_high':
        $orderby['p.monthly_rent_amount'] = 'DESC';
        break;
    case 'newest':
        $orderby['r.created_at'] = 'DESC';
        break;
    case 'popular':
    default:
        $orderby['r.view_count'] = 'DESC';
        break;
}

// 차량 목록 조회
require_once SKINPATH . '/vendor/AriRent/Rent.php';

$rents = \AriRent\Rent::getRents($where, $orderby, ['offset' => $offset, 'count' => $perPage]);
$totalCount = \AriRent\Rent::getRentCount($where);

// 페이지네이션 계산
$totalPages = ceil($totalCount / $perPage);

// 브랜드 목록 (필터용)
// $brands = \AriRent\Rent::getBrands();

// JSON-LD 데이터 구성
$jsonLd['mainEntity']['numberOfItems'] = $totalCount;

if (!empty($rents)) {
    foreach ($rents as $index => $rent) {
        $itemPosition = ($page - 1) * $perPage + $index + 1;

        $jsonLd['mainEntity']['itemListElement'][] = [
            "@type" => "ListItem",
            "position" => $itemPosition,
            "item" => [
                "@type" => "Car",
                "name" => $rent->title,
                "brand" => [
                    "@type" => "Brand",
                    "name" => $rent->brand
                ],
                "fuelType" => $rent->fuel_type,
                "vehicleModelDate" => $rent->model_year,
                "image" => $rent->featured_image ?: ExpertNote\Core::getBaseUrl() . '/assets/images/car-placeholder.png',
                "url" => ExpertNote\Core::getBaseUrl() . '/item/' . $rent->idx,
                "offers" => [
                    "@type" => "Offer",
                    "priceCurrency" => "KRW",
                    "price" => $rent->min_price ?: 0,
                    "priceSpecification" => [
                        "@type" => "UnitPriceSpecification",
                        "price" => $rent->min_price ?: 0,
                        "priceCurrency" => "KRW",
                        "unitText" => "MONTH"
                    ],
                    "availability" => "https://schema.org/InStock",
                    "itemCondition" => $rent->car_type === 'NEW'
                        ? "https://schema.org/NewCondition"
                        : "https://schema.org/UsedCondition"
                ]
            ]
        ];
    }
}
?>

<!-- JSON-LD 구조화된 데이터 -->
<script type="application/ld+json">
<?php echo json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<!-- Page Header -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-6 fw-bold mb-2">
                    <?php if ($searchQuery): ?>
                        '<?php echo htmlspecialchars($searchQuery)?>' 검색 결과
                    <?php else: ?>
                        차량 검색
                    <?php endif; ?>
                </h1>
                <p class="text-muted mb-0">
                    <?php if ($totalCount > 0): ?>
                        총 <strong class="text-primary"><?php echo number_format($totalCount)?></strong>개의 차량이 검색되었습니다.
                    <?php else: ?>
                        검색된 차량이 없습니다.
                    <?php endif; ?>
                </p>
            </div>
            <div class="col-md-4 text-end">
                <button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                    <i class="bi bi-funnel"></i> 필터
                </button>
            </div>
        </div>
    </div>
</section>

<!-- Search & Filter Section -->
<section class="py-4 bg-white border-bottom">
    <div class="container">
        <!-- Search Form -->
        <div class="row mb-3">
            <div class="col-12">
                <form action="/search" method="GET" class="row g-3">
                    <div class="col-md-9">
                        <input type="text" class="form-control form-control-lg" name="q"
                               placeholder="차량명, 브랜드를 검색해보세요"
                               value="<?php echo htmlspecialchars($searchQuery)?>" required>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="bi bi-search"></i> 검색
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Filters (Collapsible) -->
        <div class="collapse <?php echo ($carType || $brand || $fuelType) ? 'show' : ''?>" id="filterCollapse">
            <form action="/search" method="GET" id="filterForm">
                <input type="hidden" name="q" value="<?php echo htmlspecialchars($searchQuery)?>">

                <div class="row g-3">
                    <!-- 차량 타입 필터 -->
                    <div class="col-md-3">
                        <label class="form-label small text-muted">차량 타입</label>
                        <select class="form-select" name="car_type" onchange="this.form.submit()">
                            <option value="">전체</option>
                            <option value="NEW" <?php echo $carType === 'NEW' ? 'selected' : ''?>>신차</option>
                            <option value="USED" <?php echo $carType === 'USED' ? 'selected' : ''?>>중고</option>
                        </select>
                    </div>

                    <!-- 브랜드 필터 -->
                    <div class="col-md-3">
                        <label class="form-label small text-muted">브랜드</label>
                        <select class="form-select" name="brand" onchange="this.form.submit()">
                            <option value="">전체</option>
                            <?php foreach ($brands as $b): ?>
                                <option value="<?php echo htmlspecialchars($b->brand)?>"
                                        <?php echo $brand === $b->brand ? 'selected' : ''?>>
                                    <?php echo htmlspecialchars($b->brand)?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- 연료 타입 필터 -->
                    <div class="col-md-3">
                        <label class="form-label small text-muted">연료</label>
                        <select class="form-select" name="fuel_type" onchange="this.form.submit()">
                            <option value="">전체</option>
                            <option value="가솔린" <?php echo $fuelType === '가솔린' ? 'selected' : ''?>>가솔린</option>
                            <option value="디젤" <?php echo $fuelType === '디젤' ? 'selected' : ''?>>디젤</option>
                            <option value="하이브리드" <?php echo $fuelType === '하이브리드' ? 'selected' : ''?>>하이브리드</option>
                            <option value="전기" <?php echo $fuelType === '전기' ? 'selected' : ''?>>전기</option>
                        </select>
                    </div>

                    <!-- 정렬 -->
                    <div class="col-md-3">
                        <label class="form-label small text-muted">정렬</label>
                        <select class="form-select" name="sort" onchange="this.form.submit()">
                            <option value="popular" <?php echo $sort === 'popular' ? 'selected' : ''?>>인기순</option>
                            <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''?>>최신순</option>
                            <option value="price_low" <?php echo $sort === 'price_low' ? 'selected' : ''?>>낮은 가격순</option>
                            <option value="price_high" <?php echo $sort === 'price_high' ? 'selected' : ''?>>높은 가격순</option>
                        </select>
                    </div>
                </div>

                <?php if ($carType || $brand || $fuelType): ?>
                    <div class="mt-3">
                        <a href="/search?q=<?php echo urlencode($searchQuery)?>" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> 필터 초기화
                        </a>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</section>

<!-- Vehicle Grid -->
<section class="py-5">
    <div class="container">
        <?php if (empty($rents)): ?>
            <div class="text-center py-5">
                <i class="bi bi-search text-muted" style="font-size: 4rem;"></i>
                <h3 class="mt-4">검색 결과가 없습니다</h3>
                <p class="text-muted">다른 검색어로 다시 시도해보세요.</p>
                <a href="/" class="btn btn-primary mt-3">
                    <i class="bi bi-house"></i> 홈으로 가기
                </a>
            </div>
        <?php else: ?>
            <div class="row g-4" id="vehicleGrid">
                <?php foreach ($rents as $rent): ?>
                    <div class="col-md-6 col-lg-3" data-brand="<?php echo htmlspecialchars($rent->brand)?>">
                        <div class="card vehicle-card shadow-sm h-100" onclick="location.href='/item/<?php echo $rent->idx?>'">
                            <div class="vehicle-image">
                                <?php if ($rent->featured_image): ?>
                                    <img src="<?php echo htmlspecialchars($rent->featured_image)?>" alt="<?php echo htmlspecialchars($rent->title)?>">
                                <?php else: ?>
                                    <i class="bi bi-car-front-fill"></i>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge bg-<?php echo $rent->car_type === 'NEW' ? 'primary' : 'success'?>">
                                        <?php echo $rent->car_type === 'NEW' ? '신차' : '중고'?>
                                    </span>
                                    <span class="text-muted small"><?php echo htmlspecialchars($rent->brand)?></span>
                                </div>
                                <h5 class="card-title fw-bold mb-2"><?php echo htmlspecialchars($rent->title)?></h5>
                                <p class="text-muted small mb-3">
                                    <i class="bi bi-speedometer2"></i> <?php echo htmlspecialchars($rent->fuel_type)?>
                                    <span class="ms-2"><i class="bi bi-calendar-event"></i> <?php echo sprintf("%s년%s월", $rent->model_year, $rent->model_month)?></span>
                                    <span class="ms-2"><i class="bi bi-credit-card"></i> <?php echo htmlspecialchars($rent->car_number ?? '-'); ?></span>
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted d-block">월 렌트료</small>
                                        <strong class="text-primary fs-5">
                                            <?php echo $rent->min_price ? number_format($rent->min_price) . '원~' : '문의'?>
                                        </strong>
                                    </div>
                                    <button class="btn btn-outline-primary btn-sm">
                                        자세히 보기 <i class="bi bi-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav class="mt-5">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?q=<?php echo urlencode($searchQuery)?>&page=<?php echo ($page - 1)?><?php echo $carType ? '&car_type=' . $carType : ''?><?php echo $brand ? '&brand=' . urlencode($brand) : ''?><?php echo $fuelType ? '&fuel_type=' . urlencode($fuelType) : ''?><?php echo $sort !== 'popular' ? '&sort=' . $sort : ''?>">
                                    이전
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $page + 2);

                        for ($i = $startPage; $i <= $endPage; $i++):
                        ?>
                            <li class="page-item <?php echo $i === $page ? 'active' : ''?>">
                                <a class="page-link" href="?q=<?php echo urlencode($searchQuery)?>&page=<?php echo $i?><?php echo $carType ? '&car_type=' . $carType : ''?><?php echo $brand ? '&brand=' . urlencode($brand) : ''?><?php echo $fuelType ? '&fuel_type=' . urlencode($fuelType) : ''?><?php echo $sort !== 'popular' ? '&sort=' . $sort : ''?>">
                                    <?php echo $i?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?q=<?php echo urlencode($searchQuery)?>&page=<?php echo ($page + 1)?><?php echo $carType ? '&car_type=' . $carType : ''?><?php echo $brand ? '&brand=' . urlencode($brand) : ''?><?php echo $fuelType ? '&fuel_type=' . urlencode($fuelType) : ''?><?php echo $sort !== 'popular' ? '&sort=' . $sort : ''?>">
                                    다음
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<!-- Quick Consultation (Fixed Sidebar - Desktop Only) -->
<div class="quick-consult card shadow-lg">
    <div class="card-body">
        <h5 class="card-title fw-bold mb-4 text-center">빠른 상담 신청</h5>
        <form id="quickConsultForm">
            <div class="mb-3">
                <input type="text" class="form-control" placeholder="이름" required>
            </div>
            <div class="mb-3">
                <input type="tel" class="form-control" placeholder="연락처" required>
            </div>
            <div class="mb-3">
                <select class="form-select" required>
                    <option value="">차량 선택</option>
                    <option>신차 장기렌트</option>
                    <option>중고 장기렌트</option>
                </select>
            </div>
            <div class="mb-3">
                <textarea class="form-control" rows="3" placeholder="문의사항"></textarea>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="quickAgree" required>
                <label class="form-check-label small" for="quickAgree">
                    개인정보 수집 및 이용 동의
                </label>
            </div>
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-send"></i> 상담 신청
            </button>
        </form>
    </div>
</div>

<script>
// Quick consultation form
document.getElementById('quickConsultForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    alert('상담 신청이 완료되었습니다!\n빠른 시일 내에 연락드리겠습니다.');
    this.reset();
});
</script>
