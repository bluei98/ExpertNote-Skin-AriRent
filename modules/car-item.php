                <!-- Vehicle Card -->
                <div class="col" data-brand="<?php echo strtolower($item->brand ?? 'other'); ?>" onclick="location='/item/<?php echo $item->idx?>'">
                    <div class="card vehicle-card shadow-sm border-0">
                        <div class="vehicle-image position-relative">
                            <?php if (!empty($item->featured_image)): ?>
                            <img src="<?php echo $item->featured_image?>" class="img-fluid" loading="lazy" alt="<?php echo htmlspecialchars($item->title); ?>">
                            <?php else: ?>
                                <i class="bi bi-car-front-fill"></i>
                            <?php endif; ?>
                            
                            <?php if (\ExpertNote\User\User::isAdmin() && !empty($item->dealer_code)): ?>
                                <span class="badge bg-dark position-absolute top-0 start-0 m-2 rounded-0" style="font-size: 0.8rem;"><?php echo htmlspecialchars($item->dealer_name); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title fw-bold"><?php echo htmlspecialchars($item->title)?></h5>
                            <p class="text-primary fw-bold fs-5">
                                <?php if (!empty($item->min_price)): ?>
                                <?php echo __('월', 'skin'); ?> <?php echo number_format($item->min_price)?>원~
                                <?php else: ?>
                                <?php echo __('가격 문의', 'skin'); ?>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>