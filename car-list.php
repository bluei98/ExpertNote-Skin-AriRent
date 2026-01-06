<?php
/**
 * 차량 목록 페이지
 */

// 파라미터 처리
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 12;
$offset = ($page - 1) * $perPage;

// 필터 파라미터
$carType = isset($_GET['car_type']) ? $_GET['car_type'] : '';
$brand = isset($_GET['brand']) ? $_GET['brand'] : '';
$fuelType = isset($_GET['fuel_type']) ? $_GET['fuel_type'] : '';

// 레이아웃 설정 (car_type에 따라 동적으로 변경)
$pageTitle = "차량 목록";
$pageDescription = "아리렌트의 다양한 장기렌트 차량을 확인하세요. 국산차부터 수입차까지 합리적인 가격으로 제공합니다.";

if ($carType === 'NEW') {
    $pageTitle = "저신용 신차 장기렌트";
    $pageDescription = "아리렌트 저신용•무심사 신차 장기렌트 전체 목록! 현대•기아•제네시스•수입차 2024~2025년 최신형 신차를 무보증•전액할부로 만나보세요. 개인회생•연체자•타사거절도 당일 출고 가능합니다.";
} elseif ($carType === 'USED') {
    $pageTitle = "저신용 중고차 장기렌트";
    $pageDescription = "아리렌트 저신용•무심사 중고차 장기렌트 전체 목록! 검증된 고품질 중고차를 신차보다 저렴한 월 렌트비로 이용하세요. 신용불량•개인회생중이어도 무보증 전액할부 가능, 즉시 출고 차량 다수 보유.";
}

\ExpertNote\Core::setPageTitle($pageTitle);
\ExpertNote\Core::setPageSuffix("아리렌트");
\ExpertNote\Core::setPageDescription($pageDescription);

// 동적 키워드 생성
$keywords = [
    '아리렌트',
    '신용불량자 무보증 장기렌트카',
    '렌트카',
];

if ($carType === 'NEW') {
    $keywords[] = '저신용 신차 장기렌트';
    $keywords[] = '무심사 신차 할부';
} elseif ($carType === 'USED') {
    $keywords[] = '저신용 중고차 장기렌트';
    $keywords[] = '무심사 중고차 할부';
}

$keywordsString = implode(', ', array_unique($keywords));
\ExpertNote\Core::setPageKeywords($keywordsString);

// Open Graph 메타 태그
\ExpertNote\Core::addMetaTag('og:type', ["property"=>"og:type", "content"=>"website"]);
\ExpertNote\Core::addMetaTag('og:title', ["property"=>"og:title", "content"=>$pageTitle]);
\ExpertNote\Core::addMetaTag('og:description', ["property"=>"og:description", "content"=>$pageDescription]);
\ExpertNote\Core::addMetaTag('og:url', ["property"=>"og:url", "content"=>ExpertNote\Core::getBaseUrl() . $_SERVER['REQUEST_URI']]);
\ExpertNote\Core::addMetaTag('og:site_name', ["property"=>"og:site_name", "content"=>"아리렌트"]);

// 트위터 카드 메타 태그
\ExpertNote\Core::addMetaTag('twitter:card', ["name"=>"twitter:card", "content"=>"summary_large_image"]);
\ExpertNote\Core::addMetaTag('twitter:title', ["name"=>"twitter:title", "content"=>$pageTitle]);
\ExpertNote\Core::addMetaTag('twitter:description', ["name"=>"twitter:description", "content"=>$pageDescription]);
\ExpertNote\Core::addMetaTag('twitter:url', ["name"=>"twitter:url", "content"=>ExpertNote\Core::getBaseUrl() . $_SERVER['REQUEST_URI']]);

// Canonical URL
\ExpertNote\Core::addMetaTag('canonical', ["rel"=>"canonical", "href"=>ExpertNote\Core::getBaseUrl() . $_SERVER['REQUEST_URI']]);

$minPrice = isset($_GET['min_price']) ? intval($_GET['min_price']) : 0;
$maxPrice = isset($_GET['max_price']) ? intval($_GET['max_price']) : 0;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'popular';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// WHERE 조건 구성
$where = [];

if ($carType) {
    $where['r.car_type'] = $carType;
}

if ($brand) {
    $where['r.brand'] = $brand;
}

if ($fuelType) {
    $where['r.fuel_type'] = $fuelType;
}

if ($search) {
    // LIKE 검색 (Rent.php에서 ' LIKE'가 포함된 키는 LIKE 조건으로 처리)
    $where['r.title LIKE'] = "%{$search}%";
}

// 가격 범위 필터
if ($minPrice > 0) {
    $where['p.monthly_rent_amount >='] = $minPrice;
}

if ($maxPrice > 0) {
    $where['p.monthly_rent_amount <='] = $maxPrice;
}

// 정렬 조건
$orderby = [];
switch ($sort) {
    case 'price_low':
        $orderby = ['p.monthly_rent_amount' => 'ASC'];
        break;
    case 'price_high':
        $orderby = ['p.monthly_rent_amount' => 'DESC'];
        break;
    case 'latest':
        $orderby = ['r.created_at' => 'DESC'];
        break;
    case 'popular':
    default:
        $orderby = ['r.idx' => 'DESC'];
        break;
}

// 차량 목록 조회
$items = AriRent\Rent::getRents($where, $orderby, ['offset' => $offset, 'count' => $perPage]);
// echo ExpertNote\DB::getLastQuery();
if (!is_array($items)) {
    $items = [];
}
$totalCount = AriRent\Rent::getRentCount($where);
$totalPages = $totalCount > 0 ? ceil($totalCount / $perPage) : 0;

// OG 이미지 설정 (차량 목록 로드 후)
$ogImage = ExpertNote\Core::getBaseUrl() . "/skins/arirent/assets/images/og-image.jpg";
if (!empty($items) && isset($items[0]) && !empty($items[0]->featured_image)) {
    $ogImage = $items[0]->featured_image;
}
\ExpertNote\Core::addMetaTag('og:image', ["property"=>"og:image", "content"=>$ogImage]);
\ExpertNote\Core::addMetaTag('og:image:width', ["property"=>"og:image:width", "content"=>"1200"]);
\ExpertNote\Core::addMetaTag('og:image:height', ["property"=>"og:image:height", "content"=>"630"]);
\ExpertNote\Core::addMetaTag('twitter:image', ["name"=>"twitter:image", "content"=>$ogImage]);

// 브랜드 목록
// $brands = ['현대', '기아', '제네시스', 'BMW', '벤츠', '아우디', '폭스바겐', '르노', '쉐보레', '테슬라'];
$carTypes = [
    'NEW' => '신차',
    'USED' => '중고차'
];
$fuelTypes = ['휘발유', '경유', 'LPG', '전기', '하이브리드'];

// LD+JSON 구조화된 데이터 생성 (ItemList)
$ldJson = [
    "@context" => "https://schema.org",
    "@type" => "ItemList",
    "name" => $pageTitle,
    "description" => $pageDescription,
    "numberOfItems" => $totalCount,
    "itemListElement" => []
];

// 각 차량을 ListItem으로 추가
foreach ($items as $index => $vehicle) {
    $itemPosition = $offset + $index + 1;

    $carItem = [
        "@type" => "ListItem",
        "position" => $itemPosition,
        "item" => [
            "@type" => "Car",
            "name" => $vehicle->title,
            "url" => "https://" . $_SERVER['HTTP_HOST'] . "/item/" . $vehicle->idx,
            "description" => $vehicle->title . " - " . $vehicle->fuel_type
        ]
    ];

    // 대표 이미지가 있으면 추가
    if (!empty($vehicle->featured_image)) {
        $carItem["item"]["image"] = $vehicle->featured_image;
    }

    // 가격 정보 추가
    if (!empty($vehicle->min_price)) {
        $carItem["item"]["offers"] = [
            "@type" => "Offer",
            "price" => $vehicle->min_price,
            "priceCurrency" => "KRW",
            "availability" => "https://schema.org/InStock",
            "priceSpecification" => [
                "@type" => "UnitPriceSpecification",
                "price" => $vehicle->min_price,
                "priceCurrency" => "KRW",
                "unitText" => "월"
            ]
        ];
    }

    // 연료 타입
    if (!empty($vehicle->fuel_type)) {
        $carItem["item"]["fuelType"] = $vehicle->fuel_type;
    }

    // 주행거리
    if (!empty($vehicle->mileage_km)) {
        $carItem["item"]["mileageFromOdometer"] = [
            "@type" => "QuantitativeValue",
            "value" => $vehicle->mileage_km,
            "unitCode" => "KMT"
        ];
    }

    // 차량 연식
    if (!empty($vehicle->model_year) && !empty($vehicle->model_month)) {
        $carItem["item"]["productionDate"] = $vehicle->model_year . "-" . str_pad($vehicle->model_month, 2, '0', STR_PAD_LEFT);
    }

    $ldJson["itemListElement"][] = $carItem;
}
?>

<!-- LD+JSON 구조화된 데이터 -->
<script type="application/ld+json">
<?php echo json_encode($ldJson, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<!-- 페이지 헤더 -->
<section class="page-header">
    <div class="container">
        <?php if ($carType === 'NEW'): ?>
        <h1>신차 장기렌트</h1>
        <p>최신 차량을 합리적인 가격으로 만나보세요</p>
        <?php elseif ($carType === 'USED'): ?>
        <h1>중고 장기렌트</h1>
        <p>품질 좋은 중고차를 합리적인 가격으로 만나보세요</p>
        <?php else: ?>
        <h1>차량 목록</h1>
        <p>아리렌트의 다양한 장기렌트 차량을 만나보세요</p>
        <?php endif; ?>
    </div>
</section>

<!-- 메인 컨텐츠 -->
<section class="content-wrapper">
    <div class="container">
        <div class="row">
            <!-- 필터 사이드바 -->
            <div class="col-lg-3">
                <!-- 모바일 필터 버튼 -->
                <button class="btn btn-primary mobile-filter-btn" onclick="toggleMobileFilter()">
                    <i class="bi bi-funnel"></i> 필터
                </button>

                <!-- 필터 오버레이 (모바일) -->
                <div class="filter-overlay" onclick="toggleMobileFilter()"></div>

                <aside class="filter-sidebar">
                    <form method="GET" id="filterForm">
                        <!-- 검색 -->
                        <div class="filter-section">
                            <h3><i class="bi bi-search"></i> 검색</h3>
                            <input type="text" name="search" class="form-control" placeholder="차량명, 브랜드 검색" value="<?php echo htmlspecialchars($search); ?>">
                        </div>

                        <!-- 차종 -->
                        <div class="filter-section">
                            <h3><i class="bi bi-car-front"></i> 차종</h3>
                            <?php foreach ($carTypes as $typeKey => $typeName): ?>
                            <div class="filter-option">
                                <label>
                                    <input type="radio" name="car_type" value="<?php echo $typeKey; ?>" <?php echo $carType === $typeKey ? 'checked' : ''; ?>>
                                    <?php echo $typeName; ?>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- 브랜드 -->
                        <!-- <div class="filter-section">
                            <h3><i class="bi bi-tag"></i> 브랜드</h3>
                            <?php foreach ($brands as $brandName): ?>
                            <div class="filter-option">
                                <label>
                                    <input type="radio" name="brand" value="<?php echo $brandName; ?>" <?php echo $brand === $brandName ? 'checked' : ''; ?>>
                                    <?php echo $brandName; ?>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div> -->

                        <!-- 연료 -->
                        <div class="filter-section">
                            <h3><i class="bi bi-fuel-pump"></i> 연료</h3>
                            <?php foreach ($fuelTypes as $fuel): ?>
                            <div class="filter-option">
                                <label>
                                    <input type="radio" name="fuel_type" value="<?php echo $fuel; ?>" <?php echo $fuelType === $fuel ? 'checked' : ''; ?>>
                                    <?php echo $fuel; ?>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- 가격대 -->
                        <div class="filter-section">
                            <h3><i class="bi bi-currency-dollar"></i> 가격대 (월)</h3>
                            <div class="price-range-inputs">
                                <input type="number" name="min_price" class="form-control form-control-sm" placeholder="최소" value="<?php echo $minPrice > 0 ? $minPrice : ''; ?>" step="10000">
                                <span>~</span>
                                <input type="number" name="max_price" class="form-control form-control-sm" placeholder="최대" value="<?php echo $maxPrice > 0 ? $maxPrice : ''; ?>" step="10000">
                            </div>
                            <small class="text-muted">단위: 만원</small>
                        </div>

                        <!-- 필터 적용 버튼 -->
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-check-circle"></i> 필터 적용
                        </button>

                        <!-- 필터 초기화 -->
                        <button type="button" class="btn-reset-filter" onclick="resetFilters()">
                            <i class="bi bi-arrow-counterclockwise"></i> 필터 초기화
                        </button>
                    </form>
                </aside>
            </div>

            <!-- 차량 목록 -->
            <div class="col-lg-9">
                <!-- 검색 및 정렬 바 -->
                <div class="search-sort-bar">
                    <div class="row align-items-center">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="result-info">
                                전체 <strong><?php echo number_format($totalCount); ?></strong>대의 차량
                            </div>
                        </div>
                        <div class="col-md-6">
                            <select class="form-select" onchange="changeSort(this.value)">
                                <option value="popular" <?php echo $sort === 'popular' ? 'selected' : ''; ?>>인기순</option>
                                <option value="latest" <?php echo $sort === 'latest' ? 'selected' : ''; ?>>최신순</option>
                                <option value="price_low" <?php echo $sort === 'price_low' ? 'selected' : ''; ?>>가격 낮은순</option>
                                <option value="price_high" <?php echo $sort === 'price_high' ? 'selected' : ''; ?>>가격 높은순</option>
                            </select>
                        </div>
                    </div>
                </div>

                <?php if (count($items) > 0): ?>
                <!-- 차량 그리드 -->
                <div class="row row-cols-1 row-cols-md-4 row-cols-lg-3 g-4">
                    <?php foreach ($items as $item):
                        include SKINPATH."/modules/car-item.php";
                    endforeach; ?>
                </div>

                <!-- 페이지네이션 -->
                <?php if ($totalPages > 1): ?>
                <div class="pagination-wrapper">
                    <div class="pagination">
                        <!-- 이전 페이지 -->
                        <?php if ($page > 1): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                        <?php else: ?>
                        <span class="disabled"><i class="bi bi-chevron-left"></i></span>
                        <?php endif; ?>

                        <!-- 페이지 번호 -->
                        <?php
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $page + 2);

                        if ($startPage > 1):
                        ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => 1])); ?>">1</a>
                        <?php if ($startPage > 2): ?>
                        <span class="disabled">...</span>
                        <?php endif; ?>
                        <?php endif; ?>

                        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <?php if ($i == $page): ?>
                        <span class="active"><?php echo $i; ?></span>
                        <?php else: ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($endPage < $totalPages): ?>
                        <?php if ($endPage < $totalPages - 1): ?>
                        <span class="disabled">...</span>
                        <?php endif; ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $totalPages])); ?>"><?php echo $totalPages; ?></a>
                        <?php endif; ?>

                        <!-- 다음 페이지 -->
                        <?php if ($page < $totalPages): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                        <?php else: ?>
                        <span class="disabled"><i class="bi bi-chevron-right"></i></span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php else: ?>
                <!-- 빈 결과 -->
                <div class="empty-result">
                    <i class="bi bi-inbox"></i>
                    <h3>검색 결과가 없습니다</h3>
                    <p>다른 조건으로 검색해 보세요</p>
                    <button class="btn btn-primary" onclick="resetFilters()">
                        <i class="bi bi-arrow-counterclockwise"></i> 필터 초기화
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
    // 정렬 변경
    function changeSort(sortValue) {
        const url = new URL(window.location);
        url.searchParams.set('sort', sortValue);
        url.searchParams.set('page', '1'); // 정렬 변경 시 1페이지로
        window.location.href = url.toString();
    }

    // 필터 초기화
    function resetFilters() {
        window.location.href = window.location.pathname;
    }

    // 모바일 필터 토글
    function toggleMobileFilter() {
        const sidebar = document.querySelector('.filter-sidebar');
        const overlay = document.querySelector('.filter-overlay');

        sidebar.classList.toggle('show');
        overlay.classList.toggle('show');
    }

    // 필터 폼 자동 제출 (라디오 버튼 클릭 시)
    document.querySelectorAll('.filter-option input[type="radio"]').forEach(input => {
        input.addEventListener('change', function() {
            // 페이지를 1로 리셋
            const form = document.getElementById('filterForm');
            const pageInput = form.querySelector('input[name="page"]');
            if (pageInput) {
                pageInput.value = '1';
            } else {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'page';
                input.value = '1';
                form.appendChild(input);
            }
            form.submit();
        });
    });
</script>
