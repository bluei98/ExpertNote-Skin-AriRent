<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" type="image/x-icon" href="<?php echo ExpertNote\SiteMeta::get('site_favicon_default') ?>">

    <title><?php ExpertNote\Core::printPageTitle()?></title>
    <meta name="title" content="<?php echo ExpertNote\Core::getPageTitle()?>">
    <meta name="keywords" content="<?php echo ExpertNote\Core::getPageKeywords()?>">
    <meta name="description" content="<?php echo ExpertNote\Core::getPageDescription()?>">

    <meta itemprop="name" content="<?php echo ExpertNote\Core::getPageTitle()?>">
    <meta itemprop="description" content="<?php echo ExpertNote\Core::getPageDescription()?>">

    <?php ExpertNote\Core::printMetaTags()?>

    <link rel="alternate" hreflang="x-default" href="<?php echo ExpertNote\Core::getBaseUrl()?>">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;600;700;800;900&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css2/common.css">

<?php if(ExpertNote\SiteMeta::get('google_analytics_key')):?>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-26ECL3HDKL"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', '<?php echo ExpertNote\SiteMeta::get('google_analytics_key')?>');
    </script>
<?php endif;?>
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="top-bar-content">
                <div class="top-bar-left">
                    <span><i class="bi bi-telephone-fill"></i> 상담문의: 010-4299-3772</span>
                    <span><i class="bi bi-clock-fill"></i> 운영시간: 평일 09:00 - 18:00</span>
                    <span><i class="bi bi-geo-alt-fill"></i> 전국 어디서나 출고 가능</span>
                </div>
                <div class="top-bar-right">
                    <a href="/kakaochannel" title="카카오톡 채널" target="_blank"><i class="bi bi-chat-dots-fill"></i></a>
                    <!-- <a href="#" title="인스타그램"><i class="bi bi-instagram"></i></a> -->
                    <a href="https://www.youtube.com/@%EC%95%84%EB%A6%AC%EB%A0%8C%ED%8A%B8" title="유튜브"><i class="bi bi-youtube" target="_blank"></i></a>
                    <a href="/forum/blog" title="블로그"><i class="bi bi-rss"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg" id="mainNavbar">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="<?php echo ExpertNote\SiteMeta::get('site_logo_default') ?>" height="40" data-light-src="<?php echo ExpertNote\SiteMeta::get('site_logo_default') ?>" data-dark-src="<?php echo ExpertNote\SiteMeta::get('site_logo_dark') ?>" alt="<?php echo sprintf("%s 로고", ExpertNote\SiteMeta::get('site_title')[$i18n->locale])?>"></a>
            </a>

            <!-- Mobile Toggle Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" aria-controls="mobileMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Desktop Menu (lg 이상에서만 표시) -->
            <div class="d-none d-lg-flex align-items-center flex-grow-1" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link px-3" href="/company">회사소개</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="/car-list?car_type=NEW">신차장기렌트</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="/car-list?car_type=USED">중고장기렌트</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="/forum/review">출고후기</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="/forum/blog">블로그</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="/forum/announcement">고객센터</a>
                    </li>
                    <!-- <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            페이지
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="login.html"><i class="bi bi-box-arrow-in-right"></i> 로그인</a></li>
                            <li><a class="dropdown-item" href="signup.html"><i class="bi bi-person-plus"></i> 회원가입</a></li>
                            <li><a class="dropdown-item" href="signup-done.html"><i class="bi bi-check-circle"></i> 회원가입 완료</a></li>
                            <li><a class="dropdown-item" href="find-password.html"><i class="bi bi-key"></i> 비밀번호 찾기</a></li>
                            <li><a class="dropdown-item" href="find-password-done.html"><i class="bi bi-envelope-check"></i> 비밀번호 찾기 완료</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="car-list.html"><i class="bi bi-car-front"></i> 차량 목록</a></li>
                            <li><a class="dropdown-item" href="forum-list-default.html"><i class="bi bi-list-ul"></i> 게시판</a></li>
                            <li><a class="dropdown-item" href="forum-list-blog.html"><i class="bi bi-newspaper"></i> 블로그</a></li>
                            <li><a class="dropdown-item" href="forum-list-card.html"><i class="bi bi-images"></i> 갤러리</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="typography.html"><i class="bi bi-fonts"></i> Typography</a></li>
                            <li><a class="dropdown-item" href="components.html"><i class="bi bi-grid-3x3-gap"></i> Components</a></li>
                        </ul>
                    </li> -->
                </ul>
                <a href="#consultForm" class="btn btn-consult">
                    <i class="bi bi-chat-heart-fill"></i> 무료 상담신청
                </a>
            </div>
        </div>
    </nav>

    <!-- Mobile Offcanvas Menu -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="mobileMenuLabel">
                <img src="<?php echo ExpertNote\SiteMeta::get('site_logo_default') ?>" height="40" data-light-src="<?php echo ExpertNote\SiteMeta::get('site_logo_default') ?>" data-dark-src="<?php echo ExpertNote\SiteMeta::get('site_logo_dark') ?>" alt="<?php echo sprintf("%s 로고", ExpertNote\SiteMeta::get('site_title')[$i18n->locale])?>"></a>
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="nav flex-column mobile-nav">
                <li class="nav-item">
                    <a class="nav-link" href="about.html" data-bs-dismiss="offcanvas">
                        <i class="bi bi-building"></i> 회사소개
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="car-list.html" data-bs-dismiss="offcanvas">
                        <i class="bi bi-car-front-fill"></i> 신차장기렌트
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-bs-dismiss="offcanvas">
                        <i class="bi bi-shield-check"></i> 중고장기렌트
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-bs-dismiss="offcanvas">
                        <i class="bi bi-chat-quote"></i> 출고후기
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="forum-list-default.html" data-bs-dismiss="offcanvas">
                        <i class="bi bi-headset"></i> 고객센터
                    </a>
                </li>
            </ul>
            <hr>
            <div class="mobile-submenu">
                <h6 class="px-3 mb-2 text-muted small">페이지</h6>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="login.html" data-bs-dismiss="offcanvas">
                            <i class="bi bi-box-arrow-in-right"></i> 로그인
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="signup.html" data-bs-dismiss="offcanvas">
                            <i class="bi bi-person-plus"></i> 회원가입
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="signup-done.html" data-bs-dismiss="offcanvas">
                            <i class="bi bi-check-circle"></i> 회원가입 완료
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="find-password.html" data-bs-dismiss="offcanvas">
                            <i class="bi bi-key"></i> 비밀번호 찾기
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="find-password-done.html" data-bs-dismiss="offcanvas">
                            <i class="bi bi-envelope-check"></i> 비밀번호 찾기 완료
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="car-list.html" data-bs-dismiss="offcanvas">
                            <i class="bi bi-car-front"></i> 차량 목록
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="forum-list-default.html" data-bs-dismiss="offcanvas">
                            <i class="bi bi-list-ul"></i> 게시판
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="forum-list-blog.html" data-bs-dismiss="offcanvas">
                            <i class="bi bi-newspaper"></i> 블로그
                        </a>
                    </li>
                </ul>
            </div>
            <hr>
            <div class="mobile-contact">
                <p class="mb-2"><i class="bi bi-telephone-fill text-primary"></i> 010-4299-3772</p>
                <p class="text-muted small">평일 09:00 - 18:00</p>
            </div>
            <a href="#consultForm" class="btn btn-consult w-100 mt-3" data-bs-dismiss="offcanvas">
                <i class="bi bi-chat-heart-fill"></i> 무료 상담신청
            </a>
        </div>
    </div>

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
                                <a href="#" class="btn-hero-primary">
                                    <i class="bi bi-search"></i> 차량 검색하기
                                </a>
                                <a href="#" class="btn-hero-secondary">
                                    <i class="bi bi-play-circle"></i> 이용방법 보기
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="hero-decoration"></div>
                    <div class="hero-image">
                        <img src="https://www.hyundai.com/contents/vr360/LX06/exterior/A2B/001.png" alt="현대 자동차">
                    </div>
                </div>

                <!-- Slide 2 -->
                <!-- <div class="swiper-slide hero-slide" style="background: linear-gradient(135deg, #1e293b 0%, #0f766e 100%);">
                    <div class="container">
                        <div class="hero-slide-content">
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
                        <img src="https://www.genesis.com/content/dam/genesis-p2/kr/assets/models/gv80/24my/exterior/gv80-24my-exterior-color-savile-silver.png" alt="제네시스 GV80">
                    </div>
                </div> -->

                <!-- Slide 3 -->
                <!-- <div class="swiper-slide hero-slide" style="background: linear-gradient(135deg, #1e293b 0%, #7c3aed 100%);">
                    <div class="container">
                        <div class="hero-slide-content">
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
                        <img src="https://www.kia.com/content/dam/kia2/kr/ko/vehicles/ev9/24my/exterior/kia-ev9-24my-exterior-color-runway-red.png" alt="기아 EV9">
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
                            <input type="text" class="form-control" placeholder="홍길동">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">연락처</label>
                            <input type="tel" class="form-control" placeholder="010-0000-0000">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">관심 차종</label>
                            <select class="form-select">
                                <option selected>선택해주세요</option>
                                <option>국산 세단</option>
                                <option>국산 SUV</option>
                                <option>수입 세단</option>
                                <option>수입 SUV</option>
                                <option>전기차</option>
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

    <!-- Quick Menu -->
    <section class="quick-menu-section">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">
                    <i class="bi bi-grid-fill"></i>
                    빠른 메뉴
                </div>
                <h2 class="section-title">원하시는 서비스를 <span>선택하세요</span></h2>
                <p class="section-desc">드림카렌트만의 다양한 렌트 서비스를 만나보세요</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="quick-menu-card">
                        <div class="quick-menu-icon">
                            <i class="bi bi-car-front-fill"></i>
                        </div>
                        <h4 class="quick-menu-title">신차 장기렌트</h4>
                        <p class="quick-menu-desc">최신 신차를 부담 없이 장기렌트로 이용해 보세요</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="quick-menu-card">
                        <div class="quick-menu-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h4 class="quick-menu-title">중고차 장기렌트</h4>
                        <p class="quick-menu-desc">품질 검증된 중고차를 합리적인 가격에 렌트하세요</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="quick-menu-card">
                        <div class="quick-menu-icon">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <h4 class="quick-menu-title">구독 렌트</h4>
                        <p class="quick-menu-desc">월 단위로 자유롭게 이용하는 구독형 렌트 서비스</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
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
    </section>

    <!-- Car Search Filter -->
    <section class="search-filter-section">
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
                            <img src="https://www.carlogos.org/car-logos/hyundai-logo.png" alt="현대">
                            <span>현대</span>
                        </div>
                        <div class="brand-item">
                            <img src="https://www.carlogos.org/car-logos/kia-logo.png" alt="기아">
                            <span>기아</span>
                        </div>
                        <div class="brand-item">
                            <img src="https://www.carlogos.org/car-logos/genesis-logo.png" alt="제네시스">
                            <span>제네시스</span>
                        </div>
                        <div class="brand-item">
                            <img src="https://www.carlogos.org/car-logos/bmw-logo.png" alt="BMW">
                            <span>BMW</span>
                        </div>
                        <div class="brand-item">
                            <img src="https://www.carlogos.org/car-logos/mercedes-benz-logo.png" alt="벤츠">
                            <span>벤츠</span>
                        </div>
                        <div class="brand-item">
                            <img src="https://www.carlogos.org/car-logos/audi-logo.png" alt="아우디">
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
    </section>

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
            <div class="row g-4">
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="car-card">
                        <div class="car-image">
                            <span class="car-badge new">NEW</span>
                            <img src="https://www.hyundai.com/contents/vr360/CN07/exterior/WC9/001.png" alt="아반떼">
                        </div>
                        <div class="car-body">
                            <h4 class="car-title">아반떼 CN7</h4>
                            <p class="car-subtitle">현대 | 2024년식</p>
                            <div class="car-info">
                                <span class="car-info-item">가솔린</span>
                                <span class="car-info-item">자동</span>
                                <span class="car-info-item">1.6L</span>
                            </div>
                            <div class="car-price">
                                <p class="car-price-label">월 렌트료</p>
                                <p class="car-price-value">32<small>만원~</small></p>
                                <p class="car-deposit">보증금 0원 가능</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="car-card">
                        <div class="car-image">
                            <span class="car-badge hot">HOT</span>
                            <img src="https://www.hyundai.com/contents/vr360/SU07/exterior/WC9/001.png" alt="투싼">
                        </div>
                        <div class="car-body">
                            <h4 class="car-title">투싼 NX4</h4>
                            <p class="car-subtitle">현대 | 2024년식</p>
                            <div class="car-info">
                                <span class="car-info-item">디젤</span>
                                <span class="car-info-item">자동</span>
                                <span class="car-info-item">2.0L</span>
                            </div>
                            <div class="car-price">
                                <p class="car-price-label">월 렌트료</p>
                                <p class="car-price-value">45<small>만원~</small></p>
                                <p class="car-deposit">보증금 0원 가능</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="car-card">
                        <div class="car-image">
                            <span class="car-badge recommend">추천</span>
                            <img src="https://www.kia.com/content/dam/kia2/kr/ko/vehicles/k8/24my/exterior/kia-k8-24my-exterior-color-snow-white-pearl.png" alt="K8">
                        </div>
                        <div class="car-body">
                            <h4 class="car-title">K8</h4>
                            <p class="car-subtitle">기아 | 2024년식</p>
                            <div class="car-info">
                                <span class="car-info-item">가솔린</span>
                                <span class="car-info-item">자동</span>
                                <span class="car-info-item">2.5L</span>
                            </div>
                            <div class="car-price">
                                <p class="car-price-label">월 렌트료</p>
                                <p class="car-price-value">58<small>만원~</small></p>
                                <p class="car-deposit">보증금 0원 가능</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="car-card">
                        <div class="car-image">
                            <span class="car-badge no-deposit">무보증</span>
                            <img src="https://www.hyundai.com/contents/vr360/PE07/exterior/WW2/001.png" alt="팰리세이드">
                        </div>
                        <div class="car-body">
                            <h4 class="car-title">팰리세이드</h4>
                            <p class="car-subtitle">현대 | 2024년식</p>
                            <div class="car-info">
                                <span class="car-info-item">디젤</span>
                                <span class="car-info-item">자동</span>
                                <span class="car-info-item">2.2L</span>
                            </div>
                            <div class="car-price">
                                <p class="car-price-label">월 렌트료</p>
                                <p class="car-price-value">72<small>만원~</small></p>
                                <p class="car-deposit">보증금 0원 가능</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-5">
                <a href="#" class="btn-more">
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
            <div class="row g-4">
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="car-card">
                        <div class="car-image">
                            <span class="car-badge hot">인기</span>
                            <img src="https://www.hyundai.com/contents/vr360/DN08/exterior/W3G/001.png" alt="쏘나타">
                        </div>
                        <div class="car-body">
                            <h4 class="car-title">쏘나타 DN8</h4>
                            <p class="car-subtitle">현대 | 2022년식 | 25,000km</p>
                            <div class="car-info">
                                <span class="car-info-item">가솔린</span>
                                <span class="car-info-item">자동</span>
                            </div>
                            <div class="car-price">
                                <p class="car-price-label">월 렌트료</p>
                                <p class="car-price-value">35<small>만원~</small></p>
                                <p class="car-deposit">즉시 출고 가능</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="car-card">
                        <div class="car-image">
                            <span class="car-badge no-deposit">무보증</span>
                            <img src="https://www.kia.com/content/dam/kia2/kr/ko/vehicles/sportage/23my/exterior/kia-sportage-23my-exterior-color-snow-white-pearl.png" alt="스포티지">
                        </div>
                        <div class="car-body">
                            <h4 class="car-title">스포티지 NQ5</h4>
                            <p class="car-subtitle">기아 | 2023년식 | 18,000km</p>
                            <div class="car-info">
                                <span class="car-info-item">디젤</span>
                                <span class="car-info-item">자동</span>
                            </div>
                            <div class="car-price">
                                <p class="car-price-label">월 렌트료</p>
                                <p class="car-price-value">42<small>만원~</small></p>
                                <p class="car-deposit">즉시 출고 가능</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="car-card">
                        <div class="car-image">
                            <span class="car-badge recommend">추천</span>
                            <img src="https://www.genesis.com/content/dam/genesis-p2/kr/assets/models/g80/23my/exterior/g80-23my-exterior-color-uyuni-white.png" alt="G80">
                        </div>
                        <div class="car-body">
                            <h4 class="car-title">제네시스 G80</h4>
                            <p class="car-subtitle">제네시스 | 2022년식 | 32,000km</p>
                            <div class="car-info">
                                <span class="car-info-item">가솔린</span>
                                <span class="car-info-item">자동</span>
                            </div>
                            <div class="car-price">
                                <p class="car-price-label">월 렌트료</p>
                                <p class="car-price-value">68<small>만원~</small></p>
                                <p class="car-deposit">즉시 출고 가능</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="car-card">
                        <div class="car-image">
                            <span class="car-badge new">신규</span>
                            <img src="https://www.hyundai.com/contents/vr360/ST07/exterior/SSS/001.png" alt="싼타페">
                        </div>
                        <div class="car-body">
                            <h4 class="car-title">싼타페 MX5</h4>
                            <p class="car-subtitle">현대 | 2023년식 | 15,000km</p>
                            <div class="car-info">
                                <span class="car-info-item">하이브리드</span>
                                <span class="car-info-item">자동</span>
                            </div>
                            <div class="car-price">
                                <p class="car-price-label">월 렌트료</p>
                                <p class="car-price-value">55<small>만원~</small></p>
                                <p class="car-deposit">즉시 출고 가능</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-5">
                <a href="#" class="btn-more">
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
                <p class="section-desc">드림카렌트를 이용해주신 고객님들의 생생한 후기</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="review-card">
                        <div class="review-image">
                            <img src="https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=400&h=300&fit=crop" alt="출고 후기">
                        </div>
                        <div class="review-body">
                            <h4 class="review-title">저신용이라 걱정했는데 정말 가능하네요!</h4>
                            <div class="review-meta">
                                <div class="review-author">
                                    <div class="review-author-avatar">김</div>
                                    <span class="review-author-name">김*호 님</span>
                                </div>
                                <div class="review-stars">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                </div>
                            </div>
                            <p class="review-date">2024.01.15 | 아반떼 CN7</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="review-card">
                        <div class="review-image">
                            <img src="https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=400&h=300&fit=crop" alt="출고 후기">
                        </div>
                        <div class="review-body">
                            <h4 class="review-title">상담도 친절하고 출고도 빠르게 받았어요</h4>
                            <div class="review-meta">
                                <div class="review-author">
                                    <div class="review-author-avatar">이</div>
                                    <span class="review-author-name">이*영 님</span>
                                </div>
                                <div class="review-stars">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                </div>
                            </div>
                            <p class="review-date">2024.01.12 | 투싼 NX4</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="review-card">
                        <div class="review-image">
                            <img src="https://images.unsplash.com/photo-1502877338535-766e1452684a?w=400&h=300&fit=crop" alt="출고 후기">
                        </div>
                        <div class="review-body">
                            <h4 class="review-title">보증금 없이 K8 출고했습니다. 감사합니다!</h4>
                            <div class="review-meta">
                                <div class="review-author">
                                    <div class="review-author-avatar">박</div>
                                    <span class="review-author-name">박*현 님</span>
                                </div>
                                <div class="review-stars">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-half"></i>
                                </div>
                            </div>
                            <p class="review-date">2024.01.08 | K8</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="why-section">
        <div class="container">
            <div class="section-header">
                <div class="section-badge" style="background: rgba(255,255,255,0.1); color: #fff;">
                    <i class="bi bi-trophy-fill"></i>
                    드림카렌트만의 장점
                </div>
                <h2 class="section-title">왜 <span>드림카렌트</span>인가요?</h2>
                <p class="section-desc">다른 곳과는 다른 드림카렌트만의 특별한 혜택</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="why-card">
                        <div class="why-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h4 class="why-title">100% 무심사</h4>
                        <p class="why-desc">신용등급 관계없이 누구나 이용 가능한 무심사 장기렌트</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="why-card">
                        <div class="why-icon">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                        <h4 class="why-title">보증금 0원</h4>
                        <p class="why-desc">초기 비용 부담 없이 보증금 0원부터 시작 가능</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="why-card">
                        <div class="why-icon">
                            <i class="bi bi-truck"></i>
                        </div>
                        <h4 class="why-title">전국 출고</h4>
                        <p class="why-desc">전국 어디서나 원하는 장소로 출고 배송 가능</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
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
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="100">
                    <div class="stat-item">
                        <div class="stat-number">15,000+</div>
                        <div class="stat-label">누적 출고 건수</div>
                    </div>
                </div>
                <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="200">
                    <div class="stat-item">
                        <div class="stat-number">98.5%</div>
                        <div class="stat-label">고객 만족도</div>
                    </div>
                </div>
                <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="300">
                    <div class="stat-item">
                        <div class="stat-number">500+</div>
                        <div class="stat-label">보유 차량</div>
                    </div>
                </div>
                <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="400">
                    <div class="stat-item">
                        <div class="stat-number">10년+</div>
                        <div class="stat-label">업력</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-card" data-aos="fade-up">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h2 class="cta-title">지금 바로 무료 상담 받아보세요!</h2>
                        <p class="cta-desc">전문 상담사가 고객님께 맞는 최적의 렌트 조건을 안내해 드립니다.</p>
                    </div>
                    <div class="col-lg-4">
                        <div class="cta-buttons">
                            <a href="tel:010-4299-3772" class="btn-cta-primary">
                                <i class="bi bi-telephone-fill"></i> 010-4299-3772
                            </a>
                            <a href="#" class="btn-cta-secondary">
                                <i class="bi bi-chat-dots-fill"></i> 카톡 상담
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="footer-brand">
                        <i class="bi bi-car-front-fill"></i>
                        DreanCar
                    </div>
                    <p class="footer-desc">
                        드림카렌트는 무심사 저신용 장기렌트 전문 플랫폼입니다.<br>
                        누구나 부담 없이 원하는 차를 만날 수 있도록 최선을 다하겠습니다.
                    </p>
                    <div class="footer-social">
                        <a href="#"><i class="bi bi-instagram"></i></a>
                        <a href="#"><i class="bi bi-youtube"></i></a>
                        <a href="#"><i class="bi bi-chat-dots-fill"></i></a>
                        <a href="#"><i class="bi bi-pencil-square"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h5 class="footer-title">서비스</h5>
                    <ul class="footer-links">
                        <li><a href="#">신차 장기렌트</a></li>
                        <li><a href="#">중고차 장기렌트</a></li>
                        <li><a href="#">구독 렌트</a></li>
                        <li><a href="#">법인 렌트</a></li>
                        <li><a href="#">견적 문의</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h5 class="footer-title">회사 정보</h5>
                    <ul class="footer-links">
                        <li><a href="about.html">회사소개</a></li>
                        <li><a href="#">출고 후기</a></li>
                        <li><a href="#">공지사항</a></li>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">제휴 문의</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-4">
                    <h5 class="footer-title">고객센터</h5>
                    <ul class="footer-contact">
                        <li>
                            <i class="bi bi-telephone-fill"></i>
                            <span>대표전화: 010-4299-3772<br>평일 09:00 - 18:00 (점심 12:00 - 13:00)</span>
                        </li>
                        <li>
                            <i class="bi bi-envelope-fill"></i>
                            <span>이메일: contact@dreancar.co.kr</span>
                        </li>
                        <li>
                            <i class="bi bi-geo-alt-fill"></i>
                            <span>주소: 서울특별시 강남구 테헤란로 123<br>드림카빌딩 5층</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p class="footer-copyright">
                    &copy; 2024 DreanCar. All rights reserved. 사업자등록번호: 123-45-67890
                </p>
                <div class="footer-policy">
                    <a href="terms.html">이용약관</a>
                    <a href="privacy.html">개인정보처리방침</a>
                    <a href="#">위치기반서비스</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Floating Buttons -->
    <div class="floating-buttons">
        <a href="/kakaolink" class="floating-btn kakao" title="카카오톡 상담">
            <i class="bi bi-chat-dots-fill"></i>
        </a>
        <a href="tel:010-4299-3772" class="floating-btn phone" title="전화 상담">
            <i class="bi bi-telephone-fill"></i>
        </a>
        <a href="#" class="floating-btn top" id="scrollTop" title="맨 위로">
            <i class="bi bi-chevron-up"></i>
        </a>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script>
        // AOS Initialize
        AOS.init({
            duration: 800,
            once: true
        });

        // Swiper Initialize
        const heroSwiper = new Swiper('.hero-swiper', {
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            effect: 'fade',
            fadeEffect: {
                crossFade: true
            }
        });

        // Navbar Scroll Effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('mainNavbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Scroll to Top
        document.getElementById('scrollTop').addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Brand Filter Click
        document.querySelectorAll('.brand-item').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.brand-item').forEach(i => i.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Price Filter Click
        document.querySelectorAll('.price-item').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.price-item').forEach(i => i.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
</body>
</html>
