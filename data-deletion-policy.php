<?php
/**
 * 데이터 삭제 정책 페이지
 */

ExpertNote\Core::setLayout("v2");
// 레이아웃 설정
\ExpertNote\Core::setPageTitle("데이터 삭제 정책 - 아리렌트");
\ExpertNote\Core::setPageDescription("아리렌트 데이터 삭제 정책입니다.");
?>
    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <div class="page-header-content">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/">홈</a></li>
                        <li class="breadcrumb-item active" aria-current="page">데이터 삭제 정책</li>
                    </ol>
                </nav>
                <h1 class="page-title">데이터 삭제 정책</h1>
                <p class="page-desc">아리렌트의 사용자 데이터 삭제 정책입니다</p>
            </div>
        </div>
    </section>

    <!-- Data Deletion Policy Section -->
    <section class="terms-section">
        <div class="container">
            <div class="terms-container">
                <div class="terms-card">
                    <div class="terms-header">
                        <h2><i class="bi bi-trash3 me-2"></i>아리렌트 데이터 삭제 정책</h2>
                        <p class="terms-date">시행일: 2025년 1월 1일 | 버전: v1.0</p>
                    </div>

                    <!-- 목차 -->
                    <div class="terms-toc">
                        <h4><i class="bi bi-list-ul me-2"></i>목차</h4>
                        <ul>
                            <li><a href="#article1">제1조 총칙</a></li>
                            <li><a href="#article2">제2조 삭제 대상 데이터</a></li>
                            <li><a href="#article3">제3조 데이터 삭제 요청 방법</a></li>
                            <li><a href="#article4">제4조 삭제 처리 절차</a></li>
                            <li><a href="#article5">제5조 삭제 처리 기간</a></li>
                            <li><a href="#article6">제6조 삭제 예외 사항</a></li>
                            <li><a href="#article7">제7조 소셜 로그인 데이터 삭제</a></li>
                            <li><a href="#article8">제8조 삭제 확인</a></li>
                            <li><a href="#article9">제9조 문의처</a></li>
                        </ul>
                    </div>

                    <!-- 제1조 -->
                    <div class="terms-chapter" id="article1">
                        <h3><span class="chapter-number">제1조</span> 총칙</h3>
                        <div class="terms-article">
                            <p>아리렌트(이하 "회사")는 고객님의 개인정보 자기결정권을 존중하며, 고객님이 언제든지 본인의 개인정보 삭제를 요청할 수 있는 권리를 보장합니다.</p>
                            <p>본 정책은 「개인정보보호법」 및 「정보통신망 이용촉진 및 정보보호 등에 관한 법률」에 따라 고객님의 데이터 삭제 요청에 대한 처리 절차와 방법을 안내합니다.</p>
                        </div>
                    </div>

                    <!-- 제2조 -->
                    <div class="terms-chapter" id="article2">
                        <h3><span class="chapter-number">제2조</span> 삭제 대상 데이터</h3>
                        <div class="terms-article">
                            <p>삭제 요청 시 다음과 같은 데이터가 삭제됩니다.</p>
                            <h4>1. 계정 정보</h4>
                            <ul>
                                <li>이름, 이메일 주소, 연락처</li>
                                <li>로그인 정보 및 인증 토큰</li>
                                <li>프로필 정보</li>
                            </ul>

                            <h4>2. 서비스 이용 정보</h4>
                            <ul>
                                <li>상담 신청 내역</li>
                                <li>관심 차량 정보</li>
                                <li>서비스 이용 기록</li>
                            </ul>

                            <h4>3. 소셜 로그인 연동 정보</h4>
                            <ul>
                                <li>Facebook, Google, Kakao 등 소셜 로그인 연동 데이터</li>
                                <li>소셜 계정에서 제공받은 프로필 정보</li>
                            </ul>
                        </div>
                    </div>

                    <!-- 제3조 -->
                    <div class="terms-chapter" id="article3">
                        <h3><span class="chapter-number">제3조</span> 데이터 삭제 요청 방법</h3>
                        <div class="terms-article">
                            <p>고객님은 다음 방법을 통해 데이터 삭제를 요청하실 수 있습니다.</p>

                            <h4>1. 이메일 요청</h4>
                            <div class="bg-light p-3 rounded mb-3">
                                <p class="mb-1"><strong>이메일:</strong> support@arirent.co.kr</p>
                                <p class="mb-0"><strong>제목:</strong> [데이터 삭제 요청] 회원명</p>
                            </div>
                            <p>이메일 요청 시 다음 정보를 포함해 주세요:</p>
                            <ul>
                                <li>성명</li>
                                <li>가입 시 사용한 이메일 또는 연락처</li>
                                <li>삭제 요청 사유 (선택)</li>
                                <li>본인 확인 정보</li>
                            </ul>

                            <h4>2. 전화 요청</h4>
                            <div class="bg-light p-3 rounded mb-3">
                                <p class="mb-1"><strong>고객센터:</strong> 1666-5623</p>
                                <p class="mb-0"><strong>운영시간:</strong> 평일 09:00 - 18:00</p>
                            </div>

                            <h4>3. 웹사이트 마이페이지</h4>
                            <p>로그인 후 마이페이지 > 회원탈퇴 메뉴를 통해 직접 삭제를 요청하실 수 있습니다.</p>
                        </div>
                    </div>

                    <!-- 제4조 -->
                    <div class="terms-chapter" id="article4">
                        <h3><span class="chapter-number">제4조</span> 삭제 처리 절차</h3>
                        <div class="terms-article">
                            <p>데이터 삭제 요청은 다음 절차에 따라 처리됩니다.</p>
                            <ol>
                                <li><strong>요청 접수:</strong> 고객님의 삭제 요청을 접수합니다.</li>
                                <li><strong>본인 확인:</strong> 요청자가 본인임을 확인합니다.</li>
                                <li><strong>삭제 대상 확인:</strong> 삭제 대상 데이터를 확인합니다.</li>
                                <li><strong>법적 보존 의무 검토:</strong> 법령에 따른 보존 의무 대상 여부를 검토합니다.</li>
                                <li><strong>데이터 삭제:</strong> 확인된 데이터를 안전하게 삭제합니다.</li>
                                <li><strong>삭제 완료 통보:</strong> 삭제 완료 후 고객님께 결과를 통보합니다.</li>
                            </ol>
                        </div>
                    </div>

                    <!-- 제5조 -->
                    <div class="terms-chapter" id="article5">
                        <h3><span class="chapter-number">제5조</span> 삭제 처리 기간</h3>
                        <div class="terms-article">
                            <table class="table table-bordered mt-3">
                                <thead>
                                    <tr>
                                        <th>구분</th>
                                        <th>처리 기간</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>일반 데이터 삭제</td>
                                        <td>요청일로부터 <strong>7일 이내</strong></td>
                                    </tr>
                                    <tr>
                                        <td>소셜 로그인 연동 데이터</td>
                                        <td>요청일로부터 <strong>7일 이내</strong></td>
                                    </tr>
                                    <tr>
                                        <td>백업 시스템에서의 삭제</td>
                                        <td>요청일로부터 <strong>30일 이내</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- 제6조 -->
                    <div class="terms-chapter" id="article6">
                        <h3><span class="chapter-number">제6조</span> 삭제 예외 사항</h3>
                        <div class="terms-article">
                            <p>다음의 경우 법령에 따라 일정 기간 동안 데이터를 보존해야 하므로 즉시 삭제가 불가능할 수 있습니다.</p>
                            <table class="table table-bordered mt-3">
                                <thead>
                                    <tr>
                                        <th>보존 항목</th>
                                        <th>보존 기간</th>
                                        <th>근거 법령</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>계약 및 청약철회 기록</td>
                                        <td>5년</td>
                                        <td>전자상거래법</td>
                                    </tr>
                                    <tr>
                                        <td>대금결제 및 재화공급 기록</td>
                                        <td>5년</td>
                                        <td>전자상거래법</td>
                                    </tr>
                                    <tr>
                                        <td>소비자 불만 또는 분쟁처리 기록</td>
                                        <td>3년</td>
                                        <td>전자상거래법</td>
                                    </tr>
                                    <tr>
                                        <td>웹사이트 방문 기록</td>
                                        <td>1년</td>
                                        <td>통신비밀보호법</td>
                                    </tr>
                                </tbody>
                            </table>
                            <p class="mt-3">위 기간이 경과한 후에는 해당 데이터도 완전히 삭제됩니다.</p>
                        </div>
                    </div>

                    <!-- 제7조 -->
                    <div class="terms-chapter" id="article7">
                        <h3><span class="chapter-number">제7조</span> 소셜 로그인 데이터 삭제</h3>
                        <div class="terms-article">
                            <p>소셜 로그인(Facebook, Google, Kakao 등)을 통해 가입한 경우, 다음과 같이 처리됩니다.</p>

                            <h4>1. Facebook 로그인 사용자</h4>
                            <ul>
                                <li>아리렌트에 저장된 Facebook 연동 정보가 삭제됩니다.</li>
                                <li>Facebook 앱 설정에서도 아리렌트 연결을 해제하실 것을 권장합니다.</li>
                                <li>Facebook 설정 > 앱 및 웹사이트 > 아리렌트 > 삭제</li>
                            </ul>

                            <h4>2. Google 로그인 사용자</h4>
                            <ul>
                                <li>아리렌트에 저장된 Google 연동 정보가 삭제됩니다.</li>
                                <li>Google 계정 설정에서도 아리렌트 연결을 해제하실 것을 권장합니다.</li>
                            </ul>

                            <h4>3. Kakao 로그인 사용자</h4>
                            <ul>
                                <li>아리렌트에 저장된 Kakao 연동 정보가 삭제됩니다.</li>
                                <li>카카오 계정 설정에서도 아리렌트 연결을 해제하실 것을 권장합니다.</li>
                            </ul>

                            <div class="alert alert-info mt-3">
                                <i class="bi bi-info-circle me-2"></i>
                                소셜 플랫폼에서 직접 앱 연결을 해제하셔도 아리렌트에 저장된 데이터는 별도로 삭제 요청을 해주셔야 합니다.
                            </div>
                        </div>
                    </div>

                    <!-- 제8조 -->
                    <div class="terms-chapter" id="article8">
                        <h3><span class="chapter-number">제8조</span> 삭제 확인</h3>
                        <div class="terms-article">
                            <p>데이터 삭제가 완료되면 다음과 같이 확인하실 수 있습니다.</p>
                            <ol>
                                <li><strong>삭제 완료 통보:</strong> 요청 시 기재한 이메일 또는 연락처로 삭제 완료 통보를 받으실 수 있습니다.</li>
                                <li><strong>삭제 확인 코드:</strong> 요청 시 삭제 확인 코드를 발급받으실 수 있습니다. 이 코드를 통해 삭제 처리 상태를 확인할 수 있습니다.</li>
                                <li><strong>로그인 불가:</strong> 삭제 완료 후에는 기존 계정으로 로그인이 불가능합니다.</li>
                            </ol>
                        </div>
                    </div>

                    <!-- 제9조 -->
                    <div class="terms-chapter" id="article9">
                        <h3><span class="chapter-number">제9조</span> 문의처</h3>
                        <div class="terms-article">
                            <p>데이터 삭제와 관련하여 궁금하신 사항이 있으시면 아래로 문의해 주세요.</p>
                            <div class="bg-light p-4 rounded mt-3">
                                <h5><i class="bi bi-headset me-2"></i>고객센터</h5>
                                <ul class="list-unstyled mb-0 mt-3">
                                    <li><strong>전화:</strong> 1666-5623</li>
                                    <li><strong>이메일:</strong> support@arirent.co.kr</li>
                                    <li><strong>운영시간:</strong> 평일 09:00 - 18:00</li>
                                </ul>
                            </div>
                            <div class="bg-light p-4 rounded mt-3">
                                <h5><i class="bi bi-person-badge me-2"></i>개인정보 보호책임자</h5>
                                <ul class="list-unstyled mb-0 mt-3">
                                    <li><strong>성명:</strong> 엄성용</li>
                                    <li><strong>직책:</strong> 개인정보보호팀장</li>
                                    <li><strong>연락처:</strong> 010-5942-1002</li>
                                    <li><strong>이메일:</strong> support@arirent.co.kr</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- 부칙 -->
                    <div class="terms-chapter">
                        <h3><span class="chapter-number">부칙</span></h3>
                        <div class="terms-article">
                            <p>본 데이터 삭제 정책은 2025년 1월 1일부터 시행합니다.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
