<?php
ExpertNote\Core::setLayout("v2");
/**
 * 차량 견적 페이지
 * 브랜드/모델 선택 후 가격 낮은 순으로 차량 리스팅 (AJAX 방식)
 */

// 페이지 메타 설정
$pageTitle = "장기렌트 견적 조회";
$pageDescription = "아리렌트 장기렌트 견적을 간편하게 조회하세요. 브랜드와 모델을 선택하면 최저가 순으로 차량을 확인할 수 있습니다.";

\ExpertNote\Core::setPageTitle($pageTitle);
\ExpertNote\Core::setPageSuffix("아리렌트");
\ExpertNote\Core::setPageDescription($pageDescription);
\ExpertNote\Core::setPageKeywords("장기렌트 견적, 렌트카 가격, 아리렌트, 저신용 장기렌트, 무심사 장기렌트");
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
    color: #fff !important;
}
.brand-item.active .brand-name,
.brand-item.active span {
    color: #fff !important;
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
.model-item.active .text-muted {
    color: #fff !important;
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
/* 카드 내 상담 버튼 */
.estimate-card-cta {
    display: flex;
    border-top: 1px solid #e9ecef;
}
.estimate-cta-btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.35rem;
    padding: 0.6rem;
    font-size: 0.8rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s;
}
.estimate-cta-btn.cta-phone {
    color: #fff;
    background: #0d6efd;
}
.estimate-cta-btn.cta-phone:hover {
    background: #0b5ed7;
}
.estimate-cta-btn.cta-kakao {
    color: #3C1E1E;
    background: #FEE500;
}
.estimate-cta-btn.cta-kakao:hover {
    background: #e6cf00;
}
/* 로딩 */
.estimate-loading {
    text-align: center;
    padding: 2rem;
    color: #6c757d;
}
.estimate-loading .spinner-border {
    width: 1.5rem;
    height: 1.5rem;
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
            <div class="brand-grid" id="estimateBrands">
                <div class="estimate-loading"><span class="spinner-border"></span></div>
            </div>
        </div>

        <!-- 2단계: 모델 선택 -->
        <div id="estimateModelSection" style="display:none">
            <h5 class="mb-3">
                <span class="badge bg-primary rounded-pill me-2">2</span>
                <?php echo __('모델 선택', 'skin')?>
                <small class="text-muted ms-2" id="estimateBrandName"></small>
            </h5>
            <div class="model-grid" id="estimateModels"></div>
        </div>
    </div>

    <!-- 차량 목록 -->
    <div id="estimateResult">
        <div class="text-center py-5">
            <i class="bi bi-hand-index-thumb" style="font-size: 4rem; color: #0d6efd; opacity: 0.5;"></i>
            <p class="mt-3 text-muted fs-5"><?php echo __('원하시는 브랜드를 선택해주세요', 'skin')?></p>
        </div>
    </div>
</section>

<script>
(function() {
    var API_URL = '/api/arirent/estimate';
    var state = { brandIdx: 0, modelIdx: 0, carType: '', brandName: '', modelName: '' };

    // 숫자 포맷
    function fmt(n) { return Number(n).toLocaleString(); }
    // HTML 이스케이프
    function esc(s) { var d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }

    // API 호출
    function fetchEstimate(params) {
        var qs = Object.keys(params).filter(function(k){ return params[k]; }).map(function(k){ return k+'='+encodeURIComponent(params[k]); }).join('&');
        return fetch(API_URL + (qs ? '?' + qs : '')).then(function(r){ return r.json(); });
    }

    // 브랜드 렌더링
    function renderBrands(brands) {
        var el = document.getElementById('estimateBrands');
        el.innerHTML = brands.map(function(b) {
            var isActive = state.brandIdx == b.idx;
            var img = b.logo_url
                ? '<img src="'+esc(b.logo_url)+'" alt="'+esc(b.brand_name)+'" loading="lazy">'
                : '<div style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;background:#e9ecef;border-radius:50%;margin-bottom:.5rem"><span style="font-size:1rem;font-weight:bold">'+esc((b.brand_name||'').substring(0,1))+'</span></div>';
            return '<div class="brand-item'+(isActive?' active':'')+'" data-idx="'+b.idx+'" data-name="'+esc(b.brand_name)+'">'+img+'<span class="brand-name">'+esc(b.brand_name)+'</span></div>';
        }).join('');

        el.querySelectorAll('.brand-item').forEach(function(item) {
            item.addEventListener('click', function() {
                var idx = parseInt(this.dataset.idx);
                // 같은 브랜드 클릭 시 해제
                if (state.brandIdx === idx) {
                    state.brandIdx = 0; state.modelIdx = 0; state.carType = ''; state.brandName = ''; state.modelName = '';
                    loadData();
                    return;
                }
                state.brandIdx = idx;
                state.modelIdx = 0;
                state.carType = '';
                state.brandName = this.dataset.name;
                state.modelName = '';
                loadData();
            });
        });
    }

    // 모델 렌더링
    function renderModels(models) {
        var section = document.getElementById('estimateModelSection');
        var el = document.getElementById('estimateModels');
        document.getElementById('estimateBrandName').textContent = state.brandName;

        if (!state.brandIdx || !models || models.length === 0) {
            section.style.display = 'none';
            return;
        }
        section.style.display = '';
        var html = models.map(function(m) {
            return '<div class="model-item'+(state.modelIdx == m.idx ? ' active' : '')+'" data-idx="'+m.idx+'" data-name="'+esc(m.model_name)+'">'+esc(m.model_name)+' <span class="text-muted">('+m.vehicle_count+')</span></div>';
        }).join('');
        el.innerHTML = html;

        el.querySelectorAll('.model-item').forEach(function(item) {
            item.addEventListener('click', function() {
                state.modelIdx = parseInt(this.dataset.idx);
                state.modelName = this.dataset.name || '';
                loadData();
            });
        });
    }

    // 차량 카드 HTML 생성
    function vehicleCard(v) {
        var title = v.model_name ? v.brand_name + ' ' + v.model_name : v.title;
        var img = v.featured_image
            ? '<img src="'+esc(v.featured_image)+'" class="estimate-card-img" alt="'+esc(title)+'" loading="lazy">'
            : '<div class="estimate-card-img d-flex align-items-center justify-content-center bg-light"><i class="bi bi-car-front" style="font-size:3rem;color:#ccc"></i></div>';
        var badge = v.car_type === 'NEW'
            ? '<span class="badge bg-success me-1"><?php echo __('신차', 'skin')?></span>'
            : '<span class="badge bg-secondary me-1"><?php echo __('중고', 'skin')?></span>';
        var info = badge + esc(v.fuel_type) + ' · ' + v.model_year + '<?php echo __('년', 'skin')?> ' + v.model_month + '<?php echo __('월', 'skin')?>';
        if (v.mileage_km > 0) info += ' · ' + fmt(v.mileage_km) + 'km';

        var priceHtml = '';
        if (v.prices && v.prices.length > 0) {
            priceHtml = '<table class="price-detail-table"><thead><tr><th><?php echo __('보증금', 'skin')?></th><th><?php echo __('기간', 'skin')?></th><th><?php echo __('월 렌트료', 'skin')?></th></tr></thead><tbody>';
            v.prices.forEach(function(p) {
                if (v.car_type === 'NEW' && (v.dealer_code || '') === 'JET' && p.rental_period_months < 36) return;
                var dep = p.deposit_amount <= 100
                    ? '<span class="deposit-badge low">' + fmt(p.deposit_amount) + '<?php echo __('만', 'skin')?></span>'
                    : fmt(p.deposit_amount) + '<?php echo __('만', 'skin')?>';
                priceHtml += '<tr><td>'+dep+'</td><td>'+p.rental_period_months+'<?php echo __('개월', 'skin')?></td><td class="fw-bold">'+fmt(p.monthly_rent_amount)+'<?php echo __('원', 'skin')?></td></tr>';
            });
            priceHtml += '</tbody></table>';
        }

        var ctaHtml = '<div class="estimate-card-cta">';
        ctaHtml += '<a href="tel:1666-5623" class="estimate-cta-btn cta-phone"><i class="bi bi-telephone-fill"></i> <?php echo __('전화 상담', 'skin')?></a>';
        ctaHtml += '<a href="/kakaolink" target="_blank" class="estimate-cta-btn cta-kakao"><i class="bi bi-chat-dots-fill"></i> <?php echo __('카톡 상담', 'skin')?></a>';
        ctaHtml += '</div>';

        return '<div class="col-12 col-sm-6 col-lg-4"><div class="estimate-card"><a href="/item/'+v.idx+'" class="text-decoration-none">'+img+'<div class="estimate-card-body"><div class="estimate-card-title">'+esc(title)+'</div><div class="estimate-card-info">'+info+'</div>'+priceHtml+'</div></a>'+ctaHtml+'</div></div>';
    }

    // 차량 목록 렌더링
    function renderVehicles(vehicles) {
        var el = document.getElementById('estimateResult');
        if (!state.brandIdx) {
            el.innerHTML = '<div class="text-center py-5"><i class="bi bi-hand-index-thumb" style="font-size:4rem;color:#0d6efd;opacity:.5"></i><p class="mt-3 text-muted fs-5"><?php echo __('원하시는 브랜드를 선택해주세요', 'skin')?></p></div>';
            return;
        }
        if (!state.modelIdx) {
            el.innerHTML = '<div class="text-center py-5"><i class="bi bi-hand-index-thumb" style="font-size:4rem;color:#0d6efd;opacity:.5"></i><p class="mt-3 text-muted fs-5"><?php echo __('모델을 선택해주세요', 'skin')?></p></div>';
            return;
        }
        if (!vehicles || vehicles.length === 0) {
            el.innerHTML = '<div class="text-center py-5"><i class="bi bi-car-front" style="font-size:4rem;color:#dee2e6"></i><p class="mt-3 text-muted"><?php echo __('해당 조건의 차량이 없습니다.', 'skin')?></p></div>';
            return;
        }

        var resultName = esc(state.brandName) + (state.modelName ? ' ' + esc(state.modelName) : '');
        var typeActive = function(t) { return state.carType === t ? ' active' : ''; };

        var html = '<div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">';
        html += '<h5 class="mb-0">' + resultName + ' <small class="text-muted ms-2">' + vehicles.length + '<?php echo __('대', 'skin')?></small></h5>';
        html += '<div class="car-type-tabs">';
        html += '<div class="car-type-tab'+typeActive('')+'" data-type=""><?php echo __('전체', 'skin')?></div>';
        html += '<div class="car-type-tab'+typeActive('NEW')+'" data-type="NEW"><?php echo __('신차', 'skin')?></div>';
        html += '<div class="car-type-tab'+typeActive('USED')+'" data-type="USED"><?php echo __('중고', 'skin')?></div>';
        html += '</div></div>';
        html += '<div class="row g-3">';
        vehicles.forEach(function(v) { html += vehicleCard(v); });
        html += '</div>';
        el.innerHTML = html;

        // 신차/중고 탭 이벤트
        el.querySelectorAll('.car-type-tab').forEach(function(tab) {
            tab.addEventListener('click', function() {
                state.carType = this.dataset.type;
                loadData();
            });
        });
    }

    // 데이터 로드
    function loadData() {
        // 브랜드 active 상태 갱신
        document.querySelectorAll('#estimateBrands .brand-item').forEach(function(item) {
            item.classList.toggle('active', parseInt(item.dataset.idx) === state.brandIdx);
        });

        if (!state.brandIdx) {
            renderModels([]);
            renderVehicles(null);
            return;
        }

        if (!state.modelIdx) {
            // 브랜드만 선택: 모델 목록만 조회
            fetchEstimate({ brand_idx: state.brandIdx }).then(function(res) {
                if (res.result === 'SUCCESS' && res.data) {
                    if (res.data.brands) renderBrands(res.data.brands);
                    renderModels(res.data.models || []);
                    renderVehicles(null);
                }
            });
            return;
        }

        // 로딩 표시
        document.getElementById('estimateResult').innerHTML = '<div class="estimate-loading"><span class="spinner-border"></span></div>';

        fetchEstimate({ brand_idx: state.brandIdx, model_idx: state.modelIdx, car_type: state.carType }).then(function(res) {
            if (res.result === 'SUCCESS' && res.data) {
                if (res.data.brands) renderBrands(res.data.brands);
                renderModels(res.data.models || []);
                renderVehicles(res.data.vehicles || []);
            }
        });
    }

    // 초기 로드: 브랜드 목록만
    fetchEstimate({}).then(function(res) {
        if (res.result === 'SUCCESS' && res.data && res.data.brands) {
            renderBrands(res.data.brands);
        }
    });
})();
</script>
