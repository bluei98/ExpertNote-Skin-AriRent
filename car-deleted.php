<?php
/**
 * 차량 삭제/없음 페이지
 */

// 페이지 메타 정보
\ExpertNote\Core::setPageTitle("차량을 찾을 수 없습니다 - 아리렌트");
\ExpertNote\Core::setPageDescription("요청하신 차량 정보가 존재하지 않거나 삭제되었습니다. 다른 차량을 확인해보세요.");
\ExpertNote\Core::setPageKeywords("차량 없음, 404, 아리렌트");
?>

<section class="py-5" style="min-height: 60vh; display: flex; align-items: center;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <div class="mb-4" data-aos="fade-down">
                    <i class="bi bi-car-front" style="font-size: 5rem; color: #ddd;"></i>
                </div>
                <h2 class="display-5 fw-bold mb-3" data-aos="fade-up">차량을 찾을 수 없습니다</h2>
                <p class="text-muted mb-4" data-aos="fade-up" data-aos-delay="100">
                    요청하신 차량 정보가 존재하지 않거나 삭제되었습니다.<br>
                    다른 차량을 둘러보시겠어요?
                </p>
                <div class="d-flex gap-3 justify-content-center flex-wrap" data-aos="fade-up" data-aos-delay="200">
                    <a href="/" class="btn btn-primary btn-lg">
                        <i class="bi bi-house-door"></i> 홈으로 가기
                    </a>
                    <a href="/car-list" class="btn btn-outline-primary btn-lg">
                        <i class="bi bi-list-ul"></i> 차량 목록 보기
                    </a>
                </div>
                <div class="mt-5 pt-4 border-top" data-aos="fade-up" data-aos-delay="300">
                    <p class="text-muted small mb-3">인기 차량을 확인해보세요</p>
                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        <a href="/car-list?brand=현대" class="badge bg-light text-dark text-decoration-none px-3 py-2">현대</a>
                        <a href="/car-list?brand=기아" class="badge bg-light text-dark text-decoration-none px-3 py-2">기아</a>
                        <a href="/car-list?brand=제네시스" class="badge bg-light text-dark text-decoration-none px-3 py-2">제네시스</a>
                        <a href="/car-list?car_type=NEW" class="badge bg-light text-dark text-decoration-none px-3 py-2">신차</a>
                        <a href="/car-list?car_type=USED" class="badge bg-light text-dark text-decoration-none px-3 py-2">중고차</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
