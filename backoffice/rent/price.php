<?php
// 차량 가격 옵션 관리
$breadcrumb = ["rent", "price"];
$_page_title = __('가격 옵션 관리', 'manager');

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

// 가격 옵션 목록 조회
$pricesSql = "SELECT * FROM " . DB_PREFIX . "rent_price WHERE rent_idx = :rent_idx ORDER BY deposit_amount, rental_period_months";
$prices = \ExpertNote\DB::getRows($pricesSql, ['rent_idx' => $rentIdx]);

// POST 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $data = [
            'rent_idx' => $rentIdx,
            'deposit_amount' => intval($_POST['deposit_amount'] ?? 0) ?: null,
            'rental_period_months' => intval($_POST['rental_period_months'] ?? 0) ?: null,
            'monthly_rent_amount' => intval($_POST['monthly_rent_amount'] ?? 0) ?: null,
            'yearly_mileage_limit' => intval($_POST['yearly_mileage_limit'] ?? 0) ?: null
        ];

        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO " . DB_PREFIX . "rent_price ({$columns}) VALUES ({$placeholders})";
        \ExpertNote\DB::query($sql, $data);
        $success = '가격 옵션이 추가되었습니다.';
        // 다시 조회
        $prices = \ExpertNote\DB::getRows($pricesSql, ['rent_idx' => $rentIdx]);
    } elseif ($_POST['action'] === 'delete') {
        $priceIdx = intval($_POST['price_idx'] ?? 0);
        if ($priceIdx > 0) {
            $sql = "DELETE FROM " . DB_PREFIX . "rent_price WHERE idx = :idx AND rent_idx = :rent_idx";
            \ExpertNote\DB::query($sql, ['idx' => $priceIdx, 'rent_idx' => $rentIdx]);
            $success = '가격 옵션이 삭제되었습니다.';
            // 다시 조회
            $prices = \ExpertNote\DB::getRows($pricesSql, ['rent_idx' => $rentIdx]);
        }
    } elseif ($_POST['action'] === 'update') {
        $priceIdx = intval($_POST['price_idx'] ?? 0);
        if ($priceIdx > 0) {
            $data = [
                'idx' => $priceIdx,
                'rent_idx' => $rentIdx,
                'deposit_amount' => intval($_POST['deposit_amount'] ?? 0) ?: null,
                'rental_period_months' => intval($_POST['rental_period_months'] ?? 0) ?: null,
                'monthly_rent_amount' => intval($_POST['monthly_rent_amount'] ?? 0) ?: null,
                'yearly_mileage_limit' => intval($_POST['yearly_mileage_limit'] ?? 0) ?: null
            ];

            $sql = "UPDATE " . DB_PREFIX . "rent_price SET
                deposit_amount = :deposit_amount,
                rental_period_months = :rental_period_months,
                monthly_rent_amount = :monthly_rent_amount,
                yearly_mileage_limit = :yearly_mileage_limit
                WHERE idx = :idx AND rent_idx = :rent_idx";
            \ExpertNote\DB::query($sql, $data);
            $success = '가격 옵션이 수정되었습니다.';
            // 다시 조회
            $prices = \ExpertNote\DB::getRows($pricesSql, ['rent_idx' => $rentIdx]);
        }
    }
}
?>

<div class="card">
    <div class="card-header d-flex align-items-center">
        <h5 class="mb-0">
            <i class="ph-currency-krw me-2"></i>
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

        <!-- 가격 옵션 추가 폼 -->
        <form method="post" class="mb-4">
            <input type="hidden" name="action" value="add">
            <div class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label"><?php echo __('보증금 (만원)', 'manager') ?></label>
                    <input type="number" name="deposit_amount" class="form-control" placeholder="0">
                </div>
                <div class="col-md-2">
                    <label class="form-label"><?php echo __('렌트기간 (개월)', 'manager') ?></label>
                    <input type="number" name="rental_period_months" class="form-control" placeholder="36">
                </div>
                <div class="col-md-3">
                    <label class="form-label"><?php echo __('월렌트비 (원)', 'manager') ?></label>
                    <input type="number" name="monthly_rent_amount" class="form-control" placeholder="500000">
                </div>
                <div class="col-md-2">
                    <label class="form-label"><?php echo __('연간주행 (만km)', 'manager') ?></label>
                    <input type="number" name="yearly_mileage_limit" class="form-control" placeholder="2">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="ph-plus me-1"></i><?php echo __('추가', 'manager') ?>
                    </button>
                </div>
            </div>
        </form>

        <!-- 가격 옵션 목록 -->
        <?php if (count($prices) > 0): ?>
        <table class="table table-hover table-bordered">
            <thead class="bg-dark text-light">
                <tr>
                    <th class="text-center"><?php echo __('보증금', 'manager') ?></th>
                    <th class="text-center"><?php echo __('렌트기간', 'manager') ?></th>
                    <th class="text-center"><?php echo __('월렌트비', 'manager') ?></th>
                    <th class="text-center"><?php echo __('연간주행', 'manager') ?></th>
                    <th class="text-center" width="100"><?php echo __('관리', 'manager') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prices as $price): ?>
                <tr>
                    <td class="text-end">
                        <?php echo $price->deposit_amount ? number_format($price->deposit_amount) . __('만원', 'manager') : '-' ?>
                    </td>
                    <td class="text-center">
                        <?php echo $price->rental_period_months ? $price->rental_period_months . __('개월', 'manager') : '-' ?>
                    </td>
                    <td class="text-end">
                        <?php echo $price->monthly_rent_amount ? number_format($price->monthly_rent_amount) . __('원', 'manager') : '-' ?>
                    </td>
                    <td class="text-center">
                        <?php echo $price->yearly_mileage_limit ? $price->yearly_mileage_limit . __('만km', 'manager') : '-' ?>
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-primary" onclick="editPrice(<?php echo htmlspecialchars(json_encode($price)) ?>)">
                                <i class="ph-pencil-simple"></i>
                            </button>
                            <form method="post" class="d-inline" onsubmit="return confirm('삭제하시겠습니까?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="price_idx" value="<?php echo $price->idx ?>">
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="ph-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="text-center py-5 text-muted">
            <i class="ph-currency-krw fs-1 mb-2 d-block"></i>
            <?php echo __('등록된 가격 옵션이 없습니다.', 'manager') ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- 수정 모달 -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="price_idx" id="edit_price_idx">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo __('가격 옵션 수정', 'manager') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('보증금 (만원)', 'manager') ?></label>
                        <input type="number" name="deposit_amount" id="edit_deposit_amount" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('렌트기간 (개월)', 'manager') ?></label>
                        <input type="number" name="rental_period_months" id="edit_rental_period_months" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('월렌트비 (원)', 'manager') ?></label>
                        <input type="number" name="monthly_rent_amount" id="edit_monthly_rent_amount" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('연간주행 (만km)', 'manager') ?></label>
                        <input type="number" name="yearly_mileage_limit" id="edit_yearly_mileage_limit" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo __('취소', 'manager') ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo __('저장', 'manager') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editPrice(price) {
    document.getElementById('edit_price_idx').value = price.idx;
    document.getElementById('edit_deposit_amount').value = price.deposit_amount || '';
    document.getElementById('edit_rental_period_months').value = price.rental_period_months || '';
    document.getElementById('edit_monthly_rent_amount').value = price.monthly_rent_amount || '';
    document.getElementById('edit_yearly_mileage_limit').value = price.yearly_mileage_limit || '';

    new bootstrap.Modal(document.getElementById('editModal')).show();
}
</script>
