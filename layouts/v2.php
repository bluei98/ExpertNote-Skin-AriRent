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
                    <a href="/forum/blog" title="블로그"><i class="bi bi-rss-fill"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg" id="mainNavbar">
        <div class="container">
            <a class="navbar-brand" href="/"><img src="<?php echo ExpertNote\SiteMeta::get('site_logo_default') ?>" height="50" data-light-src="<?php echo ExpertNote\SiteMeta::get('site_logo_default') ?>" data-dark-src="<?php echo ExpertNote\SiteMeta::get('site_logo_dark') ?>" alt="<?php echo sprintf("%s 로고", ExpertNote\SiteMeta::get('site_title')[$i18n->locale])?>"></a>

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
                <img src="<?php echo ExpertNote\SiteMeta::get('site_logo_dark') ?>" height="40" data-light-src="<?php echo ExpertNote\SiteMeta::get('site_logo_default') ?>" data-dark-src="<?php echo ExpertNote\SiteMeta::get('site_logo_dark') ?>" alt="<?php echo sprintf("%s 로고", ExpertNote\SiteMeta::get('site_title')[$i18n->locale])?>"></a>
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="nav flex-column mobile-nav">
                <li class="nav-item">
                    <a class="nav-link" href="/company" data-bs-dismiss="offcanvas">
                        <i class="bi bi-building"></i> 회사소개
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/car-list?car_type=NEW" data-bs-dismiss="offcanvas">
                        <i class="bi bi-car-front-fill"></i> 신차장기렌트
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/car-list?car_type=USED" data-bs-dismiss="offcanvas">
                        <i class="bi bi-shield-check"></i> 중고장기렌트
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/forum/review" data-bs-dismiss="offcanvas">
                        <i class="bi bi-chat-quote"></i> 출고후기
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/forum/review" data-bs-dismiss="offcanvas">
                        <i class="bi bi-headset"></i> 고객센터
                    </a>
                </li>
            </ul>
            <hr>
            <!-- <div class="mobile-submenu">
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
            </div> -->
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

<?php echo $contents?>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="footer-brand">
                        <a class="navbar-brand" href="/"><img src="<?php echo ExpertNote\SiteMeta::get('site_logo_dark') ?>" height="50" data-light-src="<?php echo ExpertNote\SiteMeta::get('site_logo_default') ?>" data-dark-src="<?php echo ExpertNote\SiteMeta::get('site_logo_dark') ?>" alt="<?php echo sprintf("%s 로고", ExpertNote\SiteMeta::get('site_title')[$i18n->locale])?>"></a>
                    </div>
                    <p class="footer-desc">
                        아리렌트는 무심사 저신용 장기렌트 전문 플랫폼입니다.<br>
                        누구나 부담 없이 원하는 차를 만날 수 있도록 최선을 다하겠습니다.
                    </p>
                    <div class="footer-social">
                        <!-- <a href="#"><i class="bi bi-instagram"></i></a> -->
                        <a href="https://www.youtube.com/@%EC%95%84%EB%A6%AC%EB%A0%8C%ED%8A%B8"><i class="bi bi-youtube"></i></a>
                        <a href="/kakaolink"><i class="bi bi-chat-dots-fill"></i></a>
                        <a href="/forum/blog"><i class="bi bi-rss-fill"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h5 class="footer-title">서비스</h5>
                    <ul class="footer-links">
                        <li><a href="/car-list?car_type=NEW">신차 장기렌트</a></li>
                        <li><a href="/car-list?car_type=USDED">중고차 장기렌트</a></li>
                        <li><a href="/forum/review">출고 후기</a></li>
                        <li><a href="/forum/blog">블로그</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h5 class="footer-title">회사 정보</h5>
                    <ul class="footer-links">
                        <li><a href="/company">회사소개</a></li>
                        <li><a href="/forum/announcement">공지사항</a></li>
                        <!-- <li><a href="#">FAQ</a></li>
                        <li><a href="#">제휴 문의</a></li> -->
                    </ul>
                </div>
                <div class="col-lg-4 col-md-4">
                    <h5 class="footer-title">고객센터</h5>
                    <ul class="footer-contact">
                        <li class="align-items-center">
                            <i class="bi bi-telephone-fill"></i>
                            <span>대표전화: 010-4299-3772<br>평일 09:00 - 18:00 (점심 12:00 - 13:00)</span>
                        </li>
                        <li class="align-items-center">
                            <i class="bi bi-envelope-fill"></i>
                            <span>이메일: support@arirent.co.kr</span>
                        </li>
                        <li class="align-items-center">
                            <i class="bi bi-geo-alt-fill"></i>
                            <span>주소: 대전광역시 동구 용운로 80 (용운동)</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p class="footer-copyright">
                    &copy; 2024 DreanCar. All rights reserved. 사업자등록번호: 272-09-03361
                </p>
                <div class="footer-policy">
                    <a href="/terms">이용약관</a>
                    <a href="/privacy">개인정보처리방침</a>
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
