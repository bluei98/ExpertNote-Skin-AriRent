<?php
/**
 * 이용약관 페이지
 */

// 레이아웃 설정
\ExpertNote\Core::setPageTitle("이용약관 - 아리렌트");
\ExpertNote\Core::setPageDescription("아리렌트 서비스 이용약관입니다.");
?>

<style>
    .terms-header {
        background: var(--primary-color);
        color: white;
        padding: 3rem 0;
        text-align: center;
    }

    .terms-header h1 {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }

    .terms-header p {
        font-size: 1rem;
        margin-bottom: 0;
        opacity: 0.9;
    }

    .terms-content {
        padding: 3rem 0;
        background: white;
    }

    .terms-container {
        max-width: 900px;
        margin: 0 auto;
        background: white;
        padding: 2rem;
    }

    .terms-chapter {
        margin-bottom: 3rem;
    }

    .terms-chapter h2 {
        font-size: 1.5rem;
        font-weight: bold;
        color: var(--primary-color);
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--primary-color);
    }

    .terms-article {
        margin-bottom: 2rem;
    }

    .terms-article h3 {
        font-size: 1.2rem;
        font-weight: bold;
        color: var(--dark-color);
        margin-bottom: 1rem;
    }

    .terms-article p,
    .terms-article ol,
    .terms-article ul {
        font-size: 0.95rem;
        line-height: 1.8;
        color: #555;
        margin-bottom: 1rem;
    }

    .terms-article ol {
        padding-left: 2rem;
    }

    .terms-article ol li {
        margin-bottom: 0.5rem;
    }

    .terms-notice {
        background: var(--light-color);
        border-left: 4px solid var(--primary-color);
        padding: 1rem 1.5rem;
        margin: 2rem 0;
    }

    .terms-notice p {
        margin-bottom: 0;
        color: var(--dark-color);
    }

    .terms-footer {
        text-align: right;
        padding-top: 2rem;
        margin-top: 3rem;
        border-top: 1px solid #e9ecef;
        color: #888;
        font-size: 0.9rem;
    }

    @media (max-width: 768px) {
        .terms-container {
            padding: 1rem;
        }

        .terms-chapter h2 {
            font-size: 1.3rem;
        }

        .terms-article h3 {
            font-size: 1.1rem;
        }
    }
</style>

<!-- 헤더 -->
<section class="terms-header">
    <div class="container">
        <h1>이용약관</h1>
        <p>아리렌트 서비스 이용약관</p>
    </div>
</section>

<!-- 약관 내용 -->
<section class="terms-content">
    <div class="container">
        <div class="terms-container">
            <!-- 제1장 총칙 -->
            <div class="terms-chapter">
                <h2>제1장 총칙</h2>

                <div class="terms-article">
                    <h3>제1조 (목적)</h3>
                    <p>
                        본 약관은 아리렌트(이하 "회사"라 합니다)가 제공하는 장기렌트 서비스 및 기타 관련 서비스(이하 "서비스"라 합니다)의 이용과 관련하여 회사와 회원 간의 권리, 의무 및 책임사항, 기타 필요한 사항을 규정함을 목적으로 합니다.
                    </p>
                </div>

                <div class="terms-article">
                    <h3>제2조 (약관의 효력 및 변경)</h3>
                    <ol>
                        <li>본 약관은 서비스를 이용하고자 하는 모든 회원에 대하여 그 효력을 발생합니다.</li>
                        <li>본 약관의 내용은 서비스 화면에 게시하거나 기타의 방법으로 회원에게 공지하고, 이에 동의한 회원이 서비스에 가입함으로써 효력이 발생합니다.</li>
                        <li>회사는 필요한 경우 관련 법령을 위배하지 않는 범위 내에서 본 약관을 변경할 수 있으며, 약관이 변경되는 경우 지체 없이 이를 사전 공지합니다.</li>
                        <li>회원이 변경된 약관에 동의하지 않는 경우, 회원은 서비스 이용을 중단하고 탈퇴할 수 있습니다. 약관 변경 공지 후 7일 이내에 거부 의사를 표시하지 않는 경우 승인한 것으로 봅니다.</li>
                    </ol>
                </div>

                <div class="terms-article">
                    <h3>제3조 (약관 외 준칙)</h3>
                    <p>
                        본 약관에 명시되지 않은 사항은 전기통신기본법, 전기통신사업법, 정보통신망 이용촉진 및 정보보호 등에 관한 법률, 자동차관리법 등 관련 법령 및 회사가 정한 서비스의 세부 이용지침 등의 규정에 따릅니다.
                    </p>
                </div>
            </div>

            <!-- 제2장 회원가입과 서비스 이용 -->
            <div class="terms-chapter">
                <h2>제2장 회원가입과 서비스 이용</h2>

                <div class="terms-article">
                    <h3>제4조 (회원의 정의)</h3>
                    <ol>
                        <li>"회원"이란 회사와 서비스 이용계약을 체결하고 회사가 제공하는 서비스를 이용하는 고객을 말합니다.</li>
                        <li>회원은 회사가 제공하는 서비스를 본 약관에 따라 이용할 수 있습니다.</li>
                    </ol>
                </div>

                <div class="terms-article">
                    <h3>제5조 (이용계약의 성립)</h3>
                    <ol>
                        <li>이용계약은 서비스를 이용하고자 하는 자의 이용신청에 대하여 회사가 승낙함으로써 성립합니다.</li>
                        <li>서비스 이용신청은 온라인 또는 전화 상담을 통해 이루어집니다.</li>
                        <li>회사는 다음 각 호에 해당하는 경우 이용신청을 거부할 수 있습니다.
                            <ol style="list-style-type: lower-alpha; padding-left: 2rem;">
                                <li>실명이 아니거나 타인의 명의를 이용한 경우</li>
                                <li>허위의 정보를 기재하거나, 회사가 제시하는 내용을 기재하지 않은 경우</li>
                                <li>관련 법령에 위배되거나 사회의 안녕질서 또는 미풍양속을 저해할 목적으로 신청한 경우</li>
                                <li>기타 회사가 정한 이용 조건에 맞지 않는 경우</li>
                            </ol>
                        </li>
                    </ol>
                </div>

                <div class="terms-article">
                    <h3>제6조 (서비스 이용 제한)</h3>
                    <ol>
                        <li>회사는 전시, 사변, 천재지변 또는 이에 준하는 국가비상사태가 발생하거나 발생할 우려가 있는 경우, 전기통신사업법에 의한 기간통신사업자가 전기통신서비스를 중지하는 경우 등 부득이한 사유가 있는 경우에는 서비스의 전부 또는 일부를 제한하거나 중지할 수 있습니다.</li>
                        <li>회사는 서비스 개선을 위한 정기점검 또는 보수 시 필요한 경우 서비스를 일시 중단할 수 있으며, 이 경우 사전에 공지합니다.</li>
                    </ol>
                </div>

                <div class="terms-article">
                    <h3>제7조 (서비스 이용료)</h3>
                    <p>
                        회사가 제공하는 서비스는 기본적으로 무료입니다. 단, 실제 차량 렌트 계약 체결 시에는 별도의 렌트 계약서에 명시된 요금이 적용됩니다.
                    </p>
                </div>
            </div>

            <!-- 제3장 계약 당사자의 의무 -->
            <div class="terms-chapter">
                <h2>제3장 계약 당사자의 의무</h2>

                <div class="terms-article">
                    <h3>제8조 (회사의 의무)</h3>
                    <ol>
                        <li>회사는 관련 법령과 본 약관이 금지하거나 미풍양속에 반하는 행위를 하지 않으며, 계속적이고 안정적인 서비스 제공을 위해 최선을 다합니다.</li>
                        <li>회사는 회원의 개인정보 보호를 위하여 보안시스템을 구축하고 개인정보처리방침을 공시하고 준수합니다.</li>
                        <li>회사는 서비스 이용과 관련하여 회원으로부터 제기된 의견이나 불만이 정당하다고 인정될 경우 이를 처리하여야 하며, 처리 시 일정 기간이 소요될 경우 회원에게 그 사유와 처리 일정을 통보합니다.</li>
                    </ol>
                </div>

                <div class="terms-article">
                    <h3>제9조 (회원의 의무)</h3>
                    <ol>
                        <li>회원은 다음 각 호의 행위를 하여서는 안 됩니다.
                            <ol style="list-style-type: lower-alpha; padding-left: 2rem;">
                                <li>신청 또는 변경 시 허위 내용의 등록</li>
                                <li>타인의 정보 도용</li>
                                <li>회사가 게시한 정보의 변경</li>
                                <li>회사가 정한 정보 이외의 정보(컴퓨터 프로그램 등) 등의 송신 또는 게시</li>
                                <li>회사 및 기타 제3자의 저작권 등 지적재산권에 대한 침해</li>
                                <li>회사 및 기타 제3자의 명예를 손상시키거나 업무를 방해하는 행위</li>
                                <li>외설 또는 폭력적인 메시지, 화상, 음성, 기타 공서양속에 반하는 정보를 서비스에 공개 또는 게시하는 행위</li>
                            </ol>
                        </li>
                    </ol>
                </div>
            </div>

            <!-- 제4장 책임 제한 -->
            <div class="terms-chapter">
                <h2>제4장 책임 제한</h2>

                <div class="terms-article">
                    <h3>제10조 (회사의 면책)</h3>
                    <ol>
                        <li>회사는 천재지변 또는 이에 준하는 불가항력으로 인하여 서비스를 제공할 수 없는 경우에는 서비스 제공에 관한 책임이 면제됩니다.</li>
                        <li>회사는 회원의 귀책사유로 인한 서비스 이용의 장애에 대하여 책임을 지지 않습니다.</li>
                        <li>회사는 회원이 서비스를 이용하여 기대하는 수익을 상실한 것에 대하여 책임을 지지 않으며, 그 밖의 서비스를 통하여 얻은 자료로 인한 손해에 관하여 책임을 지지 않습니다.</li>
                        <li>회사는 회원이 게재한 정보, 자료, 사실의 신뢰도, 정확성 등의 내용에 관하여는 책임을 지지 않습니다.</li>
                    </ol>
                </div>

                <div class="terms-article">
                    <h3>제11조 (정보의 제공)</h3>
                    <ol>
                        <li>회사는 회원에게 서비스 이용에 필요한 정보를 제공할 수 있으며, 이는 서비스 화면, 이메일, 문자메시지 등의 방법으로 전달될 수 있습니다.</li>
                        <li>회원은 언제든지 정보 수신을 거부할 수 있습니다.</li>
                    </ol>
                </div>
            </div>

            <!-- 제5장 개인정보 보호 -->
            <div class="terms-chapter">
                <h2>제5장 개인정보 보호</h2>

                <div class="terms-article">
                    <h3>제12조 (개인정보의 보호)</h3>
                    <ol>
                        <li>회사는 관련 법령이 정하는 바에 따라 회원의 개인정보를 보호하기 위해 노력합니다.</li>
                        <li>회원의 개인정보 보호에 관한 사항은 관련 법령 및 회사가 정한 개인정보처리방침에 따릅니다.</li>
                    </ol>
                </div>
            </div>

            <!-- 제6장 분쟁 조정 -->
            <div class="terms-chapter">
                <h2>제6장 분쟁 조정</h2>

                <div class="terms-article">
                    <h3>제13조 (분쟁 해결)</h3>
                    <ol>
                        <li>회사는 회원으로부터 제출되는 불만사항 및 의견을 우선적으로 처리합니다.</li>
                        <li>다만, 신속한 처리가 곤란한 경우 회원에게 그 사유와 처리 일정을 통보합니다.</li>
                        <li>회사와 회원 간 발생한 분쟁에 관한 소송은 민사소송법상의 관할법원에 제소합니다.</li>
                    </ol>
                </div>

                <div class="terms-article">
                    <h3>제14조 (준거법 및 관할법원)</h3>
                    <ol>
                        <li>회사와 회원 간 제기된 소송은 대한민국법을 준거법으로 합니다.</li>
                        <li>회사와 회원 간 발생한 분쟁에 관한 소송은 민사소송법상의 관할법원에 제소합니다.</li>
                    </ol>
                </div>
            </div>

            <div class="terms-notice">
                <p><strong>부칙</strong></p>
                <p>본 약관은 2025년 1월 1일부터 시행됩니다.</p>
            </div>

            <div class="terms-footer">
                <p>아리렌트</p>
                <p>최종 수정일: 2025년 1월 1일</p>
            </div>
        </div>
    </div>
</section>
