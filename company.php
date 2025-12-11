<?php
/**
 * 회사소개 페이지
 */

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

<!-- 히어로 섹션 -->
<section class="company-hero">
    <div class="container text-center">
        <h1>비대면 무심사, 누구나 이용 가능</h1>
        <p>신뢰할 수 있는 장기렌트 전문 아리렌트</p>
    </div>
</section>

<!-- 특징 섹션 -->
<section class="feature-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-lightning-charge-fill"></i>
                    </div>
                    <h3>출고 기다림 없음</h3>
                    <p>빠른 차량 출고로 바로 이용 가능합니다.<br>불필요한 대기 시간을 최소화합니다.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-car-front-fill"></i>
                    </div>
                    <h3>다양한 차종 선택</h3>
                    <p>국산차부터 수입차까지 원하는 차량을<br>자유롭게 선택할 수 있습니다.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-patch-check-fill"></i>
                    </div>
                    <h3>허위매물 금지</h3>
                    <p>실제 보유 차량만 투명하게 제공하여<br>신뢰할 수 있는 거래를 보장합니다.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 회사 소개 섹션 -->
<section class="intro-section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <h2>가장 큰 직영 렌트사</h2>
                <p>
                    아리렌트는 고객 만족을 최우선으로 하는 장기렌트 전문 기업입니다.
                    다년간의 경험과 노하우를 바탕으로 최고의 서비스를 제공하고 있습니다.
                </p>
                <p>
                    대규모 협력 네트워크와 실제 보유 차량을 통해 고객에게
                    신뢰할 수 있는 렌트 서비스를 제공합니다.
                </p>
                <p>
                    빠른 출고, 합리적인 가격, 투명한 계약으로
                    고객의 만족을 이끌어내고 있습니다.
                </p>
            </div>
            <div class="col-lg-6">
                <div class="intro-image-wrapper">
                    <img src="/skins/arirent/assets/images/company-intro.jpg"
                         class="img-fluid"
                         alt="아리렌트 회사 소개"
                         onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'text-center py-5\'><i class=\'bi bi-building\' style=\'font-size: 6rem; color: #1b71d7; opacity: 0.3;\'></i></div>'">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 핵심 가치 섹션 -->
<section class="values-section">
    <div class="container">
        <h2>아리렌트의 핵심 가치</h2>
        <div class="row g-3">
            <div class="col-lg-6">
                <div class="value-item">
                    <h3>
                        <i class="bi bi-people-fill"></i>
                        고객 중심
                    </h3>
                    <p>
                        고객의 니즈를 최우선으로 생각하며, 만족도 높은 서비스를 제공하기 위해 끊임없이 노력합니다.
                        전문 상담사가 1:1 맞춤 상담을 통해 최적의 렌트 조건을 제안합니다.
                    </p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="value-item">
                    <h3>
                        <i class="bi bi-gem"></i>
                        신뢰와 투명성
                    </h3>
                    <p>
                        모든 거래는 투명하게 진행되며, 숨겨진 비용이나 조건 없이 정직한 계약을 약속드립니다.
                        실제 보유 차량만을 제공하여 신뢰를 쌓아갑니다.
                    </p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="value-item">
                    <h3>
                        <i class="bi bi-lightning-charge-fill"></i>
                        신속한 처리
                    </h3>
                    <p>
                        빠른 심사와 출고 프로세스로 고객이 원하는 시기에 차량을 이용할 수 있도록 지원합니다.
                        불필요한 대기 시간을 최소화하여 편의를 제공합니다.
                    </p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="value-item">
                    <h3>
                        <i class="bi bi-award-fill"></i>
                        전문성
                    </h3>
                    <p>
                        장기렌트 분야의 전문 지식과 경험을 갖춘 팀이 고객에게 최상의 솔루션을 제공합니다.
                        시장 동향과 최신 정보를 바탕으로 합리적인 제안을 드립니다.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 연락처 섹션 -->
<section class="contact-section">
    <div class="container">
        <h2>언제든지 문의하세요</h2>
        <p class="text-center">전문 상담사가 친절하게 안내해 드립니다</p>
        <div class="contact-info mt-5">
            <div class="contact-item">
                <i class="bi bi-telephone-fill"></i>
                <h4>전화 상담</h4>
                <p style="font-size: 1.2rem;">010-4299-3772</p>
            </div>
            <div class="contact-item">
                <i class="bi bi-clock-fill"></i>
                <h4>운영 시간</h4>
                <p style="font-size: 1.2rem;">평일 09:00 - 18:00</p>
            </div>
            <div class="contact-item">
                <i class="bi bi-envelope-fill"></i>
                <h4>이메일</h4>
                <p style="font-size: 1.2rem;">contact@arirent.co.kr</p>
            </div>
        </div>
    </div>
</section>
