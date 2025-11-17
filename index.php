<?php
// AriRent 클래스 로드
require_once __DIR__ . '/vendor/autoload.php';

// 페이지 메타 설정
$pageTitle = '아리렌트';
$pageSuffix = '신차장기렌트 전문';
$pageDescription = '아리렌트는 신차 및 중고차 장기렌트 전문 업체입니다. 현대, 기아, 제네시스부터 수입차까지 다양한 차량을 합리적인 가격으로 제공합니다. 전문 상담을 통해 최적의 렌트 조건을 찾아드립니다.';
$pageKeywords = '아리렌트, 신차 장기렌트, 중고차 장기렌트, 장기렌트, 자동차 리스, 차량 렌트, 현대 장기렌트, 기아 장기렌트, 제네시스 렌트, 수입차 렌트, 합리적인 가격';

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
    <!-- Hero Carousel -->
    <div id="heroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active carousel-item-color-1">
                <div class="d-flex justify-content-center align-items-center h-100">
                    <div class="text-center text-white p-4">
                        <h2 class="display-3 fw-bold mb-3">제네시스 GV80</h2>
                        <p class="fs-4 mb-4">럭셔리 SUV의 정점</p>
                        <div class="fs-1 fw-bold mb-4">월 425,000원~</div>
                        <button class="btn btn-lg px-5 py-3 fw-bold" style="background: var(--accent-color); color: #fff;">상담 신청하기</button>
                    </div>
                </div>
            </div>
            <div class="carousel-item carousel-item-color-2">
                <div class="d-flex justify-content-center align-items-center h-100">
                    <div class="text-center text-white p-4">
                        <h2 class="display-3 fw-bold mb-3">현대 팰리세이드</h2>
                        <p class="fs-4 mb-4">가족을 위한 최고의 선택</p>
                        <div class="fs-1 fw-bold mb-4">월 380,000원~</div>
                        <button class="btn btn-lg px-5 py-3 fw-bold" style="background: var(--accent-color); color: #fff;">상담 신청하기</button>
                    </div>
                </div>
            </div>
            <div class="carousel-item carousel-item-color-3">
                <div class="d-flex justify-content-center align-items-center h-100">
                    <div class="text-center text-white p-4">
                        <h2 class="display-3 fw-bold mb-3">기아 카니발</h2>
                        <p class="fs-4 mb-4">넓고 편안한 프리미엄 미니밴</p>
                        <div class="fs-1 fw-bold mb-4">월 350,000원~</div>
                        <button class="btn btn-lg px-5 py-3 fw-bold" style="background: var(--accent-color); color: #fff;">상담 신청하기</button>
                    </div>
                </div>
            </div>
            <div class="carousel-item carousel-item-color-4">
                <div class="d-flex justify-content-center align-items-center h-100">
                    <div class="text-center text-white p-4">
                        <h2 class="display-3 fw-bold mb-3">현대 아반떼</h2>
                        <p class="fs-4 mb-4">경제적이고 실용적인 준중형 세단</p>
                        <div class="fs-1 fw-bold mb-4">월 280,000원~</div>
                        <button class="btn btn-lg px-5 py-3 fw-bold" style="background: var(--accent-color); color: #fff;">상담 신청하기</button>
                    </div>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>

    <!-- Brand Filter -->
    <section class="container my-5" style="margin-top: -50px !important; position: relative; z-index: 100;">
        <div class="bg-white shadow-lg p-4" data-aos="fade-up">
            <div class="d-flex flex-wrap justify-content-center gap-2">
                <button class="btn btn-outline-secondary px-4 filter-btn active" data-brand="all">전체</button>
                <button class="btn btn-outline-secondary px-4 filter-btn" data-brand="hyundai">현대</button>
                <button class="btn btn-outline-secondary px-4 filter-btn" data-brand="kia">기아</button>
                <button class="btn btn-outline-secondary px-4 filter-btn" data-brand="genesis">제네시스</button>
                <button class="btn btn-outline-secondary px-4 filter-btn" data-brand="renault">르노</button>
                <button class="btn btn-outline-secondary px-4 filter-btn" data-brand="chevrolet">쉐보레</button>
                <button class="btn btn-outline-secondary px-4 filter-btn" data-brand="import">수입차</button>
            </div>
        </div>
    </section>

    <!-- Quick Consultation (Desktop) -->
    <aside class="card shadow-lg quick-consult d-none d-xl-block">
        <div class="card-body p-4">
            <h3 class="text-primary text-center mb-4">빠른 상담 신청</h3>
            <form id="consultForm">
                <div class="mb-3">
                    <input type="text" class="form-control" placeholder="이름" required>
                </div>
                <div class="mb-3">
                    <input type="tel" class="form-control" placeholder="연락처" required>
                </div>
                <div class="mb-3">
                    <select class="form-select" required>
                        <option value="">지역 선택</option>
                        <option value="seoul">서울</option>
                        <option value="gyeonggi">경기</option>
                        <option value="incheon">인천</option>
                        <option value="busan">부산</option>
                        <option value="daegu">대구</option>
                        <option value="etc">기타</option>
                    </select>
                </div>
                <div class="mb-3">
                    <select class="form-select" required>
                        <option value="">차종 선택</option>
                        <option value="small">경차/소형</option>
                        <option value="mid">준중형/중형</option>
                        <option value="large">대형</option>
                        <option value="suv">SUV</option>
                        <option value="import">수입차</option>
                    </select>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="privacy" required>
                    <label class="form-check-label small" for="privacy">
                        개인정보 수집 및 이용 동의
                    </label>
                </div>
                <button type="submit" class="btn w-100 fw-bold" style="background: var(--accent-color); color: #fff;">상담 신청하기</button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="container my-5 py-5">
        <!-- Vehicle Showcase -->
        <section id="new-rental">
            <h2 class="text-center fw-bold mb-3" data-aos="fade-up">신차 장기렌트 인기 차량</h2>
            <p class="text-center text-muted mb-5" data-aos="fade-up" data-aos-delay="100">
                아리렌트에서 가장 인기 있는 차량을 만나보세요
            </p>

            <div class="row row-cols-2 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4" id="vehicleGrid">
<?php
$res = AriRent\Rent::getRents(["r.car_type" =>"NEW", "r.dealer_idx"=>1, "r.status"=>"active"], [], ["offset"=>0, "count"=>20]);
foreach($res as $item):
?>
                <!-- Vehicle Card -->
                <div class="col" data-brand="<?php echo strtolower($item->brand ?? 'other'); ?>" data-aos="fade-up" onclick="location='/item/<?php echo $item->idx?>'">
                    <div class="card vehicle-card shadow-sm border-0">
                        <div class="vehicle-image">
                            <?php if (!empty($item->featured_image)): ?>
                            <img src="<?php echo $item->featured_image?>" class="img-fluid" loading="lazy" alt="<?php echo htmlspecialchars($item->title); ?>">
                            <?php else: ?>
                            <i class="bi bi-car-front-fill"></i>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title fw-bold"><?php echo htmlspecialchars($item->title)?></h5>
                            <p class="text-primary fw-bold fs-5">
                                <?php if (!empty($item->min_price)): ?>
                                월 <?php echo number_format($item->min_price)?>원~
                                <?php else: ?>
                                가격 문의
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
<?php endforeach;?>
            </div>
        </section>
    </main>

    <!-- Reviews Section -->
    <section class="bg-light py-5" id="reviews">
        <div class="container">
            <h2 class="text-center fw-bold mb-3" data-aos="fade-up">믿을 수 있는 아리렌트 출고 후기</h2>
            <p class="text-center text-muted mb-5" data-aos="fade-up" data-aos-delay="100">
                실제 고객님들의 생생한 후기를 확인하세요
            </p>

            <!-- Review Carousel -->
            <div id="reviewCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#reviewCarousel" data-bs-slide-to="0" class="active"></button>
                    <button type="button" data-bs-target="#reviewCarousel" data-bs-slide-to="1"></button>
                </div>

                <div class="carousel-inner">
                    <!-- Slide 1 -->
                    <div class="carousel-item active">
                        <div class="row g-4">
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="card shadow-sm border-0">
                                    <div class="review-image"><i class="bi bi-truck-front-fill"></i></div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-primary text-white d-flex justify-content-center align-items-center me-3" style="width: 50px; height: 50px;">
                                                <strong>김**</strong>
                                            </div>
                                            <div>
                                                <h5 class="mb-0">현대 팰리세이드</h5>
                                                <div style="color: #fae100;">
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="card-text text-muted">
                                            가족들과 여행 다니기 딱 좋은 차입니다.
                                            아리렌트에서 친절하게 상담해주셔서 좋은 조건으로 계약했어요.
                                            출고도 빠르고 만족스럽습니다!
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="card shadow-sm border-0">
                                    <div class="review-image"><i class="bi bi-bus-front-fill"></i></div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-primary text-white d-flex justify-content-center align-items-center me-3" style="width: 50px; height: 50px;">
                                                <strong>이**</strong>
                                            </div>
                                            <div>
                                                <h5 class="mb-0">기아 카니발</h5>
                                                <div style="color: #fae100;">
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="card-text text-muted">
                                            미니밴이 필요해서 알아보다가 아리렌트를 통해 계약했습니다.
                                            합리적인 가격에 좋은 차량 받았어요.
                                            3년 동안 잘 타겠습니다!
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-lg-4 d-none d-lg-block">
                                <div class="card shadow-sm border-0">
                                    <div class="review-image"><i class="bi bi-truck-front-fill"></i></div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-primary text-white d-flex justify-content-center align-items-center me-3" style="width: 50px; height: 50px;">
                                                <strong>박**</strong>
                                            </div>
                                            <div>
                                                <h5 class="mb-0">제네시스 GV80</h5>
                                                <div style="color: #fae100;">
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="card-text text-muted">
                                            고급 SUV를 합리적인 가격에 이용할 수 있어서 좋습니다.
                                            아리렌트 담당자분이 세세하게 설명해주셔서 믿고 계약했어요.
                                            추천합니다!
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Slide 2 -->
                    <div class="carousel-item">
                        <div class="row g-4">
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="card shadow-sm border-0">
                                    <div class="review-image"><i class="bi bi-car-front-fill"></i></div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-primary text-white d-flex justify-content-center align-items-center me-3" style="width: 50px; height: 50px;">
                                                <strong>최**</strong>
                                            </div>
                                            <div>
                                                <h5 class="mb-0">현대 아반떼</h5>
                                                <div style="color: #fae100;">
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="card-text text-muted">
                                            출퇴근용으로 사용하는데 연비도 좋고 승차감도 편안합니다.
                                            장기렌트가 처음이라 걱정했는데 친절한 상담 덕분에 잘 결정했네요!
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="card shadow-sm border-0">
                                    <div class="review-image"><i class="bi bi-truck-front-fill"></i></div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-primary text-white d-flex justify-content-center align-items-center me-3" style="width: 50px; height: 50px;">
                                                <strong>정**</strong>
                                            </div>
                                            <div>
                                                <h5 class="mb-0">기아 스포티지</h5>
                                                <div style="color: #fae100;">
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="card-text text-muted">
                                            SUV가 필요해서 알아보다가 아리렌트에서 좋은 조건으로 계약했습니다.
                                            차량 상태도 완벽하고 모든 과정이 만족스러웠어요!
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-lg-4 d-none d-lg-block">
                                <div class="card shadow-sm border-0">
                                    <div class="review-image"><i class="bi bi-car-front-fill"></i></div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-primary text-white d-flex justify-content-center align-items-center me-3" style="width: 50px; height: 50px;">
                                                <strong>강**</strong>
                                            </div>
                                            <div>
                                                <h5 class="mb-0">현대 쏘나타</h5>
                                                <div style="color: #fae100;">
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="card-text text-muted">
                                            중형 세단을 찾다가 아리렌트를 통해 계약했는데 정말 잘한 선택 같아요.
                                            가격 대비 훌륭한 차량이고 서비스도 좋습니다!
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Carousel Controls -->
                <button class="carousel-control-prev" type="button" data-bs-target="#reviewCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#reviewCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
    </section>