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

// 레이아웃 설정 (car_type에 따라 동적으로 변경)
$pageTitle = "차량 목록 - 아리렌트";
$pageDescription = "아리렌트의 다양한 장기렌트 차량을 확인하세요. 국산차부터 수입차까지 합리적인 가격으로 제공합니다.";

if ($carType === 'NEW') {
    $pageTitle = "신차 장기렌트 - 아리렌트";
    $pageDescription = "아리렌트의 신차 장기렌트 차량을 확인하세요. 최신 차량을 합리적인 가격으로 만나보세요.";
} elseif ($carType === 'USED') {
    $pageTitle = "중고 장기렌트 - 아리렌트";
    $pageDescription = "아리렌트의 중고 장기렌트 차량을 확인하세요. 합리적인 가격으로 품질 좋은 중고차를 만나보세요.";
}

\ExpertNote\Core::setPageTitle($pageTitle);
\ExpertNote\Core::setPageDescription($pageDescription);

$brand = isset($_GET['brand']) ? $_GET['brand'] : '';
$fuelType = isset($_GET['fuel_type']) ? $_GET['fuel_type'] : '';
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
        $orderby = ['r.view_count' => 'DESC'];
        break;
}

// 차량 목록 조회
$vehicles = AriRent\Rent::getRents($where, $orderby, ['offset' => $offset, 'count' => $perPage]);
// echo ExpertNote\DB::getLastQuery();
if (!is_array($vehicles)) {
    $vehicles = [];
}
$totalCount = AriRent\Rent::getRentCount($where);
$totalPages = $totalCount > 0 ? ceil($totalCount / $perPage) : 0;

// 브랜드 목록
// $brands = ['현대', '기아', '제네시스', 'BMW', '벤츠', '아우디', '폭스바겐', '르노', '쉐보레', '테슬라'];
$carTypes = [
    'NEW' => '신차',
    'USED' => '중고차'
];
$fuelTypes = ['휘발유', '경유', 'LPG', '전기', '하이브리드'];
?>

<style>
    .page-header {
        background: var(--primary-color);
        color: white;
        padding: 3rem 0;
        text-align: center;
    }

    .page-header h1 {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }

    .page-header p {
        font-size: 1rem;
        margin-bottom: 0;
        opacity: 0.9;
    }

    .content-wrapper {
        padding: 3rem 0;
    }

    /* 필터 사이드바 */
    .filter-sidebar {
        background: white;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        position: sticky;
        top: 80px;
        max-height: calc(100vh - 100px);
        overflow-y: auto;
    }

    .filter-section {
        margin-bottom: 2rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid #e9ecef;
    }

    .filter-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .filter-section h3 {
        font-size: 1rem;
        font-weight: bold;
        margin-bottom: 1rem;
        color: var(--dark-color);
    }

    .filter-option {
        margin-bottom: 0.5rem;
    }

    .filter-option label {
        cursor: pointer;
        display: flex;
        align-items: center;
        padding: 0.5rem;
        transition: background 0.2s;
    }

    .filter-option label:hover {
        background: var(--light-color);
    }

    .filter-option input[type="checkbox"],
    .filter-option input[type="radio"] {
        margin-right: 0.5rem;
    }

    .price-range-inputs {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .price-range-inputs input {
        flex: 1;
    }

    /* 검색 및 정렬 바 */
    .search-sort-bar {
        background: white;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .result-info {
        color: #666;
        font-size: 0.95rem;
    }

    .result-info strong {
        color: var(--primary-color);
        font-size: 1.1rem;
    }

    /* 차량 카드 */
    .vehicle-card {
        transition: transform 0.3s, box-shadow 0.3s;
        cursor: pointer;
        height: 100%;
        background: white;
    }

    .vehicle-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2) !important;
    }

    .vehicle-image {
        height: 200px;
        background: var(--light-color);
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 5rem;
        color: var(--primary-color);
        overflow: hidden;
    }

    .vehicle-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .vehicle-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        background: var(--accent-color);
        color: #fff;
        padding: 0.25rem 0.75rem;
        font-size: 0.85rem;
        font-weight: bold;
    }

    .vehicle-info {
        padding: 1rem;
    }

    .vehicle-specs {
        display: flex;
        gap: 1rem;
        margin-top: 0.5rem;
        font-size: 0.85rem;
        color: #666;
    }

    .vehicle-specs span {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    /* 페이지네이션 */
    .pagination-wrapper {
        margin-top: 3rem;
        display: flex;
        justify-content: center;
    }

    .pagination {
        display: flex;
        gap: 0.5rem;
    }

    .pagination a,
    .pagination span {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background: white;
        border: 1px solid #dee2e6;
        color: var(--dark-color);
        text-decoration: none;
        transition: all 0.3s;
    }

    .pagination a:hover {
        background: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
    }

    .pagination .active {
        background: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
    }

    .pagination .disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* 필터 리셋 버튼 */
    .btn-reset-filter {
        width: 100%;
        background: var(--light-color);
        border: 1px solid #dee2e6;
        color: var(--dark-color);
        padding: 0.5rem;
        font-weight: bold;
        transition: all 0.3s;
    }

    .btn-reset-filter:hover {
        background: var(--dark-color);
        color: white;
    }

    /* 모바일 필터 버튼 */
    .mobile-filter-btn {
        display: none;
        width: 100%;
        margin-bottom: 1rem;
    }

    /* 빈 결과 */
    .empty-result {
        text-align: center;
        padding: 5rem 2rem;
        background: white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .empty-result i {
        font-size: 5rem;
        color: #dee2e6;
        margin-bottom: 1rem;
    }

    .empty-result h3 {
        color: var(--dark-color);
        margin-bottom: 1rem;
    }

    .empty-result p {
        color: #666;
        margin-bottom: 2rem;
    }

    @media (max-width: 991px) {
        .filter-sidebar {
            position: fixed;
            top: 0;
            left: -100%;
            width: 80%;
            max-width: 300px;
            height: 100vh;
            z-index: 1050;
            transition: left 0.3s;
            max-height: 100vh;
        }

        .filter-sidebar.show {
            left: 0;
        }

        .filter-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1040;
            display: none;
        }

        .filter-overlay.show {
            display: block;
        }

        .mobile-filter-btn {
            display: block;
        }
    }

    @media (max-width: 768px) {
        .page-header h1 {
            font-size: 1.5rem;
        }

        .vehicle-image {
            height: 150px;
        }

        .search-sort-bar {
            padding: 1rem;
        }
    }
</style>

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

                <?php if (count($vehicles) > 0): ?>
                <!-- 차량 그리드 -->
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <?php foreach ($vehicles as $vehicle): ?>
                    <div class="col" data-aos="fade-up">
                        <div class="card vehicle-card shadow-sm border-0" onclick="location.href='/item/<?php echo $vehicle->idx; ?>'">
                            <div class="position-relative">
                                <?php if ($vehicle->car_type === 'NEW'): ?>
                                <span class="vehicle-badge">신차</span>
                                <?php endif; ?>
                                <div class="vehicle-image">
                                    <?php if (!empty($vehicle->featured_image)): ?>
                                    <img src="<?php echo $vehicle->featured_image; ?>" alt="<?php echo htmlspecialchars($vehicle->title); ?>" loading="lazy">
                                    <?php else: ?>
                                    <i class="bi bi-car-front-fill"></i>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="text-muted small mb-1"><?php echo htmlspecialchars($vehicle->brand ?? ''); ?></div>
                                <h5 class="card-title fw-bold mb-2"><?php echo htmlspecialchars($vehicle->title); ?></h5>
                                <div class="vehicle-specs mb-2">
                                    <span><i class="bi bi-fuel-pump-fill"></i> <?php echo htmlspecialchars($vehicle->fuel_type ?? '-'); ?></span>
                                    <span><i class="bi bi-speedometer2"></i> <?php echo number_format($vehicle->mileage_km ?? 0); ?>km</span>
                                </div>
                                <p class="text-primary fw-bold fs-5 mb-0">
                                    <?php if (!empty($vehicle->min_price)): ?>
                                    월 <?php echo number_format($vehicle->min_price); ?>원~
                                    <?php else: ?>
                                    가격 문의
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
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
