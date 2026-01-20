<?php
ExpertNote\Core::setLayout("v2");
/**
 * 계약 진행 과정 안내 페이지
 */

$pageTitle = "계약 진행 과정";
$pageDescription = "아리렌트 장기렌트 계약 진행 과정을 안내해 드립니다. 고객 확인 사항부터 차량 출고까지 단계별로 확인하세요.";

\ExpertNote\Core::setPageTitle($pageTitle);
\ExpertNote\Core::setPageSuffix("아리렌트");
\ExpertNote\Core::setPageDescription($pageDescription);
\ExpertNote\Core::setPageKeywords("장기렌트 계약, 렌트카 계약 과정, 아리렌트 계약, 무심사 장기렌트 계약");

// Open Graph 메타 태그
\ExpertNote\Core::addMetaTag('og:type', ["property"=>"og:type", "content"=>"website"]);
\ExpertNote\Core::addMetaTag('og:title', ["property"=>"og:title", "content"=>$pageTitle]);
\ExpertNote\Core::addMetaTag('og:description', ["property"=>"og:description", "content"=>$pageDescription]);
?>

<style>
/* 메인 컨테이너 */
.contract-process {
    max-width: 900px;
    margin: 0 auto;
    padding: 3rem 1.5rem;
}

/* 헤더 섹션 */
.contract-header {
    text-align: center;
    margin-bottom: 4rem;
    position: relative;
}
.contract-header::after {
    content: "";
    display: block;
    width: 80px;
    height: 4px;
    background: linear-gradient(90deg, #3b82f6, #8b5cf6);
    margin: 1.5rem auto 0;
    border-radius: 2px;
}
.contract-header h1 {
    font-size: 2.25rem;
    font-weight: 800;
    background: linear-gradient(135deg, #1e3a5f 0%, #3b82f6 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 0.75rem;
}
.contract-header h1 i {
    -webkit-text-fill-color: #3b82f6;
}
.contract-header p {
    color: #64748b;
    font-size: 1.1rem;
}

/* 타임라인 스타일 */
.timeline {
    position: relative;
    padding-left: 50px;
}
.timeline::before {
    content: "";
    position: absolute;
    left: 20px;
    top: 30px;
    bottom: 30px;
    width: 3px;
    background: linear-gradient(180deg, #3b82f6 0%, #8b5cf6 50%, #06b6d4 100%);
    border-radius: 3px;
}

/* 스텝 카드 */
.process-step {
    position: relative;
    margin-bottom: 2rem;
    opacity: 0;
    transform: translateX(-20px);
    animation: slideIn 0.5s ease forwards;
}
.process-step:nth-child(1) { animation-delay: 0.1s; }
.process-step:nth-child(2) { animation-delay: 0.2s; }
.process-step:nth-child(3) { animation-delay: 0.3s; }
.process-step:nth-child(4) { animation-delay: 0.4s; }
.process-step:nth-child(5) { animation-delay: 0.5s; }

@keyframes slideIn {
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* 스텝 번호 원형 배지 */
.step-badge {
    position: absolute;
    left: -50px;
    top: 1.5rem;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    font-weight: 700;
    color: #fff;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    z-index: 1;
}
.step-badge.step-1 { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.step-badge.step-2 { background: linear-gradient(135deg, #06b6d4, #0891b2); }
.step-badge.step-3 { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
.step-badge.step-4 { background: linear-gradient(135deg, #10b981, #059669); }

/* 스텝 카드 내용 */
.step-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid #e2e8f0;
}
.step-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.12);
}

/* 스텝 헤더 */
.step-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.step-header .step-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}
.process-step:nth-child(1) .step-icon { background: #eff6ff; color: #3b82f6; }
.process-step:nth-child(2) .step-icon { background: #ecfeff; color: #06b6d4; }
.process-step:nth-child(3) .step-icon { background: #f5f3ff; color: #8b5cf6; }
.process-step:nth-child(4) .step-icon { background: #ecfdf5; color: #10b981; }

.step-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
}

/* 스텝 내용 */
.step-body {
    padding: 1.25rem 1.5rem;
}
.step-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
.step-list li {
    position: relative;
    padding: 0.6rem 0 0.6rem 1.75rem;
    color: #475569;
    font-size: 0.95rem;
    line-height: 1.6;
    border-bottom: 1px dashed #f1f5f9;
}
.step-list li:last-child {
    border-bottom: none;
}
.step-list li::before {
    content: "";
    position: absolute;
    left: 0;
    top: 1rem;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #cbd5e1;
}
.step-list li.sub-note {
    font-size: 0.85rem;
    color: #94a3b8;
    padding-left: 2.5rem;
}
.step-list li.sub-note::before {
    width: 6px;
    height: 6px;
    left: 0.75rem;
    background: #e2e8f0;
}
.step-list li.warning {
    color: #dc2626;
    font-weight: 500;
}
.step-list li.warning::before {
    background: #fca5a5;
}
.step-list li.highlight {
    color: #2563eb;
    font-weight: 600;
}
.step-list li.highlight::before {
    background: #93c5fd;
}

/* 차량 출고 안내 */
.delivery-notice {
    margin-top: 3rem;
    background: #f8fafc;
    border-radius: 12px;
    padding: 1.5rem;
    border: 1px solid #e2e8f0;
}
.delivery-notice-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1rem;
    font-weight: 600;
    color: #475569;
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #e2e8f0;
}
.delivery-notice-title i {
    color: #f59e0b;
}
.delivery-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
.delivery-list li {
    padding: 0.4rem 0;
    font-size: 0.9rem;
    color: #64748b;
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
}
.delivery-list li i {
    margin-top: 0.15rem;
    flex-shrink: 0;
}
.delivery-list li.warning {
    color: #dc2626;
    font-weight: 500;
}
.delivery-list li.bonus {
    color: #16a34a;
    font-weight: 500;
}
.delivery-list li.sub {
    padding-left: 1.25rem;
    font-size: 0.85rem;
    color: #94a3b8;
}

/* 모바일 반응형 */
@media (max-width: 768px) {
    .contract-process {
        padding: 2rem 1rem;
    }
    .contract-header h1 {
        font-size: 1.75rem;
    }
    .timeline {
        padding-left: 40px;
    }
    .timeline::before {
        left: 15px;
    }
    .step-badge {
        left: -40px;
        width: 36px;
        height: 36px;
        font-size: 1rem;
    }
    .step-header {
        padding: 1rem;
    }
    .step-header .step-icon {
        width: 40px;
        height: 40px;
        font-size: 1.25rem;
    }
    .step-title {
        font-size: 1.05rem;
    }
    .step-body {
        padding: 1rem;
    }
    .step-list li {
        font-size: 0.9rem;
    }
}
</style>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="page-header-content" data-aos="fade-up">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/"><?php echo __('홈', 'skin')?></a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo __('계약 진행 과정', 'skin')?></li>
                </ol>
            </nav>
            <h1 class="page-title"><?php echo __('계약 진행 과정', 'skin')?></h1>
        </div>
    </div>
</section>

<section class="contract-process">
    <div class="contract-header">
        <h1><i class="bi bi-clipboard-check"></i> <?php echo __('아리렌트 계약 진행 과정', 'skin')?></h1>
        <p><?php echo __('간편하고 빠른 4단계 계약 절차를 안내해 드립니다', 'skin')?></p>
    </div>

    <div class="timeline">
        <!-- Step 1: 고객 확인 사항 -->
        <div class="process-step">
            <div class="step-badge step-1">1</div>
            <div class="step-card">
                <div class="step-header">
                    <div class="step-icon">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                    <h3 class="step-title"><?php echo __('고객 확인 사항', 'skin')?></h3>
                </div>
                <div class="step-body">
                    <ul class="step-list">
                        <li><?php echo __('자격요건: 만 21세 이상 ~ 만 65세 이하', 'skin')?></li>
                        <li class="sub-note"><?php echo __('(만 21세 진행시 가능차종 별도 / 렌트료 추가)', 'skin')?></li>
                        <li><?php echo __('운전면허 취득 1년 이상', 'skin')?></li>
                        <li><?php echo __('본인명의 휴대폰 필수', 'skin')?></li>
                        <li class="highlight"><?php echo __('출고금액 준비: 보증금 + 한달 렌트료(선납)', 'skin')?></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Step 2: 전달 준비서류 -->
        <div class="process-step">
            <div class="step-badge step-2">2</div>
            <div class="step-card">
                <div class="step-header">
                    <div class="step-icon">
                        <i class="bi bi-file-earmark-text-fill"></i>
                    </div>
                    <h3 class="step-title"><?php echo __('전달 준비서류', 'skin')?></h3>
                </div>
                <div class="step-body">
                    <ul class="step-list">
                        <li><?php echo __('운전면허증 사진', 'skin')?></li>
                        <li><?php echo __('주민등록등본 (발행기준 1개월 이내)', 'skin')?></li>
                        <li><?php echo __('사업자 등록증 (해당시)', 'skin')?></li>
                        <li><?php echo __('차종에 따라 재직증빙 또는 소득증빙 필요', 'skin')?></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Step 3: 계약금 입금 -->
        <div class="process-step">
            <div class="step-badge step-3">3</div>
            <div class="step-card">
                <div class="step-header">
                    <div class="step-icon">
                        <i class="bi bi-credit-card-fill"></i>
                    </div>
                    <h3 class="step-title"><?php echo __('계약금 입금', 'skin')?></h3>
                </div>
                <div class="step-body">
                    <ul class="step-list">
                        <li class="highlight"><?php echo __('계약금 30~50만원 입금 (렌트료의 일부)', 'skin')?></li>
                        <li><?php echo __('계약금 입금 후 최대 7일까지 차량 선점 가능 (차량마다 상이)', 'skin')?></li>
                        <li class="warning"><?php echo __('계약금 입금 후 귀책사유 및 단순 변심 취소시 계약금 미반환', 'skin')?></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Step 4: 계약서 발송 & 작성 / 잔금 입금 -->
        <div class="process-step">
            <div class="step-badge step-4">4</div>
            <div class="step-card">
                <div class="step-header">
                    <div class="step-icon">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                    <h3 class="step-title"><?php echo __('계약서 작성 & 잔금 입금', 'skin')?></h3>
                </div>
                <div class="step-body">
                    <ul class="step-list">
                        <li class="highlight"><?php echo __('보증금 + 잔여 렌트료 입금 (계약금 제외 금액)', 'skin')?></li>
                        <li><?php echo __('계약서 작성 및 필요서류 제출 후 현장 차량 인도', 'skin')?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- 차량 출고 관련 안내 -->
    <div class="delivery-notice">
        <div class="delivery-notice-title">
            <i class="bi bi-truck"></i>
            <?php echo __('차량 출고 안내', 'skin')?>
        </div>
        <ul class="delivery-list">
            <li class="warning"><i class="bi bi-x-circle"></i> <?php echo __('당일 출고 불가', 'skin')?></li>
            <li><i class="bi bi-car-front"></i> <?php echo __('신차 : 오후 5시전 → 익일 오후', 'skin')?></li>
            <li><i class="bi bi-car-front"></i> <?php echo __('중고차 : 오후 5시전 → 익일 오후', 'skin')?></li>
            <li class="sub"><?php echo __('※ 주말, 공휴일 제외', 'skin')?></li>
        </ul>
    </div>

    <!-- 공통서류 안내 -->
    <div class="delivery-notice" style="margin-top: 1.5rem;">
        <div class="delivery-notice-title">
            <i class="bi bi-file-earmark-text"></i>
            <?php echo __('공통서류', 'skin')?>
        </div>
        <ul class="delivery-list">
            <li><i class="bi bi-check-circle"></i> <?php echo __('주민등록 등본 2부', 'skin')?></li>
            <li><i class="bi bi-check-circle"></i> <?php echo __('주민등록 초본 2부', 'skin')?></li>
            <li><i class="bi bi-check-circle"></i> <?php echo __('가족관계 증명서 2부', 'skin')?></li>
            <li><i class="bi bi-check-circle"></i> <?php echo __('본인서명사실확인서 2부', 'skin')?></li>
        </ul>
    </div>
</section>
