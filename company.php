<?php
/**
 * 회사소개 페이지
 */

// 레이아웃 설정
\ExpertNote\Core::setPageTitle("회사소개 - 아리렌트");
\ExpertNote\Core::setPageDescription("신뢰할 수 있는 장기렌트 전문 아리렌트입니다. 고객 만족을 최우선으로 하는 아리렌트를 소개합니다.");
?>

<style>
    .company-hero {
        background: var(--primary-color);
        color: white;
        padding: 5rem 0;
        text-align: center;
    }

    .company-hero h1 {
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 1.5rem;
    }

    .company-hero p {
        font-size: 1.2rem;
        margin-bottom: 0;
    }

    .feature-section {
        padding: 5rem 0;
        background: white;
    }

    .feature-card {
        text-align: center;
        padding: 2rem;
        transition: transform 0.3s;
    }

    .feature-card:hover {
        transform: translateY(-10px);
    }

    .feature-icon {
        width: 100px;
        height: 100px;
        margin: 0 auto 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--light-color);
        font-size: 3rem;
        color: var(--primary-color);
    }

    .intro-section {
        padding: 5rem 0;
        background: var(--light-color);
    }

    .intro-section h2 {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 1.5rem;
        color: var(--primary-color);
    }

    .intro-section p {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #555;
    }

    .values-section {
        padding: 5rem 0;
        background: white;
    }

    .values-section h2 {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 3rem;
        text-align: center;
        color: var(--primary-color);
    }

    .value-item {
        background: white;
        border: 2px solid var(--light-color);
        padding: 2rem;
        margin-bottom: 2rem;
        transition: all 0.3s;
    }

    .value-item:hover {
        border-color: var(--primary-color);
        box-shadow: 0 10px 30px rgba(27, 113, 215, 0.1);
    }

    .value-item h3 {
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 1rem;
        color: var(--primary-color);
    }

    .value-item p {
        font-size: 1rem;
        line-height: 1.6;
        color: #666;
        margin-bottom: 0;
    }

    .contact-section {
        padding: 5rem 0;
        background: var(--primary-color);
        color: white;
        text-align: center;
    }

    .contact-section h2 {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 2rem;
    }

    .contact-info {
        display: flex;
        justify-content: center;
        gap: 3rem;
        flex-wrap: wrap;
        margin-top: 2rem;
    }

    .contact-item {
        text-align: center;
    }

    .contact-item i {
        font-size: 3rem;
        margin-bottom: 1rem;
    }

    .contact-item h4 {
        font-size: 1.2rem;
        margin-bottom: 0.5rem;
    }

    .contact-item p {
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 0;
    }

    @media (max-width: 768px) {
        .company-hero h1 {
            font-size: 1.8rem;
        }

        .company-hero {
            padding: 3rem 0;
        }

        .feature-section,
        .intro-section,
        .values-section,
        .contact-section {
            padding: 3rem 0;
        }

        .contact-info {
            gap: 2rem;
        }
    }
</style>

<!-- 히어로 섹션 -->
<section class="company-hero">
    <div class="container">
        <h1 data-aos="fade-up">비대면 무심사, 누구나 이용 가능</h1>
        <p data-aos="fade-up" data-aos-delay="100">신뢰할 수 있는 장기렌트 전문 아리렌트</p>
    </div>
</section>

<!-- 특징 섹션 -->
<section class="feature-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-lightning-charge-fill"></i>
                    </div>
                    <h3 class="fw-bold mb-3">출고 기다림 없음</h3>
                    <p class="text-muted">빠른 차량 출고로<br>바로 이용 가능합니다</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-car-front-fill"></i>
                    </div>
                    <h3 class="fw-bold mb-3">다양한 차종 선택</h3>
                    <p class="text-muted">국산차부터 수입차까지<br>원하는 차량을 선택하세요</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-patch-check-fill"></i>
                    </div>
                    <h3 class="fw-bold mb-3">허위매물 금지</h3>
                    <p class="text-muted">실제 보유 차량만<br>투명하게 제공합니다</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 회사 소개 섹션 -->
<section class="intro-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
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
            <div class="col-lg-6" data-aos="fade-left">
                <div class="bg-white p-4 shadow">
                    <img src="/skins/arirent/assets/images/company-intro.jpg"
                         class="img-fluid w-100"
                         alt="아리렌트 회사 소개"
                         onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'text-center py-5\'><i class=\'bi bi-building\' style=\'font-size: 5rem; color: var(--primary-color);\'></i></div>'">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 핵심 가치 섹션 -->
<section class="values-section">
    <div class="container">
        <h2 data-aos="fade-up">아리렌트의 핵심 가치</h2>
        <div class="row">
            <div class="col-lg-6" data-aos="fade-up">
                <div class="value-item">
                    <h3><i class="bi bi-people-fill me-2"></i> 고객 중심</h3>
                    <p>
                        고객의 니즈를 최우선으로 생각하며,
                        만족도 높은 서비스를 제공하기 위해 끊임없이 노력합니다.
                        전문 상담사가 1:1 맞춤 상담을 통해 최적의 렌트 조건을 제안합니다.
                    </p>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
                <div class="value-item">
                    <h3><i class="bi bi-gem me-2"></i> 신뢰와 투명성</h3>
                    <p>
                        모든 거래는 투명하게 진행되며,
                        숨겨진 비용이나 조건 없이 정직한 계약을 약속드립니다.
                        실제 보유 차량만을 제공하여 신뢰를 쌓아갑니다.
                    </p>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
                <div class="value-item">
                    <h3><i class="bi bi-lightning-charge-fill me-2"></i> 신속한 처리</h3>
                    <p>
                        빠른 심사와 출고 프로세스로
                        고객이 원하는 시기에 차량을 이용할 수 있도록 지원합니다.
                        불필요한 대기 시간을 최소화하여 편의를 제공합니다.
                    </p>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="300">
                <div class="value-item">
                    <h3><i class="bi bi-award-fill me-2"></i> 전문성</h3>
                    <p>
                        장기렌트 분야의 전문 지식과 경험을 갖춘 팀이
                        고객에게 최상의 솔루션을 제공합니다.
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
        <h2 data-aos="fade-up">언제든지 문의하세요</h2>
        <p data-aos="fade-up" data-aos-delay="100">전문 상담사가 친절하게 안내해 드립니다</p>
        <div class="contact-info">
            <div class="contact-item" data-aos="fade-up" data-aos-delay="200">
                <i class="bi bi-telephone-fill"></i>
                <h4>전화 상담</h4>
                <p>010-4299-3772</p>
            </div>
            <div class="contact-item" data-aos="fade-up" data-aos-delay="300">
                <i class="bi bi-clock-fill"></i>
                <h4>운영 시간</h4>
                <p>평일 09:00 - 18:00</p>
            </div>
            <div class="contact-item" data-aos="fade-up" data-aos-delay="400">
                <i class="bi bi-envelope-fill"></i>
                <h4>이메일</h4>
                <p>contact@arirent.co.kr</p>
            </div>
        </div>
    </div>
</section>
