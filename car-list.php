<?php
/**
 * 차량 목록 페이지
 */

ExpertNote\Core::setLayout("v2");

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
    $pageTitle = "무심사 저신용 신차 장기렌트";
    $pageDescription = "아리렌트 저신용•무심사 신차 장기렌트 전체 목록! 현대•기아•제네시스•수입차 2024~2025년 최신형 신차를 무보증•전액할부로 만나보세요. 개인회생•연체자•타사거절도 당일 출고 가능합니다.";
} elseif ($carType === 'USED') {
    $pageTitle = "무심사 저신용 중고차 장기렌트";
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
$where = [
    "r.status IN"=>['active', 'rented']
];

if ($carType) {
    $where['r.car_type'] = $carType;
}

if(strtolower($_GET['car_filter']) == "updated") {
    $where['r.updated_at >'] = date('Y-m-d H:i:s', strtotime('-1 days'));
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
echo ExpertNote\DB::getLastQuery();
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

<style>
    /* Car List Specific Styles */
    .car-list-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 100px 0 60px;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .car-list-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.1)" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,112C672,96,768,96,864,112C960,128,1056,160,1152,160C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
        background-size: cover;
    }

    .car-list-hero h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .car-list-hero p {
        font-size: 1.1rem;
        opacity: 0.9;
    }

    .filter-section {
        background: white;
        padding: 30px 0;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .filter-group {
        margin-bottom: 20px;
    }

    .filter-title {
        font-size: 0.9rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .filter-title i {
        color: #667eea;
    }

    .filter-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .filter-btn {
        padding: 8px 16px;
        border: 2px solid #e0e0e0;
        background: white;
        border-radius: 25px;
        font-size: 0.9rem;
        transition: all 0.3s;
        cursor: pointer;
    }

    .filter-btn:hover {
        border-color: #667eea;
        color: #667eea;
        transform: translateY(-2px);
    }

    .filter-btn.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-color: transparent;
    }

    .car-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: all 0.3s;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .car-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }

    .car-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        z-index: 10;
    }

    .car-badge.new {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .car-badge.popular {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }

    .car-image {
        position: relative;
        width: 100%;
        height: 220px;
        overflow: hidden;
        background: linear-gradient(to bottom, #f8f9fa, #e9ecef);
    }

    .car-image .sold-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 5;
    }

    .car-image .sold-overlay span {
        color: #fff;
        font-size: 1.5rem;
        font-weight: 700;
        padding: 10px 25px;
        border: 3px solid #fff;
        border-radius: 8px;
        text-transform: uppercase;
        letter-spacing: 2px;
    }

    .car-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s;
    }

    .car-card:hover .car-image img {
        transform: scale(1.1);
    }

    .car-wishlist {
        position: absolute;
        top: 15px;
        right: 15px;
        width: 40px;
        height: 40px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        transition: all 0.3s;
        z-index: 10;
    }

    .car-wishlist:hover {
        background: #667eea;
        color: white;
        transform: scale(1.1);
    }

    .car-wishlist.active {
        background: #f5576c;
        color: white;
    }

    .car-body {
        padding: 20px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .car-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 8px;
    }

    .car-trim {
        font-size: 0.9rem;
        color: #666;
        margin-bottom: 15px;
    }

    .car-specs {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 15px;
    }

    .spec-item {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 0.85rem;
        color: #666;
        padding: 5px 10px;
        background: #f8f9fa;
        border-radius: 15px;
    }

    .spec-item i {
        color: #667eea;
        font-size: 0.9rem;
    }

    .car-pricing {
        margin-top: auto;
        padding-top: 15px;
        border-top: 2px dashed #e0e0e0;
    }

    .deposit {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .deposit-label {
        font-size: 0.85rem;
        color: #666;
    }

    .deposit-amount {
        font-size: 0.95rem;
        font-weight: 600;
        color: #333;
    }

    .monthly-price {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .price-label {
        font-size: 0.9rem;
        color: #666;
    }

    .price-amount {
        font-size: 1.5rem;
        font-weight: 700;
        color: #667eea;
    }

    .price-amount span {
        font-size: 0.9rem;
        font-weight: 500;
    }

    .car-actions {
        display: flex;
        gap: 10px;
    }

    .btn-detail {
        flex: 1;
        padding: 12px;
        background: white;
        border: 2px solid #667eea;
        color: #667eea;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-detail:hover {
        background: #667eea;
        color: white;
    }

    .btn-consult-car {
        flex: 1;
        padding: 12px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-consult-car:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
    }

    .search-section {
        background: white;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 30px;
    }

    .search-input {
        width: 100%;
        padding: 15px 50px 15px 20px;
        border: 2px solid #e0e0e0;
        border-radius: 30px;
        font-size: 1rem;
        transition: all 0.3s;
    }

    .search-input:focus {
        border-color: #667eea;
        outline: none;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .search-btn {
        position: absolute;
        right: 5px;
        top: 50%;
        transform: translateY(-50%);
        width: 45px;
        height: 45px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 50%;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
    }

    .search-btn:hover {
        transform: translateY(-50%) scale(1.1);
    }

    .pagination {
        margin-top: 50px;
    }

    .pagination .page-link {
        border: 2px solid #e0e0e0;
        color: #667eea;
        padding: 10px 18px;
        margin: 0 3px;
        border-radius: 10px;
        font-weight: 600;
    }

    .pagination .page-link:hover {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }

    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: transparent;
    }

    @media (max-width: 768px) {
        .car-list-hero h1 {
            font-size: 1.8rem;
        }

        .filter-section {
            position: relative;
        }

        .filter-buttons {
            overflow-x: auto;
            flex-wrap: nowrap;
            padding-bottom: 10px;
        }

        .filter-btn {
            white-space: nowrap;
        }

        .car-image {
            height: 180px;
        }
    }
</style>

    <!-- Hero Section -->
    <section class="car-list-hero">
        <div class="container">
            <div class="text-center" data-aos="fade-up">
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
        </div>
    </section>

    <!-- Filter Section -->
    <section class="filter-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <!-- Search -->
                    <div class="search-section" data-aos="fade-up">
                        <div class="position-relative">
                            <form action="/search">
                                <input type="text" name="q" class="search-input" placeholder="차량명으로 검색하세요 (예: 아반떼, 쏘나타, 그랜저)">
                                <button class="search-btn">
                                    <i class="bi bi-search"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Manufacturer Filter -->
                    <!-- <div class="filter-group" data-aos="fade-up" data-aos-delay="100">
                        <div class="filter-title">
                            <i class="bi bi-building"></i>
                            <span>제조사</span>
                        </div>
                        <div class="filter-buttons">
                            <button class="filter-btn active" data-filter="all">전체</button>
                            <button class="filter-btn" data-filter="hyundai">현대</button>
                            <button class="filter-btn" data-filter="genesis">제네시스</button>
                            <button class="filter-btn" data-filter="kia">기아</button>
                            <button class="filter-btn" data-filter="renault">르노코리아</button>
                            <button class="filter-btn" data-filter="kg">KG모빌리티</button>
                            <button class="filter-btn" data-filter="chevrolet">쉐보레</button>
                            <button class="filter-btn" data-filter="import">수입차</button>
                        </div>
                    </div> -->

                    <!-- Price Filter -->
                    <!-- <div class="filter-group" data-aos="fade-up" data-aos-delay="200">
                        <div class="filter-title">
                            <i class="bi bi-cash-stack"></i>
                            <span>월 렌트료</span>
                        </div>
                        <div class="filter-buttons">
                            <button class="filter-btn active" data-price="all">전체</button>
                            <button class="filter-btn" data-price="300-500">30~50만원</button>
                            <button class="filter-btn" data-price="500-700">50~70만원</button>
                            <button class="filter-btn" data-price="700-900">70~90만원</button>
                            <button class="filter-btn" data-price="900+">90만원 이상</button>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
    </section>

    <!-- Car List Section -->
    <section class="py-5" style="background: #f8f9fa;">
        <div class="container">
            <div class="row g-4" id="carList">
<?php if (count($items) > 0): ?>
                <!-- 차량 그리드 -->
                <div class="row row-cols-1 row-cols-md-4 row-cols-lg-3 g-4">
<?php
    foreach ($items as $item):
        include SKINPATH."/modules/car-item-new-for-list.php";
    endforeach;
endif;?>
                </div>

            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <nav aria-label="Page navigation" class="d-flex justify-content-center" data-aos="fade-up">
                <ul class="pagination">
                    <!-- 이전 페이지 -->
                    <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                    <?php else: ?>
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1"><i class="bi bi-chevron-left"></i></a>
                    </li>
                    <?php endif; ?>

                    <!-- 페이지 번호 -->
                    <?php
                    $startPage = max(1, $page - 2);
                    $endPage = min($totalPages, $page + 2);

                    if ($startPage > 1):
                    ?>
                    <li class="page-item">
                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => 1])); ?>">1</a>
                    </li>
                    <?php if ($startPage > 2): ?>
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                    </li>
                    <?php endfor; ?>

                    <?php if ($endPage < $totalPages): ?>
                    <?php if ($endPage < $totalPages - 1): ?>
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php endif; ?>
                    <li class="page-item">
                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $totalPages])); ?>"><?php echo $totalPages; ?></a>
                    </li>
                    <?php endif; ?>

                    <!-- 다음 페이지 -->
                    <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                    <?php else: ?>
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1"><i class="bi bi-chevron-right"></i></a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </section>