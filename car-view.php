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
?>

<style>
    .car-detail-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
    }

    .car-gallery {
        position: relative;
        margin-bottom: 2rem;
    }

    .main-image {
        width: 100%;
        height: 500px;
        object-fit: cover;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }

    .thumbnail-list {
        display: flex;
        gap: 10px;
        margin-top: 15px;
        overflow-x: auto;
        padding: 10px 0;
    }

    .thumbnail {
        width: 100px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s;
        border: 3px solid transparent;
    }

    .thumbnail:hover,
    .thumbnail.active {
        border-color: var(--primary-color);
        transform: scale(1.05);
    }

    .price-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
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
        border-radius: 20px;
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
        border-radius: 10px;
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

    @media (max-width: 768px) {
        .main-image {
            height: 300px;
        }

        .sticky-sidebar {
            position: relative;
            top: 0;
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
            <!-- 이미지 갤러리 -->
            <div class="car-gallery">
                <?php if(!empty($images)): ?>
                    <img src="<?php echo $images[0]->image_url; ?>" alt="<?php echo htmlspecialchars($car->title); ?>" class="main-image" id="mainImage">

                    <div class="thumbnail-list">
                        <?php foreach($images as $index => $image): ?>
                            <img src="<?php echo $image->image_url; ?>"
                                 alt="이미지 <?php echo $index + 1; ?>"
                                 class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>"
                                 onclick="changeMainImage('<?php echo $image->image_url; ?>', this)">
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="main-image d-flex align-items-center justify-content-center bg-light">
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
                    <?php if($dealer): ?>
                    <div class="spec-item">
                        <span class="spec-label">판매 대리점</span>
                        <span><?php echo $dealer->dealer_name; ?></span>
                    </div>
                    <?php endif; ?>
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
                                $exteriorOptions = explode(',', $car->option_exterior);
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
                                $safetyOptions = explode(',', $car->option_safety);
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
                                $convenienceOptions = explode(',', $car->option_convenience);
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
                                $seatOptions = explode(',', $car->option_seat);
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
                    <?php foreach($contractTerms as $key => $value): ?>
                        <div class="spec-item">
                            <span class="spec-label"><?php echo $key; ?></span>
                            <span><?php echo is_array($value) ? implode(', ', $value) : $value; ?></span>
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

                    <h6 class="mt-4 mb-3 text-primary">면책금</h6>
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
            <?php endif; ?>

            <!-- 운전자 범위 -->
            <?php if(!empty($driverRange)): ?>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h4 class="card-title mb-4"><i class="bi bi-person-check"></i> 운전자 범위</h4>
                    <?php foreach($driverRange as $key => $value): ?>
                        <div class="spec-item">
                            <span class="spec-label"><?php echo $key; ?></span>
                            <span><?php echo is_array($value) ? implode(', ', $value) : $value; ?></span>
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

                <!-- 대리점 정보 -->
                <?php if($dealer): ?>
                <div class="card shadow-sm mt-4">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-building"></i> 판매 대리점</h5>
                        <p class="mb-1"><strong><?php echo $dealer->dealer_name; ?></strong></p>
                        <p class="small text-muted mb-0">대리점 코드: <?php echo $dealer->dealer_code; ?></p>
                    </div>
                </div>
                <?php endif; ?>

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
// 메인 이미지 변경
function changeMainImage(src, thumbnail) {
    document.getElementById('mainImage').src = src;

    // 썸네일 active 클래스 변경
    document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
    thumbnail.classList.add('active');
}

// 찜하기
function addToWishlist() {
    // TODO: 찜하기 API 연동
    alert('찜 목록에 추가되었습니다!');
}

// 카카오톡 상담
function openKakaoChat() {
    // TODO: 카카오톡 채널 URL로 변경
    window.open('https://pf.kakao.com/your-channel', '_blank');
}

// 링크 공유
function shareLink() {
    const url = window.location.href;

    if (navigator.share) {
        navigator.share({
            title: '<?php echo addslashes($car->title); ?>',
            text: '<?php echo addslashes($car->title); ?> - 아리렌트',
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
        alert('링크가 복사되었습니다!');
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
</script>
