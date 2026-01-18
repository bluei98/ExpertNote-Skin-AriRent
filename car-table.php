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
</style>

<section class="container my-4">
    <table class="table table-bordered car-table">
    <colgroup>
        <col/>
        <col width="100"/>
        <col/>
        <col width="100"/>
        <col/>
        <col/>
        <col width="100"/>
        <col width="150"/>
        <col width="100"/>
    </colgroup>
    <thead>
    <tr>
        <th class="bg-light text-center">차종</th>
        <th class="bg-light text-center" colspan="4">장기렌트 목록</th>
        <th class="bg-light text-center">보증금</th>
        <th class="bg-light text-center">기간</th>
        <th class="bg-light text-center">렌트료</th>
        <th class="bg-light text-center">인수가</th>
        <th class="bg-light text-center">비고</th>
    </tr>
    </thead>
    <?php $i=0;foreach ($vehicles as $item): $i++;
    // if($i>5) break;
    ?>
    <tr data-vehicle-id="<?php echo $item->idx?>">
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
            <?php foreach($item->prices as $price): ?>
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
        <td rowspan="4" class="align-middle text-center">
        </td>
    </tr>
    <tr data-vehicle-id="<?php echo $item->idx?>">
        <td class="text-center bg-light">등급</td>
        <td class="text-center"><?php echo $item->grade ?? '-'?></td>
        <td class="align-middle text-center bg-light">주행거리</td>
        <td class="align-middle text-center"><?php echo $item->mileage_km?> km</td>
    </tr>
    <tr data-vehicle-id="<?php echo $item->idx?>">
        <td class="text-center bg-light">색상</td>
        <td class="text-center"><?php echo $item->color ?? '-'?></td>
        <td class="align-middle text-center bg-light">보험 연령</td>
        <td class="align-middle text-center">26세</td>
    </tr>
    <tr data-vehicle-id="<?php echo $item->idx?>">
        <td class="text-center bg-light">옵션</td>
        <td class="text-center" colspan="3">컨비니언스, 16인치 전면가공 휠, 스타일</td>
    </tr>

    <?php endforeach; ?>
    </table>
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
});
</script>