<?php
/**
 * 이용약관 페이지
 */

ExpertNote\Core::setLayout("v2");
// 레이아웃 설정
\ExpertNote\Core::setPageTitle("이용약관 - 아리렌트");
\ExpertNote\Core::setPageDescription("아리렌트 서비스 이용약관입니다.");
?>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <div class="page-header-content">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">홈</a></li>
                        <li class="breadcrumb-item active" aria-current="page">이용약관</li>
                    </ol>
                </nav>
                <h1 class="page-title">이용약관</h1>
                <p class="page-desc">아리렌트 서비스 이용에 관한 약관입니다</p>
            </div>
        </div>
    </section>

    <!-- Terms Section -->
    <section class="terms-section">
        <div class="container">
            <div class="terms-container">
                <div class="terms-card">
                    <div class="terms-header">
                        <h2><i class="bi bi-file-earmark-text me-2"></i>아리렌트 이용약관</h2>
                        <p class="terms-date">시행일: 2025년 1월 1일</p>
                    </div>

                    <!-- 목차 -->
                    <div class="terms-toc">
                        <h4><i class="bi bi-list-ul me-2"></i>목차</h4>
                        <ul>
                            <li><a href="#chapter1">제1장 총칙</a></li>
                            <li><a href="#chapter2">제2장 회원가입과 서비스 이용</a></li>
                            <li><a href="#chapter3">제3장 계약 당사자의 의무</a></li>
                            <li><a href="#chapter4">제4장 책임 제한</a></li>
                            <li><a href="#chapter5">제5장 개인정보 보호</a></li>
                            <li><a href="#chapter6">제6장 분쟁 조정</a></li>
                        </ul>
                    </div>

                    <!-- 제1장 -->
                    <div class="terms-chapter" id="chapter1">
                        <h3><span class="chapter-number">제1장</span> 총칙</h3>

                        <div class="terms-article">
                            <h4>제1조 (목적)</h4>
                            <p>본 약관은 아리렌트(이하 "회사")가 제공하는 장기렌트 서비스 및 기타 관련 서비스의 이용과 관련하여 회사와 회원 간의 권리, 의무 및 책임사항을 규정함을 목적으로 합니다.</p>
                        </div>

                        <div class="terms-article">
                            <h4>제2조 (약관의 효력 및 변경)</h4>
                            <ol>
                                <li>본 약관은 서비스를 이용하고자 하는 모든 회원에게 효력이 발생합니다.</li>
                                <li>본 약관은 서비스 화면에 게시하거나 기타의 방법으로 회원에게 공지하고, 이에 동의한 회원이 서비스에 가입함으로써 효력이 발생합니다.</li>
                                <li>회사는 필요한 경우 관련 법령을 위배하지 않는 범위 내에서 본 약관을 변경할 수 있으며, 변경 시 사전에 공지합니다.</li>
                                <li>회원이 변경된 약관에 대해 거부 의사를 7일 이내에 표시하지 않으면 승인한 것으로 간주됩니다.</li>
                            </ol>
                        </div>

                        <div class="terms-article">
                            <h4>제3조 (약관 외 준칙)</h4>
                            <p>본 약관에 명시되지 않은 사항에 대해서는 전기통신기본법, 자동차관리법 등 관련 법령이 적용됩니다.</p>
                        </div>
                    </div>

                    <!-- 제2장 -->
                    <div class="terms-chapter" id="chapter2">
                        <h3><span class="chapter-number">제2장</span> 회원가입과 서비스 이용</h3>

                        <div class="terms-article">
                            <h4>제4조 (회원의 정의)</h4>
                            <p>회원이란 회사와 서비스 이용계약을 체결한 고객을 말하며, 본 약관에 따라 회사가 제공하는 서비스를 이용할 수 있습니다.</p>
                        </div>

                        <div class="terms-article">
                            <h4>제5조 (이용계약의 성립)</h4>
                            <ol>
                                <li>이용계약은 회원이 되고자 하는 자의 이용신청에 대한 회사의 승낙으로 성립합니다.</li>
                                <li>이용신청은 온라인 또는 전화 상담을 통해 할 수 있습니다.</li>
                                <li>회사는 다음 각 호에 해당하는 경우 이용신청을 거부할 수 있습니다.
                                    <ul>
                                        <li>실명이 아니거나 타인의 명의를 이용한 경우</li>
                                        <li>허위의 정보를 기재한 경우</li>
                                        <li>관련 법령에 위배되거나 기타 조건을 충족하지 못한 경우</li>
                                    </ul>
                                </li>
                            </ol>
                        </div>

                        <div class="terms-article">
                            <h4>제6조 (서비스 이용 제한)</h4>
                            <ol>
                                <li>회사는 천재지변, 불가항력, 긴급상황 등의 경우 서비스의 일부 또는 전부를 제한하거나 중지할 수 있습니다.</li>
                                <li>회사는 정기점검 등의 사유로 서비스를 일시 중단할 수 있으며, 이 경우 사전에 공지합니다.</li>
                            </ol>
                        </div>

                        <div class="terms-article">
                            <h4>제7조 (서비스 이용료)</h4>
                            <p>회사가 제공하는 서비스는 기본적으로 무료입니다. 단, 실제 차량 렌트 계약 체결 시에는 계약서에 명시된 별도의 요금이 적용됩니다.</p>
                        </div>
                    </div>

                    <!-- 제3장 -->
                    <div class="terms-chapter" id="chapter3">
                        <h3><span class="chapter-number">제3장</span> 계약 당사자의 의무</h3>

                        <div class="terms-article">
                            <h4>제8조 (회사의 의무)</h4>
                            <ol>
                                <li>회사는 관련 법령을 준수하고 지속적이고 안정적인 서비스를 제공하기 위해 최선을 다합니다.</li>
                                <li>회사는 회원의 개인정보를 보호하고 적절한 보안시스템을 구축합니다.</li>
                                <li>회사는 회원으로부터 제기되는 정당한 불만사항에 대해 신속하게 처리하고 그 결과를 통보합니다.</li>
                            </ol>
                        </div>

                        <div class="terms-article">
                            <h4>제9조 (회원의 의무)</h4>
                            <p>회원은 다음 행위를 하여서는 안 됩니다.</p>
                            <ol>
                                <li>신청 또는 변경 시 허위 내용의 등록</li>
                                <li>타인의 정보 도용</li>
                                <li>회사가 게시한 정보의 무단 변경</li>
                                <li>회사가 정한 정보 이외의 정보(컴퓨터 프로그램 등)의 송신 또는 게시</li>
                                <li>회사와 기타 제3자의 저작권 등 지적재산권에 대한 침해</li>
                                <li>회사 및 기타 제3자의 명예를 손상시키거나 업무를 방해하는 행위</li>
                                <li>외설 또는 폭력적인 메시지, 화상, 음성 등을 게시하는 행위</li>
                            </ol>
                        </div>
                    </div>

                    <!-- 제4장 -->
                    <div class="terms-chapter" id="chapter4">
                        <h3><span class="chapter-number">제4장</span> 책임 제한</h3>

                        <div class="terms-article">
                            <h4>제10조 (회사의 면책)</h4>
                            <ol>
                                <li>회사는 천재지변 또는 이에 준하는 불가항력으로 인하여 서비스를 제공할 수 없는 경우에는 서비스 제공에 관한 책임이 면제됩니다.</li>
                                <li>회사는 회원의 귀책사유로 인한 서비스 이용의 장애에 대하여는 책임을 지지 않습니다.</li>
                                <li>회사는 회원이 서비스를 이용하여 기대하는 수익을 상실한 것에 대하여 책임을 지지 않으며, 그 밖의 서비스를 통하여 얻은 자료로 인한 손해에 관하여 책임을 지지 않습니다.</li>
                                <li>회사는 회원이 서비스에 게재한 정보, 자료, 사실의 신뢰도, 정확성 등에 대해서는 책임을 지지 않습니다.</li>
                            </ol>
                        </div>

                        <div class="terms-article">
                            <h4>제11조 (정보의 제공)</h4>
                            <ol>
                                <li>회사는 회원에게 서비스 이용에 필요한 정보를 서비스 화면, 이메일, 문자메시지 등의 방법으로 제공할 수 있습니다.</li>
                                <li>회원은 원하지 않는 정보 수신에 대해 거부 의사를 표시할 수 있습니다.</li>
                            </ol>
                        </div>
                    </div>

                    <!-- 제5장 -->
                    <div class="terms-chapter" id="chapter5">
                        <h3><span class="chapter-number">제5장</span> 개인정보 보호</h3>

                        <div class="terms-article">
                            <h4>제12조 (개인정보의 보호)</h4>
                            <p>회사는 정보통신망 이용촉진 및 정보보호 등에 관한 법률, 개인정보보호법 등 관련 법령이 정하는 바에 따라 회원의 개인정보를 보호하기 위해 노력합니다. 개인정보의 보호 및 이용에 대해서는 관련 법령 및 회사의 개인정보처리방침이 적용됩니다.</p>
                        </div>
                    </div>

                    <!-- 제6장 -->
                    <div class="terms-chapter" id="chapter6">
                        <h3><span class="chapter-number">제6장</span> 분쟁 조정</h3>

                        <div class="terms-article">
                            <h4>제13조 (분쟁 해결)</h4>
                            <ol>
                                <li>회사는 회원으로부터 제출되는 불만사항 및 의견을 우선적으로 처리합니다.</li>
                                <li>회사가 신속하게 처리하기 곤란한 경우에는 회원에게 그 사유와 처리 일정을 통보합니다.</li>
                                <li>서비스 이용 중 발생한 회원과 회사 간의 소송은 민사소송법상의 관할법원에 제소합니다.</li>
                            </ol>
                        </div>

                        <div class="terms-article">
                            <h4>제14조 (준거법 및 관할법원)</h4>
                            <ol>
                                <li>본 약관의 해석 및 회사와 회원 간의 분쟁에 대하여는 대한민국의 법률을 적용합니다.</li>
                                <li>서비스 이용 중 발생한 회원과 회사 간의 소송은 민사소송법상의 관할법원에 제소합니다.</li>
                            </ol>
                        </div>
                    </div>

                    <!-- 부칙 -->
                    <div class="terms-chapter">
                        <h3><span class="chapter-number">부칙</span></h3>
                        <div class="terms-article">
                            <p>본 약관은 2025년 1월 1일부터 시행합니다.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>