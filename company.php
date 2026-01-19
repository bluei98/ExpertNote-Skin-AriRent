<?php
/**
 * 회사소개 페이지
 */

ExpertNote\Core::setLayout("v2");

// 레이아웃 설정
\ExpertNote\Core::setPageTitle("회사소개 - 아리렌트");
\ExpertNote\Core::setPageDescription("신뢰할 수 있는 장기렌트 전문 아리렌트입니다. 고객 만족을 최우선으로 하는 아리렌트를 소개합니다.");

// SEO 키워드
\ExpertNote\Core::setPageKeywords("아리렌트, 회사소개, 장기렌트 전문, 자동차 렌트, 신뢰, 투명성, 고객 중심");

// Open Graph 메타 태그
\ExpertNote\Core::addMetaTag('og:type', ["property"=>"og:type", "content"=>"website"]);
\ExpertNote\Core::addMetaTag('og:title', ["property"=>"og:title", "content"=>"회사소개 - 아리렌트"]);
\ExpertNote\Core::addMetaTag('og:description', ["property"=>"og:description", "content"=>"신뢰할 수 있는 장기렌트 전문 아리렌트입니다. 고객 만족을 최우선으로 하는 아리렌트를 소개합니다."]);
\ExpertNote\Core::addMetaTag('og:url', ["property"=>"og:url", "content"=>ExpertNote\Core::getBaseUrl() . "/company"]);
\ExpertNote\Core::addMetaTag('og:site_name', ["property"=>"og:site_name", "content"=>"아리렌트"]);
?>
    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <div class="page-header-content" data-aos="fade-up">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">홈</a></li>
                        <li class="breadcrumb-item active" aria-current="page">회사소개</li>
                    </ol>
                </nav>
                <h1 class="page-title">회사소개</h1>
                <p class="page-desc">아리렌트는 고객의 꿈을 실현하는 장기렌트 전문 기업입니다</p>
            </div>
        </div>
    </section>
    <!-- About Intro Section -->
    <section class="about-intro-section">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="about-image-wrapper">
                        <img src="https://images.unsplash.com/photo-1560179707-f14e90ef3623?w=600&h=400&fit=crop" alt="아리렌트 사무실" class="about-main-image">
                        <div class="about-image-badge">
                            <span class="badge-number">10+</span>
                            <span class="badge-text">Years Experience</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="about-intro-content">
                        <div class="section-badge">
                            <i class="bi bi-building"></i>
                            About Us
                        </div>
                        <h2 class="about-title">
                            누구나 원하는 차를 만날 수 있도록<br>
                            <span>아리렌트</span>가 함께합니다
                        </h2>
                        <p class="about-text">
                            아리렌트는 2014년 설립 이래, 신용등급에 관계없이 누구나 쉽고 편리하게 차량을 이용할 수 있는
                            무심사 장기렌트 서비스를 제공해 왔습니다.
                        </p>
                        <p class="about-text">
                            저희는 고객의 신용 상황이 차량 이용의 장벽이 되어서는 안 된다고 믿습니다.
                            그래서 복잡한 심사 과정 없이, 보증금 부담 없이 원하는 차량을 렌트할 수 있는
                            혁신적인 서비스를 만들어가고 있습니다.
                        </p>
                        <div class="about-features">
                            <div class="about-feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>무심사 100% 승인</span>
                            </div>
                            <div class="about-feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>보증금 0원 가능</span>
                            </div>
                            <div class="about-feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>전국 출고 서비스</span>
                            </div>
                            <div class="about-feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>1:1 전담 상담</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Vision Section -->
    <section class="vision-section">
        <div class="container">
            <div class="section-header text-center" data-aos="fade-up">
                <div class="section-badge">
                    <i class="bi bi-eye-fill"></i>
                    Our Vision
                </div>
                <h2 class="section-title">아리렌트의 <span>비전</span></h2>
                <p class="section-desc">고객의 이동을 더 자유롭게, 더 행복하게</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="vision-card">
                        <div class="vision-icon">
                            <i class="bi bi-lightbulb-fill"></i>
                        </div>
                        <h4 class="vision-title">Mission</h4>
                        <p class="vision-desc">
                            신용등급의 벽을 허물고, 누구나 원하는 차량을 합리적인 가격에 이용할 수 있는
                            포용적인 모빌리티 서비스를 제공합니다.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="vision-card">
                        <div class="vision-icon">
                            <i class="bi bi-rocket-takeoff-fill"></i>
                        </div>
                        <h4 class="vision-title">Vision</h4>
                        <p class="vision-desc">
                            대한민국 No.1 무심사 장기렌트 플랫폼으로서,
                            혁신적인 서비스와 고객 중심의 가치로 업계를 선도합니다.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="vision-card">
                        <div class="vision-icon">
                            <i class="bi bi-heart-fill"></i>
                        </div>
                        <h4 class="vision-title">Core Value</h4>
                        <p class="vision-desc">
                            신뢰, 투명성, 고객 중심의 가치를 바탕으로
                            모든 고객에게 최고의 렌트 경험을 선사합니다.
                        </p>
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

    <!-- History Section -->
    <!-- <section class="history-section">
        <div class="container">
            <div class="section-header text-center" data-aos="fade-up">
                <div class="section-badge">
                    <i class="bi bi-clock-history"></i>
                    History
                </div>
                <h2 class="section-title">아리렌트의 <span>발자취</span></h2>
                <p class="section-desc">고객과 함께 성장해온 아리렌트의 역사</p>
            </div>
            <div class="timeline">
                <div class="timeline-item" data-aos="fade-up" data-aos-delay="100">
                    <div class="timeline-marker"></div>
                    <div class="timeline-content">
                        <span class="timeline-year">2024</span>
                        <h4 class="timeline-title">누적 출고 15,000건 달성</h4>
                        <p class="timeline-desc">무심사 장기렌트 분야 1위 달성</p>
                    </div>
                </div>
                <div class="timeline-item" data-aos="fade-up" data-aos-delay="200">
                    <div class="timeline-marker"></div>
                    <div class="timeline-content">
                        <span class="timeline-year">2022</span>
                        <h4 class="timeline-title">전국 네트워크 확장</h4>
                        <p class="timeline-desc">전국 출고 서비스 시작, 지방 고객 서비스 강화</p>
                    </div>
                </div>
                <div class="timeline-item" data-aos="fade-up" data-aos-delay="300">
                    <div class="timeline-marker"></div>
                    <div class="timeline-content">
                        <span class="timeline-year">2020</span>
                        <h4 class="timeline-title">온라인 플랫폼 런칭</h4>
                        <p class="timeline-desc">디지털 전환을 통한 비대면 상담 서비스 시작</p>
                    </div>
                </div>
                <div class="timeline-item" data-aos="fade-up" data-aos-delay="400">
                    <div class="timeline-marker"></div>
                    <div class="timeline-content">
                        <span class="timeline-year">2017</span>
                        <h4 class="timeline-title">무심사 서비스 도입</h4>
                        <p class="timeline-desc">신용등급 관계없이 누구나 이용 가능한 서비스 시작</p>
                    </div>
                </div>
                <div class="timeline-item" data-aos="fade-up" data-aos-delay="500">
                    <div class="timeline-marker"></div>
                    <div class="timeline-content">
                        <span class="timeline-year">2014</span>
                        <h4 class="timeline-title">아리렌트 설립</h4>
                        <p class="timeline-desc">서울 강남구에서 장기렌트 전문 기업으로 출발</p>
                    </div>
                </div>
            </div>
        </div>
    </section> -->

    <!-- Location Section -->
    <!-- <section class="location-section">
        <div class="container">
            <div class="section-header text-center" data-aos="fade-up">
                <div class="section-badge">
                    <i class="bi bi-geo-alt-fill"></i>
                    Location
                </div>
                <h2 class="section-title">오시는 <span>길</span></h2>
                <p class="section-desc">아리렌트 본사 위치 안내</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-8" data-aos="fade-right">
                    <div class="map-wrapper">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3165.3530953847607!2d127.0276368!3d37.4979517!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x357ca159000000%3A0x0!2z7YWM7Zey652A66GcIDEyMw!5e0!3m2!1sko!2skr!4v1234567890"
                            width="100%"
                            height="400"
                            style="border:0; border-radius: 16px;"
                            allowfullscreen=""
                            loading="lazy">
                        </iframe>
                    </div>
                </div>
                <div class="col-lg-4" data-aos="fade-left">
                    <div class="location-info-card">
                        <h4 class="location-info-title">아리렌트 본사</h4>
                        <ul class="location-info-list">
                            <li>
                                <i class="bi bi-geo-alt-fill"></i>
                                <div>
                                    <strong>주소</strong>
                                    <p>서울특별시 강남구 테헤란로 123<br>드림카빌딩 5층</p>
                                </div>
                            </li>
                            <li>
                                <i class="bi bi-telephone-fill"></i>
                                <div>
                                    <strong>대표전화</strong>
                                    <p>1588-0000</p>
                                </div>
                            </li>
                            <li>
                                <i class="bi bi-clock-fill"></i>
                                <div>
                                    <strong>운영시간</strong>
                                    <p>평일 09:00 - 18:00<br>(점심 12:00 - 13:00)</p>
                                </div>
                            </li>
                            <li>
                                <i class="bi bi-envelope-fill"></i>
                                <div>
                                    <strong>이메일</strong>
                                    <p>contact@dreancar.co.kr</p>
                                </div>
                            </li>
                        </ul>
                        <div class="location-transport">
                            <h5><i class="bi bi-train-front-fill"></i> 지하철</h5>
                            <p>2호선 강남역 3번 출구 도보 5분</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> -->

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
                            <a href="tel:1588-0000" class="btn-cta-primary">
                                <i class="bi bi-telephone-fill"></i> 1566-5623
                            </a>
                            <a href="/kakaolink" class="btn-cta-secondary">
                                <i class="bi bi-chat-dots-fill"></i> 카톡 상담
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>