<?php
ExpertNote\Core::setLayout("v2");
/**
 * 자주 묻는 질문 (FAQ) 페이지
 */

$pageTitle = "자주 묻는 질문";
$pageDescription = "아리렌트 장기렌트 서비스에 대한 자주 묻는 질문과 답변을 확인하세요. 저신용 장기렌트, 계약 절차, 보증금 등 궁금한 사항을 해결해 드립니다.";

\ExpertNote\Core::setPageTitle($pageTitle);
\ExpertNote\Core::setPageSuffix("아리렌트");
\ExpertNote\Core::setPageDescription($pageDescription);
\ExpertNote\Core::setPageKeywords("장기렌트 FAQ, 저신용 장기렌트, 무심사 렌트, 장기렌트 질문, 아리렌트 FAQ");

// Open Graph 메타 태그
\ExpertNote\Core::addMetaTag('og:type', ["property"=>"og:type", "content"=>"website"]);
\ExpertNote\Core::addMetaTag('og:title', ["property"=>"og:title", "content"=>$pageTitle]);
\ExpertNote\Core::addMetaTag('og:description', ["property"=>"og:description", "content"=>$pageDescription]);
?>

<style>
/* 메인 컨테이너 */
.faq-section {
    max-width: 900px;
    margin: 0 auto;
    padding: 3rem 1.5rem;
}

/* 헤더 섹션 */
.faq-header {
    text-align: center;
    margin-bottom: 3rem;
    position: relative;
}
.faq-header::after {
    content: "";
    display: block;
    width: 80px;
    height: 4px;
    background: linear-gradient(90deg, #3b82f6, #8b5cf6);
    margin: 1.5rem auto 0;
    border-radius: 2px;
}
.faq-header h1 {
    font-size: 2.25rem;
    font-weight: 800;
    background: linear-gradient(135deg, #1e3a5f 0%, #3b82f6 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 0.75rem;
}
.faq-header h1 i {
    -webkit-text-fill-color: #3b82f6;
}
.faq-header p {
    color: #64748b;
    font-size: 1.1rem;
}

/* FAQ 아코디언 스타일 */
.faq-accordion {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.faq-item {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    border: 1px solid #e2e8f0;
    overflow: hidden;
    transition: all 0.3s ease;
}
.faq-item:hover {
    box-shadow: 0 8px 30px rgba(0,0,0,0.1);
}
.faq-item.active {
    border-color: #3b82f6;
    box-shadow: 0 8px 30px rgba(59, 130, 246, 0.15);
}

/* 질문 헤더 */
.faq-question {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem 1.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #fff;
}
.faq-question:hover {
    background: #f8fafc;
}
.faq-item.active .faq-question {
    background: linear-gradient(135deg, #eff6ff 0%, #f0f9ff 100%);
}

.faq-question .q-badge {
    width: 36px;
    height: 36px;
    min-width: 36px;
    border-radius: 10px;
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: #fff;
    font-weight: 700;
    font-size: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
}
.faq-item.active .faq-question .q-badge {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
}

.faq-question .q-text {
    flex: 1;
    font-size: 1.05rem;
    font-weight: 600;
    color: #1e293b;
    line-height: 1.5;
}

.faq-question .q-icon {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    color: #64748b;
}
.faq-item.active .faq-question .q-icon {
    background: #3b82f6;
    color: #fff;
    transform: rotate(180deg);
}

/* 답변 영역 */
.faq-answer {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.4s ease, padding 0.3s ease;
}
.faq-item.active .faq-answer {
    max-height: 1000px;
}

.faq-answer-content {
    padding: 1.5rem 1.5rem 1.5rem 1.5rem;
    margin-left: 52px;
    border-left: 3px solid #e2e8f0;
}
.faq-item.active .faq-answer-content {
    border-left-color: #3b82f6;
}

.faq-answer-content .a-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: 8px;
    background: linear-gradient(135deg, #10b981, #059669);
    color: #fff;
    font-weight: 700;
    font-size: 0.85rem;
    margin-bottom: 0.75rem;
}

.faq-answer-content p {
    color: #475569;
    font-size: 0.95rem;
    line-height: 1.8;
    margin: 0;
    white-space: pre-line;
}

.faq-answer-content .answer-list {
    list-style: none;
    padding: 0;
    margin: 0.5rem 0 0 0;
}
.faq-answer-content .answer-list li {
    position: relative;
    padding: 0.4rem 0 0.4rem 1.5rem;
    color: #475569;
    font-size: 0.95rem;
    line-height: 1.6;
}
.faq-answer-content .answer-list li::before {
    content: "";
    position: absolute;
    left: 0;
    top: 0.85rem;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #cbd5e1;
}
.faq-answer-content .answer-list li.highlight::before {
    background: #3b82f6;
}
.faq-answer-content .answer-list li.sub {
    padding-left: 2.5rem;
    font-size: 0.9rem;
    color: #64748b;
}
.faq-answer-content .answer-list li.sub::before {
    left: 1rem;
    width: 6px;
    height: 6px;
    background: #e2e8f0;
}

/* CTA 섹션 */
.faq-cta {
    margin-top: 3rem;
    background: linear-gradient(135deg, #1e3a5f 0%, #3b82f6 100%);
    border-radius: 16px;
    padding: 2rem;
    text-align: center;
    color: #fff;
}
.faq-cta h3 {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}
.faq-cta p {
    color: rgba(255,255,255,0.8);
    margin-bottom: 1.5rem;
    font-size: 0.95rem;
}
.faq-cta .cta-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}
.faq-cta .btn-cta {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.875rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.95rem;
    text-decoration: none;
    transition: all 0.3s ease;
}
.faq-cta .btn-cta-primary {
    background: #fff;
    color: #1e3a5f;
}
.faq-cta .btn-cta-primary:hover {
    background: #f1f5f9;
    transform: translateY(-2px);
}
.faq-cta .btn-cta-secondary {
    background: rgba(255,255,255,0.15);
    color: #fff;
    border: 1px solid rgba(255,255,255,0.3);
}
.faq-cta .btn-cta-secondary:hover {
    background: rgba(255,255,255,0.25);
    transform: translateY(-2px);
}

/* 모바일 반응형 */
@media (max-width: 768px) {
    .faq-section {
        padding: 2rem 1rem;
    }
    .faq-header h1 {
        font-size: 1.75rem;
    }
    .faq-question {
        padding: 1rem;
    }
    .faq-question .q-badge {
        width: 32px;
        height: 32px;
        min-width: 32px;
        font-size: 0.9rem;
    }
    .faq-question .q-text {
        font-size: 0.95rem;
    }
    .faq-answer-content {
        margin-left: 0;
        padding: 1rem;
        border-left: none;
        border-top: 1px dashed #e2e8f0;
    }
    .faq-cta .cta-buttons {
        flex-direction: column;
    }
    .faq-cta .btn-cta {
        width: 100%;
        justify-content: center;
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
                    <li class="breadcrumb-item active" aria-current="page"><?php echo __('자주 묻는 질문', 'skin')?></li>
                </ol>
            </nav>
            <h1 class="page-title"><?php echo __('자주 묻는 질문', 'skin')?></h1>
        </div>
    </div>
</section>

<section class="faq-section">
    <div class="faq-header">
        <h1><i class="bi bi-question-circle"></i> <?php echo __('자주 묻는 질문', 'skin')?></h1>
        <p><?php echo __('아리렌트 장기렌트에 대해 궁금한 점을 확인하세요', 'skin')?></p>
    </div>

    <div class="faq-accordion">
        <!-- FAQ 1 -->
        <div class="faq-item active" data-aos="fade-up" data-aos-delay="100">
            <div class="faq-question">
                <span class="q-badge">Q</span>
                <span class="q-text"><?php echo __('저신용장기렌트 조건은 어떻게 되나요?', 'skin')?></span>
                <span class="q-icon"><i class="bi bi-chevron-down"></i></span>
            </div>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    <span class="a-badge">A</span>
                    <p><?php echo __('보통 연령은 만26세 이상, 운전경력 1년이상(면허취득 1년이상)이면 자격이 됩니다.

일부 차량에 한해 만21세 이상 만26세 미만의 보험적용도 가능합니다.
이 경우에는 기존의 렌트료에서 월렌트료가 추가(5만~10만) 됩니다.', 'skin')?></p>
                </div>
            </div>
        </div>

        <!-- FAQ 2 -->
        <div class="faq-item" data-aos="fade-up" data-aos-delay="150">
            <div class="faq-question">
                <span class="q-badge">Q</span>
                <span class="q-text"><?php echo __('장기렌트 진행 절차가 궁금합니다.', 'skin')?></span>
                <span class="q-icon"><i class="bi bi-chevron-down"></i></span>
            </div>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    <span class="a-badge">A</span>
                    <ul class="answer-list">
                        <li class="highlight"><?php echo __('1. 차량상담', 'skin')?></li>
                        <li class="highlight"><?php echo __('2. 차량재고확인', 'skin')?></li>
                        <li class="highlight"><?php echo __('3. 계약접수 (운전면허증 및 등본)', 'skin')?></li>
                        <li class="sub"><?php echo __('차량에 따라 재직 or 소득 증빙', 'skin')?></li>
                        <li class="highlight"><?php echo __('4. 차량선점 및 전자약정 (계약금 30~50만원)', 'skin')?></li>
                        <li class="highlight"><?php echo __('5. 필요서류 준비 및 잔금입금', 'skin')?></li>
                        <li class="highlight"><?php echo __('6. 차량인도 (탁송 또는 방문출고)', 'skin')?></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- FAQ 3 -->
        <div class="faq-item" data-aos="fade-up" data-aos-delay="200">
            <div class="faq-question">
                <span class="q-badge">Q</span>
                <span class="q-text"><?php echo __('상담은 어떻게 진행이 되나요?', 'skin')?></span>
                <span class="q-icon"><i class="bi bi-chevron-down"></i></span>
            </div>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    <span class="a-badge">A</span>
                    <p><?php echo __('홈페이지에 상담 신청 또는 카톡 남겨주시면
장기렌트 전문 상담사가 1:1로 직접 상담을 진행합니다.
상담은 무료이며, 부담 없이 문의하시면 됩니다.', 'skin')?></p>
                </div>
            </div>
        </div>

        <!-- FAQ 4 -->
        <div class="faq-item" data-aos="fade-up" data-aos-delay="250">
            <div class="faq-question">
                <span class="q-badge">Q</span>
                <span class="q-text"><?php echo __('신용불량인데 조회 없이 이용이 가능한가요?', 'skin')?></span>
                <span class="q-icon"><i class="bi bi-chevron-down"></i></span>
            </div>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    <span class="a-badge">A</span>
                    <p><?php echo __('개인 신용등급, 연체 이력, 개인회생·파산 이력자, 신용불량자, 신용회복 등
신용조회 없이 보증금 조건으로 장기렌트를 해드리기 때문에 신용상태를 체크하지 않습니다.', 'skin')?></p>
                </div>
            </div>
        </div>

        <!-- FAQ 5 -->
        <div class="faq-item" data-aos="fade-up" data-aos-delay="300">
            <div class="faq-question">
                <span class="q-badge">Q</span>
                <span class="q-text"><?php echo __('장기렌트 이용 중 사고시에 보험료 할증되나요?', 'skin')?></span>
                <span class="q-icon"><i class="bi bi-chevron-down"></i></span>
            </div>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    <span class="a-badge">A</span>
                    <p><?php echo __('할증되지 않습니다. 렌터카는 피보험자가 렌터카 회사 명의로 가입되기 때문에 사고로 인한 보험료 할증이 없고 월 렌트료도 인상되지 않습니다.

다만 사고시 면책금을 내야하는 상황이 발생합니다.', 'skin')?></p>
                </div>
            </div>
        </div>

        <!-- FAQ 6 -->
        <div class="faq-item" data-aos="fade-up" data-aos-delay="350">
            <div class="faq-question">
                <span class="q-badge">Q</span>
                <span class="q-text"><?php echo __('장기렌트의 장점은 무엇인가요?', 'skin')?></span>
                <span class="q-icon"><i class="bi bi-chevron-down"></i></span>
            </div>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    <span class="a-badge">A</span>
                    <p><?php echo __('장기렌트 상품은 기본적으로 임대 상품이다 보니 부채 또는 자산으로 잡히지 않으며, 개인 신용점수와는 무관한 상품이라 신용점수에 영향을 주지 않는 특징이 있습니다.

큰 여유자금이 필요하지 않아 초기비용에 대한 부담이 적은점도 좋은 장점이 될 수 있습니다.', 'skin')?></p>
                </div>
            </div>
        </div>

        <!-- FAQ 7 -->
        <div class="faq-item" data-aos="fade-up" data-aos-delay="400">
            <div class="faq-question">
                <span class="q-badge">Q</span>
                <span class="q-text"><?php echo __('보증금은 계약종료 후 돌려받을 수 있나요?', 'skin')?></span>
                <span class="q-icon"><i class="bi bi-chevron-down"></i></span>
            </div>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    <span class="a-badge">A</span>
                    <p><?php echo __('지불하신 보증금은 계약 만기 이후 차량 반납하실 때 100% 환급 받으실 수 있습니다.

다만, 차량의 파손 및 고장으로 인한 수리비용이 발생하는 경우 보증금에서 차감될 수 있습니다.', 'skin')?></p>
                </div>
            </div>
        </div>

        <!-- FAQ 8 -->
        <div class="faq-item" data-aos="fade-up" data-aos-delay="450">
            <div class="faq-question">
                <span class="q-badge">Q</span>
                <span class="q-text"><?php echo __('차량 출고는 얼마나 걸리나요?', 'skin')?></span>
                <span class="q-icon"><i class="bi bi-chevron-down"></i></span>
            </div>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    <span class="a-badge">A</span>
                    <p><?php echo __('보통 빠르면 다음날 출고 가능하며, 경·정비가 필요한 상황이라면 1~3일 내 차량이 출고됩니다.', 'skin')?></p>
                </div>
            </div>
        </div>

        <!-- FAQ 9 -->
        <div class="faq-item" data-aos="fade-up" data-aos-delay="500">
            <div class="faq-question">
                <span class="q-badge">Q</span>
                <span class="q-text"><?php echo __('자동차검사는 해주시나요?', 'skin')?></span>
                <span class="q-icon"><i class="bi bi-chevron-down"></i></span>
            </div>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    <span class="a-badge">A</span>
                    <p><?php echo __('자동차검사는 운전자 의무라 이용중인 고객님께서 하셔야 합니다.

자동차검사는 의무이기 때문에 반드시 하셔야 합니다. 검사기간이 도래하면 렌트사에서 고객님께 알려드립니다.', 'skin')?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA 섹션 -->
    <div class="faq-cta" data-aos="fade-up">
        <h3><?php echo __('더 궁금한 점이 있으신가요?', 'skin')?></h3>
        <p><?php echo __('전문 상담사가 친절하게 답변해 드립니다', 'skin')?></p>
        <div class="cta-buttons">
            <a href="tel:1666-5623" class="btn-cta btn-cta-primary">
                <i class="bi bi-telephone-fill"></i> <?php echo __('1666-5623', 'skin')?>
            </a>
            <a href="/kakaolink" class="btn-cta btn-cta-secondary" target="_blank">
                <i class="bi bi-chat-dots-fill"></i> <?php echo __('카카오톡 상담', 'skin')?>
            </a>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // FAQ 아코디언 기능
    const faqItems = document.querySelectorAll('.faq-item');

    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');

        question.addEventListener('click', function() {
            // 현재 활성화된 항목인지 확인
            const isActive = item.classList.contains('active');

            // 모든 항목 닫기
            faqItems.forEach(faq => {
                faq.classList.remove('active');
            });

            // 클릭한 항목이 이전에 활성화되지 않았다면 열기
            if (!isActive) {
                item.classList.add('active');
            }
        });
    });
});
</script>
