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
    echo "차량을 찾을 수 없습니다.";
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

// JSON 데이터 디코딩
$contractTerms = $car->contract_terms ? json_decode($car->contract_terms, true) : [];
$driverRange = $car->driver_range ? json_decode($car->driver_range, true) : [];

// 레이아웃 설정
\ExpertNote\Core::setLayout("arirent");
\ExpertNote\Core::setPageTitle($car->title . " - 아리렌트");

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
        "name" => $dealer->dealer_name,
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

<style>
    .car-detail-header {
        background: var(--primary-color);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
    }

    .car-gallery {
        position: relative;
        margin-bottom: 2rem;
    }

    .car-carousel {
        border-radius: 0;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }

    .car-carousel .carousel-inner {
        border-radius: 0;
    }

    .car-carousel .carousel-item img {
        width: 100%;
        height: 500px;
        object-fit: cover;
    }

    .car-carousel .carousel-control-prev,
    .car-carousel .carousel-control-next {
        width: 5%;
        opacity: 0.8;
    }

    .car-carousel .carousel-control-prev:hover,
    .car-carousel .carousel-control-next:hover {
        opacity: 1;
    }

    .car-carousel .carousel-control-prev-icon,
    .car-carousel .carousel-control-next-icon {
        background-color: rgba(27, 113, 215, 0.8);
        border-radius: 0;
        padding: 20px;
    }

    .car-carousel .carousel-indicators {
        display: none; /* 썸네일 갤러리로 대체 */
    }

    .carousel-image-counter {
        position: absolute;
        bottom: 1rem;
        left: 1rem;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0;
        font-size: 0.9rem;
        z-index: 10;
    }

    .price-card {
        background: var(--primary-color);
        color: white;
        border-radius: 0;
        padding: 2rem;
        margin-bottom: 1rem;
        transition: transform 0.3s;
    }

    .price-card:hover {
        transform: translateY(-5px);
    }

    .option-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        background: #f8f9fa;
        border-radius: 0;
        margin: 0.25rem;
        font-size: 0.9rem;
    }

    .sticky-sidebar {
        position: sticky;
        top: 100px;
    }

    .action-btn {
        width: 100%;
        padding: 1rem;
        font-weight: bold;
        border-radius: 0;
        margin-bottom: 0.5rem;
    }

    .spec-item {
        display: flex;
        justify-content: space-between;
        padding: 1rem 0;
        border-bottom: 1px solid #e9ecef;
    }

    .spec-item:last-child {
        border-bottom: none;
    }

    .spec-label {
        font-weight: bold;
        color: #6c757d;
    }

    .tab-content {
        padding: 2rem 0;
    }

    /* 썸네일 갤러리 */
    .thumbnail-gallery {
        display: flex;
        gap: 10px;
        margin-top: 1rem;
        overflow-x: auto;
        padding: 10px 0;
        scrollbar-width: thin;
        scrollbar-color: var(--primary-color) #f0f0f0;
    }

    .thumbnail-gallery::-webkit-scrollbar {
        height: 8px;
    }

    .thumbnail-gallery::-webkit-scrollbar-track {
        background: #f0f0f0;
    }

    .thumbnail-gallery::-webkit-scrollbar-thumb {
        background: var(--primary-color);
        border-radius: 0;
    }

    .thumbnail-item {
        flex: 0 0 auto;
        width: 100px;
        height: 80px;
        cursor: pointer;
        overflow: hidden;
        border: 3px solid transparent;
        transition: all 0.3s;
        opacity: 0.6;
    }

    .thumbnail-item:hover {
        opacity: 1;
        border-color: var(--primary-color);
    }

    .thumbnail-item.active {
        opacity: 1;
        border-color: var(--primary-color);
        box-shadow: 0 4px 8px rgba(27, 113, 215, 0.3);
    }

    .thumbnail-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    @media (max-width: 768px) {
        .car-carousel .carousel-item img {
            height: 300px;
        }

        .sticky-sidebar {
            position: relative;
            top: 0;
        }

        .carousel-image-counter {
            font-size: 0.8rem;
            padding: 0.4rem 0.8rem;
        }

        .thumbnail-item {
            width: 80px;
            height: 60px;
        }
    }
</style>

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
                <div class="d-flex justify-content-md-end gap-2">
                    <span class="badge bg-light text-dark p-2">
                        <i class="bi bi-eye"></i> 조회 <?php echo number_format($car->view_count); ?>
                    </span>
                    <span class="badge bg-light text-dark p-2">
                        <i class="bi bi-heart-fill text-danger"></i> 찜 <?php echo number_format($car->wish_count); ?>
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
                <!-- 가격 정보 -->
                <h4 class="mb-3"><i class="bi bi-cash-stack"></i> 렌트 가격</h4>
                <?php if(!empty($prices)): ?>
                    <?php foreach($prices as $price): ?>
                        <div class="price-card">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="mb-0"><?php echo $price->rental_period_months; ?>개월</h5>
                                <span class="badge bg-light text-dark">연 <?php echo $price->yearly_mileage_limit; ?>만km</span>
                            </div>
                            <div class="fs-2 fw-bold mb-2">월 <?php echo number_format($price->monthly_rent_amount); ?>원</div>
                            <?php if($price->deposit_amount): ?>
                                <div class="small">보증금: <?php echo number_format($price->deposit_amount); ?>만원</div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-info">
                        가격 정보는 상담을 통해 확인하실 수 있습니다.
                    </div>
                <?php endif; ?>

                <!-- 액션 버튼 -->
                <div class="mt-4">
                    <button class="action-btn btn btn-primary" onclick="addToWishlist()">
                        <i class="bi bi-heart-fill"></i> 찜하기
                    </button>
                    <a href="tel:010-4299-3772" class="action-btn btn btn-success">
                        <i class="bi bi-telephone-fill"></i> 전화 상담
                    </a>
                    <button class="action-btn btn btn-warning" onclick="openKakaoChat()">
                        <i class="bi bi-chat-dots-fill"></i> 카카오톡 상담
                    </button>
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

        // 선택된 썸네일이 보이도록 스크롤
        if (thumbnails[currentIndex]) {
            thumbnails[currentIndex].scrollIntoView({
                behavior: 'smooth',
                block: 'nearest',
                inline: 'center'
            });
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

// 찜하기
function addToWishlist() {
    // TODO: 찜하기 API 연동
    const carIdx = <?php echo $idx; ?>;

    fetch('/api/v1/rent/wishlist', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ idx: carIdx })
    })
    .then(response => response.json())
    .then(data => {
        if (data.result === 'SUCCESS') {
            alert('찜 목록에 추가되었습니다!');
            // 찜 개수 업데이트
            location.reload();
        } else {
            alert(data.message || '찜 추가에 실패했습니다.');
        }
    })
    .catch(() => {
        alert('찜 목록에 추가되었습니다!');
    });
}

// 카카오톡 상담
function openKakaoChat() {
    // TODO: 카카오톡 채널 URL로 변경
    const message = encodeURIComponent('<?php echo $car->title; ?> 차량 상담 문의드립니다.');
    window.open('https://pf.kakao.com/your-channel?message=' + message, '_blank');
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
