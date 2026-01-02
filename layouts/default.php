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
    <!-- <meta itemprop="image" content="https://forexliga.com/ko/skins/forexliga/assets/images/featured-ko-00.jpg"> -->

    <?php ExpertNote\Core::printMetaTags()?>

    <link rel="alternate" hreflang="x-default" href="<?php echo ExpertNote\Core::getBaseUrl()?>">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- AOS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Common CSS -->
    <link href="/assets/css/common.min.css" rel="stylesheet">
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
    <div class="bg-primary text-white text-center py-2">
        <span class="me-3 d-none d-md-inline"><i class="bi bi-telephone-fill"></i> 상담문의: 010-4299-3772</span>
        <span><i class="bi bi-clock-fill"></i> 운영시간: 평일 09:00 - 18:00</span>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container-xl px-4">
            <a class="navbar-brand fw-bold fs-3 text-primary" href="/">
                <img src="<?php echo ExpertNote\SiteMeta::get('site_logo_default') ?>" height="40" data-light-src="<?php echo ExpertNote\SiteMeta::get('site_logo_default') ?>" data-dark-src="<?php echo ExpertNote\SiteMeta::get('site_logo_dark') ?>" alt="<?php echo sprintf("%s 로고", ExpertNote\SiteMeta::get('site_title')[$i18n->locale])?>"></a>
            </a>

            <!-- Desktop Menu -->
            <div class="collapse navbar-collapse d-none d-lg-flex" id="navbarNav">
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
                </ul>
                <div class="d-flex gap-3">
                    <i class="bi bi-search fs-5" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#searchModal"></i>
                    <!-- <i class="bi bi-cart fs-5" style="cursor: pointer;"></i> -->
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
            <h5 class="offcanvas-title fw-bold fs-3 text-primary" id="offcanvasMenuLabel">
                <img src="<?php echo ExpertNote\SiteMeta::get('site_logo_default') ?>" height="40" data-light-src="<?php echo ExpertNote\SiteMeta::get('site_logo_default') ?>" data-dark-src="<?php echo ExpertNote\SiteMeta::get('site_logo_dark') ?>" alt="<?php echo sprintf("%s 로고", ExpertNote\SiteMeta::get('site_title')[$i18n->locale])?>"></a>
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="/company">회사소개</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/car-list?car_type=NEW">신차장기렌트</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/car-list?car_type=USED">중고장기렌트</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/forum/review">출고후기</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/forum/blog">블로그</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/forum/announcement">고객센터</a>
                </li>
            </ul>
            <div class="offcanvas-icons">
                <i class="bi bi-search" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#searchModal" data-bs-dismiss="offcanvas"></i>
            </div>
        </div>
    </div>

    <!-- Search Modal -->
    <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="searchModalLabel">차량 검색</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="/search" method="GET" id="searchForm">
                        <div class="input-group input-group-lg">
                            <input type="text" class="form-control" name="q" placeholder="차량명, 브랜드를 검색해보세요" required>
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-search"></i> 검색
                            </button>
                        </div>
                    </form>
                    <div class="mt-3">
                        <small class="text-muted">인기 검색어:</small>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            <a href="/search?q=그랜저" class="badge bg-light text-dark text-decoration-none">그랜저</a>
                            <a href="/search?q=아반떼" class="badge bg-light text-dark text-decoration-none">아반떼</a>
                            <a href="/search?q=쏘나타" class="badge bg-light text-dark text-decoration-none">쏘나타</a>
                            <a href="/search?q=K5" class="badge bg-light text-dark text-decoration-none">K5</a>
                            <a href="/search?q=싼타페" class="badge bg-light text-dark text-decoration-none">싼타페</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php echo $contents?>

    <!-- Footer -->
    <footer class="py-5 d-none d-md-block">
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
<?php if(!ExpertNote\User\User::isLogin()):?>
                    <a href="/login" class="d-block">로그인</a>
<?php else: ?>
                    <a href="/backoffice" class="d-block">백오피스</a>
<?php endif;?>
                </div>
            </div>
            <hr class="my-4" style="border-color: rgba(255,255,255,0.1);">
            <p class="text-center text-white-50">&copy; 2025 AriRent. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- Mobile Bottom Navigation -->
    <div class="d-md-none mb-5"></div>
    <nav class="mobile-bottom-nav bg-white shadow-lg">
        <div class="d-flex justify-content-around align-items-center p-3">
            <a href="tel:010-4299-3772" class="text-decoration-none text-dark text-center">
                <i class="bi bi-telephone fs-4 d-block"></i>
                <small>전화</small>
            </a>
            <a href="http://pf.kakao.com/_ugtHn/chat" class="text-decoration-none text-dark text-center">
                <i class="bi bi-chat-dots fs-4 d-block"></i>
                <small>카톡</small>
            </a>
            <a href="#" class="text-decoration-none text-dark text-center" data-bs-toggle="modal" data-bs-target="#searchModal">
                <i class="bi bi-search fs-4 d-block"></i>
                <small>차량검색</small>
            </a>
            <a href="/forum/blog" class="text-decoration-none text-dark text-center">
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

        // 오프캔버스 열림/닫힘 시 body 클래스 토글
        const offcanvasMenu = document.getElementById('offcanvasMenu');
        if (offcanvasMenu) {
            offcanvasMenu.addEventListener('show.bs.offcanvas', function () {
                document.body.classList.add('offcanvas-open');
            });
            offcanvasMenu.addEventListener('hidden.bs.offcanvas', function () {
                document.body.classList.remove('offcanvas-open');
            });
        }

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

        // Consultation Form - Discord 웹훅 전송
        document.getElementById('consultForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            // 폼 데이터 수집
            const formData = new FormData(this);
            const data = {
                name: formData.get('name'),
                phone: formData.get('phone'),
                region: formData.get('region'),
                car_type: formData.get('car_type')
            };

            // Discord 웹훅 페이로드 생성
            const webhookPayload = {
                embeds: [{
                    title: "🚗 새로운 상담 신청",
                    color: 3447003, // 파란색
                    fields: [
                        {
                            name: "👤 이름",
                            value: data.name,
                            inline: true
                        },
                        {
                            name: "📱 연락처",
                            value: data.phone,
                            inline: true
                        },
                        {
                            name: "📍 지역",
                            value: data.region,
                            inline: true
                        },
                        {
                            name: "🚙 차종",
                            value: data.car_type,
                            inline: true
                        }
                    ],
                    timestamp: new Date().toISOString(),
                    footer: {
                        text: "ARI RENT 상담 신청"
                    }
                }]
            };

            try {
                // Discord 웹훅으로 전송
                const response = await fetch('https://discordapp.com/api/webhooks/1439930770943901848/BwO0WGZ0kavQHGVn7F_LCt2zGJrC0dqTYtJWKpP4KUON9t61t6BWBjYowWPQ1HRMKZv8', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(webhookPayload)
                });

                if (response.ok) {
                    // 성공 메시지
                    alert('상담 신청이 완료되었습니다!\n빠른 시일 내에 연락드리겠습니다.');
                    // 폼 초기화
                    this.reset();
                } else {
                    throw new Error('웹훅 전송 실패');
                }
            } catch (error) {
                // 오류 메시지
                alert('상담 신청 중 오류가 발생했습니다. 다시 시도해주세요.');
                console.error('Discord 웹훅 전송 오류:', error);
            }
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
    <script src="/assets/js/ExpertNote.min.js?<?php echo filectime(ABSPATH."/assets/js/ExpertNote.min.js")?>"></script>
</body>
</html>