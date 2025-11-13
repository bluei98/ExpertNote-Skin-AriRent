<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php ExpertNote\Core::printPageTitle()?></title>
    <meta name="title" content="<?php echo ExpertNote\Core::getPageTitle()?>">
    <meta name="keywords" content="<?php echo ExpertNote\Core::getPageKeywords()?>">
    <meta name="description" content="<?php echo ExpertNote\Core::getPageDescription()?>">

    <meta itemprop="name" content="<?php echo ExpertNote\Core::getPageTitle()?>">
    <meta itemprop="description" content="<?php echo ExpertNote\Core::getPageDescription()?>">
    <!-- <meta itemprop="image" content="https://forexliga.com/ko/skins/forexliga/assets/images/featured-ko-00.jpg"> -->


    <?php echo ExpertNote\Core::printMetaTags()?>

    <link rel="alternate" hreflang="x-default" href="<?php echo ExpertNote\Core::getBaseUrl()?>">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- AOS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #1B71D7;
            --secondary-color: #0F4C81;
            --accent-color: #FFC107;
            --light-color: #F8F9FA;
            --dark-color: #2C3E50;
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

        .carousel-item-color-1 {
            background: var(--primary-color);
        }

        .carousel-item-color-2 {
            background: var(--secondary-color);
        }

        .carousel-item-color-3 {
            background: #1A5FA0;
        }

        .carousel-item-color-4 {
            background: var(--primary-color);
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
            background: var(--light-color);
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 5rem;
            color: var(--primary-color);
            border-radius: 0;
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
            border-radius: 0;
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
            <a class="navbar-brand fw-bold fs-3 text-primary" href="/">ARI RENT</a>

            <!-- Desktop Menu -->
            <div class="collapse navbar-collapse d-none d-lg-flex" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link px-3" href="/company">회사소개</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="/#new-rental">신차장기렌트</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="/#used-rental">중고장기렌트</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="/#reviews">출고후기</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="/#contact">고객센터</a>
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
                    <a class="nav-link" href="/company" data-bs-dismiss="offcanvas">회사소개</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/#new-rental" data-bs-dismiss="offcanvas">신차장기렌트</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/#used-rental" data-bs-dismiss="offcanvas">중고장기렌트</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/#reviews" data-bs-dismiss="offcanvas">출고후기</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/#contact" data-bs-dismiss="offcanvas">고객센터</a>
                </li>
            </ul>
            <div class="offcanvas-icons">
                <i class="bi bi-search"></i>
                <i class="bi bi-cart"></i>
            </div>
        </div>
    </div>

<?php echo $contents?>

    <!-- Footer -->
    <footer class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-3">
                    <h5 style="color: var(--accent-color);">아리렌트</h5>
                    <p>신차 장기렌트 전문</p>
                    <p>고객만족 1위 브랜드</p>
                </div>
                <div class="col-md-3">
                    <h5 style="color: var(--accent-color);">고객센터</h5>
                    <p><i class="bi bi-telephone-fill"></i> 010-4299-3772</p>
                    <p><i class="bi bi-clock-fill"></i> 평일 09:00 - 18:00</p>
                    <p><i class="bi bi-envelope-fill"></i> contact@arirent.co.kr</p>
                </div>
                <!-- <div class="col-md-3">
                    <h5 style="color: var(--accent-color);">회사정보</h5>
                    <p>대표: 홍길동</p>
                    <p>사업자등록번호: 123-45-67890</p>
                    <p>주소: 서울시 강남구</p>
                </div> -->
                <div class="col-md-3">
                    <h5 style="color: var(--accent-color);">약관 및 정책</h5>
                    <a href="/terms" class="d-block">이용약관</a>
                    <a href="/privacy" class="d-block">개인정보처리방침</a>
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