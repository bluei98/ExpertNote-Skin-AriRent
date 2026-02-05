<?php
ExpertNote\Core::setLayout('v2');

// 페이지 메타 설정
$pageTitle = '아리렌트';
$pageSuffix = '무심사 저신용 신차 • 중고차 장기렌트';
$pageDescription = '아리렌트 무심사 장기렌트, 저신용, 6~10등급, 개인회생, 신용불량자도 초기 부담 걱정없이 출고 가능한 신차,중고차 전문업체';
$pageKeywords = implode(",", [
    '아리렌트',
    '저신용 신차 장기렌트',
    '무심사 신차 할부',
    '저신용 중고차 장기렌트',
    '무심사 중고차 할부',
    '저신용 렌트카',
    '신용불량자 무보증 장기렌트카'
]);

ExpertNote\Core::setPageTitle($pageTitle);
ExpertNote\Core::setPageSuffix($pageSuffix);
ExpertNote\Core::setPageDescription($pageDescription);
ExpertNote\Core::setPageKeywords($pageKeywords);

// Open Graph 메타 태그
\ExpertNote\Core::addMetaTag('og:type', ["property"=>"og:type", "content"=>"website"]);
\ExpertNote\Core::addMetaTag('og:title', ["property"=>"og:title", "content"=>$pageTitle . " - " . $pageSuffix]);
\ExpertNote\Core::addMetaTag('og:description', ["property"=>"og:description", "content"=>$pageDescription]);
\ExpertNote\Core::addMetaTag('og:url', ["property"=>"og:url", "content"=>ExpertNote\Core::getBaseUrl()]);
\ExpertNote\Core::addMetaTag('og:site_name', ["property"=>"og:site_name", "content"=>"아리렌트"]);

// 대표 이미지 (있는 경우)
// $ogImage = ExpertNote\Core::getBaseUrl() . "/skins/arirent/assets/images/og-image.jpg"; // 실제 이미지 경로로 변경 필요
// \ExpertNote\Core::addMetaTag('og:image', ["property"=>"og:image", "content"=>$ogImage]);
// \ExpertNote\Core::addMetaTag('og:image:width', ["property"=>"og:image:width", "content"=>"1200"]);
// \ExpertNote\Core::addMetaTag('og:image:height', ["property"=>"og:image:height", "content"=>"630"]);

// 트위터 카드 메타 태그
\ExpertNote\Core::addMetaTag('twitter:card', ["name"=>"twitter:card", "content"=>"summary_large_image"]);
\ExpertNote\Core::addMetaTag('twitter:title', ["name"=>"twitter:title", "content"=>$pageTitle . " - " . $pageSuffix]);
\ExpertNote\Core::addMetaTag('twitter:description', ["name"=>"twitter:description", "content"=>$pageDescription]);
\ExpertNote\Core::addMetaTag('twitter:url', ["name"=>"twitter:url", "content"=>ExpertNote\Core::getBaseUrl()]);
// \ExpertNote\Core::addMetaTag('twitter:image', ["name"=>"twitter:image", "content"=>$ogImage]);
?>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="swiper hero-swiper">
            <div class="swiper-wrapper">
                <!-- Slide 1 -->
                <div class="swiper-slide hero-slide">
                    <div class="container">
                        <div class="hero-slide-content" data-aos="fade-right" data-aos-delay="200">
                            <div class="hero-badge">
                                <i class="bi bi-stars"></i>
                                2026 새해 특별 프로모션
                            </div>
                            <h1 class="hero-title">
                                신용등급 관계없이<br>
                                <span>무심사 장기렌트</span>
                            </h1>
                            <p class="hero-desc">
                                저신용, 무직자, 사업자 누구나 OK!<br>
                                보증금 0원부터 시작하는 합리적인 장기렌트
                            </p>
                            <div class="hero-buttons">
                                <a href="#" class="btn-hero-primary" data-bs-toggle="modal" data-bs-target="#searchModal" aria-label="검색">
                                    <i class="bi bi-search"></i> 차량 검색하기
                                </a>
                                <a href="/how-to-contract" class="btn-hero-secondary">
                                    <i class="bi bi-play-circle"></i> 이용방법 보기
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="hero-decoration"></div>
                    <div class="hero-image">
                        <img src="https://www.hyundai.com/contents/vr360/LX06/exterior/A2B/001.png" alt="현대 자동차" loading="lazy">
                    </div>
                </div>

                <!-- Slide 2 -->
                <!-- <div class="swiper-slide hero-slide" style="background: linear-gradient(135deg, #1e293b 0%, #0f766e 100%);">
                    <div class="container">
                        <div class="hero-slide-content" data-aos="fade-right" data-aos-delay="200">
                            <div class="hero-badge">
                                <i class="bi bi-lightning-fill"></i>
                                즉시 출고 가능
                            </div>
                            <h1 class="hero-title">
                                중고차 장기렌트<br>
                                <span>월 30만원대부터</span>
                            </h1>
                            <p class="hero-desc">
                                품질 검증된 중고차를 저렴한 가격에!<br>
                                당일 상담, 빠른 출고 가능
                            </p>
                            <div class="hero-buttons">
                                <a href="#" class="btn-hero-primary">
                                    <i class="bi bi-car-front"></i> 중고차 보기
                                </a>
                                <a href="#" class="btn-hero-secondary">
                                    <i class="bi bi-telephone"></i> 상담 신청
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="hero-decoration"></div>
                    <div class="hero-image">
                        <img src="https://www.genesis.com/content/dam/genesis-p2/kr/assets/models/gv80/24my/exterior/gv80-24my-exterior-color-savile-silver.png" alt="제네시스 GV80" loading="lazy">
                    </div>
                </div> -->

                <!-- Slide 3 -->
                <!-- <div class="swiper-slide hero-slide" style="background: linear-gradient(135deg, #1e293b 0%, #7c3aed 100%);">
                    <div class="container">
                        <div class="hero-slide-content" data-aos="fade-right" data-aos-delay="200">
                            <div class="hero-badge">
                                <i class="bi bi-gift"></i>
                                특별 혜택
                            </div>
                            <h1 class="hero-title">
                                보증금 0원<br>
                                <span>신차 장기렌트</span>
                            </h1>
                            <p class="hero-desc">
                                초기 비용 부담 없이 새 차를 만나보세요<br>
                                국산차부터 수입차까지 전 차종 가능
                            </p>
                            <div class="hero-buttons">
                                <a href="#" class="btn-hero-primary">
                                    <i class="bi bi-calculator"></i> 견적 내기
                                </a>
                                <a href="#" class="btn-hero-secondary">
                                    <i class="bi bi-info-circle"></i> 자세히 보기
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="hero-decoration"></div>
                    <div class="hero-image">
                        <img src="https://www.kia.com/content/dam/kia2/kr/ko/vehicles/ev9/24my/exterior/kia-ev9-24my-exterior-color-runway-red.png" alt="기아 EV9" loading="lazy">
                    </div>
                </div> -->
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </section>

    <!-- Quick Consult Form -->
    <section class="quick-consult-section">
        <div class="container mt-4">
            <div class="quick-consult-card" id="consultForm">
                <h3 class="quick-consult-title">빠른 상담 신청</h3>
                <p class="quick-consult-subtitle">연락처를 남겨주시면 전문 상담사가 빠르게 연락드립니다.</p>
                <form class="consult-form">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">이름</label>
                            <input type="text" name="name" class="form-control" placeholder="홍길동">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">연락처</label>
                            <input type="tel" name="phone" class="form-control" placeholder="010-0000-0000">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">관심 차종</label>
                            <select class="form-select" name="car_type">
                                <option selected>선택해주세요</option>
                                <option value="경차/소형">경차/소형</option>
                                <option value="준중형/중형">준중형/중형</option>
                                <option value="대형">대형</option>
                                <option value="SUV">SUV</option>
                                <option value="수입차">수입차</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn-submit">
                                <i class="bi bi-send"></i> 상담 신청하기
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- 견적 조회 섹션 -->
    <section class="car-list-section" id="estimate">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">
                    <i class="bi bi-calculator"></i>
                    <?php echo __('견적 조회', 'skin')?>
                </div>
                <h2 class="section-title"><?php echo __('브랜드별', 'skin')?> <span><?php echo __('장기렌트 견적', 'skin')?></span></h2>
                <p class="section-desc"><?php echo __('브랜드와 모델을 선택하면 최저가 순으로 차량을 확인할 수 있습니다', 'skin')?></p>
            </div>

            <style>
            .estimate-selector{background:#fff;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.08);padding:2rem}
            .brand-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(100px,1fr));gap:.75rem}
            .brand-item{display:flex;flex-direction:column;align-items:center;padding:.75rem .5rem;border:2px solid #e9ecef;border-radius:8px;cursor:pointer;transition:all .2s;text-decoration:none;color:#333}
            .brand-item:hover{border-color:#0d6efd;background:#f0f7ff;color:#0d6efd}
            .brand-item.active{border-color:#0d6efd;background:#0d6efd;color:#fff}
            .brand-item img{width:40px;height:40px;object-fit:contain;margin-bottom:.5rem}
            .brand-item .brand-name{font-size:.8rem;text-align:center;line-height:1.2;word-break:keep-all}
            .model-grid{display:flex;flex-wrap:wrap;gap:.5rem}
            .model-item{padding:.5rem 1rem;border:2px solid #e9ecef;border-radius:20px;cursor:pointer;transition:all .2s;text-decoration:none;color:#333;font-size:.9rem}
            .model-item:hover{border-color:#0d6efd;color:#0d6efd}
            .model-item.active{border-color:#0d6efd;background:#0d6efd;color:#fff}
            .estimate-card{border:1px solid #dee2e6;border-radius:12px;overflow:hidden;transition:all .2s;background:#fff;height:100%}
            .estimate-card:hover{box-shadow:0 4px 16px rgba(0,0,0,.1);transform:translateY(-2px)}
            .estimate-card-img{width:100%;height:180px;object-fit:cover;background:#f8f9fa}
            .estimate-card-body{padding:1rem}
            .estimate-card-title{font-weight:bold;font-size:1rem;margin-bottom:.5rem;line-height:1.3}
            .estimate-card-info{font-size:.85rem;color:#6c757d;margin-bottom:.75rem}
            .deposit-badge{display:inline-block;padding:.15rem .5rem;border-radius:4px;font-size:.75rem;font-weight:bold}
            .deposit-badge.low{background:#27ee91;color:#000}
            .car-type-tabs{display:flex;gap:.5rem}
            .car-type-tab{padding:.4rem 1rem;border:2px solid #e9ecef;border-radius:20px;cursor:pointer;transition:all .2s;color:#333;font-size:.9rem}
            .car-type-tab:hover{border-color:#0d6efd;color:#0d6efd}
            .car-type-tab.active{border-color:#0d6efd;background:#0d6efd;color:#fff}
            .price-detail-table{width:100%;margin:0}
            .price-detail-table th,.price-detail-table td{padding:.4rem .5rem;font-size:.8rem;text-align:center;border-bottom:1px solid #eee}
            .price-detail-table th{background:#f8f9fa;font-weight:600}
            .estimate-card-cta{display:flex;border-top:1px solid #e9ecef}
            .estimate-cta-btn{flex:1;display:flex;align-items:center;justify-content:center;gap:.35rem;padding:.6rem;font-size:.8rem;font-weight:600;text-decoration:none;transition:all .2s}
            .estimate-cta-btn.cta-phone{color:#fff;background:#0d6efd}
            .estimate-cta-btn.cta-phone:hover{background:#0b5ed7}
            .estimate-cta-btn.cta-kakao{color:#3C1E1E;background:#FEE500}
            .estimate-cta-btn.cta-kakao:hover{background:#e6cf00}
            .estimate-loading{text-align:center;padding:2rem;color:#6c757d}
            .estimate-loading .spinner-border{width:1.5rem;height:1.5rem}
            @media(max-width:768px){
                .brand-grid{grid-template-columns:repeat(auto-fill,minmax(80px,1fr));gap:.5rem}
                .brand-item{padding:.5rem .25rem}.brand-item img{width:32px;height:32px}
                .brand-item .brand-name{font-size:.7rem}
                .estimate-selector{padding:1rem}.estimate-card-img{height:150px}
            }
            </style>

            <div class="estimate-selector mb-4">
                <div class="mb-4">
                    <h5 class="mb-3">
                        <span class="badge bg-primary rounded-pill me-2">1</span>
                        <?php echo __('브랜드 선택', 'skin')?>
                    </h5>
                    <div class="brand-grid" id="estimateBrands">
                        <div class="estimate-loading"><span class="spinner-border"></span></div>
                    </div>
                </div>
                <div id="estimateModelSection" style="display:none">
                    <h5 class="mb-3">
                        <span class="badge bg-primary rounded-pill me-2">2</span>
                        <?php echo __('모델 선택', 'skin')?>
                        <small class="text-muted ms-2" id="estimateBrandName"></small>
                    </h5>
                    <div class="model-grid" id="estimateModels"></div>
                </div>
            </div>

            <div id="estimateResult">
                <div class="text-center py-4">
                    <i class="bi bi-hand-index-thumb" style="font-size:3rem;color:#0d6efd;opacity:.5"></i>
                    <p class="mt-2 text-muted"><?php echo __('원하시는 브랜드를 선택해주세요', 'skin')?></p>
                </div>
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
                        state.brandIdx = 0; state.modelIdx = 0; state.carType = ''; state.brandName = '';
                        loadData();
                        return;
                    }
                    state.brandIdx = idx;
                    state.modelIdx = 0;
                    state.carType = '';
                    state.brandName = this.dataset.name;
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
                return '<div class="model-item'+(state.modelIdx == m.idx ? ' active' : '')+'" data-idx="'+m.idx+'" data-name="'+esc(m.model_name)+'">'+esc(m.model_name)+'</div>';
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
                        ? '<span class="deposit-badge low">' + fmt(p.deposit_amount) + '만</span>'
                        : fmt(p.deposit_amount) + '만';
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
                el.innerHTML = '<div class="text-center py-4"><i class="bi bi-hand-index-thumb" style="font-size:3rem;color:#0d6efd;opacity:.5"></i><p class="mt-2 text-muted"><?php echo __('원하시는 브랜드를 선택해주세요', 'skin')?></p></div>';
                return;
            }
            if (!state.modelIdx) {
                el.innerHTML = '<div class="text-center py-4"><i class="bi bi-hand-index-thumb" style="font-size:3rem;color:#0d6efd;opacity:.5"></i><p class="mt-2 text-muted"><?php echo __('모델을 선택해주세요', 'skin')?></p></div>';
                return;
            }
            if (!vehicles || vehicles.length === 0) {
                el.innerHTML = '<div class="text-center py-5"><i class="bi bi-car-front" style="font-size:4rem;color:#dee2e6"></i><p class="mt-3 text-muted"><?php echo __('해당 조건의 차량이 없습니다.', 'skin')?></p></div>';
                return;
            }

            var resultName = esc(state.brandName) + (state.modelName ? ' ' + esc(state.modelName) : '');
            var typeActive = function(t) { return state.carType === t ? ' active' : ''; };

            var html = '<div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">';
            html += '<h5 class="mb-0">' + resultName + ' <small class="text-muted ms-2">' + vehicles.length + '대</small></h5>';
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

    <!-- Quick Menu -->
    <!-- <section class="quick-menu-section">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">
                    <i class="bi bi-grid-fill"></i>
                    빠른 메뉴
                </div>
                <h2 class="section-title">원하시는 서비스를 <span>선택하세요</span></h2>
                <p class="section-desc">아리렌트만의 다양한 렌트 서비스를 만나보세요</p>
            </div>
            <div class="row row-cols-2 row-cols-md-4 row-cols-lg-4 row-cols-xl-4 g-4">
                <div class="col">
                    <div class="quick-menu-card">
                        <div class="quick-menu-icon">
                            <i class="bi bi-car-front-fill"></i>
                        </div>
                        <h4 class="quick-menu-title">신차 장기렌트</h4>
                        <p class="quick-menu-desc">최신 신차를 부담 없이 장기렌트로 이용해 보세요</p>
                    </div>
                </div>
                <div class="col">
                    <div class="quick-menu-card">
                        <div class="quick-menu-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h4 class="quick-menu-title">중고차 장기렌트</h4>
                        <p class="quick-menu-desc">품질 검증된 중고차를 합리적인 가격에 렌트하세요</p>
                    </div>
                </div>
                <div class="col">
                    <div class="quick-menu-card">
                        <div class="quick-menu-icon">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <h4 class="quick-menu-title">구독 렌트</h4>
                        <p class="quick-menu-desc">월 단위로 자유롭게 이용하는 구독형 렌트 서비스</p>
                    </div>
                </div>
                <div class="col">
                    <div class="quick-menu-card">
                        <div class="quick-menu-icon">
                            <i class="bi bi-calculator"></i>
                        </div>
                        <h4 class="quick-menu-title">견적 문의</h4>
                        <p class="quick-menu-desc">원하시는 차량의 맞춤 견적을 무료로 받아보세요</p>
                    </div>
                </div>
            </div>
        </div>
    </section> -->

    <!-- Car Search Filter -->
    <!-- <section class="search-filter-section">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">
                    <i class="bi bi-search"></i>
                    차량 검색
                </div>
                <h2 class="section-title">원하는 <span>차량을 찾아보세요</span></h2>
            </div>
            <div class="filter-card">
                <div class="filter-group">
                    <div class="filter-label">
                        <i class="bi bi-building"></i> 제조사 선택
                    </div>
                    <div class="brand-filter">
                        <div class="brand-item active">
                            <img src="https://www.carlogos.org/car-logos/hyundai-logo.png" alt="현대" loading="lazy">
                            <span>현대</span>
                        </div>
                        <div class="brand-item">
                            <img src="https://www.carlogos.org/car-logos/kia-logo.png" alt="기아" loading="lazy">
                            <span>기아</span>
                        </div>
                        <div class="brand-item">
                            <img src="https://www.carlogos.org/car-logos/genesis-logo.png" alt="제네시스" loading="lazy">
                            <span>제네시스</span>
                        </div>
                        <div class="brand-item">
                            <img src="https://www.carlogos.org/car-logos/bmw-logo.png" alt="BMW" loading="lazy">
                            <span>BMW</span>
                        </div>
                        <div class="brand-item">
                            <img src="https://www.carlogos.org/car-logos/mercedes-benz-logo.png" alt="벤츠" loading="lazy">
                            <span>벤츠</span>
                        </div>
                        <div class="brand-item">
                            <img src="https://www.carlogos.org/car-logos/audi-logo.png" alt="아우디" loading="lazy">
                            <span>아우디</span>
                        </div>
                    </div>
                </div>
                <div class="filter-group">
                    <div class="filter-label">
                        <i class="bi bi-cash-stack"></i> 월 렌트료
                    </div>
                    <div class="price-filter">
                        <div class="price-item active">전체</div>
                        <div class="price-item">30만원 이하</div>
                        <div class="price-item">30~50만원</div>
                        <div class="price-item">50~70만원</div>
                        <div class="price-item">70~100만원</div>
                        <div class="price-item">100만원 이상</div>
                    </div>
                </div>
                <div class="search-input-group">
                    <input type="text" class="form-control" placeholder="차량명 또는 모델명을 입력하세요">
                    <button class="btn-search">
                        <i class="bi bi-search"></i> 검색
                    </button>
                </div>
            </div>
        </div>
    </section> -->

    <!-- New Car List Section -->
    <section class="car-list-section">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">
                    <i class="bi bi-star-fill"></i>
                    신차 장기렌트
                </div>
                <h2 class="section-title">인기 <span>신차 장기렌트</span></h2>
                <p class="section-desc">가장 많이 찾는 인기 신차를 확인해 보세요</p>
            </div>
            <div class="row row-cols-1 row-cols-md-4 row-cols-lg-4 row-cols-xl-4 g-4">
<?php
$newCars = AriRent\Rent::getRents(["r.car_type" =>"NEW", "r.status"=>"active"], ["r.idx" => "DESC"], ["offset"=>0, "count"=>8]);
foreach($newCars as $item):
    include SKINPATH."/modules/car-item-new.php";
endforeach;
?>
            </div>
            <div class="text-center mt-5">
                <a href="/car-list?car_type=NEW" class="btn-more">
                    신차 전체 보기 <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Used Car List Section -->
    <section class="car-list-section bg-light">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">
                    <i class="bi bi-lightning-fill"></i>
                    즉시 출고
                </div>
                <h2 class="section-title">즉시 출고 <span>중고차 장기렌트</span></h2>
                <p class="section-desc">품질 검증된 중고차를 당일 출고 가능합니다</p>
            </div>
            <div class="row row-cols-1 row-cols-md-4 row-cols-lg-4 row-cols-xl-4 g-4">
<?php
$usedCars = AriRent\Rent::getRents(["r.car_type" =>"USED", "r.status"=>"active"], ["r.idx" => "DESC"], ["offset"=>0, "count"=>8]);
foreach($usedCars as $item):
    include SKINPATH."/modules/car-item-new.php";
endforeach;
?>
            </div>
            <div class="text-center mt-5">
                <a href="/car-list?car_type=USED" class="btn-more">
                    중고차 전체 보기 <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Review Section -->
    <section class="review-section">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">
                    <i class="bi bi-chat-quote-fill"></i>
                    고객 후기
                </div>
                <h2 class="section-title">실제 <span>출고 후기</span></h2>
                <p class="section-desc">아리렌트를 이용해주신 고객님들의 생생한 후기</p>
            </div>
            <div class="row row-cols-1 row-cols-md-3 row-cols-lg-3 g-4">
<?php
// 포럼 리뷰 게시판에서 최근 후기 가져오기
$reviewThreads = ExpertNote\Forum\Thread::getThreads(
    ["f.forum_code = :forum_code", "f.status = 'PUBLISHED'", "f.parent_idx = 0"],
    ["f.publish_time DESC"],
    [0, 6],
    ['forum_code' => 'review']
);

// 본문에서 첫 번째 이미지 추출 함수
function getFirstImageFromContent($contents) {
    if (preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $contents, $match)) {
        return $match[1];
    }
    return null;
}

// 작성자 이름 마스킹 함수
function maskAuthorName($name) {
    if (mb_strlen($name) <= 1) return $name . '*';
    return mb_substr($name, 0, 1) . str_repeat('*', mb_strlen($name) - 1);
}

if (!empty($reviewThreads)):
    foreach($reviewThreads as $review):
        $reviewImage = getFirstImageFromContent($review->contents);
        $authorName = !empty($review->nickname) ? $review->nickname : $review->username;
        $maskedName = maskAuthorName($authorName);
        $reviewUrl = "/forum/review/" . $review->idx;
?>
                <div class="col">
                    <a href="<?php echo $reviewUrl ?>" class="text-decoration-none">
                    <div class="review-card">
                        <div class="review-image">
                            <?php if ($reviewImage): ?>
                            <img src="<?php echo htmlspecialchars($reviewImage) ?>" alt="<?php echo htmlspecialchars($review->title) ?>" loading="lazy">
                            <?php else: ?>
                            <div class="review-thumb-placeholder">
                                <i class="bi bi-car-front-fill"></i>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="review-body">
                            <h4 class="review-title"><?php echo htmlspecialchars($review->title) ?></h4>
                            <div class="review-meta">
                                <div class="review-author">
                                    <div class="review-author-avatar"><?php echo mb_substr(htmlspecialchars($authorName), 0, 1) ?></div>
                                    <span class="review-author-name"><?php echo htmlspecialchars($maskedName) ?> 님</span>
                                </div>
                                <!-- <div class="review-stars">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                </div> -->
                            </div>
                            <p class="review-date"><?php echo date('Y.m.d', strtotime($review->publish_time)) ?></p>
                        </div>
                    </div>
                            </a>
                </div>
<?php endforeach; endif; ?>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="why-section mt-5">
        <div class="container">
            <div class="section-header">
                <div class="section-badge" style="background: rgba(255,255,255,0.1); color: #fff;">
                    <i class="bi bi-trophy-fill"></i>
                    아리렌트만의 장점
                </div>
                <h2 class="section-title">왜 <span>아리렌트</span>인가요?</h2>
                <p class="section-desc">다른 곳과는 다른 아리렌트만의 특별한 혜택</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="why-card">
                        <div class="why-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h4 class="why-title">100% 무심사</h4>
                        <p class="why-desc">신용등급 관계없이 누구나 이용 가능한 무심사 장기렌트</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="why-card">
                        <div class="why-icon">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                        <h4 class="why-title">보증금 0원</h4>
                        <p class="why-desc">초기 비용 부담 없이 보증금 0원부터 시작 가능</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="why-card">
                        <div class="why-icon">
                            <i class="bi bi-truck"></i>
                        </div>
                        <h4 class="why-title">전국 출고</h4>
                        <p class="why-desc">전국 어디서나 원하는 장소로 출고 배송 가능</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="why-card">
                        <div class="why-icon">
                            <i class="bi bi-headset"></i>
                        </div>
                        <h4 class="why-title">전담 상담</h4>
                        <p class="why-desc">전문 상담사의 1:1 맞춤 상담 및 사후 관리 서비스</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <!-- <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <div class="stat-number">15,000+</div>
                        <div class="stat-label">누적 출고 건수</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <div class="stat-number">98.5%</div>
                        <div class="stat-label">고객 만족도</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <div class="stat-number">500+</div>
                        <div class="stat-label">보유 차량</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <div class="stat-number">10년+</div>
                        <div class="stat-label">업력</div>
                    </div>
                </div>
            </div>
        </div>
    </section> -->

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-card">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h2 class="cta-title">지금 바로 무료 상담 받아보세요!</h2>
                        <p class="cta-desc">전문 상담사가 고객님께 맞는 최적의 렌트 조건을 안내해 드립니다.</p>
                    </div>
                    <div class="col-lg-4">
                        <div class="cta-buttons">
                            <a href="tel:1666-5623" class="btn-cta-primary">
                                <i class="bi bi-telephone-fill"></i> 1666-5623
                            </a>
                            <a href="/kakaolink" class="btn-cta-secondary" target="_blank">
                                <i class="bi bi-chat-dots-fill"></i> 카톡 상담
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>