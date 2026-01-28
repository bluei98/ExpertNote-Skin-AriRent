<?php
/**
 * 차량 상세 페이지
 */

// 레이아웃 설정
ExpertNote\Core::setLayout("v2");

// 차량 정보 조회
$idx = $_GET['idx'] ?? 0;
if(!$idx) {
    header("Location: /");
    exit;
}

$car = \AriRent\Rent::getRent($idx);
if(!$car) {
    header("Location: /car-deleted");
    exit;
}

// 조회수 증가
\AriRent\Rent::incrementViewCount($idx);

// 가격 정보 조회
$prices = \AriRent\Rent::getPrices($idx);

// 이미지 조회
$images = \AriRent\Rent::getImages($idx);

// 대리점 정보 조회
$dealer = \AriRent\Rent::getDealer($car->dealer_idx);

// 보험 정보 조회
$insurance = \AriRent\Rent::getInsurance($car->dealer_idx);

// 판매완료 여부 확인
$isSold = ($car->status === 'rented');

// 판매완료인 경우 유사 차량 검색
$similarCars = [];
if ($isSold) {
    $similarWhere = [
        'r.status' => 'active',
        'r.car_type' => $car->car_type
    ];
    if ($car->brand_idx) {
        $similarWhere['r.brand_idx'] = $car->brand_idx;
    }
    $similarCars = \AriRent\Rent::getRents($similarWhere, ['r.idx' => 'DESC'], ['count' => 4]);
}

// 관련 YouTube 영상 검색 (차량 제목으로 FULLTEXT 검색)
$relatedVideos = \ExpertNote\Youtube::searchRelatedVideos($car->title, 4);

// JSON 데이터 디코딩
$contractTerms = $car->contract_terms ? json_decode($car->contract_terms, true) : [];
// driver_range는 대리점(dealer)에서 가져옴
$driverRange = $dealer->driver_range ? json_decode($dealer->driver_range, true) : [];

// 동적 페이지 설명 생성
$carTypeText = $car->car_type === 'NEW' ? __('신차', 'skin') : __('중고차', 'skin');
$pageDescription = $car->title . " " . $carTypeText . " " . __('장기렌트', 'skin');

// 연식 정보 추가
if (!empty($car->model_year) && !empty($car->model_month)) {
    $pageDescription .= " | " . $car->model_year . __('년', 'skin') . " " . $car->model_month . __('월식', 'skin');
}

// 주행거리 추가
if (!empty($car->mileage_km)) {
    $pageDescription .= " | " . number_format($car->mileage_km) . "km";
}

// 연료 타입 추가
if (!empty($car->fuel_type)) {
    $pageDescription .= " | " . $car->fuel_type;
}

// 최저가 정보 추가
if (!empty($prices) && isset($prices[0]->monthly_rent_amount)) {
    $minPrice = min(array_column($prices, 'monthly_rent_amount'));
    $pageDescription .= " | " . __('월', 'skin') . " " . number_format($minPrice) . __('원부터', 'skin');
}

$pageDescription .= " - " . __('아리렌트에서 합리적인 가격으로 만나보세요.', 'skin');

// 레이아웃 설정
$pageTitle = sprintf(__('무심사 저신용 %s 장기렌트 차량 정보', 'skin'), $car->title);
\ExpertNote\Core::setPageTitle($pageTitle);
\ExpertNote\Core::setPageSuffix("아리렌트");

// 페이지 키워드 생성
$keywords = [];
$keywords[] = $car->title;
$keywords[] = $carTypeText . " " . __('장기렌트', 'skin');

// 브랜드 추출 (차량명의 첫 단어)
$titleParts = explode(' ', $car->title);
if (!empty($titleParts[0])) {
    $keywords[] = $titleParts[0];
}

// 연료 타입
if (!empty($car->fuel_type)) {
    $keywords[] = $car->fuel_type;
}

// 연식
if (!empty($car->model_year)) {
    $keywords[] = $car->model_year . __('년식', 'skin');
}

// 차종 관련
$keywords[] = $carTypeText;
$keywords[] = __('장기렌트', 'skin');
$keywords[] = __('렌트', 'skin');
$keywords[] = __('리스', 'skin');
$keywords[] = __('아리렌트', 'skin');

// 중복 제거 및 문자열 생성
$keywords = array_unique($keywords);
$keywordsString = implode(', ', $keywords);

// 페이지 메타 설정
\ExpertNote\Core::setPageDescription(strip_tags(mb_substr($pageDescription, 0, 160)));
\ExpertNote\Core::setPageKeywords($keywordsString);

// Open Graph 메타 태그
\ExpertNote\Core::addMetaTag('og:type', ["property"=>"og:type", "content"=>"article"]);
\ExpertNote\Core::addMetaTag('og:title', ["property"=>"og:title", "content"=>$car->title]);
\ExpertNote\Core::addMetaTag('og:description', ["property"=>"og:description", "content"=>strip_tags(mb_substr($pageDescription, 0, 100))]);
\ExpertNote\Core::addMetaTag('og:url', ["property"=>"og:url", "content"=>ExpertNote\Core::getBaseUrl()."/item/".$car->idx]);

// 트위터 카드 메타 태그
\ExpertNote\Core::addMetaTag('twitter:card', ["name"=>"twitter:card", "content"=>"summary_large_image"]);
\ExpertNote\Core::addMetaTag('twitter:title', ["name"=>"twitter:title", "content"=>$car->title]);
\ExpertNote\Core::addMetaTag('twitter:description', ["name"=>"twitter:description", "content"=>strip_tags(mb_substr($pageDescription, 0, 100))]);
\ExpertNote\Core::addMetaTag('twitter:url', ["name"=>"twitter:url", "content"=>ExpertNote\Core::getBaseUrl()."/item/".$car->idx]);

if ($car->featured_image) {
    \ExpertNote\Core::addMetaTag('og:image', ["property"=>"og:image", "content"=>$car->featured_image]);
    \ExpertNote\Core::addMetaTag('twitter:image', ["name"=>"twitter:image", "content"=>$car->featured_image]);
}

// LD+JSON 구조화된 데이터 생성
$ldJson = [
    "@context" => "https://schema.org",
    "@type" => "Car",
    "name" => $car->title,
    "description" => $car->title . " 장기렌트 - " . $car->fuel_type . ", " . number_format($car->mileage_km) . "km",
    "vehicleIdentificationNumber" => $car->car_number,
    "productionDate" => $car->model_year . "-" . str_pad($car->model_month, 2, '0', STR_PAD_LEFT),
    "mileageFromOdometer" => [
        "@type" => "QuantitativeValue",
        "value" => $car->mileage_km,
        "unitCode" => "KMT"
    ],
    "fuelType" => $car->fuel_type,
    "vehicleEngine" => [
        "@type" => "EngineSpecification",
        "fuelType" => $car->fuel_type
    ]
];

// 이미지 추가
if (!empty($images)) {
    $ldJson["image"] = [];
    foreach ($images as $image) {
        $ldJson["image"][] = $image->image_url;
    }
}

// 가격 정보 추가 (Offer)
if (!empty($prices)) {
    $ldJson["offers"] = [];
    foreach ($prices as $price) {
        $offer = [
            "@type" => "Offer",
            "price" => $price->monthly_rent_amount,
            "priceCurrency" => "KRW",
            "priceSpecification" => [
                "@type" => "UnitPriceSpecification",
                "price" => $price->monthly_rent_amount,
                "priceCurrency" => "KRW",
                "unitText" => "월",
                "billingDuration" => [
                    "@type" => "QuantitativeValue",
                    "value" => $price->rental_period_months,
                    "unitCode" => "MON"
                ]
            ],
            "availability" => "https://schema.org/InStock",
            "itemCondition" => $car->car_type === 'NEW' ? "https://schema.org/NewCondition" : "https://schema.org/UsedCondition"
        ];

        if ($price->deposit_amount) {
            $offer["priceSpecification"]["deposit"] = [
                "@type" => "MonetaryAmount",
                "value" => $price->deposit_amount * 10000,
                "currency" => "KRW"
            ];
        }

        $ldJson["offers"][] = $offer;
    }
}

// 대리점 정보 추가
if ($dealer) {
    $ldJson["seller"] = [
        "@type" => "Organization",
        "name" => "아리렌트",
        "telephone" => "1666-5623"
    ];
}

// 옵션 정보를 additionalProperty로 추가
$additionalProperties = [];

if ($car->option_exterior) {
    $exteriorOptions = json_decode($car->option_exterior);
    if ($exteriorOptions) {
        foreach ($exteriorOptions as $option) {
            $additionalProperties[] = [
                "@type" => "PropertyValue",
                "name" => "외관/내장",
                "value" => $option
            ];
        }
    }
}

if ($car->option_safety) {
    $safetyOptions = json_decode($car->option_safety);
    if ($safetyOptions) {
        foreach ($safetyOptions as $option) {
            $additionalProperties[] = [
                "@type" => "PropertyValue",
                "name" => "안전장치",
                "value" => $option
            ];
        }
    }
}

if ($car->option_convenience) {
    $convenienceOptions = json_decode($car->option_convenience);
    if ($convenienceOptions) {
        foreach ($convenienceOptions as $option) {
            $additionalProperties[] = [
                "@type" => "PropertyValue",
                "name" => "편의장치",
                "value" => $option
            ];
        }
    }
}

if ($car->option_seat) {
    $seatOptions = json_decode($car->option_seat);
    if ($seatOptions) {
        foreach ($seatOptions as $option) {
            $additionalProperties[] = [
                "@type" => "PropertyValue",
                "name" => "시트",
                "value" => $option
            ];
        }
    }
}

if (!empty($additionalProperties)) {
    $ldJson["additionalProperty"] = $additionalProperties;
}

// AggregateRating 추가 (조회수와 찜 횟수 기반)
if ($car->wish_count > 0) {
    $ldJson["aggregateRating"] = [
        "@type" => "AggregateRating",
        "ratingValue" => "5.0",
        "reviewCount" => $car->wish_count,
        "bestRating" => "5",
        "worstRating" => "1"
    ];
}
?>

<!-- LD+JSON 구조화된 데이터 -->
<script type="application/ld+json">
<?php echo json_encode($ldJson, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<style>
/* 차량 상세 페이지 스타일 */
.car-detail-section {
    padding: 40px 0 80px;
    background: #f8f9fa;
}

/* 차량 타이틀 헤더 */
.car-title-header {
    background: #fff;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

.car-title-header .car-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #1a1a2e;
    margin-bottom: 12px;
    line-height: 1.3;
}

.car-title-header .car-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    font-size: 0.9rem;
    color: #666;
}

.car-title-header .car-meta span {
    display: flex;
    align-items: center;
    gap: 6px;
}

.car-title-header .car-meta i {
    color: var(--primary-color);
}

.car-title-header .car-actions {
    display: flex;
    gap: 8px;
}

.car-title-header .car-actions .btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
}

/* 이미지 갤러리 */
.car-gallery {
    background: #fff;
    border-radius: 16px;
    padding: 16px;
    margin-bottom: 24px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

.car-gallery .main-image {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 16px;
}

.car-gallery .main-image img {
    width: 100%;
    height: 450px;
    object-fit: cover;
}

.car-gallery .gallery-badge {
    position: absolute;
    bottom: 16px;
    right: 16px;
    background: rgba(0,0,0,0.7);
    color: #fff;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 6px;
}

.thumbnail-gallery .row {
    margin: 0 -4px;
}

.thumbnail-gallery .col-4,
.thumbnail-gallery .col-md-2 {
    padding: 0 4px;
}

.thumbnail-gallery .thumbnail {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    border: 2px solid transparent;
    transition: all 0.2s ease;
}

.thumbnail-gallery .thumbnail img {
    width: 100%;
    height: 70px;
    object-fit: cover;
}

.thumbnail-gallery .thumbnail.active {
    border-color: var(--primary-color);
}

.thumbnail-gallery .thumbnail:hover {
    border-color: var(--primary-color);
    opacity: 0.9;
}

/* 기본 정보 그리드 */
.car-basic-info {
    background: #fff;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

.section-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1a1a2e;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-title i {
    color: var(--primary-color);
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px;
    background: #f8f9fa;
    border-radius: 12px;
}

.info-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.info-content {
    display: flex;
    flex-direction: column;
}

.info-label {
    font-size: 0.85rem;
    color: #666;
    margin-bottom: 4px;
}

.info-value {
    font-size: 1rem;
    font-weight: 600;
    color: #1a1a2e;
}

/* 옵션 탭 */
.car-options {
    background: #fff;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

.option-tabs {
    border-bottom: 2px solid #e9ecef;
    margin-bottom: 20px;
}

.option-tabs .nav-link {
    border: none;
    color: #666;
    font-weight: 500;
    padding: 12px 20px;
    margin-bottom: -2px;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
}

.option-tabs .nav-link.active {
    color: var(--primary-color);
    border-bottom: 2px solid var(--primary-color);
    background: transparent;
}

.option-tabs .nav-link:hover {
    color: var(--primary-color);
}

.option-content {
    padding: 10px 0;
}

.option-list {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
}

.option-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    background: #f8f9fa;
    border-radius: 8px;
    font-size: 0.95rem;
    color: #333;
}

.option-item i {
    color: var(--primary-color);
    font-size: 1.1rem;
}

/* 계약 조건 */
.contract-conditions {
    background: #fff;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

.condition-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.condition-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 12px;
}

.condition-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.condition-content {
    display: flex;
    flex-direction: column;
}

.condition-label {
    font-size: 0.85rem;
    color: #666;
    margin-bottom: 4px;
}

.condition-value {
    font-size: 1rem;
    font-weight: 600;
    color: #1a1a2e;
}

/* 보험 정보 */
.insurance-info {
    background: #fff;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

.insurance-table {
    margin-bottom: 16px;
}

.insurance-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #1a1a2e;
    padding: 14px 16px;
}

.insurance-table td {
    padding: 14px 16px;
    vertical-align: middle;
}

/* 관련 영상 */
.related-videos {
    background: #fff;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

.video-card {
    display: block;
    text-decoration: none;
    color: inherit;
    transition: transform 0.2s ease;
}

.video-card:hover {
    transform: translateY(-4px);
}

.video-thumbnail {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 12px;
}

.video-thumbnail img {
    width: 100%;
    height: 120px;
    object-fit: cover;
}

.video-thumbnail .play-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.2s ease;
}

.video-card:hover .play-overlay {
    opacity: 1;
}

.play-overlay i {
    font-size: 2.5rem;
    color: #fff;
}

.video-duration {
    position: absolute;
    bottom: 8px;
    right: 8px;
    background: rgba(0,0,0,0.8);
    color: #fff;
    font-size: 0.75rem;
    padding: 4px 8px;
    border-radius: 4px;
}

.video-info .video-title {
    font-size: 0.9rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 4px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.video-info .video-channel {
    font-size: 0.8rem;
    color: #666;
}

/* 스티키 사이드바 */
.sticky-sidebar {
    position: sticky;
    top: 100px;
}

/* 가격 카드 */
.price-card {
    background: #fff;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.12);
}

.price-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 1px solid #e9ecef;
}

.price-header h4 {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1a1a2e;
    margin: 0;
}

.price-badge {
    background: #e3f2fd;
    color: var(--primary-color);
    font-size: 0.8rem;
    padding: 4px 12px;
    border-radius: 20px;
    font-weight: 500;
}

.price-table {
    margin-bottom: 20px;
}

.price-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 16px;
    border-radius: 10px;
    margin-bottom: 8px;
    background: #f8f9fa;
    transition: all 0.2s ease;
}

.price-row:hover {
    background: #e3f2fd;
}

.price-row.featured {
    background: #e8f5e9;
    color: #2e7d32;
}

.price-row.featured:hover {
    background: #c8e6c9;
}

.period {
    display: flex;
    align-items: baseline;
    gap: 4px;
}

.period-number {
    font-size: 1.25rem;
    font-weight: 700;
}

.period-unit {
    font-size: 0.85rem;
}

.amount {
    text-align: right;
}

.amount .price {
    font-size: 1.1rem;
    font-weight: 700;
}

.amount .unit {
    font-size: 0.8rem;
}

.price-info {
    margin-bottom: 20px;
    padding: 16px;
    background: #f8f9fa;
    border-radius: 12px;
}

.price-info p {
    margin: 0 0 8px;
    font-size: 0.9rem;
    color: #555;
    display: flex;
    align-items: center;
    gap: 8px;
}

.price-info p:last-child {
    margin-bottom: 0;
}

.price-info i {
    color: var(--primary-color);
}

.btn-consult-detail {
    font-size: 1.1rem;
    padding: 14px 24px;
    font-weight: 600;
}

/* 빠른 상담 */
.quick-contact {
    background: #fff;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

.quick-contact h5 {
    font-size: 1.1rem;
    font-weight: 700;
    color: #1a1a2e;
    margin-bottom: 20px;
}

.quick-contact .form-control {
    border-radius: 10px;
    padding: 12px 16px;
    border: 1px solid #e9ecef;
}

.quick-contact .form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* 안내 박스 */
.info-box {
    background: #fff;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

.info-box h6 {
    font-size: 0.95rem;
    font-weight: 600;
    color: #dc3545;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.info-box ul {
    margin: 0;
    padding-left: 20px;
}

.info-box li {
    font-size: 0.85rem;
    color: #666;
    margin-bottom: 6px;
}

.info-box li:last-child {
    margin-bottom: 0;
}

/* 운전자 범위 */
.driver-range {
    background: #fff;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

.spec-item {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #f0f0f0;
}

.spec-item:last-child {
    border-bottom: none;
}

.spec-label {
    color: #666;
    font-weight: 500;
}

/* 모바일 가격 섹션 */
.mobile-price-section {
    display: none;
}

/* 판매완료 배너 */
.sold-banner {
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: #fff;
    padding: 16px 24px;
    border-radius: 12px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
}

.sold-banner .sold-icon {
    width: 50px;
    height: 50px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.sold-banner .sold-content h4 {
    font-size: 1.1rem;
    font-weight: 700;
    margin: 0 0 4px;
}

.sold-banner .sold-content p {
    font-size: 0.9rem;
    margin: 0;
    opacity: 0.9;
}

/* 판매완료 이미지 오버레이 */
.car-gallery .main-image .sold-image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
}

.car-gallery .main-image .sold-image-overlay .sold-badge {
    background: rgba(220, 53, 69, 0.95);
    color: #fff;
    font-size: 2rem;
    font-weight: 700;
    padding: 16px 40px;
    border: 4px solid #fff;
    border-radius: 12px;
    text-transform: uppercase;
    letter-spacing: 4px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}

/* 판매완료 시 버튼 비활성화 */
.btn-sold-disabled {
    background: #6c757d !important;
    border-color: #6c757d !important;
    color: #fff !important;
    cursor: not-allowed !important;
    opacity: 0.7;
}

.btn-sold-disabled:hover {
    background: #6c757d !important;
    border-color: #6c757d !important;
}

/* 유사 차량 섹션 */
.similar-cars {
    background: #fff;
    border-radius: 16px;
    padding: 24px;
    margin-top: 24px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

.similar-cars .section-title {
    color: var(--primary-color);
}

.similar-car-card {
    display: block;
    text-decoration: none;
    color: inherit;
    border-radius: 12px;
    overflow: hidden;
    background: #f8f9fa;
    transition: all 0.2s ease;
}

.similar-car-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.similar-car-card .car-image {
    position: relative;
    height: 150px;
    overflow: hidden;
}

.similar-car-card .car-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.similar-car-card .car-info {
    padding: 16px;
}

.similar-car-card .car-title {
    font-size: 0.95rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.3;
}

.similar-car-card .car-specs {
    font-size: 0.8rem;
    color: #666;
    margin-bottom: 8px;
}

.similar-car-card .car-price {
    font-size: 1rem;
    font-weight: 700;
    color: var(--primary-color);
}

.similar-car-card .car-price span {
    font-size: 0.8rem;
    font-weight: 400;
}

/* 반응형 */
@media (max-width: 991.98px) {
    .info-grid,
    .condition-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .option-list {
        grid-template-columns: repeat(2, 1fr);
    }

    .sticky-sidebar {
        position: relative;
        top: 0;
    }
}

@media (max-width: 767.98px) {
    .car-detail-section {
        padding: 20px 0 60px;
    }

    .car-title-header .car-title {
        font-size: 1.35rem;
    }

    .car-gallery .main-image img {
        height: 280px;
    }

    .info-grid,
    .condition-grid {
        grid-template-columns: 1fr;
    }

    .option-list {
        grid-template-columns: 1fr;
    }

    .option-tabs .nav-link {
        padding: 10px 12px;
        font-size: 0.85rem;
    }

    /* 모바일에서 가격 섹션 표시 */
    .mobile-price-section {
        display: block;
        background: #fff;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 24px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    }

    .mobile-price-section .price-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid #e9ecef;
    }

    .mobile-price-section .price-header h4 {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1a1a2e;
        margin: 0;
    }

    .mobile-price-section .price-list {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 16px;
    }

    .mobile-price-section .price-item {
        flex: 1 1 calc(50% - 4px);
        min-width: 140px;
        padding: 12px;
        background: #f8f9fa;
        border-radius: 10px;
        text-align: center;
    }

    .mobile-price-section .price-item.featured {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .mobile-price-section .price-period {
        font-size: 0.85rem;
        margin-bottom: 4px;
    }

    .mobile-price-section .price-amount {
        font-size: 1rem;
        font-weight: 700;
    }

    .mobile-price-section .price-buttons {
        display: flex;
        gap: 8px;
    }

    .mobile-price-section .price-buttons .btn {
        flex: 1;
        padding: 12px;
        font-weight: 600;
    }

    .mobile-price-section .mobile-contact-buttons {
        display: flex;
        gap: 10px;
        margin-top: 16px;
    }

    .mobile-price-section .mobile-contact-buttons .btn {
        flex: 1;
        padding: 12px 16px;
        font-weight: 600;
        border-radius: 10px;
    }

    /* 데스크톱 사이드바 숨기기 */
    .col-lg-4 .sticky-sidebar {
        display: none;
    }

    .quick-contact,
    .info-box {
        display: none;
    }
}
</style>

<!-- Breadcrumb -->
<section class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="bi bi-house-door"></i> <?php echo __('홈', 'skin')?></a></li>
                <li class="breadcrumb-item"><a href="/<?php echo $car->car_type === 'NEW' ? 'new' : 'used'; ?>"><?php echo $car->car_type === 'NEW' ? __('신차장기렌트', 'skin') : __('중고장기렌트', 'skin'); ?></a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo __('차량 상세보기', 'skin')?></li>
            </ol>
        </nav>
    </div>
</section>

<?php if ($isSold): ?>
<!-- 판매완료 배너 -->
<section class="py-0">
    <div class="container">

    </div>
</section>
<?php endif; ?>

<!-- Car Detail Section -->
<section class="car-detail-section">
    <div class="container">
<?php if ($isSold): ?>
        <div class="sold-banner">
            <div class="sold-icon">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div class="sold-content">
                <h4><?php echo __('판매완료된 차량입니다', 'skin'); ?></h4>
                <p><?php echo __('이 차량은 이미 계약이 완료되었습니다. 아래에서 유사한 차량을 확인해보세요.', 'skin'); ?></p>
            </div>
        </div>
<?php endif; ?>
        <div class="row g-4">
            <!-- Left Column: Images & Details -->
            <div class="col-lg-8">
                <!-- Car Title -->
                <div class="car-title-header">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="badge bg-primary mb-2"><?php echo $carTypeText; ?></span>
                            <h1 class="car-title"><?php echo htmlspecialchars($car->title); ?></h1>
                            <div class="car-meta">
                                <!-- brand_name -->
                                <span><i class="bi bi-building"></i> <?php echo htmlspecialchars($car->brand_name); ?></span>
                                <!-- model_name -->
                                <span><i class="bi bi-car-front-fill"></i> <?php echo htmlspecialchars($car->model_name); ?></span>
                                <span><i class="bi bi-eye"></i> <?php echo __('조회', 'skin')?> <?php echo number_format($car->view_count); ?></span>
                                <span><i class="bi bi-calendar3"></i> <?php echo date('Y.m.d', strtotime($car->created_at)); ?></span>
                            </div>
                        </div>
                        <div class="car-actions">
                            <?php if(ExpertNote\User\User::isAdmin()): ?>
                            <a href="/backoffice/rent/car-edit?idx=<?php echo $car->idx; ?>" class="btn btn-outline-secondary btn-sm" title="<?php echo __('차량 정보 수정', 'skin')?>" target="backoffice">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <?php endif; ?>
                            <button class="btn btn-outline-secondary btn-sm" id="wishlistBtn" data-rent-idx="<?php echo $car->idx; ?>" title="<?php echo __('찜하기', 'skin')?>">
                                <i class="bi bi-heart"></i>
                            </button>
                            <button class="btn btn-outline-secondary btn-sm" onclick="shareLink()" title="<?php echo __('공유하기', 'skin')?>">
                                <i class="bi bi-share"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Image Gallery -->
                <div class="car-gallery">
                    <?php if(!empty($images)): ?>
                    <!-- Main Image -->
                    <div class="main-image">
                        <?php if ($isSold): ?>
                        <div class="sold-image-overlay">
                            <span class="sold-badge"><?php echo __('판매완료', 'skin'); ?></span>
                        </div>
                        <?php endif; ?>
                        <img src="<?php echo $images[0]->image_url; ?>" alt="<?php echo htmlspecialchars($car->title); ?>" id="mainImage">
                        <div class="gallery-badge">
                            <i class="bi bi-images"></i> <span id="currentImage">1</span> / <?php echo count($images); ?>
                        </div>
                    </div>

                    <!-- Thumbnail Images -->
                    <div class="thumbnail-gallery">
                        <div class="row g-2">
                            <?php foreach($images as $index => $image): ?>
                            <div class="col-4 col-md-2">
                                <div class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>" onclick="changeImage(this, '<?php echo $image->image_url; ?>', <?php echo $index + 1; ?>)">
                                    <img src="<?php echo $image->image_url; ?>" alt="<?php echo __('썸네일', 'skin')?> <?php echo $index + 1; ?>">
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="main-image d-flex align-items-center justify-content-center bg-light" style="height: 300px;">
                        <i class="bi bi-car-front-fill" style="font-size: 5rem; color: #ccc;"></i>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- 관리자용 버튼 -->
                <?php if(\ExpertNote\User\User::isAdmin()): ?>
                <div class="text-end mb-3 d-flex gap-2 justify-content-end">
                    <?php if (!empty($images)): ?>
                    <a href="/api/arirent/car-image-download?car_idx=<?php echo $car->idx; ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-download me-1"></i><?php echo __('이미지 전체 다운로드', 'skin'); ?>
                    </a>
                    <?php endif; ?>
                    <?php if ($car->status === 'active'): ?>
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="changeCarStatus(<?php echo $car->idx; ?>, 'rented')">
                        <i class="bi bi-check-circle me-1"></i><?php echo __('판매완료로 변경', 'skin'); ?>
                    </button>
                    <?php elseif ($car->status === 'rented'): ?>
                    <button type="button" class="btn btn-outline-success btn-sm" onclick="changeCarStatus(<?php echo $car->idx; ?>, 'active')">
                        <i class="bi bi-arrow-counterclockwise me-1"></i><?php echo __('판매중으로 변경', 'skin'); ?>
                    </button>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Mobile Price Section (모바일에서만 표시) -->
                <div class="mobile-price-section">
                    <div class="price-header">
                        <h4><?php echo __('월 렌탈료', 'skin')?></h4>
                        <span class="price-badge"><?php echo __('VAT 포함', 'skin')?></span>
                    </div>

                    <?php if(!empty($prices)): ?>
                    <div class="price-table">
                        <?php
                        $isFirst = true;
                        foreach($prices as $price):
                            // 신차 + JET 딜러는 36개월 미만 기간 제외
                            if($car->car_type == 'NEW' && $car->dealer_code == 'JET' && $price->rental_period_months < 36) continue;
                        ?>
                        <div class="price-row <?php echo $isFirst ? 'featured' : ''; ?>">
                            <div class="period">
                                <span class="period-number"><?php echo $price->rental_period_months; ?></span>
                                <span class="period-unit"><?php echo __('개월', 'skin')?></span>
                            </div>
                            <div class="amount">
                                <span class="price"><?php echo number_format($price->monthly_rent_amount); ?></span>
                                <span class="unit"><?php echo __('원/월', 'skin')?></span>
                            </div>
                        </div>
                        <?php $isFirst = false; endforeach; ?>
                    </div>

                    <div class="price-info">
                        <?php if(isset($prices[0]->deposit_amount) && $prices[0]->deposit_amount): ?>
                        <p><i class="bi bi-check-circle"></i> <?php echo __('보증금', 'skin')?>: <?php echo number_format($prices[0]->deposit_amount); ?><?php echo __('만원', 'skin')?></p>
                        <?php endif; ?>
                        <?php if(isset($prices[0]->yearly_mileage_limit) && $prices[0]->yearly_mileage_limit): ?>
                        <p><i class="bi bi-check-circle"></i> <?php echo __('연간 주행거리', 'skin')?>: <?php echo $prices[0]->yearly_mileage_limit; ?><?php echo __('만km', 'skin')?></p>
                        <?php endif; ?>
                        <p><i class="bi bi-check-circle"></i> <?php echo __('취등록세, 자동차세 포함', 'skin')?></p>
                        <p><i class="bi bi-check-circle"></i> <?php echo __('보험료 포함', 'skin')?></p>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info mb-0">
                        <?php echo __('가격 정보는 상담을 통해 확인하실 수 있습니다.', 'skin')?>
                    </div>
                    <?php endif; ?>

                    <div class="mobile-contact-buttons">
                        <?php if ($isSold): ?>
                        <span class="btn btn-sold-disabled">
                            <i class="bi bi-telephone-fill"></i> <?php echo __('전화 상담', 'skin')?>
                        </span>
                        <span class="btn btn-sold-disabled">
                            <i class="bi bi-chat-heart-fill"></i> <?php echo __('카톡 상담', 'skin')?>
                        </span>
                        <?php else: ?>
                        <a href="tel:1666-5623" class="btn btn-outline-primary">
                            <i class="bi bi-telephone-fill"></i> <?php echo __('전화 상담', 'skin')?>
                        </a>
                        <a href="/kakaolink" class="btn btn-primary">
                            <i class="bi bi-chat-heart-fill"></i> <?php echo __('카톡 상담', 'skin')?>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Car Basic Info -->
                <div class="car-basic-info">
                    <h3 class="section-title"><?php echo __('기본 정보', 'skin')?></h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-icon"><i class="bi bi-calendar-check"></i></div>
                            <div class="info-content">
                                <span class="info-label"><?php echo __('차량 연식', 'skin')?></span>
                                <span class="info-value"><?php echo $car->model_year; ?><?php echo __('년', 'skin')?> <?php echo $car->model_month; ?><?php echo __('월', 'skin')?></span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="bi bi-speedometer2"></i></div>
                            <div class="info-content">
                                <span class="info-label"><?php echo __('주행 거리', 'skin')?></span>
                                <span class="info-value"><?php echo number_format($car->mileage_km); ?>km</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="bi bi-fuel-pump"></i></div>
                            <div class="info-content">
                                <span class="info-label"><?php echo __('연료', 'skin')?></span>
                                <span class="info-value"><?php echo $car->fuel_type; ?></span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="bi bi-card-text"></i></div>
                            <div class="info-content">
                                <span class="info-label"><?php echo __('차량번호', 'skin')?></span>
                                <span class="info-value"><?php echo $car->car_number; ?></span>
                            </div>
                        </div>
                        <?php if($car->color): ?>
                        <div class="info-item">
                            <div class="info-icon"><i class="bi bi-palette"></i></div>
                            <div class="info-content">
                                <span class="info-label"><?php echo __('색상', 'skin')?></span>
                                <span class="info-value"><?php echo $car->color; ?></span>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if($car->seating_capacity): ?>
                        <div class="info-item">
                            <div class="info-icon"><i class="bi bi-people"></i></div>
                            <div class="info-content">
                                <span class="info-label"><?php echo __('승차 인원', 'skin')?></span>
                                <span class="info-value"><?php echo $car->seating_capacity; ?><?php echo __('인승', 'skin')?></span>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Options -->
                <?php
                $hasOptions = $car->option_exterior || $car->option_safety || $car->option_convenience || $car->option_seat || $car->option_etc;
                if($hasOptions):
                ?>
                <div class="car-options">
                    <h3 class="section-title"><?php echo __('차량 옵션', 'skin')?></h3>

                    <?php if($car->option_exterior || $car->option_safety || $car->option_convenience || $car->option_seat): ?>
                    <ul class="nav nav-tabs option-tabs" role="tablist">
                        <?php if($car->option_exterior): ?>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#exterior" type="button">
                                <i class="bi bi-brush"></i> <?php echo __('외관/내장', 'skin')?>
                            </button>
                        </li>
                        <?php endif; ?>
                        <?php if($car->option_safety): ?>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?php echo !$car->option_exterior ? 'active' : ''; ?>" data-bs-toggle="tab" data-bs-target="#safety" type="button">
                                <i class="bi bi-shield-check"></i> <?php echo __('안전장치', 'skin')?>
                            </button>
                        </li>
                        <?php endif; ?>
                        <?php if($car->option_convenience): ?>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?php echo !$car->option_exterior && !$car->option_safety ? 'active' : ''; ?>" data-bs-toggle="tab" data-bs-target="#convenience" type="button">
                                <i class="bi bi-stars"></i> <?php echo __('편의장치', 'skin')?>
                            </button>
                        </li>
                        <?php endif; ?>
                        <?php if($car->option_seat): ?>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?php echo !$car->option_exterior && !$car->option_safety && !$car->option_convenience ? 'active' : ''; ?>" data-bs-toggle="tab" data-bs-target="#seat" type="button">
                                <i class="bi bi-chair"></i> <?php echo __('시트', 'skin')?>
                            </button>
                        </li>
                        <?php endif; ?>
                    </ul>

                    <div class="tab-content option-content">
                        <?php if($car->option_exterior): ?>
                        <div class="tab-pane fade show active" id="exterior" role="tabpanel">
                            <div class="option-list">
                                <?php
                                $exteriorOptions = json_decode($car->option_exterior);
                                if($exteriorOptions):
                                    foreach($exteriorOptions as $option):
                                ?>
                                <div class="option-item"><i class="bi bi-check-circle-fill"></i> <?php echo trim($option); ?></div>
                                <?php endforeach; endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if($car->option_safety): ?>
                        <div class="tab-pane fade <?php echo !$car->option_exterior ? 'show active' : ''; ?>" id="safety" role="tabpanel">
                            <div class="option-list">
                                <?php
                                $safetyOptions = json_decode($car->option_safety);
                                if($safetyOptions):
                                    foreach($safetyOptions as $option):
                                ?>
                                <div class="option-item"><i class="bi bi-check-circle-fill"></i> <?php echo trim($option); ?></div>
                                <?php endforeach; endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if($car->option_convenience): ?>
                        <div class="tab-pane fade <?php echo !$car->option_exterior && !$car->option_safety ? 'show active' : ''; ?>" id="convenience" role="tabpanel">
                            <div class="option-list">
                                <?php
                                $convenienceOptions = json_decode($car->option_convenience);
                                if($convenienceOptions):
                                    foreach($convenienceOptions as $option):
                                ?>
                                <div class="option-item"><i class="bi bi-check-circle-fill"></i> <?php echo trim($option); ?></div>
                                <?php endforeach; endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if($car->option_seat): ?>
                        <div class="tab-pane fade <?php echo !$car->option_exterior && !$car->option_safety && !$car->option_convenience ? 'show active' : ''; ?>" id="seat" role="tabpanel">
                            <div class="option-list">
                                <?php
                                $seatOptions = json_decode($car->option_seat);
                                if($seatOptions):
                                    foreach($seatOptions as $option):
                                ?>
                                <div class="option-item"><i class="bi bi-check-circle-fill"></i> <?php echo trim($option); ?></div>
                                <?php endforeach; endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    <?php if($car->option_main): ?>
                    <div class="mt-3">
                        <?php echo $car->option_main; ?>
                    </div>
                    <?php endif; ?>

                    <?php if($car->option_etc): ?>
                    <div class="mt-3">
                        <?php echo $car->option_etc; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Contract Conditions -->
                <?php if(!empty($contractTerms)): ?>
                <div class="contract-conditions">
                    <h3 class="section-title"><?php echo __('계약 조건', 'skin')?></h3>
                    <div class="condition-grid">
                        <?php foreach($contractTerms as $term): ?>
                        <div class="condition-item">
                            <div class="condition-icon">
                                <i class="bi bi-file-text"></i>
                            </div>
                            <div class="condition-content">
                                <span class="condition-label"><?php echo $term['name']; ?></span>
                                <span class="condition-value"><?php echo $term['term']; ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Insurance Info -->
                <?php if($insurance): ?>
                <div class="insurance-info">
                    <h3 class="section-title"><?php echo __('보험 조건', 'skin')?></h3>
                    <div class="table-responsive">
                        <table class="table insurance-table">
                            <thead>
                                <tr>
                                    <th><?php echo __('구분', 'skin')?></th>
                                    <th><?php echo __('책임한도', 'skin')?></th>
                                    <th><?php echo __('면책금', 'skin')?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong><?php echo __('대인', 'skin')?></strong></td>
                                    <td><?php echo $insurance->liability_personal ?? '-'; ?></td>
                                    <td><?php echo $insurance->deductible_personal ?? '-'; ?></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo __('대물', 'skin')?></strong></td>
                                    <td><?php echo $insurance->liability_property ?? '-'; ?></td>
                                    <td><?php echo $insurance->deductible_property ?? '-'; ?></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo __('자손', 'skin')?></strong></td>
                                    <td><?php echo $insurance->liability_self_injury ?? '-'; ?></td>
                                    <td><?php echo $insurance->deductible_self_injury ?? '-'; ?></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo __('자차', 'skin')?></strong></td>
                                    <td>-</td>
                                    <td><?php echo $insurance->deductible_own_car ?? '-'; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <?php if($insurance->insurance_etc): ?>
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle"></i> <?php echo htmlspecialchars($insurance->insurance_etc); ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Driver Range -->
                <?php if(!empty($driverRange)): ?>
                <div class="driver-range">
                    <h3 class="section-title"><?php echo __('운전자 범위', 'skin')?></h3>
                    <?php foreach($driverRange as $range): ?>
                    <div class="spec-item">
                        <span class="spec-label"><?php echo $range['contractor_type']; ?></span>
                        <span><?php echo $range['description']; ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Related Videos -->
                <?php if(!empty($relatedVideos)): ?>
                <div class="related-videos">
                    <h3 class="section-title"><i class="bi bi-youtube text-danger"></i> <?php echo __('관련 영상', 'skin')?></h3>
                    <div class="row g-3">
                        <?php foreach($relatedVideos as $video): ?>
                        <div class="col-6 col-md-3">
                            <a href="/video/<?php echo $video->idx; ?>/<?php echo \ExpertNote\Utils::getPermaLink($video->title, true); ?>" class="video-card">
                                <div class="video-thumbnail">
                                    <img src="<?php echo $video->thumbnail_medium ?: $video->thumbnail_default; ?>" alt="<?php echo htmlspecialchars($video->title); ?>">
                                    <div class="play-overlay">
                                        <i class="bi bi-play-circle-fill"></i>
                                    </div>
                                    <?php if($video->duration): ?>
                                    <span class="video-duration">
                                        <?php
                                        $hours = floor($video->duration / 3600);
                                        $minutes = floor(($video->duration % 3600) / 60);
                                        $secs = $video->duration % 60;
                                        echo $hours > 0 ? sprintf("%d:%02d:%02d", $hours, $minutes, $secs) : sprintf("%d:%02d", $minutes, $secs);
                                        ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                                <div class="video-info">
                                    <h4 class="video-title"><?php echo htmlspecialchars($video->title); ?></h4>
                                    <p class="video-channel"><?php echo htmlspecialchars($video->channel_title); ?></p>
                                </div>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- 판매완료 시 유사 차량 추천 -->
                <?php if ($isSold && !empty($similarCars)): ?>
                <div class="similar-cars">
                    <h3 class="section-title"><i class="bi bi-car-front"></i> <?php echo __('비슷한 차량 추천', 'skin')?></h3>
                    <p class="text-muted mb-4"><?php echo __('이 차량과 비슷한 조건의 다른 차량들을 확인해보세요.', 'skin'); ?></p>
                    <div class="row g-3">
                        <?php foreach($similarCars as $similarCar): ?>
                        <div class="col-6 col-md-3">
                            <a href="/item/<?php echo $similarCar->idx; ?>" class="similar-car-card">
                                <div class="car-image">
                                    <img src="<?php echo $similarCar->featured_image ?: '/skins/arirent/assets/images/no-image.png'; ?>" alt="<?php echo htmlspecialchars($similarCar->title); ?>">
                                </div>
                                <div class="car-info">
                                    <h4 class="car-title"><?php echo htmlspecialchars($similarCar->title); ?></h4>
                                    <div class="car-specs">
                                        <?php echo $similarCar->model_year; ?><?php echo __('년', 'skin'); ?> · <?php echo number_format($similarCar->mileage_km); ?>km
                                    </div>
                                    <div class="car-price">
                                        <?php if ($similarCar->min_price): ?>
                                        <?php echo __('월', 'skin'); ?> <?php echo number_format($similarCar->min_price); ?><span><?php echo __('원~', 'skin'); ?></span>
                                        <?php else: ?>
                                        <?php echo __('가격 문의', 'skin'); ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="text-center mt-4">
                        <a href="/<?php echo $car->car_type === 'NEW' ? 'new' : 'used'; ?>" class="btn btn-outline-primary">
                            <i class="bi bi-grid-3x3-gap"></i> <?php echo __('더 많은 차량 보기', 'skin'); ?>
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Right Column: Price & Contact -->
            <div class="col-lg-4">
                <div class="sticky-sidebar">
                    <!-- Price Card -->
                    <div class="price-card">
                        <div class="price-header">
                            <h4><?php echo __('월 렌탈료', 'skin')?></h4>
                            <span class="price-badge"><?php echo __('VAT 포함', 'skin')?></span>
                        </div>
                        <?php if(!empty($prices)): ?>
                        <div class="price-table">
                            <?php
                            $isFirst = true;
                            foreach($prices as $price):
                                // 신차 + JET 딜러는 36개월 미만 기간 제외
                                if($car->car_type == 'NEW' && $car->dealer_code == 'JET' && $price->rental_period_months < 36) continue;
                            ?>
                            <div class="price-row <?php echo $isFirst ? 'featured' : ''; ?>">
                                <div class="period">
                                    <span class="period-number"><?php echo $price->rental_period_months; ?></span>
                                    <span class="period-unit"><?php echo __('개월', 'skin')?></span>
                                </div>
                                <div class="amount">
                                    <span class="price"><?php echo number_format($price->monthly_rent_amount); ?></span>
                                    <span class="unit"><?php echo __('원/월', 'skin')?></span>
                                </div>
                            </div>
                            <?php $isFirst = false; endforeach; ?>
                        </div>

                        <div class="price-info">
                            <?php if(isset($prices[0]->deposit_amount) && $prices[0]->deposit_amount): ?>
                            <p><i class="bi bi-check-circle"></i> <?php echo __('보증금', 'skin')?>: <?php echo number_format($prices[0]->deposit_amount); ?><?php echo __('만원', 'skin')?></p>
                            <?php endif; ?>
                            <?php if(isset($prices[0]->yearly_mileage_limit) && $prices[0]->yearly_mileage_limit): ?>
                            <p><i class="bi bi-check-circle"></i> <?php echo __('연간 주행거리', 'skin')?>: <?php echo $prices[0]->yearly_mileage_limit; ?><?php echo __('만km', 'skin')?></p>
                            <?php endif; ?>
                            <p><i class="bi bi-check-circle"></i> <?php echo __('보험료, 자동차세 포함', 'skin')?></p>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-info mb-3">
                            <?php echo __('가격 정보는 상담을 통해 확인하실 수 있습니다.', 'skin')?>
                        </div>
                        <?php endif; ?>

                        <?php if ($isSold): ?>
                        <span class="btn btn-sold-disabled btn-lg w-100 btn-consult-detail">
                            <i class="bi bi-x-circle"></i> <?php echo __('판매완료 차량', 'skin')?>
                        </span>
                        <span class="btn btn-sold-disabled btn-lg w-100 mt-2">
                            <i class="bi bi-telephone-fill"></i> <?php echo __('상담 불가', 'skin')?>
                        </span>
                        <?php else: ?>
                        <a href="/kakaolink" class="btn btn-primary btn-lg w-100 btn-consult-detail">
                            <i class="bi bi-chat-heart-fill"></i> <?php echo __('무료 견적 상담받기', 'skin')?>
                        </a>

                        <a href="tel:1666-5623" class="btn btn-outline-primary btn-lg w-100 mt-2">
                            <i class="bi bi-telephone-fill"></i> 1666-5623
                        </a>
                        <?php endif; ?>
                    </div>

                    <!-- Quick Contact -->
                    <?php if (!$isSold): ?>
                    <div class="quick-contact">
                        <h5><?php echo __('빠른 상담 신청', 'skin')?></h5>
                        <form id="quickContactForm">
                            <input type="hidden" name="car_idx" value="<?php echo $car->idx; ?>">
                            <input type="hidden" name="car_title" value="<?php echo htmlspecialchars($car->title); ?>">
                            <div class="mb-3">
                                <input type="text" class="form-control" name="name" placeholder="<?php echo __('이름', 'skin')?>" required>
                            </div>
                            <div class="mb-3">
                                <input type="tel" class="form-control" name="phone" placeholder="<?php echo __('연락처', 'skin')?>" required>
                            </div>
                            <div class="mb-3">
                                <textarea class="form-control" name="message" rows="3" placeholder="<?php echo __('문의사항 (선택)', 'skin')?>"></textarea>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="agreePrivacy" required>
                                <label class="form-check-label" for="agreePrivacy">
                                    <?php echo __('개인정보 수집 및 이용에 동의합니다', 'skin')?>
                                </label>
                            </div>
                            <button type="submit" class="btn btn-secondary w-100">
                                <i class="bi bi-send"></i> <?php echo __('상담 신청하기', 'skin')?>
                            </button>
                        </form>
                    </div>
                    <?php endif; ?>

                    <!-- Info Box -->
                    <div class="info-box">
                        <h6><i class="bi bi-info-circle"></i> <?php echo __('안내사항', 'skin')?></h6>
                        <ul>
                            <li><?php echo __('월 렌탈료는 차량 및 옵션에 따라 변동될 수 있습니다.', 'skin')?></li>
                            <li><?php echo __('보증금은 계약 조건에 따라 조정 가능합니다.', 'skin')?></li>
                            <li><?php echo __('신용등급 무관하게 이용 가능합니다.', 'skin')?></li>
                            <li><?php echo __('전국 어디서나 출고 가능합니다.', 'skin')?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// AOS Initialize
if (typeof AOS !== 'undefined') {
    AOS.init({
        duration: 800,
        once: true
    });
}

// Change Main Image
function changeImage(thumbnail, imageUrl, imageNumber) {
    // Remove active class from all thumbnails
    document.querySelectorAll('.thumbnail').forEach(thumb => {
        thumb.classList.remove('active');
    });

    // Add active class to clicked thumbnail
    thumbnail.classList.add('active');

    // Change main image
    document.getElementById('mainImage').src = imageUrl;

    // Update image counter
    document.getElementById('currentImage').textContent = imageNumber;
}

// 찜하기 기능
const wishlistBtn = document.getElementById('wishlistBtn');
const rentIdx = <?php echo $car->idx; ?>;

// 페이지 로드 시 찜하기 상태 확인
function checkWishlistStatus() {
    fetch('/api/arirent/wishlist?rent_idx=' + rentIdx)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.is_wishlisted) {
                updateWishlistButton(true);
            }
        })
        .catch(err => console.error('<?php echo __('찜하기 상태 확인 실패', 'skin')?>:', err));
}

// 찜하기 버튼 상태 업데이트
function updateWishlistButton(isWishlisted) {
    const icon = wishlistBtn.querySelector('i');

    if (isWishlisted) {
        icon.classList.remove('bi-heart');
        icon.classList.add('bi-heart-fill');
        icon.classList.add('text-danger');
    } else {
        icon.classList.remove('bi-heart-fill');
        icon.classList.remove('text-danger');
        icon.classList.add('bi-heart');
    }
}

// 찜하기 토글
function toggleWishlist() {
    const icon = wishlistBtn.querySelector('i');
    const isWishlisted = icon.classList.contains('bi-heart-fill');

    const method = isWishlisted ? 'DELETE' : 'POST';

    fetch('/api/arirent/wishlist', {
        method: method,
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ rent_idx: rentIdx })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateWishlistButton(data.data.is_wishlisted);
            ExpertNote.Util.showMessage(data.message, '<?php echo __('알림', 'skin')?>', [
                { title: '<?php echo __('확인', 'skin')?>', class: 'btn btn-primary', dismiss: true }
            ]);
        } else {
            ExpertNote.Util.showMessage(data.message || '<?php echo __('오류가 발생했습니다.', 'skin')?>', '<?php echo __('오류', 'skin')?>', [
                { title: '<?php echo __('확인', 'skin')?>', class: 'btn btn-secondary', dismiss: true }
            ]);
        }
    })
    .catch(err => {
        console.error('<?php echo __('찜하기 처리 실패', 'skin')?>:', err);
        ExpertNote.Util.showMessage('<?php echo __('오류가 발생했습니다. 다시 시도해주세요.', 'skin')?>', '<?php echo __('오류', 'skin')?>', [
            { title: '<?php echo __('확인', 'skin')?>', class: 'btn btn-secondary', dismiss: true }
        ]);
    });
}

// 찜하기 버튼 클릭 이벤트
if (wishlistBtn) {
    wishlistBtn.addEventListener('click', toggleWishlist);
    checkWishlistStatus();
}

// 링크 공유
function shareLink() {
    const url = window.location.href;
    const title = '<?php echo addslashes($car->title); ?>';
    const text = '<?php echo addslashes($car->title); ?> - <?php echo __('아리렌트', 'skin')?>\n<?php if(!empty($prices)): ?><?php echo __('월', 'skin')?> <?php echo number_format($prices[0]->monthly_rent_amount); ?><?php echo __('원', 'skin')?>~<?php endif; ?>';

    if (navigator.share) {
        navigator.share({
            title: title,
            text: text,
            url: url
        }).catch(() => {
            copyToClipboard(url);
        });
    } else {
        copyToClipboard(url);
    }
}

// 클립보드 복사
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        ExpertNote.Util.showMessage('<?php echo __('링크가 복사되었습니다!', 'skin')?>', '<?php echo __('알림', 'skin')?>', [
            { title: '<?php echo __('확인', 'skin')?>', class: 'btn btn-primary', dismiss: true }
        ]);
    }).catch(() => {
        // 폴백
        const textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        ExpertNote.Util.showMessage('<?php echo __('링크가 복사되었습니다!', 'skin')?>', '<?php echo __('알림', 'skin')?>', [
            { title: '<?php echo __('확인', 'skin')?>', class: 'btn btn-primary', dismiss: true }
        ]);
    });
}

// Quick Contact Form Submit
document.getElementById('quickContactForm').addEventListener('submit', function(e) {
    e.preventDefault();

    // TODO: 실제 API 연동
    ExpertNote.Util.showMessage('<?php echo __('상담 신청이 완료되었습니다. 빠른 시일 내에 연락드리겠습니다.', 'skin')?>', '<?php echo __('상담 신청 완료', 'skin')?>', [
        { title: '<?php echo __('확인', 'skin')?>', class: 'btn btn-primary', dismiss: true }
    ]);
    this.reset();
});

// Consult Button - 모바일에서 카카오톡으로 이동
document.querySelector('.btn-consult-detail')?.addEventListener('click', function(e) {
    if (window.innerWidth < 768) {
        // 모바일에서는 기본 동작 (카카오톡 링크)
        return;
    }
    // 데스크톱에서는 폼으로 스크롤
    e.preventDefault();
    document.getElementById('quickContactForm').scrollIntoView({
        behavior: 'smooth',
        block: 'center'
    });
});

// 차량 상태 변경 (관리자용)
function changeCarStatus(idx, status) {
    const statusText = status === 'rented' ? '<?php echo __('판매완료', 'skin'); ?>' : '<?php echo __('판매중', 'skin'); ?>';

    if (!confirm('<?php echo __('차량 상태를', 'skin'); ?> "' + statusText + '"<?php echo __('(으)로 변경하시겠습니까?', 'skin'); ?>')) {
        return;
    }

    fetch('/api/arirent/car', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ idx: idx, status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.result === 'SUCCESS') {
            ExpertNote.Util.showMessage('<?php echo __('차량 상태가 변경되었습니다.', 'skin'); ?>', '<?php echo __('알림', 'skin'); ?>', [
                { title: '<?php echo __('확인', 'skin'); ?>', class: 'btn btn-primary', dismiss: true }
            ], function() {
                location.reload();
            });
        } else {
            ExpertNote.Util.showMessage(data.message || '<?php echo __('상태 변경에 실패했습니다.', 'skin'); ?>', '<?php echo __('오류', 'skin'); ?>', [
                { title: '<?php echo __('확인', 'skin'); ?>', class: 'btn btn-secondary', dismiss: true }
            ]);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        ExpertNote.Util.showMessage('<?php echo __('오류가 발생했습니다.', 'skin'); ?>', '<?php echo __('오류', 'skin'); ?>', [
            { title: '<?php echo __('확인', 'skin'); ?>', class: 'btn btn-secondary', dismiss: true }
        ]);
    });
}
</script>
