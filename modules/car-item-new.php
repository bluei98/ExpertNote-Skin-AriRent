<div data-brand="<?php echo strtolower($item->brand ?? 'other'); ?>" onclick="location='/item/<?php echo $item->idx?>'" style="cursor: pointer;">
    <div class="car-card">
        <div class="car-image">
            <!-- <span class="car-badge new">신규등록</span> -->
            <!-- <span class="car-badge hot">인기</span> -->
            <!-- <span class="car-badge recommend">추천차량</span> -->
            <!-- <span class="car-badge no-deposit">무보증</span> -->
            <?php if (\ExpertNote\User\User::isAdmin() && !empty($item->dealer_code)): ?>
                <span class="badge bg-dark position-absolute top-3 end-0 p-2 m-2" style="font-size: 0.8rem;"><?php echo htmlspecialchars($item->dealer_name); ?></span>
            <?php endif; ?>
            <?php if (!empty($item->featured_image)): ?>
            <img src="<?php echo $item->featured_image?>" class="img-fluid" loading="lazy" alt="<?php echo htmlspecialchars($item->title); ?>">
            <?php else: ?>
                <i class="bi bi-car-front-fill"></i>
            <?php endif; ?>
        </div>
        <div class="car-body">
            <h4 class="car-title"><?php echo htmlspecialchars($item->title)?></h4>
            <!-- <p class="car-subtitle">현대 | 2024년식</p> -->
            <!-- <div class="car-info">
                <span class="car-info-item">가솔린</span>
                <span class="car-info-item">자동</span>
                <span class="car-info-item">1.6L</span>
            </div> -->
            <div class="car-price">
                <p class="car-price-label">월 렌트료</p>
                <p class="car-price-value">
                    <?php if (!empty($item->min_price)): ?>
                    <?php echo __('월', 'skin'); ?> <?php echo number_format($item->min_price)?>원~
                    <?php else: ?>
                    <?php echo __('가격 문의', 'skin'); ?>
                    <?php endif; ?>
                </p>
                <!-- <p class="car-deposit">보증금 0원 가능</p> -->
            </div>
        </div>
    </div>
</div>

                