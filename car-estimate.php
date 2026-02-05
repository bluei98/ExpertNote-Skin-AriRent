<?php
ExpertNote\Core::setLayout("v2");
/**
 * 차량 견적 페이지
 * 브랜드/모델 선택 후 가격 낮은 순으로 차량 리스팅
 */

// 페이지 메타 설정
$pageTitle = "장기렌트 견적 조회";
$pageDescription = "아리렌트 장기렌트 견적을 간편하게 조회하세요. 브랜드와 모델을 선택하면 최저가 순으로 차량을 확인할 수 있습니다.";

\ExpertNote\Core::setPageTitle($pageTitle);
\ExpertNote\Core::setPageSuffix("아리렌트");
\ExpertNote\Core::setPageDescription($pageDescription);
\ExpertNote\Core::setPageKeywords("장기렌트 견적, 렌트카 가격, 아리렌트, 저신용 장기렌트, 무심사 장기렌트");

// 파라미터 처리
$brandIdx = isset($_GET['brand']) ? intval($_GET['brand']) : 0;
$modelIdx = isset($_GET['model']) ? intval($_GET['model']) : 0;
$carType = isset($_GET['car_type']) ? strtoupper($_GET['car_type']) : '';

// 브랜드 목록 조회 (활성 차량이 있는 브랜드만)
$allBrands = \AriRent\Rent::getBrands(['is_active' => 1], ['sort_order' => 'ASC']);
$brands = [];
foreach ($allBrands as $b) {
    $cnt = \AriRent\Rent::getRentCount(['r.status' => 'active', 'r.brand_idx' => $b->idx]);
    if ($cnt > 0) {
        $b->vehicle_count = $cnt;
        $brands[] = $b;
    }
}

// 선택된 브랜드의 모델 목록 조회 (활성 차량이 있는 모델만)
$models = [];
$selectedBrand = null;
if ($brandIdx) {
    $allModels = \AriRent\Rent::getModels(['brand_idx' => $brandIdx, 'is_active' => 1], ['sort_order' => 'ASC']);
    foreach ($allModels as $m) {
        $cnt = \AriRent\Rent::getRentCount(['r.status' => 'active', 'r.brand_idx' => $brandIdx, 'r.model_idx' => $m->idx]);
        if ($cnt > 0) {
            $m->vehicle_count = $cnt;
            $models[] = $m;
        }
    }
    // 선택된 브랜드 정보
    foreach ($brands as $b) {
        if ($b->idx == $brandIdx) {
            $selectedBrand = $b;
            break;
        }
    }
}

// 차량 목록 조회 (브랜드 또는 모델 선택 시)
$vehicles = [];
$selectedModel = null;
if ($brandIdx) {
    $where = [
        'r.status' => 'active',
        'r.brand_idx' => $brandIdx
    ];

    if ($modelIdx) {
        $where['r.model_idx'] = $modelIdx;
        // 선택된 모델 정보
        foreach ($models as $m) {
            if ($m->idx == $modelIdx) {
                $selectedModel = $m;
                break;
            }
        }
    }

    if ($carType) {
        $where['r.car_type'] = $carType;
    }

    // 가격 낮은 순 정렬
    $vehicles = \AriRent\Rent::getRents($where, ['p.monthly_rent_amount' => 'ASC'], [], true);
    if (!is_array($vehicles)) {
        $vehicles = [];
    }
}
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/"><?php echo __('홈', 'skin')?></a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo __('견적 조회', 'skin')?></li>
                </ol>
            </nav>
            <h1 class="page-title"><?php echo __('장기렌트 견적 조회', 'skin')?></h1>
            <p class="text-light"><?php echo __('브랜드와 모델을 선택하면 최저가 순으로 차량을 확인할 수 있습니다', 'skin')?></p>
        </div>
    </div>
</section>

<style>
/* 견적 페이지 스타일 */
.estimate-selector {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    padding: 2rem;
    margin-top: -2rem;
    position: relative;
    z-index: 10;
}
.brand-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 0.75rem;
}
.brand-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 0.75rem 0.5rem;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    color: #333;
}
.brand-item:hover {
    border-color: #0d6efd;
    background: #f0f7ff;
    color: #0d6efd;
}
.brand-item.active {
    border-color: #0d6efd;
    background: #0d6efd;
    color: #fff;
}
.brand-item img {
    width: 40px;
    height: 40px;
    object-fit: contain;
    margin-bottom: 0.5rem;
}
.brand-item .brand-name {
    font-size: 0.8rem;
    text-align: center;
    line-height: 1.2;
    word-break: keep-all;
}
.model-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}
.model-item {
    padding: 0.5rem 1rem;
    border: 2px solid #e9ecef;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    color: #333;
    font-size: 0.9rem;
}
.model-item:hover {
    border-color: #0d6efd;
    color: #0d6efd;
}
.model-item.active {
    border-color: #0d6efd;
    background: #0d6efd;
    color: #fff;
}
/* 차량 카드 스타일 */
.estimate-card {
    border: 1px solid #dee2e6;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.2s;
    background: #fff;
    height: 100%;
}
.estimate-card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}
.estimate-card-img {
    width: 100%;
    height: 180px;
    object-fit: cover;
    background: #f8f9fa;
}
.estimate-card-body {
    padding: 1rem;
}
.estimate-card-title {
    font-weight: bold;
    font-size: 1rem;
    margin-bottom: 0.5rem;
    line-height: 1.3;
}
.estimate-card-info {
    font-size: 0.85rem;
    color: #6c757d;
    margin-bottom: 0.75rem;
}
.estimate-card-price {
    background: #f0f7ff;
    padding: 0.75rem 1rem;
    border-top: 1px solid #e9ecef;
}
.estimate-card-price .price-label {
    font-size: 0.75rem;
    color: #6c757d;
}
.estimate-card-price .price-value {
    font-size: 1.25rem;
    font-weight: bold;
    color: #0d6efd;
}
.estimate-card-price .deposit-value {
    font-size: 0.9rem;
    color: #333;
}
.deposit-badge {
    display: inline-block;
    padding: 0.15rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: bold;
}
.deposit-badge.low {
    background: #27ee91;
    color: #000;
}
/* 차종 필터 탭 */
.car-type-tabs {
    display: flex;
    gap: 0.5rem;
}
.car-type-tab {
    padding: 0.4rem 1rem;
    border: 2px solid #e9ecef;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    color: #333;
    font-size: 0.9rem;
}
.car-type-tab:hover {
    border-color: #0d6efd;
    color: #0d6efd;
}
.car-type-tab.active {
    border-color: #0d6efd;
    background: #0d6efd;
    color: #fff;
}
/* 가격 테이블 */
.price-detail-table {
    width: 100%;
    margin: 0;
}
.price-detail-table th,
.price-detail-table td {
    padding: 0.4rem 0.5rem;
    font-size: 0.8rem;
    text-align: center;
    border-bottom: 1px solid #eee;
}
.price-detail-table th {
    background: #f8f9fa;
    font-weight: 600;
}
@media (max-width: 768px) {
    .brand-grid {
        grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
        gap: 0.5rem;
    }
    .brand-item {
        padding: 0.5rem 0.25rem;
    }
    .brand-item img {
        width: 32px;
        height: 32px;
    }
    .brand-item .brand-name {
        font-size: 0.7rem;
    }
    .estimate-selector {
        padding: 1rem;
        margin-top: -1rem;
    }
    .estimate-card-img {
        height: 150px;
    }
}
</style>

<section class="container my-4">
    <!-- 브랜드/모델 선택 영역 -->
    <div class="estimate-selector mb-4">
        <!-- 1단계: 브랜드 선택 -->
        <div class="mb-4">
            <h5 class="mb-3">
                <span class="badge bg-primary rounded-pill me-2">1</span>
                <?php echo __('브랜드 선택', 'skin')?>
            </h5>
            <div class="brand-grid">
                <?php foreach ($brands as $brand): ?>
                <a href="?brand=<?php echo $brand->idx?><?php echo $carType ? '&car_type='.$carType : ''?>"
                   class="brand-item<?php echo ($brandIdx == $brand->idx) ? ' active' : ''?>">
                    <?php if ($brand->logo_url): ?>
                    <img src="<?php echo htmlspecialchars($brand->logo_url)?>" alt="<?php echo htmlspecialchars($brand->brand_name)?>" loading="lazy">
                    <?php else: ?>
                    <div style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;background:#e9ecef;border-radius:50%;margin-bottom:0.5rem;">
                        <span style="font-size:1rem;font-weight:bold;"><?php echo mb_substr($brand->brand_name, 0, 1)?></span>
                    </div>
                    <?php endif; ?>
                    <span class="brand-name"><?php echo htmlspecialchars($brand->brand_name)?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 2단계: 모델 선택 -->
        <?php if ($brandIdx && !empty($models)): ?>
        <div class="mb-3">
            <h5 class="mb-3">
                <span class="badge bg-primary rounded-pill me-2">2</span>
                <?php echo __('모델 선택', 'skin')?>
                <small class="text-muted ms-2"><?php echo htmlspecialchars($selectedBrand->brand_name ?? '')?></small>
            </h5>
            <div class="model-grid">
                <a href="?brand=<?php echo $brandIdx?><?php echo $carType ? '&car_type='.$carType : ''?>"
                   class="model-item<?php echo !$modelIdx ? ' active' : ''?>"><?php echo __('전체', 'skin')?></a>
                <?php foreach ($models as $model): ?>
                <a href="?brand=<?php echo $brandIdx?>&model=<?php echo $model->idx?><?php echo $carType ? '&car_type='.$carType : ''?>"
                   class="model-item<?php echo ($modelIdx == $model->idx) ? ' active' : ''?>">
                    <?php echo htmlspecialchars($model->model_name)?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php elseif ($brandIdx && empty($models)): ?>
        <div class="mb-3">
            <p class="text-muted"><?php echo __('등록된 모델이 없습니다.', 'skin')?></p>
        </div>
        <?php endif; ?>
    </div>

    <!-- 차량 목록 -->
    <?php if ($brandIdx && !empty($vehicles)): ?>
    <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="mb-0">
                <?php
                $resultTitle = htmlspecialchars($selectedBrand->brand_name ?? '');
                if ($selectedModel) {
                    $resultTitle .= ' ' . htmlspecialchars($selectedModel->model_name);
                }
                echo $resultTitle;
                ?>
                <small class="text-muted ms-2"><?php echo count($vehicles)?>대</small>
            </h5>
        </div>
        <!-- 신차/중고 필터 -->
        <div class="car-type-tabs">
            <a href="?brand=<?php echo $brandIdx?><?php echo $modelIdx ? '&model='.$modelIdx : ''?>"
               class="car-type-tab<?php echo !$carType ? ' active' : ''?>"><?php echo __('전체', 'skin')?></a>
            <a href="?brand=<?php echo $brandIdx?><?php echo $modelIdx ? '&model='.$modelIdx : ''?>&car_type=NEW"
               class="car-type-tab<?php echo $carType === 'NEW' ? ' active' : ''?>"><?php echo __('신차', 'skin')?></a>
            <a href="?brand=<?php echo $brandIdx?><?php echo $modelIdx ? '&model='.$modelIdx : ''?>&car_type=USED"
               class="car-type-tab<?php echo $carType === 'USED' ? ' active' : ''?>"><?php echo __('중고', 'skin')?></a>
        </div>
    </div>

    <div class="row g-3">
        <?php foreach ($vehicles as $vehicle):
            $vehicleTitle = $vehicle->model_name ? $vehicle->brand_name.' '.$vehicle->model_name : $vehicle->title;
            $depositAmount = isset($vehicle->prices[0]) ? $vehicle->prices[0]->deposit_amount : ($vehicle->deposit_amount ?? 0);
            $minRentAmount = $vehicle->min_price ?? 0;
            $featuredImage = $vehicle->featured_image ?? '';
        ?>
        <div class="col-12 col-sm-6 col-lg-4">
            <a href="/item/<?php echo $vehicle->idx?>" class="text-decoration-none">
                <div class="estimate-card">
                    <?php if ($featuredImage): ?>
                    <img src="<?php echo htmlspecialchars($featuredImage)?>" class="estimate-card-img" alt="<?php echo htmlspecialchars($vehicleTitle)?>" loading="lazy">
                    <?php else: ?>
                    <div class="estimate-card-img d-flex align-items-center justify-content-center bg-light">
                        <i class="bi bi-car-front" style="font-size: 3rem; color: #ccc;"></i>
                    </div>
                    <?php endif; ?>
                    <div class="estimate-card-body">
                        <div class="estimate-card-title"><?php echo htmlspecialchars($vehicleTitle)?></div>
                        <div class="estimate-card-info">
                            <?php if ($vehicle->car_type == 'NEW'): ?>
                                <span class="badge bg-success me-1"><?php echo __('신차', 'skin')?></span>
                            <?php else: ?>
                                <span class="badge bg-secondary me-1"><?php echo __('중고', 'skin')?></span>
                            <?php endif; ?>
                            <?php echo $vehicle->fuel_type?>
                            · <?php echo $vehicle->model_year?>년 <?php echo $vehicle->model_month?>월
                            <?php if ($vehicle->mileage_km > 0): ?>
                            · <?php echo number_format($vehicle->mileage_km)?>km
                            <?php endif; ?>
                        </div>
                        <!-- 가격 옵션 테이블 -->
                        <?php if (!empty($vehicle->prices)): ?>
                        <table class="price-detail-table">
                            <thead>
                                <tr>
                                    <th><?php echo __('보증금', 'skin')?></th>
                                    <th><?php echo __('기간', 'skin')?></th>
                                    <th><?php echo __('월 렌트료', 'skin')?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($vehicle->prices as $price):
                                    // 신차 + JET 딜러는 36개월 미만 기간 제외
                                    if ($vehicle->car_type == 'NEW' && ($vehicle->dealer_code ?? '') == 'JET' && $price->rental_period_months < 36) continue;
                                ?>
                                <tr>
                                    <td>
                                        <?php if ($price->deposit_amount <= 100): ?>
                                        <span class="deposit-badge low"><?php echo number_format($price->deposit_amount)?>만</span>
                                        <?php else: ?>
                                        <?php echo number_format($price->deposit_amount)?>만
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $price->rental_period_months?><?php echo __('개월', 'skin')?></td>
                                    <td class="fw-bold"><?php echo number_format($price->monthly_rent_amount)?><?php echo __('원', 'skin')?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>

    <?php elseif ($brandIdx && empty($vehicles)): ?>
    <div class="text-center py-5">
        <i class="bi bi-car-front" style="font-size: 4rem; color: #dee2e6;"></i>
        <p class="mt-3 text-muted"><?php echo __('해당 조건의 차량이 없습니다.', 'skin')?></p>
        <a href="?brand=<?php echo $brandIdx?>" class="btn btn-outline-primary"><?php echo __('필터 초기화', 'skin')?></a>
    </div>

    <?php else: ?>
    <!-- 브랜드 미선택 시 안내 -->
    <div class="text-center py-5">
        <i class="bi bi-hand-index-thumb" style="font-size: 4rem; color: #0d6efd; opacity: 0.5;"></i>
        <p class="mt-3 text-muted fs-5"><?php echo __('원하시는 브랜드를 선택해주세요', 'skin')?></p>
    </div>
    <?php endif; ?>
</section>
