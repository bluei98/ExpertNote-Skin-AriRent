<?php
ExpertNote\Core::setLayout("v2");
/**
 * 차량 목록 페이지
 */

// 파라미터 처리
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 12;
$offset = ($page - 1) * $perPage;

// 필터 파라미터
$carType = isset($_GET['car_type']) ? $_GET['car_type'] : 'NEW';
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
$where = [
    'r.status' => 'active'
];

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
        $orderby = ['r.view_count' => 'DESC'];
        break;
}

// 차량 목록 조회
// $vehicles = AriRent\Rent::getRents($where, $orderby, ['offset' => $offset, 'count' => $perPage]);
$vehicles = AriRent\Rent::getRents($where, $orderby, [], true);
// echo ExpertNote\DB::getLastQuery();
if (!is_array($vehicles)) {
    $vehicles = [];
}

// 차종(segment) 정렬 순서 정의
$segmentOrder = [
    '경차' => 1,
    '소형' => 2,
    '소형SUV' => 3,
    '준중형' => 4,
    '준중형SUV' => 5,
    '중형' => 6,
    '중형SUV' => 7,
    '대형' => 8,
    '대형SUV' => 9,
    'MPV' => 10,
    '트럭' => 11,
    '전기차' => 12
];

// 차종(segment) 순서로 정렬
usort($vehicles, function($a, $b) use ($segmentOrder) {
    $orderA = isset($segmentOrder[$a->segment]) ? $segmentOrder[$a->segment] : 999;
    $orderB = isset($segmentOrder[$b->segment]) ? $segmentOrder[$b->segment] : 999;

    if ($orderA === $orderB) {
        // 같은 카테고리 내에서는 브랜드명, 모델명 순으로 정렬
        $compareA = ($a->brand_name ?? '') . ($a->model_name ?? '');
        $compareB = ($b->brand_name ?? '') . ($b->model_name ?? '');
        return strcmp($compareA, $compareB);
    }

    return $orderA - $orderB;
});

$totalCount = AriRent\Rent::getRentCount($where);
$totalPages = $totalCount > 0 ? ceil($totalCount / $perPage) : 0;

// OG 이미지 설정 (차량 목록 로드 후)
$ogImage = ExpertNote\Core::getBaseUrl() . "/skins/arirent/assets/images/og-image.jpg";
if (!empty($vehicles) && isset($vehicles[0]) && !empty($vehicles[0]->featured_image)) {
    $ogImage = $vehicles[0]->featured_image;
}
\ExpertNote\Core::addMetaTag('og:image', ["property"=>"og:image", "content"=>$ogImage]);
\ExpertNote\Core::addMetaTag('og:image:width', ["property"=>"og:image:width", "content"=>"1200"]);
\ExpertNote\Core::addMetaTag('og:image:height', ["property"=>"og:image:height", "content"=>"630"]);
\ExpertNote\Core::addMetaTag('twitter:image', ["name"=>"twitter:image", "content"=>$ogImage]);

// 브랜드/모델 목록 추출 (차량 데이터에서)
$brandList = [];
$modelListByBrand = [];
foreach ($vehicles as $v) {
    $bName = $v->brand_name ?? '';
    $mName = $v->model_name ?? '';
    if ($bName && !in_array($bName, $brandList)) {
        $brandList[] = $bName;
    }
    if ($bName && $mName) {
        if (!isset($modelListByBrand[$bName])) {
            $modelListByBrand[$bName] = [];
        }
        if (!in_array($mName, $modelListByBrand[$bName])) {
            $modelListByBrand[$bName][] = $mName;
        }
    }
}
sort($brandList);
foreach ($modelListByBrand as $b => $models) {
    sort($modelListByBrand[$b]);
}

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
foreach ($vehicles as $index => $vehicle) {
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

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="page-header-content" data-aos="fade-up">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">홈</a></li>
                    <li class="breadcrumb-item active" aria-current="page">차량목록</li>
                </ol>
            </nav>
            <h1 class="page-title">차량목록</h1>
            <!-- <p class="page-desc">아리레</p> -->
        </div>
    </div>
</section>

<style>
/* 차량 테이블 hover 효과 */
.car-table tr[data-vehicle-id] {
    cursor: pointer;
    transition: background-color 0.2s ease;
}
.car-table tr[data-vehicle-id].hover td:not(.bg-light) {
    background-color: #e8f4ff !important;
}
.car-table tr[data-vehicle-id].hover td.bg-light {
    background-color: #d0e8ff !important;
}
/* 보증금 100만 이하 하이라이트 셀 hover 시 유지 */
.car-table tr[data-vehicle-id].hover td.deposit-highlight {
    background-color: #1fd882 !important;
}
/* 클립보드 복사 버튼 */
.copy-btn {
    cursor: pointer;
    color: #6c757d;
    transition: color 0.2s;
}
.copy-btn:hover {
    color: #0d6efd;
}
.copy-btn.copied {
    color: #198754;
}
/* 모바일 반응형 */
@media (max-width: 768px) {
    .table-responsive {
        display: none;
    }
    .mobile-cards {
        display: block;
    }
}
@media (min-width: 769px) {
    .mobile-cards {
        display: none;
    }
}
/* 모바일 카드 스타일 */
.mobile-card {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    margin-bottom: 1rem;
    overflow: hidden;
}
.mobile-card-header {
    background: #f8f9fa;
    padding: 0.75rem;
    border-bottom: 1px solid #dee2e6;
    cursor: pointer;
}
.mobile-card-header-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}
.mobile-card-title-wrap {
    flex: 1;
    min-width: 0;
}
.mobile-card-title {
    font-weight: bold;
    font-size: 1rem;
    margin-bottom: 0.25rem;
}
.mobile-card-info-wrap {
    display: flex;
    align-items: center;
    flex-shrink: 0;
    margin-left: 0.5rem;
}
.mobile-card-summary-text {
    font-size: 0.75rem;
    color: #666;
    text-align: right;
    white-space: nowrap;
}
.mobile-card-toggle {
    margin-left: 0.5rem;
    color: #666;
}
.mobile-card-header i {
    transition: transform 0.3s;
}
.mobile-card-header[aria-expanded="true"] i {
    transform: rotate(180deg);
}
.mobile-card-body {
    padding: 0.75rem;
}
.mobile-card-row {
    display: flex;
    border-bottom: 1px solid #eee;
    padding: 0.5rem 0;
}
.mobile-card-row:last-child {
    border-bottom: none;
}
.mobile-card-label {
    width: 80px;
    flex-shrink: 0;
    color: #6c757d;
    font-size: 0.85rem;
}
.mobile-card-value {
    flex: 1;
    font-size: 0.9rem;
}
.mobile-card-deposit {
    background: #f0f7ff;
    padding: 0.75rem;
    text-align: center;
    border-top: 1px solid #dee2e6;
}
.mobile-card-deposit.highlight {
    background: #27ee91;
}
.mobile-card-deposit-amount {
    font-size: 1.5rem;
    font-weight: bold;
    color: #333;
}
.mobile-card-prices {
    background: #fafafa;
    padding: 0.5rem;
}
.mobile-card-price-row {
    display: flex;
    justify-content: space-between;
    padding: 0.35rem 0.5rem;
    border-bottom: 1px solid #eee;
    font-size: 0.85rem;
}
.mobile-card-price-row:last-child {
    border-bottom: none;
}
.mobile-card-footer {
    padding: 0.75rem;
    text-align: center;
    border-top: 1px solid #dee2e6;
}
.mobile-card-deposit i {
    transition: transform 0.3s;
}
.mobile-card-deposit[aria-expanded="true"] i {
    transform: rotate(180deg);
}
.mobile-card-deposit {
    cursor: pointer;
}
</style>

<section class="container my-4">
    <!-- 필터 영역 -->
    <div class="filter-section mb-4 p-3 bg-light rounded">
        <div class="row g-3 align-items-end">
            <div class="col-6 col-md-2">
                <label class="form-label small mb-1">브랜드</label>
                <select class="form-select form-select-sm" id="filterBrand">
                    <option value="">전체</option>
                    <?php foreach ($brandList as $b): ?>
                    <option value="<?php echo htmlspecialchars($b)?>"><?php echo htmlspecialchars($b)?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small mb-1">모델</label>
                <select class="form-select form-select-sm" id="filterModel">
                    <option value="">전체</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small mb-1">보증금</label>
                <select class="form-select form-select-sm" id="filterDeposit">
                    <option value="">전체</option>
                    <option value="50">50만 이하</option>
                    <option value="100">100만 이하</option>
                    <option value="150">150만 이하</option>
                    <option value="200">200만 이하</option>
                    <option value="300">300만 이하</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small mb-1">월 렌트료</label>
                <select class="form-select form-select-sm" id="filterRent">
                    <option value="">전체</option>
                    <option value="0-30">30만 이하</option>
                    <option value="30-50">30~50만</option>
                    <option value="50-70">50~70만</option>
                    <option value="70-100">70~100만</option>
                    <option value="100-">100만 이상</option>
                </select>
            </div>
            <div class="col-8 col-md-3">
                <label class="form-label small mb-1">차량번호 / 차명 검색</label>
                <input type="text" class="form-control form-control-sm" id="filterSearch" placeholder="차량번호 또는 차명 입력">
            </div>
            <div class="col-4 col-md-1">
                <button type="button" class="btn btn-sm btn-outline-secondary w-100" id="filterReset" title="필터 초기화">
                    <i class="bi bi-x-circle"></i>
                </button>
            </div>
        </div>
        <div class="mt-2">
            <small class="text-muted">검색결과: <strong id="filterCount"><?php echo count($vehicles); ?></strong>대</small>
        </div>
    </div>
    <!-- 브랜드별 모델 데이터 (JavaScript용) -->
    <script>
    var modelListByBrand = <?php echo json_encode($modelListByBrand, JSON_UNESCAPED_UNICODE); ?>;
    </script>

    <div class="table-responsive">
        <table class="table table-bordered car-table">
        <colgroup>
            <col style="min-width: 200px;"/>
            <col style="min-width: 100px;"/>
            <col style="min-width: 150px;"/>
            <col style="min-width: 100px;"/>
            <col style="min-width: 150px;"/>
            <col style="min-width: 100px;"/>
            <col style="min-width: 100px;"/>
            <col style="min-width: 150px;"/>
            <col style="min-width: 100px;"/>
        </colgroup>
        <thead>
        <tr>
            <th class="bg-light text-center">차종</th>
            <th class="bg-light text-center" colspan="4">장기렌트 목록</th>
            <th class="bg-light text-center">보증금</th>
            <th class="bg-light text-center">기간</th>
            <th class="bg-light text-center">렌트료</th>
            <th class="bg-light text-center">인수가</th>
            <!-- <th class="bg-light text-center">비고</th> -->
        </tr>
        </thead>
        <?php $i=0;foreach ($vehicles as $item): $i++;
        // if($i>5) break;
        $vehicleTitle = $item->model_name ? $item->brand_name.' '.$item->model_name : $item->title;
        $depositAmount = isset($item->prices[0]) ? $item->prices[0]->deposit_amount : 0;
        $minRentAmount = isset($item->prices[0]) ? $item->prices[0]->monthly_rent_amount : 0;
        ?>
        <tr data-vehicle-id="<?php echo $item->idx?>"
            data-vehicle-group="<?php echo $item->idx?>"
            data-brand="<?php echo htmlspecialchars($item->brand_name ?? '')?>"
            data-model="<?php echo htmlspecialchars($item->model_name ?? '')?>"
            data-deposit="<?php echo $depositAmount?>"
            data-min-rent="<?php echo $minRentAmount?>"
            data-car-number="<?php echo htmlspecialchars($item->car_number)?>"
            data-title="<?php echo htmlspecialchars($vehicleTitle)?>"
            class="vehicle-row-main">
            <td rowspan="4" class="align-middle text-center bg-light">
                <p>
                    <?php echo $item->model_name ? $item->brand_name.' '.$item->model_name : $item->title?>
                    <i class="bi bi-filter" title="<?php echo sprintf("%s 모아보기", $item->model_name ? $item->brand_name.' '.$item->model_name : $item->title)?>"></i>
                    <?php if(ExpertNote\User\User::isAdmin()):?>
                        <br/><?php echo $item->dealer_name ? $item->dealer_name.' ('.$item->dealer_code.')' : $item->dealer_code?>
                    <?php endif;?>
                </p>
                <p><?php echo $item->car_number?> <i class="bi bi-clipboard copy-btn" data-copy="<?php echo htmlspecialchars($item->car_number)?>" title="차량번호 복사"></i></p>
                <p>
                    <a href="/item/<?php echo $item->idx?>" class="btn btn-sm btn-outline-primary" target="_blank">차량보기 &gt;</a>
                    <?php if(ExpertNote\User\User::isAdmin()):?><a href="/backoffice/rent/car-edit?idx=<?php echo $item->idx?>" class="btn btn-sm btn-outline-danger ms-1" target="backoffice">차량수정 &gt;</a><?php endif;?>
                </p>
            </td>
            <td class="text-center bg-light">유종</td>
            <td class="text-center"><?php echo $item->fuel_type?></td>
            <td class="text-center bg-light">연식</td>
            <td class="text-center">
                <?php if($item->car_type == 'NEW'): ?>
                    신차 (<?php echo substr($item->model_year, -2)?>년 <?php echo $item->model_month?>월)
                <?php else: ?>
                    <?php echo substr($item->model_year, -2)?>년 <?php echo $item->model_month?>월
                <?php endif; ?>
            </td>
            <td rowspan="4" class="align-middle text-center fw-bold fs-2<?php if($item->prices[0]->deposit_amount <= 100) echo ' deposit-highlight'; ?>" <?php if($item->prices[0]->deposit_amount <= 100) echo 'style="background: #27ee91;"'; ?>>
                <?php echo number_format($item->prices[0]->deposit_amount) ?><small style="font-size: 0.8rem;">만</small>
            </td>
            <td rowspan="4" colspan="3" class="p-0">
                <table class="table m-0">
                <colgroup>
                    <col width="100"/>
                    <col width="150"/>
                    <col width="100"/>
                </colgroup>
                <?php foreach($item->prices as $price):
                    // 신차 + JET 딜러는 36개월 미만 기간 제외
                    if($item->car_type == 'NEW' && $item->dealer_code == 'JET' && $price->rental_period_months < 36) continue;
                ?>
                <tr>
                    <td class="text-center border-end"><?php echo $price->rental_period_months ?></td>
                    <td class="text-center border-end"><?php echo number_format($price->monthly_rent_amount) ?></td>
                    <td class="text-center">
                        <?php if($price->rental_period_months < 36):?>
                            반납형
                        <?php else: ?>
                            선택형
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach;?>
                </table>
            </td>
            <!-- <td rowspan="4" class="align-middle text-center">
            </td> -->
        </tr>
        <tr data-vehicle-id="<?php echo $item->idx?>" data-vehicle-group="<?php echo $item->idx?>" class="vehicle-row-sub">
            <td class="text-center bg-light">등급</td>
            <td class="text-center"><?php echo $item->grade ?? '-'?></td>
            <td class="align-middle text-center bg-light">주행거리</td>
            <td class="align-middle text-center"><?php echo $item->mileage_km?> km</td>
        </tr>
        <tr data-vehicle-id="<?php echo $item->idx?>" data-vehicle-group="<?php echo $item->idx?>" class="vehicle-row-sub">
            <td class="text-center bg-light">색상</td>
            <td class="text-center"><?php echo $item->color ?? '-'?></td>
            <td class="align-middle text-center bg-light">보험 연령</td>
            <td class="align-middle text-center">26세</td>
        </tr>
        <tr data-vehicle-id="<?php echo $item->idx?>" data-vehicle-group="<?php echo $item->idx?>" class="vehicle-row-sub">
            <td class="text-center bg-light">옵션</td>
            <td class="text-center" colspan="3">
                <?php
                $options = json_decode($item->option_main);
                echo $options ? implode(', ', $options) : '-';
                ?>
            </td>
        </tr>

        <?php endforeach; ?>
        </table>
    </div>

    <!-- 모바일 카드 레이아웃 -->
    <div class="mobile-cards">
        <?php foreach ($vehicles as $item):
        $vehicleTitleMobile = $item->model_name ? $item->brand_name.' '.$item->model_name : $item->title;
        $depositAmountMobile = isset($item->prices[0]) ? $item->prices[0]->deposit_amount : 0;
        $minRentAmountMobile = isset($item->prices[0]) ? $item->prices[0]->monthly_rent_amount : 0;
        ?>
        <div class="mobile-card"
             data-vehicle-id="<?php echo $item->idx?>"
             data-brand="<?php echo htmlspecialchars($item->brand_name ?? '')?>"
             data-model="<?php echo htmlspecialchars($item->model_name ?? '')?>"
             data-deposit="<?php echo $depositAmountMobile?>"
             data-min-rent="<?php echo $minRentAmountMobile?>"
             data-car-number="<?php echo htmlspecialchars($item->car_number)?>"
             data-title="<?php echo htmlspecialchars($vehicleTitleMobile)?>">
            <div class="mobile-card-header" data-bs-toggle="collapse" data-bs-target="#details-<?php echo $item->idx?>" role="button" aria-expanded="false">
                <div class="mobile-card-header-content">
                    <div class="mobile-card-title-wrap">
                        <div class="mobile-card-title">
                            <?php echo $item->model_name ? $item->brand_name.' '.$item->model_name : $item->title?>
                        </div>
                        <div class="text-muted small"><?php echo $item->car_number?></div>
                    </div>
                    <div class="mobile-card-info-wrap">
                        <div class="mobile-card-summary-text">
                            <?php echo $item->fuel_type?> · <?php echo substr($item->model_year, -2)?>년<br>
                            <?php echo number_format($item->mileage_km)?> km
                        </div>
                        <span class="mobile-card-toggle">
                            <i class="bi bi-chevron-down"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="collapse mobile-card-body" id="details-<?php echo $item->idx?>">
                <div class="mobile-card-row">
                    <span class="mobile-card-label">유종</span>
                    <span class="mobile-card-value"><?php echo $item->fuel_type?></span>
                </div>
                <div class="mobile-card-row">
                    <span class="mobile-card-label">연식</span>
                    <span class="mobile-card-value">
                        <?php if($item->car_type == 'NEW'): ?>
                            신차 (<?php echo substr($item->model_year, -2)?>년 <?php echo $item->model_month?>월)
                        <?php else: ?>
                            <?php echo substr($item->model_year, -2)?>년 <?php echo $item->model_month?>월
                        <?php endif; ?>
                    </span>
                </div>
                <div class="mobile-card-row">
                    <span class="mobile-card-label">등급</span>
                    <span class="mobile-card-value"><?php echo $item->grade ?? '-'?></span>
                </div>
                <div class="mobile-card-row">
                    <span class="mobile-card-label">색상</span>
                    <span class="mobile-card-value"><?php echo $item->color ?? '-'?></span>
                </div>
                <div class="mobile-card-row">
                    <span class="mobile-card-label">주행거리</span>
                    <span class="mobile-card-value"><?php echo number_format($item->mileage_km)?> km</span>
                </div>
                <div class="mobile-card-row">
                    <span class="mobile-card-label">옵션</span>
                    <span class="mobile-card-value">
                        <?php
                        $options = json_decode($item->option_main);
                        echo $options ? implode(', ', $options) : '-';
                        ?>
                    </span>
                </div>
            </div>
            <div class="mobile-card-deposit<?php if($item->prices[0]->deposit_amount <= 100) echo ' highlight'; ?>"
                 data-bs-toggle="collapse" data-bs-target="#prices-<?php echo $item->idx?>" role="button">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small text-muted">보증금</div>
                        <div class="mobile-card-deposit-amount"><?php echo number_format($item->prices[0]->deposit_amount) ?>만원</div>
                    </div>
                    <div class="text-end">
                        <div class="small text-muted">월 렌트료</div>
                        <div class="fw-bold"><?php echo number_format($item->prices[0]->monthly_rent_amount) ?>원~</div>
                    </div>
                    <i class="bi bi-chevron-down ms-2"></i>
                </div>
            </div>
            <div class="collapse mobile-card-prices" id="prices-<?php echo $item->idx?>">
                <div class="mobile-card-price-row fw-bold" style="background:#eee;">
                    <span>기간</span>
                    <span>월 렌트료</span>
                    <span>인수</span>
                </div>
                <?php foreach($item->prices as $price):
                    // 신차 + JET 딜러는 36개월 미만 기간 제외
                    if($item->car_type == 'NEW' && $item->dealer_code == 'JET' && $price->rental_period_months < 36) continue;
                ?>
                <div class="mobile-card-price-row">
                    <span><?php echo $price->rental_period_months ?>개월</span>
                    <span class="fw-bold"><?php echo number_format($price->monthly_rent_amount) ?>원</span>
                    <span><?php echo $price->rental_period_months < 36 ? '반납형' : '선택형'; ?></span>
                </div>
                <?php endforeach;?>
            </div>
            <div class="mobile-card-footer">
                <a href="/item/<?php echo $item->idx?>" class="btn btn-sm btn-primary">차량보기</a>
                <?php if(ExpertNote\User\User::isAdmin()):?>
                <a href="/backoffice/rent/car-edit?idx=<?php echo $item->idx?>" class="btn btn-sm btn-outline-danger ms-1">수정</a>
                <?php endif;?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const carTable = document.querySelector('.car-table');
    if (!carTable) return;

    // 차량별 행 그룹 미리 캐싱 (성능 최적화)
    const vehicleRows = {};
    carTable.querySelectorAll('tr[data-vehicle-id]').forEach(function(row) {
        const id = row.dataset.vehicleId;
        if (!vehicleRows[id]) vehicleRows[id] = [];
        vehicleRows[id].push(row);
    });

    let currentHoverId = null;

    // 이벤트 위임: 테이블에 단일 이벤트 리스너
    carTable.addEventListener('mouseover', function(e) {
        const row = e.target.closest('tr[data-vehicle-id]');
        if (!row) return;

        const vehicleId = row.dataset.vehicleId;
        if (vehicleId === currentHoverId) return; // 같은 차량이면 무시

        // 이전 hover 제거
        if (currentHoverId && vehicleRows[currentHoverId]) {
            vehicleRows[currentHoverId].forEach(function(r) {
                r.classList.remove('hover');
            });
        }

        // 새 hover 추가
        currentHoverId = vehicleId;
        if (vehicleRows[vehicleId]) {
            vehicleRows[vehicleId].forEach(function(r) {
                r.classList.add('hover');
            });
        }
    });

    carTable.addEventListener('mouseleave', function() {
        if (currentHoverId && vehicleRows[currentHoverId]) {
            vehicleRows[currentHoverId].forEach(function(r) {
                r.classList.remove('hover');
            });
        }
        currentHoverId = null;
    });

    // 클립보드 복사 (이벤트 위임)
    carTable.addEventListener('click', function(e) {
        const copyBtn = e.target.closest('.copy-btn');
        if (!copyBtn) return;

        e.stopPropagation(); // 행 클릭 이벤트 방지
        const text = copyBtn.dataset.copy;

        navigator.clipboard.writeText(text).then(function() {
            // 복사 성공 피드백
            copyBtn.classList.remove('bi-clipboard');
            copyBtn.classList.add('bi-clipboard-check', 'copied');

            setTimeout(function() {
                copyBtn.classList.remove('bi-clipboard-check', 'copied');
                copyBtn.classList.add('bi-clipboard');
            }, 1500);
        });
    });

    // 필터 기능
    const filterBrand = document.getElementById('filterBrand');
    const filterModel = document.getElementById('filterModel');
    const filterDeposit = document.getElementById('filterDeposit');
    const filterRent = document.getElementById('filterRent');
    const filterSearch = document.getElementById('filterSearch');
    const filterReset = document.getElementById('filterReset');
    const filterCount = document.getElementById('filterCount');
    const mobileCards = document.querySelectorAll('.mobile-card');

    // 브랜드 선택 시 모델 목록 업데이트
    function updateModelOptions() {
        const selectedBrand = filterBrand.value;
        filterModel.innerHTML = '<option value="">전체</option>';

        if (selectedBrand && modelListByBrand[selectedBrand]) {
            modelListByBrand[selectedBrand].forEach(function(model) {
                const option = document.createElement('option');
                option.value = model;
                option.textContent = model;
                filterModel.appendChild(option);
            });
        }
    }

    // 필터 적용 함수
    function applyFilters() {
        const brandFilter = filterBrand.value;
        const modelFilter = filterModel.value;
        const depositMax = filterDeposit.value ? parseInt(filterDeposit.value) : null;
        const rentRange = filterRent.value;
        const searchText = filterSearch.value.toLowerCase().trim();

        let visibleCount = 0;
        const visibleVehicles = new Set();

        // 테이블 행 필터링 (메인 행 기준으로 처리)
        const mainRows = carTable.querySelectorAll('.vehicle-row-main');
        mainRows.forEach(function(row) {
            const vehicleId = row.dataset.vehicleGroup;
            const brand = row.dataset.brand || '';
            const model = row.dataset.model || '';
            const deposit = parseInt(row.dataset.deposit) || 0;
            const minRent = parseInt(row.dataset.minRent) || 0;
            const carNumber = (row.dataset.carNumber || '').toLowerCase();
            const title = (row.dataset.title || '').toLowerCase();

            let show = true;

            // 브랜드 필터
            if (brandFilter && brand !== brandFilter) {
                show = false;
            }

            // 모델 필터
            if (modelFilter && show && model !== modelFilter) {
                show = false;
            }

            // 보증금 필터
            if (depositMax !== null && show && deposit > depositMax) {
                show = false;
            }

            // 렌트료 필터
            if (rentRange && show) {
                const rentParts = rentRange.split('-');
                const rentMin = rentParts[0] ? parseInt(rentParts[0]) * 10000 : 0;
                const rentMax = rentParts[1] ? parseInt(rentParts[1]) * 10000 : Infinity;

                if (minRent < rentMin || minRent > rentMax) {
                    show = false;
                }
            }

            // 검색어 필터
            if (searchText && show) {
                if (!carNumber.includes(searchText) && !title.includes(searchText)) {
                    show = false;
                }
            }

            // 같은 차량 그룹의 모든 행 표시/숨김
            if (vehicleRows[vehicleId]) {
                vehicleRows[vehicleId].forEach(function(r) {
                    r.style.display = show ? '' : 'none';
                });
            }

            if (show) {
                visibleCount++;
                visibleVehicles.add(vehicleId);
            }
        });

        // 모바일 카드 필터링
        mobileCards.forEach(function(card) {
            const vehicleId = card.dataset.vehicleId;
            const brand = card.dataset.brand || '';
            const model = card.dataset.model || '';
            const deposit = parseInt(card.dataset.deposit) || 0;
            const minRent = parseInt(card.dataset.minRent) || 0;
            const carNumber = (card.dataset.carNumber || '').toLowerCase();
            const title = (card.dataset.title || '').toLowerCase();

            let show = true;

            // 브랜드 필터
            if (brandFilter && brand !== brandFilter) {
                show = false;
            }

            // 모델 필터
            if (modelFilter && show && model !== modelFilter) {
                show = false;
            }

            // 보증금 필터
            if (depositMax !== null && show && deposit > depositMax) {
                show = false;
            }

            // 렌트료 필터
            if (rentRange && show) {
                const rentParts = rentRange.split('-');
                const rentMin = rentParts[0] ? parseInt(rentParts[0]) * 10000 : 0;
                const rentMax = rentParts[1] ? parseInt(rentParts[1]) * 10000 : Infinity;

                if (minRent < rentMin || minRent > rentMax) {
                    show = false;
                }
            }

            // 검색어 필터
            if (searchText && show) {
                if (!carNumber.includes(searchText) && !title.includes(searchText)) {
                    show = false;
                }
            }

            card.style.display = show ? '' : 'none';
        });

        // 검색 결과 수 업데이트
        filterCount.textContent = visibleCount;
    }

    // 이벤트 리스너 등록
    filterBrand.addEventListener('change', function() {
        updateModelOptions();
        filterModel.value = ''; // 브랜드 변경 시 모델 선택 초기화
        applyFilters();
    });
    filterModel.addEventListener('change', applyFilters);
    filterDeposit.addEventListener('change', applyFilters);
    filterRent.addEventListener('change', applyFilters);
    filterSearch.addEventListener('input', function() {
        // 검색어 입력 시 약간의 딜레이 후 필터 적용 (성능 최적화)
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(applyFilters, 300);
    });

    // 초기화 버튼
    filterReset.addEventListener('click', function() {
        filterBrand.value = '';
        filterModel.value = '';
        filterModel.innerHTML = '<option value="">전체</option>';
        filterDeposit.value = '';
        filterRent.value = '';
        filterSearch.value = '';
        applyFilters();
    });
});
</script>