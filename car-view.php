<?php
/**
 * 차량 상세 페이지
 */

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

// 관련 YouTube 영상 검색 (차량 제목으로 FULLTEXT 검색)
$relatedVideos = \ExpertNote\Youtube::searchRelatedVideos($car->title, 4);

// JSON 데이터 디코딩
$contractTerms = $car->contract_terms ? json_decode($car->contract_terms, true) : [];
// driver_range는 대리점(dealer)에서 가져옴
$driverRange = $dealer->driver_range ? json_decode($dealer->driver_range, true) : [];

// 동적 페이지 설명 생성
$carTypeText = $car->car_type === 'NEW' ? '신차' : '중고차';
$pageDescription = $car->title . " " . $carTypeText . " 장기렌트";

// 연식 정보 추가
if (!empty($car->model_year) && !empty($car->model_month)) {
    $pageDescription .= " | " . $car->model_year . "년 " . $car->model_month . "월식";
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
    $pageDescription .= " | 월 " . number_format($minPrice) . "원부터";
}

$pageDescription .= " - 아리렌트에서 합리적인 가격으로 만나보세요.";

// 레이아웃 설정
\ExpertNote\Core::setPageTitle($car->title);
\ExpertNote\Core::setPageSuffix("저신용 무심사 신차 • 중고차 장기렌트 - 아리렌트");

// 페이지 키워드 생성
$keywords = [];
$keywords[] = $car->title; // 차량명
$keywords[] = $carTypeText . " 장기렌트"; // 신차/중고차 장기렌트

// 브랜드 추출 (차량명의 첫 단어)
$titleParts = explode(' ', $car->title);
if (!empty($titleParts[0])) {
    $keywords[] = $titleParts[0]; // 브랜드
}

// 연료 타입
if (!empty($car->fuel_type)) {
    $keywords[] = $car->fuel_type;
}

// 연식
if (!empty($car->model_year)) {
    $keywords[] = $car->model_year . "년식";
}

// 차종 관련
$keywords[] = $carTypeText;
$keywords[] = "장기렌트";
$keywords[] = "렌트";
$keywords[] = "리스";
$keywords[] = "아리렌트";

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
// \ExpertNote\Core::addMetaTag('og:site_name', ["property"=>"og:type", "content"=>$car->title]);

// // 트위터 카드 메타 태그
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
        "telephone" => "010-4299-3772"
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

<!-- 차량 헤더 -->
<div class="car-detail-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-3"><?php echo htmlspecialchars($car->title); ?></h1>
                <div class="d-flex flex-wrap gap-3">
                    <span><i class="bi bi-calendar"></i> <?php echo $car->model_year; ?>년 <?php echo $car->model_month; ?>월</span>
                    <span><i class="bi bi-speedometer2"></i> <?php echo number_format($car->mileage_km); ?>km</span>
                    <span><i class="bi bi-fuel-pump"></i> <?php echo $car->fuel_type; ?></span>
                    <span><i class="bi bi-card-text"></i> <?php echo $car->car_number; ?></span>
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <div class="d-flex justify-content-md-end gap-2 align-items-center">
                    <span class="badge bg-light text-dark p-2">
                        <i class="bi bi-eye"></i> 조회 <?php echo number_format($car->view_count); ?>
                    </span>
                    <span id="wishlistBtn" class="badge bg-light text-dark p-2" data-rent-idx="<?php echo $car->idx; ?>">
                        <i class="bi bi-heart-fill text-danger"></i>
                        <span class="wishlist-text">찜</span>
                        <span id="wishCount"><?php echo number_format($car->wish_count); ?></span>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mb-5">
    <div class="row">
        <!-- 메인 콘텐츠 -->
        <div class="col-lg-8">
            <!-- 이미지 캐러셀 -->
            <div class="car-gallery">
                <?php if(!empty($images)): ?>
                    <div id="carImageCarousel" class="carousel slide car-carousel" data-bs-ride="carousel">
                        <!-- 인디케이터 -->
                        <div class="carousel-indicators">
                            <?php foreach($images as $index => $image): ?>
                                <button type="button"
                                        data-bs-target="#carImageCarousel"
                                        data-bs-slide-to="<?php echo $index; ?>"
                                        <?php echo $index === 0 ? 'class="active" aria-current="true"' : ''; ?>
                                        aria-label="이미지 <?php echo $index + 1; ?>"></button>
                            <?php endforeach; ?>
                        </div>

                        <!-- 이미지 슬라이드 -->
                        <div class="carousel-inner">
                            <?php foreach($images as $index => $image): ?>
                                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                    <img src="<?php echo $image->image_url; ?>"
                                         class="d-block w-100"
                                         alt="<?php echo htmlspecialchars($car->title); ?> - 이미지 <?php echo $index + 1; ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- 이전/다음 버튼 -->
                        <button class="carousel-control-prev" type="button" data-bs-target="#carImageCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">이전</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carImageCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">다음</span>
                        </button>

                        <!-- 이미지 카운터 -->
                        <div class="carousel-image-counter">
                            <i class="bi bi-image"></i> <span id="currentImage">1</span> / <?php echo count($images); ?>
                        </div>
                    </div>

                    <!-- 썸네일 갤러리 -->
                    <div class="thumbnail-gallery">
                        <?php foreach($images as $index => $image): ?>
                            <div class="thumbnail-item <?php echo $index === 0 ? 'active' : ''; ?>"
                                 data-bs-target="#carImageCarousel"
                                 data-bs-slide-to="<?php echo $index; ?>"
                                 onclick="selectThumbnail(<?php echo $index; ?>)">
                                <img src="<?php echo $image->image_url; ?>"
                                     alt="<?php echo htmlspecialchars($car->title); ?> 썸네일 <?php echo $index + 1; ?>"
                                     loading="lazy">
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="car-carousel d-flex align-items-center justify-content-center bg-light" style="height: 500px;">
                        <i class="bi bi-car-front-fill" style="font-size: 5rem; color: #ccc;"></i>
                    </div>
                <?php endif; ?>
            </div>

            <!-- 관리자용 이미지 다운로드 버튼 -->
            <?php if(\ExpertNote\User\User::isAdmin() && !empty($images)): ?>
            <div class="text-end mb-3">
                <a href="/api/arirent/car-image-download?car_idx=<?php echo $car->idx; ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-download me-1"></i><?php echo __('이미지 전체 다운로드', 'skin'); ?>
                </a>
            </div>
            <?php endif; ?>

            <!-- 관련 YouTube 영상 -->
            <?php if(!empty($relatedVideos)): ?>
            <div class="related-videos-section mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title mb-3"><i class="bi bi-youtube text-danger"></i> <?php echo __('관련 영상', 'skin'); ?></h4>
                        <div class="video-grid">
                            <?php foreach($relatedVideos as $video): ?>
                            <a href="/video/<?php echo $video->idx; ?>/<?php echo \ExpertNote\Utils::getPermaLink($video->title, true); ?>"
                               class="video-card-link">
                                <div class="video-card">
                                    <div class="video-thumbnail">
                                        <img src="<?php echo $video->thumbnail_medium ?: $video->thumbnail_default; ?>"
                                             alt="<?php echo htmlspecialchars($video->title); ?>"
                                             loading="lazy">
                                        <div class="play-icon">
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
                                        <h6 class="video-title"><?php echo htmlspecialchars($video->title); ?></h6>
                                        <span class="video-channel"><?php echo htmlspecialchars($video->channel_title); ?></span>
                                    </div>
                                </div>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- 렌트 가격 -->
            <div class="price-section mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title mb-3"><i class="bi bi-cash-stack"></i> 렌트 가격</h4>
                        <?php if(!empty($prices)): ?>
                            <div class="price-grid">
                                <?php foreach($prices as $price): ?>
                                    <div class="price-card-mobile">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="fw-bold"><?php echo $price->rental_period_months; ?>개월</span>
                                            <span class="badge bg-light text-dark">연 <?php echo $price->yearly_mileage_limit; ?>만km</span>
                                        </div>
                                        <div class="fs-4 fw-bold text-primary mb-1">월 <?php echo number_format($price->monthly_rent_amount); ?>원</div>
                                        <?php if($price->deposit_amount): ?>
                                            <div class="small text-muted">보증금: <?php echo number_format($price->deposit_amount); ?>만원</div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <!-- 모바일용 액션 버튼 -->
                            <div class="d-flex flex-wrap gap-2 mt-3">
                                <a href="tel:010-4299-3772" class="btn btn-success flex-fill">
                                    <i class="bi bi-telephone-fill"></i> 전화 상담
                                </a>
                                <a href="http://pf.kakao.com/_ugtHn/chat" class="btn btn-warning flex-fill">
                                    <i class="bi bi-chat-dots-fill"></i> 카카오톡
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info mb-0">
                                가격 정보는 상담을 통해 확인하실 수 있습니다.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- 차량 상세 정보 -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h4 class="card-title mb-4"><i class="bi bi-info-circle"></i> 차량 상세 정보</h4>

                    <div class="spec-item">
                        <span class="spec-label">차량번호</span>
                        <span><?php echo $car->car_number; ?></span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">차량 상태</span>
                        <span><?php echo $car->car_type === 'NEW' ? '신차' : '중고차'; ?></span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">연식</span>
                        <span><?php echo $car->model_year; ?>년 <?php echo $car->model_month; ?>월</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">주행거리</span>
                        <span><?php echo number_format($car->mileage_km); ?>km</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">연료</span>
                        <span><?php echo $car->fuel_type; ?></span>
                    </div>
                </div>
            </div>

            <!-- 옵션 정보 -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h4 class="card-title mb-4"><i class="bi bi-stars"></i> 차량 옵션</h4>

<?php
if(!$car->option_exterior && !$car->option_safety && !$car->option_convenience && !$car->option_seat && !$car->option_etc):?>
                    <!-- 탭 네비게이션 -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#exterior">외관/내장</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#safety">안전장치</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#convenience">편의장치</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#seat">시트</a>
                        </li>
                    </ul>

                    <!-- 탭 콘텐츠 -->
                    <div class="tab-content">
                        <div id="exterior" class="tab-pane fade show active">
                            <?php if($car->option_exterior): ?>
                                <?php
                                $exteriorOptions = json_decode($car->option_exterior);
                                foreach($exteriorOptions as $option):
                                ?>
                                    <span class="option-badge"><i class="bi bi-check-circle text-primary"></i> <?php echo trim($option); ?></span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted">등록된 옵션이 없습니다.</p>
                            <?php endif; ?>
                        </div>

                        <div id="safety" class="tab-pane fade">
                            <?php if($car->option_safety): ?>
                                <?php
                                $safetyOptions = json_decode($car->option_safety);
                                foreach($safetyOptions as $option):
                                ?>
                                    <span class="option-badge"><i class="bi bi-shield-check text-success"></i> <?php echo trim($option); ?></span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted">등록된 옵션이 없습니다.</p>
                            <?php endif; ?>
                        </div>

                        <div id="convenience" class="tab-pane fade">
                            <?php if($car->option_convenience): ?>
                                <?php
                                $convenienceOptions = json_decode($car->option_convenience);
                                foreach($convenienceOptions as $option):
                                ?>
                                    <span class="option-badge"><i class="bi bi-gear text-info"></i> <?php echo trim($option); ?></span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted">등록된 옵션이 없습니다.</p>
                            <?php endif; ?>
                        </div>

                        <div id="seat" class="tab-pane fade">
                            <?php if($car->option_seat): ?>
                                <?php
                                $seatOptions = json_decode($car->option_seat);
                                foreach($seatOptions as $option):
                                ?>
                                    <span class="option-badge"><i class="bi bi-person-workspace text-warning"></i> <?php echo trim($option); ?></span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted">등록된 옵션이 없습니다.</p>
                            <?php endif; ?>
                        </div>
                    </div>
<?php endif;?>

<?php if($car->option_etc): ?>
                    <div>
                        <?php echo $car->option_etc?>
                    </div>
<?php endif;?>
                </div>
            </div>

            <!-- 계약 조건 -->
            <?php if(!empty($contractTerms)): ?>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h4 class="card-title mb-4"><i class="bi bi-file-text"></i> 계약 조건</h4>
                    <?php foreach($contractTerms as $key => $value):?>
                        <div class="spec-item">
                            <span class="spec-label"><?php echo $value['name']; ?></span>
                            <span><?php echo $value['term'] ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- 보험 조건 -->
            <?php if($insurance): ?>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h4 class="card-title mb-4"><i class="bi bi-shield-fill-check"></i> 보험 조건</h4>

                    <div class="row g-5">
                        <div class="col-lg-6 col-12">
                            <h6 class="mt-3 mb-3 text-primary">책임한도</h6>
                            <div class="spec-item">
                                <span class="spec-label">대인</span>
                                <span><?php echo $insurance->liability_personal ?? '-'; ?></span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-label">대물</span>
                                <span><?php echo $insurance->liability_property ?? '-'; ?></span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-label">자손</span>
                                <span><?php echo $insurance->liability_self_injury ?? '-'; ?></span>
                            </div>
                        </div>
                        <div class="col-lg-6 col-12">
                            <h6 class="mt-4 text-primary">면책금</h6>
                            <div class="spec-item">
                                <span class="spec-label">대인</span>
                                <span><?php echo $insurance->deductible_personal ?? '-'; ?></span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-label">대물</span>
                                <span><?php echo $insurance->deductible_property ?? '-'; ?></span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-label">자손</span>
                                <span><?php echo $insurance->deductible_self_injury ?? '-'; ?></span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-label">자차</span>
                                <span><?php echo $insurance->deductible_own_car ?? '-'; ?></span>
                            </div>
                        </div>
                    </div>
                    <div style="font-size: 0.9rem;">
                        <?php echo htmlspecialchars($insurance->insurance_etc); ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- 운전자 범위 -->
            <?php if(!empty($driverRange)): ?>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h4 class="card-title mb-4"><i class="bi bi-person-check"></i> 운전자 범위</h4>
                    <?php foreach($driverRange as $key => $value):?>
                        <div class="spec-item">
                            <span class="spec-label"><?php echo $value['contractor_type']; ?></span>
                            <span><?php echo $value['description']; ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- 사이드바 -->
        <div class="col-lg-4">
            <div class="sticky-sidebar">
                <!-- 액션 버튼 -->
                <div class="">
                    <button class="action-btn btn btn-primary" onclick="toggleWishlist()">
                        <i class="bi bi-heart-fill"></i> 찜하기
                    </button>
                    <a href="tel:010-4299-3772" class="action-btn btn btn-success">
                        <i class="bi bi-telephone-fill"></i> 전화 상담
                    </a>
                    <a href="http://pf.kakao.com/_ugtHn/chat" class="action-btn btn btn-warning"><i class="bi bi-chat-dots-fill"></i> 카카오톡 상담</a>
                    <button class="action-btn btn btn-outline-secondary" onclick="shareLink()">
                        <i class="bi bi-share-fill"></i> 공유하기
                    </button>
                </div>

                <!-- 주의사항 -->
                <div class="card shadow-sm mt-4">
                    <div class="card-body">
                        <h6 class="card-title text-danger"><i class="bi bi-exclamation-triangle-fill"></i> 유의사항</h6>
                        <ul class="small mb-0">
                            <li>표시된 가격은 기본 조건입니다.</li>
                            <li>실제 가격은 개인 신용 및 조건에 따라 변동될 수 있습니다.</li>
                            <li>차량 재고는 실시간으로 변동됩니다.</li>
                            <li>정확한 견적은 상담을 통해 확인해주세요.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// 캐러셀 이미지 카운터 및 썸네일 업데이트
<?php if(!empty($images)): ?>
const carCarousel = document.getElementById('carImageCarousel');
const thumbnails = document.querySelectorAll('.thumbnail-item');
const thumbnailGallery = document.querySelector('.thumbnail-gallery');

if (carCarousel) {
    carCarousel.addEventListener('slid.bs.carousel', function(event) {
        const currentIndex = event.to;

        // 이미지 카운터 업데이트
        document.getElementById('currentImage').textContent = currentIndex + 1;

        // 썸네일 active 클래스 업데이트
        thumbnails.forEach((thumb, index) => {
            if (index === currentIndex) {
                thumb.classList.add('active');
            } else {
                thumb.classList.remove('active');
            }
        });

        // 선택된 썸네일이 보이도록 갤러리 내에서만 스크롤
        if (thumbnails[currentIndex] && thumbnailGallery) {
            const thumbnail = thumbnails[currentIndex];
            const galleryRect = thumbnailGallery.getBoundingClientRect();
            const thumbnailRect = thumbnail.getBoundingClientRect();

            // 썸네일이 갤러리 영역을 벗어난 경우에만 스크롤
            if (thumbnailRect.left < galleryRect.left || thumbnailRect.right > galleryRect.right) {
                const scrollLeft = thumbnail.offsetLeft - (thumbnailGallery.offsetWidth / 2) + (thumbnail.offsetWidth / 2);
                thumbnailGallery.scrollTo({
                    left: scrollLeft,
                    behavior: 'smooth'
                });
            }
        }
    });
}

// 썸네일 클릭 함수
function selectThumbnail(index) {
    const carousel = bootstrap.Carousel.getInstance(carCarousel);
    if (carousel) {
        carousel.to(index);
    }
}
<?php endif; ?>

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
        .catch(err => console.error('찜하기 상태 확인 실패:', err));
}

// 찜하기 버튼 상태 업데이트
function updateWishlistButton(isWishlisted) {
    const icon = wishlistBtn.querySelector('i');
    const text = wishlistBtn.querySelector('.wishlist-text');
    const countBadge = document.getElementById('wishCount');

    if (isWishlisted) {
        icon.classList.remove('bi-heart');
        icon.classList.add('bi-heart-fill');
        text.textContent = '찜';
        wishlistBtn.classList.remove('btn-outline-light');
        wishlistBtn.classList.add('btn-light');
    } else {
        icon.classList.remove('bi-heart-fill');
        icon.classList.add('bi-heart');
        text.textContent = '찜';
        wishlistBtn.classList.remove('btn-light');
        wishlistBtn.classList.add('btn-outline-light');
    }
}

// 찜 수 업데이트
function updateWishCount(increment) {
    const countBadge = document.getElementById('wishCount');
    if (countBadge) {
        let currentCount = parseInt(countBadge.textContent.replace(/,/g, '')) || 0;
        currentCount += increment;
        if (currentCount < 0) currentCount = 0;
        countBadge.textContent = currentCount.toLocaleString();
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

            // 찜 수 실시간 업데이트 (새로고침 없이)
            updateWishCount(data.data.is_wishlisted ? 1 : -1);

            alert(data.message);
        } else {
            alert(data.message || '오류가 발생했습니다.');
        }
    })
    .catch(err => {
        console.error('찜하기 처리 실패:', err);
        alert('오류가 발생했습니다. 다시 시도해주세요.');
    });
}

// 찜하기 버튼 클릭 이벤트
if (wishlistBtn) {
    wishlistBtn.addEventListener('click', toggleWishlist);
    checkWishlistStatus();
}

// 카카오톡 상담
function openKakaoChat() {
    // TODO: 카카오톡 채널 URL로 변경
    const message = encodeURIComponent('<?php echo $car->title; ?> 차량 상담 문의드립니다.');
    window.open('https://pf.kakao.com/_ugtHn?message=' + message, '_blank');
}

// 링크 공유
function shareLink() {
    const url = window.location.href;
    const title = '<?php echo addslashes($car->title); ?>';
    const text = '<?php echo addslashes($car->title); ?> - 아리렌트\n<?php if(!empty($prices)): ?>월 <?php echo number_format($prices[0]->monthly_rent_amount); ?>원~<?php endif; ?>';

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
        alert('링크가 복사되었습니다!\n' + text);
    }).catch(() => {
        // 폴백
        const textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        alert('링크가 복사되었습니다!');
    });
}

// 키보드 단축키 (좌우 화살표로 이미지 네비게이션)
<?php if(!empty($images) && count($images) > 1): ?>
document.addEventListener('keydown', function(e) {
    if (e.key === 'ArrowLeft') {
        bootstrap.Carousel.getInstance(carCarousel)?.prev();
    } else if (e.key === 'ArrowRight') {
        bootstrap.Carousel.getInstance(carCarousel)?.next();
    }
});
<?php endif; ?>
</script>

<style>
/* 관련 YouTube 영상 스타일 */
.related-videos-section .video-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
}

.related-videos-section .video-card-link {
    text-decoration: none;
    color: inherit;
}

.related-videos-section .video-card {
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.related-videos-section .video-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.related-videos-section .video-thumbnail {
    position: relative;
    aspect-ratio: 16/9;
    overflow: hidden;
    background-color: #000;
}

.related-videos-section .video-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.related-videos-section .video-card:hover .video-thumbnail img {
    transform: scale(1.05);
}

.related-videos-section .play-icon {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 40px;
    color: rgba(255,255,255,0.9);
    opacity: 0;
    transition: opacity 0.2s ease;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.related-videos-section .video-card:hover .play-icon {
    opacity: 1;
}

.related-videos-section .video-duration {
    position: absolute;
    bottom: 6px;
    right: 6px;
    background: rgba(0,0,0,0.8);
    color: #fff;
    font-size: 11px;
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: 500;
}

.related-videos-section .video-info {
    padding: 10px 4px;
}

.related-videos-section .video-title {
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 4px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.4;
    max-height: 2.8em;
    color: #333;
}

.related-videos-section .video-channel {
    font-size: 11px;
    color: #666;
}

/* 태블릿에서 2열 그리드 */
@media (min-width: 576px) and (max-width: 991.98px) {
    .related-videos-section .video-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* 모바일에서 2열 그리드 */
@media (max-width: 575.98px) {
    .related-videos-section .video-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }

    .related-videos-section .video-title {
        font-size: 12px;
    }

    .related-videos-section .video-channel {
        font-size: 10px;
    }

    .related-videos-section .play-icon {
        font-size: 32px;
        opacity: 1;
    }
}

/* 가격 섹션 스타일 */
.price-section .price-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}

.price-section .price-card-mobile {
    background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%);
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 20px;
    transition: all 0.2s ease;
}

.price-section .price-card-mobile:hover {
    border-color: var(--bs-primary);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* 데스크톱에서 3열 그리드 */
@media (min-width: 992px) {
    .price-section .price-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

/* 태블릿에서 2열 그리드 */
@media (min-width: 576px) and (max-width: 991.98px) {
    .price-section .price-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* 작은 모바일에서 1열 그리드 */
@media (max-width: 575.98px) {
    .price-section .price-grid {
        grid-template-columns: 1fr;
    }

    .price-section .price-card-mobile {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
    }

    .price-section .price-card-mobile > div:first-child {
        flex: 0 0 auto;
    }

    .price-section .price-card-mobile > .fs-4 {
        flex: 1;
        text-align: right;
        margin-bottom: 0 !important;
    }

    .price-section .price-card-mobile > .small {
        flex: 0 0 100%;
        text-align: right;
        margin-top: 4px;
    }
}
</style>
