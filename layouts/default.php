<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>아리렌트 - 신차장기렌트 전문</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- AOS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #1b71d7;
            --secondary-color: #fae100;
            --dark-color: #2c3e50;
        }

        body {
            font-family: 'Noto Sans KR', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        /* Custom navbar underline effect */
        .navbar-nav .nav-link {
            position: relative;
        }

        .navbar-nav .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary-color);
            transition: width 0.3s;
        }

        .navbar-nav .nav-link:hover::after {
            width: 100%;
        }

        /* Hero carousel height */
        .hero-carousel {
            height: 600px;
        }

        .hero-carousel .carousel-inner,
        .hero-carousel .carousel-item {
            height: 100%;
        }

        .carousel-item-gradient-1 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .carousel-item-gradient-2 {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .carousel-item-gradient-3 {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .carousel-item-gradient-4 {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }

        /* Brand filter buttons */
        .filter-btn {
            transition: all 0.3s;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background-color: var(--primary-color) !important;
            color: white !important;
            border-color: var(--primary-color) !important;
        }

        /* Vehicle card hover effect */
        .vehicle-card {
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            height: 100%;
        }

        .vehicle-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2) !important;
        }

        .vehicle-image {
            height: 200px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 5rem;
            color: white;
            overflow: hidden;
        }

        .vehicle-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Quick consultation fixed sidebar */
        .quick-consult {
            position: fixed;
            right: 2rem;
            top: 50%;
            transform: translateY(-50%);
            width: 320px;
            z-index: 1030;
        }

        /* Mobile bottom nav */
        .mobile-bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            display: none;
        }

        /* Footer */
        footer {
            background: var(--dark-color);
            color: white;
        }

        footer a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
        }

        footer a:hover {
            color: var(--secondary-color);
        }

        /* Offcanvas Full Screen Menu */
        .offcanvas-fullscreen {
            width: 100vw !important;
            height: 100vh !important;
        }

        .offcanvas-fullscreen .offcanvas-body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .offcanvas-fullscreen .nav-link {
            font-size: 1.5rem;
            padding: 1rem 0;
            color: #333;
            font-weight: 500;
            transition: color 0.3s;
        }

        .offcanvas-fullscreen .nav-link:hover {
            color: var(--primary-color);
        }

        .offcanvas-fullscreen .offcanvas-icons {
            display: flex;
            gap: 2rem;
            margin-top: 2rem;
        }

        .offcanvas-fullscreen .offcanvas-icons i {
            font-size: 2rem;
            cursor: pointer;
            transition: color 0.3s;
        }

        .offcanvas-fullscreen .offcanvas-icons i:hover {
            color: var(--primary-color);
        }

        .offcanvas-fullscreen .btn-close {
            font-size: 1.5rem;
            padding: 1rem;
        }

        /* Review Image */
        .review-image {
            height: 200px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 5rem;
            color: white;
            border-radius: 15px 15px 0 0;
        }

        /* Review Carousel */
        #reviewCarousel .carousel-control-prev,
        #reviewCarousel .carousel-control-next {
            width: 5%;
            opacity: 0.8;
        }

        #reviewCarousel .carousel-control-prev:hover,
        #reviewCarousel .carousel-control-next:hover {
            opacity: 1;
        }

        #reviewCarousel .carousel-control-prev-icon,
        #reviewCarousel .carousel-control-next-icon {
            background-color: var(--primary-color);
            border-radius: 50%;
            padding: 20px;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .quick-consult {
                display: none !important;
            }
        }

        @media (max-width: 768px) {
            .hero-carousel {
                height: 400px;
            }

            .mobile-bottom-nav {
                display: block;
            }

            .brand-filter {
                margin-top: 2rem !important;
            }
        }
    </style>
</head>
<body>
    <!-- Top Bar -->
    <div class="bg-primary text-white text-center py-2">
        <span class="me-3"><i class="bi bi-telephone-fill"></i> 상담문의: 010-4299-3772</span>
        <span><i class="bi bi-clock-fill"></i> 운영시간: 평일 09:00 - 18:00</span>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold fs-3 text-primary" href="#">ARI RENT</a>

            <!-- Desktop Menu -->
            <div class="collapse navbar-collapse d-none d-lg-flex" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link px-3" href="#company">회사소개</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="#new-rental">신차장기렌트</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="#used-rental">중고장기렌트</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="#reviews">출고후기</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="#contact">고객센터</a>
                    </li>
                </ul>
                <div class="d-flex gap-3">
                    <i class="bi bi-search fs-5"></i>
                    <i class="bi bi-cart fs-5"></i>
                </div>
            </div>

            <!-- Mobile Menu Toggler -->
            <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu" aria-controls="offcanvasMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>

    <!-- Offcanvas Full Screen Mobile Menu -->
    <div class="offcanvas offcanvas-end offcanvas-fullscreen" tabindex="-1" id="offcanvasMenu" aria-labelledby="offcanvasMenuLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title fw-bold fs-3 text-primary" id="offcanvasMenuLabel">AriRent</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="#company" data-bs-dismiss="offcanvas">회사소개</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#new-rental" data-bs-dismiss="offcanvas">신차장기렌트</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#used-rental" data-bs-dismiss="offcanvas">중고장기렌트</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#reviews" data-bs-dismiss="offcanvas">출고후기</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#contact" data-bs-dismiss="offcanvas">고객센터</a>
                </li>
            </ul>
            <div class="offcanvas-icons">
                <i class="bi bi-search"></i>
                <i class="bi bi-cart"></i>
            </div>
        </div>
    </div>

    <!-- Hero Carousel -->
    <div id="heroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active carousel-item-gradient-1">
                <div class="d-flex justify-content-center align-items-center h-100">
                    <div class="text-center text-white p-4">
                        <h2 class="display-3 fw-bold mb-3" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">제네시스 GV80</h2>
                        <p class="fs-4 mb-4">럭셔리 SUV의 정점</p>
                        <div class="fs-1 fw-bold mb-4" style="color: #fae100;">월 425,000원~</div>
                        <button class="btn btn-lg px-5 py-3 rounded-pill fw-bold" style="background: #fae100; color: #333;">상담 신청하기</button>
                    </div>
                </div>
            </div>
            <div class="carousel-item carousel-item-gradient-2">
                <div class="d-flex justify-content-center align-items-center h-100">
                    <div class="text-center text-white p-4">
                        <h2 class="display-3 fw-bold mb-3" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">현대 팰리세이드</h2>
                        <p class="fs-4 mb-4">가족을 위한 최고의 선택</p>
                        <div class="fs-1 fw-bold mb-4" style="color: #fae100;">월 380,000원~</div>
                        <button class="btn btn-lg px-5 py-3 rounded-pill fw-bold" style="background: #fae100; color: #333;">상담 신청하기</button>
                    </div>
                </div>
            </div>
            <div class="carousel-item carousel-item-gradient-3">
                <div class="d-flex justify-content-center align-items-center h-100">
                    <div class="text-center text-white p-4">
                        <h2 class="display-3 fw-bold mb-3" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">기아 카니발</h2>
                        <p class="fs-4 mb-4">넓고 편안한 프리미엄 미니밴</p>
                        <div class="fs-1 fw-bold mb-4" style="color: #fae100;">월 350,000원~</div>
                        <button class="btn btn-lg px-5 py-3 rounded-pill fw-bold" style="background: #fae100; color: #333;">상담 신청하기</button>
                    </div>
                </div>
            </div>
            <div class="carousel-item carousel-item-gradient-4">
                <div class="d-flex justify-content-center align-items-center h-100">
                    <div class="text-center text-white p-4">
                        <h2 class="display-3 fw-bold mb-3" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">현대 아반떼</h2>
                        <p class="fs-4 mb-4">경제적이고 실용적인 준중형 세단</p>
                        <div class="fs-1 fw-bold mb-4" style="color: #fae100;">월 280,000원~</div>
                        <button class="btn btn-lg px-5 py-3 rounded-pill fw-bold" style="background: #fae100; color: #333;">상담 신청하기</button>
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
        <div class="bg-white rounded-4 shadow-lg p-4" data-aos="fade-up">
            <div class="d-flex flex-wrap justify-content-center gap-2">
                <button class="btn btn-outline-secondary rounded-pill px-4 filter-btn active" data-brand="all">전체</button>
                <button class="btn btn-outline-secondary rounded-pill px-4 filter-btn" data-brand="hyundai">현대</button>
                <button class="btn btn-outline-secondary rounded-pill px-4 filter-btn" data-brand="kia">기아</button>
                <button class="btn btn-outline-secondary rounded-pill px-4 filter-btn" data-brand="genesis">제네시스</button>
                <button class="btn btn-outline-secondary rounded-pill px-4 filter-btn" data-brand="renault">르노</button>
                <button class="btn btn-outline-secondary rounded-pill px-4 filter-btn" data-brand="chevrolet">쉐보레</button>
                <button class="btn btn-outline-secondary rounded-pill px-4 filter-btn" data-brand="import">수입차</button>
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
                <button type="submit" class="btn w-100 fw-bold" style="background: #fae100;">상담 신청하기</button>
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
$res = AriRent\Rent::getRents(["r.car_type" =>"NEW"]);
foreach($res as $item):
    $prices = Arirent\Rent::getPrices($item->idx);
?>
                <!-- Hyundai -->
                <div class="col" data-brand="hyundai" data-aos="fade-up">
                    <div class="card vehicle-card shadow-sm rounded-4 border-0">
                        <div class="vehicle-image"><img src="<?php echo $item->featured_image?>" class="img-fluid" loading="lazy"></div>
                        <div class="card-body">
                            <h5 class="card-title fw-bold"><?php echo $item->title?></h5>
                            <p class="text-primary fw-bold fs-5">월 <?php echo number_format($prices[0]->monthly_rent_amount)?>원~</p>
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
                                <div class="card shadow-sm rounded-4 border-0">
                                    <div class="review-image"><i class="bi bi-truck-front-fill"></i></div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 50px; height: 50px;">
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
                                <div class="card shadow-sm rounded-4 border-0">
                                    <div class="review-image"><i class="bi bi-bus-front-fill"></i></div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 50px; height: 50px;">
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
                                <div class="card shadow-sm rounded-4 border-0">
                                    <div class="review-image"><i class="bi bi-truck-front-fill"></i></div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 50px; height: 50px;">
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
                                <div class="card shadow-sm rounded-4 border-0">
                                    <div class="review-image"><i class="bi bi-car-front-fill"></i></div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 50px; height: 50px;">
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
                                <div class="card shadow-sm rounded-4 border-0">
                                    <div class="review-image"><i class="bi bi-truck-front-fill"></i></div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 50px; height: 50px;">
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
                                <div class="card shadow-sm rounded-4 border-0">
                                    <div class="review-image"><i class="bi bi-car-front-fill"></i></div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 50px; height: 50px;">
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

    <!-- Footer -->
    <footer class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-3">
                    <h5 style="color: #fae100;">아리렌트</h5>
                    <p>신차 장기렌트 전문</p>
                    <p>고객만족 1위 브랜드</p>
                </div>
                <div class="col-md-3">
                    <h5 style="color: #fae100;">고객센터</h5>
                    <p><i class="bi bi-telephone-fill"></i> 010-4299-3772</p>
                    <p><i class="bi bi-clock-fill"></i> 평일 09:00 - 18:00</p>
                    <p><i class="bi bi-envelope-fill"></i> contact@arirent.co.kr</p>
                </div>
                <!-- <div class="col-md-3">
                    <h5 style="color: #fae100;">회사정보</h5>
                    <p>대표: 홍길동</p>
                    <p>사업자등록번호: 123-45-67890</p>
                    <p>주소: 서울시 강남구</p>
                </div> -->
                <div class="col-md-3">
                    <h5 style="color: #fae100;">약관 및 정책</h5>
                    <a href="#" class="d-block">이용약관</a>
                    <a href="#" class="d-block">개인정보처리방침</a>
                    <!-- <a href="#" class="d-block">위치기반서비스 이용약관</a> -->
                </div>
            </div>
            <hr class="my-4" style="border-color: rgba(255,255,255,0.1);">
            <p class="text-center text-white-50">&copy; 2025 AriRent. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- Mobile Bottom Navigation -->
    <nav class="mobile-bottom-nav bg-white shadow-lg">
        <div class="d-flex justify-content-around align-items-center p-3">
            <a href="tel:010-4299-3772" class="text-decoration-none text-dark text-center">
                <i class="bi bi-telephone fs-4 d-block"></i>
                <small>전화</small>
            </a>
            <a href="#" class="text-decoration-none text-dark text-center">
                <i class="bi bi-chat-dots fs-4 d-block"></i>
                <small>카톡</small>
            </a>
            <a href="#" class="text-decoration-none text-dark text-center">
                <i class="bi bi-pencil-square fs-4 d-block"></i>
                <small>상담</small>
            </a>
            <a href="#" class="text-decoration-none text-dark text-center">
                <i class="bi bi-journal-text fs-4 d-block"></i>
                <small>블로그</small>
            </a>
        </div>
    </nav>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true
        });

        // Auto-start hero carousel
        const heroCarousel = new bootstrap.Carousel(document.getElementById('heroCarousel'), {
            interval: 2500,
            ride: 'carousel'
        });

        // Auto-start review carousel
        const reviewCarousel = new bootstrap.Carousel(document.getElementById('reviewCarousel'), {
            interval: 4000,
            ride: 'carousel'
        });

        // Brand Filter
        const filterBtns = document.querySelectorAll('.filter-btn');
        const vehicleCards = document.querySelectorAll('#vehicleGrid > .col');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                const brand = this.getAttribute('data-brand');

                vehicleCards.forEach(card => {
                    if (brand === 'all' || card.getAttribute('data-brand') === brand) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });

        // Consultation Form
        document.getElementById('consultForm').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('상담 신청이 완료되었습니다!\n빠른 시일 내에 연락드리겠습니다.');
            this.reset();
        });

        // Phone number formatting
        document.querySelectorAll('input[type="tel"]').forEach(input => {
            input.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 3 && value.length <= 7) {
                    value = value.replace(/(\d{3})(\d{1,4})/, '$1-$2');
                } else if (value.length > 7) {
                    value = value.replace(/(\d{3})(\d{4})(\d{1,4})/, '$1-$2-$3');
                }
                e.target.value = value;
            });
        });
    </script>
</body>
</html>