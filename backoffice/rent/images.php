<?php
// 차량 이미지 관리
$breadcrumb = ["rent", "images"];
$_page_title = __('이미지 관리', 'manager');

$rentIdx = $_GET['rent_idx'] ?? null;

if (empty($rentIdx)) {
    echo "<script>alert('차량을 선택해주세요.'); location.href='car-list';</script>";
    exit;
}

// 차량 정보 조회
$carSql = "SELECT r.*, d.dealer_name FROM " . DB_PREFIX . "rent r
           LEFT JOIN " . DB_PREFIX . "rent_dealer d ON r.dealer_idx = d.idx
           WHERE r.idx = :idx";
$car = \ExpertNote\DB::getRow($carSql, ['idx' => $rentIdx]);
if (!$car) {
    echo "<script>alert('차량을 찾을 수 없습니다.'); location.href='car-list';</script>";
    exit;
}

// 이미지 목록 조회
$imagesSql = "SELECT * FROM " . DB_PREFIX . "rent_images WHERE rent_idx = :rent_idx ORDER BY image_order, idx";
$images = \ExpertNote\DB::getRows($imagesSql, ['rent_idx' => $rentIdx]);

// POST 처리 - 이미지 추가
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $imageUrl = trim($_POST['image_url'] ?? '');
        if (!empty($imageUrl)) {
            // 마지막 순서 조회
            $maxOrderSql = "SELECT MAX(image_order) as max_order FROM " . DB_PREFIX . "rent_images WHERE rent_idx = :rent_idx";
            $maxOrder = \ExpertNote\DB::getRow($maxOrderSql, ['rent_idx' => $rentIdx])->max_order ?? 0;

            $sql = "INSERT INTO " . DB_PREFIX . "rent_images (rent_idx, image_url, image_order) VALUES (:rent_idx, :image_url, :image_order)";
            \ExpertNote\DB::query($sql, [
                'rent_idx' => $rentIdx,
                'image_url' => $imageUrl,
                'image_order' => $maxOrder + 1
            ]);
            $success = '이미지가 추가되었습니다.';
            // 다시 조회
            $images = \ExpertNote\DB::getRows($imagesSql, ['rent_idx' => $rentIdx]);
        }
    } elseif ($_POST['action'] === 'delete') {
        $imageIdx = intval($_POST['image_idx'] ?? 0);
        if ($imageIdx > 0) {
            $sql = "DELETE FROM " . DB_PREFIX . "rent_images WHERE idx = :idx AND rent_idx = :rent_idx";
            \ExpertNote\DB::query($sql, ['idx' => $imageIdx, 'rent_idx' => $rentIdx]);
            $success = '이미지가 삭제되었습니다.';
            // 다시 조회
            $images = \ExpertNote\DB::getRows($imagesSql, ['rent_idx' => $rentIdx]);
        }
    } elseif ($_POST['action'] === 'set_main') {
        $imageIdx = intval($_POST['image_idx'] ?? 0);
        if ($imageIdx > 0) {
            // 해당 이미지 URL 조회
            $imgSql = "SELECT image_url FROM " . DB_PREFIX . "rent_images WHERE idx = :idx";
            $img = \ExpertNote\DB::getRow($imgSql, ['idx' => $imageIdx]);
            if ($img) {
                // 차량 대표 이미지 업데이트
                $sql = "UPDATE " . DB_PREFIX . "rent SET image = :image WHERE idx = :idx";
                \ExpertNote\DB::query($sql, ['image' => $img->image_url, 'idx' => $rentIdx]);
                $success = '대표 이미지가 설정되었습니다.';
                // 차량 정보 다시 조회
                $car = \ExpertNote\DB::getRow($carSql, ['idx' => $rentIdx]);
            }
        }
    } elseif ($_POST['action'] === 'update_order') {
        $orders = $_POST['orders'] ?? [];
        foreach ($orders as $imageIdx => $order) {
            $sql = "UPDATE " . DB_PREFIX . "rent_images SET image_order = :image_order WHERE idx = :idx AND rent_idx = :rent_idx";
            \ExpertNote\DB::query($sql, [
                'image_order' => intval($order),
                'idx' => intval($imageIdx),
                'rent_idx' => $rentIdx
            ]);
        }
        $success = '순서가 저장되었습니다.';
        // 다시 조회
        $images = \ExpertNote\DB::getRows($imagesSql, ['rent_idx' => $rentIdx]);
    }
}
?>

<div class="card">
    <div class="card-header d-flex align-items-center">
        <h5 class="mb-0">
            <i class="ph-images me-2"></i>
            <?php echo $_page_title ?>
            <small class="text-muted ms-2">(<?php echo htmlspecialchars($car->title) ?>)</small>
        </h5>
        <div class="ms-auto">
            <a href="car-edit?idx=<?php echo $rentIdx ?>" class="btn btn-outline-secondary">
                <i class="ph-arrow-left me-1"></i><?php echo __('차량으로', 'manager') ?>
            </a>
        </div>
    </div>
    <div class="card-body">
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success ?></div>
        <?php endif; ?>

        <!-- 이미지 추가 폼 -->
        <form method="post" class="mb-4">
            <input type="hidden" name="action" value="add">
            <div class="input-group">
                <input type="url" name="image_url" class="form-control" placeholder="이미지 URL을 입력하세요" required>
                <button type="submit" class="btn btn-primary">
                    <i class="ph-plus me-1"></i><?php echo __('추가', 'manager') ?>
                </button>
            </div>
        </form>

        <!-- 이미지 목록 -->
        <?php if (count($images) > 0): ?>
        <form method="post" id="orderForm">
            <input type="hidden" name="action" value="update_order">
            <div class="row g-3">
                <?php foreach ($images as $img): ?>
                <div class="col-md-3 col-sm-4 col-6">
                    <div class="card h-100 <?php echo $car->image === $img->image_url ? 'border-primary' : '' ?>">
                        <div class="position-relative">
                            <img src="<?php echo $img->image_url ?>" class="card-img-top" style="height: 150px; object-fit: cover;">
                            <?php if ($car->image === $img->image_url): ?>
                            <span class="position-absolute top-0 start-0 badge bg-primary m-2">
                                <i class="ph-star me-1"></i><?php echo __('대표', 'manager') ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body p-2">
                            <div class="input-group input-group-sm mb-2">
                                <span class="input-group-text"><?php echo __('순서', 'manager') ?></span>
                                <input type="number" name="orders[<?php echo $img->idx ?>]" class="form-control text-center"
                                    value="<?php echo $img->image_order ?>" min="0">
                            </div>
                            <div class="btn-group btn-group-sm w-100">
                                <?php if ($car->image !== $img->image_url): ?>
                                <form method="post" class="d-inline">
                                    <input type="hidden" name="action" value="set_main">
                                    <input type="hidden" name="image_idx" value="<?php echo $img->idx ?>">
                                    <button type="submit" class="btn btn-outline-primary" title="<?php echo __('대표로 설정', 'manager') ?>">
                                        <i class="ph-star"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                                <a href="<?php echo $img->image_url ?>" target="_blank" class="btn btn-outline-secondary" title="<?php echo __('원본 보기', 'manager') ?>">
                                    <i class="ph-arrow-square-out"></i>
                                </a>
                                <form method="post" class="d-inline" onsubmit="return confirm('이미지를 삭제하시겠습니까?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="image_idx" value="<?php echo $img->idx ?>">
                                    <button type="submit" class="btn btn-outline-danger" title="<?php echo __('삭제', 'manager') ?>">
                                        <i class="ph-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="text-end mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="ph-floppy-disk me-1"></i><?php echo __('순서 저장', 'manager') ?>
                </button>
            </div>
        </form>
        <?php else: ?>
        <div class="text-center py-5 text-muted">
            <i class="ph-images fs-1 mb-2 d-block"></i>
            <?php echo __('등록된 이미지가 없습니다.', 'manager') ?>
        </div>
        <?php endif; ?>
    </div>
</div>
