<div data-brand="<?php echo strtolower($item->brand ?? 'other'); ?>">

    <div class="car-card">
        <div class="car-image">
            <!-- <span class="car-badge new">신규등록</span> -->
            <!-- <span class="car-badge hot">인기</span> -->
            <!-- <span class="car-badge recommend">추천차량</span> -->
            <!-- <span class="car-badge no-deposit">무보증</span> -->
            <div class="car-wishlist">
                <i class="bi bi-heart"></i>
            </div>
            <img src="<?php echo $item->featured_image?>" alt="아반떼">
        </div>
        <div class="car-body">
            <h5 class="car-title"><?php echo htmlspecialchars($item->title)?></h5>
            <!-- <p class="car-trim"><?php echo htmlspecialchars($item->title)?></p> -->
            <div class="car-specs">
                <span class="spec-item"><i class="bi bi-calendar-check"></i> <?php echo $item->model_year?>년 <?php echo $item->model_month?>월</span>
                <span class="spec-item"><i class="bi bi-speedometer"></i> <?php echo number_format($item->mileage_km)?>km</span>
                <span class="spec-item"><i class="bi bi-fuel-pump"></i> <?php echo $item->fuel_type ?></span>
            </div>
            <div class="car-pricing">
                <div class="deposit">
                    <span class="deposit-label">보증금</span>
                    <span class="deposit-amount">500,000원</span>
                </div>
                <div class="monthly-price">
                    <span class="price-label">월 렌트료</span>
                    <span class="price-amount">
                        <?php if (!empty($item->min_price)): ?>
                        <?php echo __('월', 'skin'); ?> <?php echo number_format($item->min_price)?><span>원~</span>
                        <?php else: ?>
                        <?php echo __('가격 문의', 'skin'); ?>
                        <?php endif; ?>
                    </span>
                </div>
                <div class="car-actions">
                    <a href="/item/<?php echo $item->idx?>" class="btn-detail text-center">상세보기</a>
                    <!-- <a href="#" class="btn-consult-car text-center">견적신청</a> -->
                </div>
            </div>
        </div>
    </div>
</div>


                