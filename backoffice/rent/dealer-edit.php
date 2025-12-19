<?php
// 대리점 수정/추가
$breadcrumb = ["rent", "dealer-edit"];

$idx = $_GET['idx'] ?? null;
$isNew = empty($idx);

$_page_title = $isNew ? __('대리점 추가', 'manager') : __('대리점 수정', 'manager');

$dealer = null;
if (!$isNew) {
    $sql = "SELECT * FROM " . DB_PREFIX . "rent_dealer WHERE idx = :idx";
    $dealer = \ExpertNote\DB::getRow($sql, ['idx' => $idx]);
    if (!$dealer) {
        echo "<script>document.addEventListener('DOMContentLoaded', function() { ExpertNote.Util.showMessage('" . __('대리점을 찾을 수 없습니다.', 'manager') . "', '" . __('오류', 'manager') . "', [{ title: '" . __('확인', 'manager') . "', class: 'btn btn-secondary', dismiss: true }], function() { location.href='dealer-list'; }); });</script>";
        exit;
    }
}

// POST 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dealerCode = trim($_POST['dealer_code'] ?? '');
    $dealerName = trim($_POST['dealer_name'] ?? '');
    $status = $_POST['status'] ?? 'DRAFT';

    if (empty($dealerCode) || empty($dealerName)) {
        $error = __('대리점 코드와 대리점명은 필수입니다.', 'manager');
    } else {
        // 대리점 코드 중복 확인
        $checkSql = "SELECT idx FROM " . DB_PREFIX . "rent_dealer WHERE dealer_code = :dealer_code";
        $params = ['dealer_code' => $dealerCode];
        if (!$isNew) {
            $checkSql .= " AND idx != :idx";
            $params['idx'] = $idx;
        }
        $existing = \ExpertNote\DB::getRow($checkSql, $params);

        if ($existing) {
            $error = __('이미 사용 중인 대리점 코드입니다.', 'manager');
        } else {
            if ($isNew) {
                $sql = "INSERT INTO " . DB_PREFIX . "rent_dealer (dealer_code, dealer_name, status) VALUES (:dealer_code, :dealer_name, :status)";
                \ExpertNote\DB::query($sql, ['dealer_code' => $dealerCode, 'dealer_name' => $dealerName, 'status' => $status]);
                $newIdx = \ExpertNote\DB::getLastInsertId();
                $redirectUrl = "dealer-edit?idx={$newIdx}";
                $successMessage = __('대리점이 추가되었습니다.', 'manager');
                echo "<script>document.addEventListener('DOMContentLoaded', function() { ExpertNote.Util.showMessage('{$successMessage}', '" . __('성공', 'manager') . "', [{ title: '" . __('확인', 'manager') . "', class: 'btn btn-primary', dismiss: true }], function() { location.href='{$redirectUrl}'; }); });</script>";
            } else {
                $sql = "UPDATE " . DB_PREFIX . "rent_dealer SET dealer_code = :dealer_code, dealer_name = :dealer_name, status = :status WHERE idx = :idx";
                \ExpertNote\DB::query($sql, ['dealer_code' => $dealerCode, 'dealer_name' => $dealerName, 'status' => $status, 'idx' => $idx]);
                $success = __('대리점 정보가 수정되었습니다.', 'manager');
                // 수정된 정보 다시 조회
                $sql = "SELECT * FROM " . DB_PREFIX . "rent_dealer WHERE idx = :idx";
                $dealer = \ExpertNote\DB::getRow($sql, ['idx' => $idx]);
            }
        }
    }
}
?>

<div class="card">
    <div class="card-header d-flex align-items-center">
        <h5 class="mb-0">
            <i class="ph-buildings me-2"></i>
            <?php echo $_page_title ?>
        </h5>
        <div class="ms-auto">
            <a href="dealer-list" class="btn btn-outline-secondary">
                <i class="ph-list me-1"></i><?php echo __('목록', 'manager') ?>
            </a>
        </div>
    </div>
    <div class="card-body">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('대리점 코드', 'manager') ?> <span class="text-danger">*</span></label>
                        <input type="text" name="dealer_code" class="form-control" required
                            value="<?php echo htmlspecialchars($dealer->dealer_code ?? '') ?>"
                            placeholder="영문 대문자 (예: DEALER001)"
                            style="text-transform: uppercase;">
                        <small class="text-muted"><?php echo __('영문 대문자로 입력하세요', 'manager') ?></small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('대리점명', 'manager') ?> <span class="text-danger">*</span></label>
                        <input type="text" name="dealer_name" class="form-control" required
                            value="<?php echo htmlspecialchars($dealer->dealer_name ?? '') ?>"
                            placeholder="<?php echo __('대리점명 (한글)', 'manager') ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('상태', 'manager') ?></label>
                        <?php $currentStatus = $dealer->status ?? 'DRAFT'; ?>
                        <select name="status" class="form-select">
                            <option value="DRAFT" <?php echo $currentStatus === 'DRAFT' ? 'selected' : '' ?>><?php echo __('임시저장', 'manager') ?></option>
                            <option value="PUBLISHED" <?php echo $currentStatus === 'PUBLISHED' ? 'selected' : '' ?>><?php echo __('발행', 'manager') ?></option>
                        </select>
                        <small class="text-muted"><?php echo __('발행 상태만 사이트에 노출됩니다.', 'manager') ?></small>
                    </div>
                </div>
            </div>

            <?php if (!$isNew): ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('등록일', 'manager') ?></label>
                        <input type="text" class="form-control" readonly
                            value="<?php echo date('Y-m-d H:i:s', strtotime($dealer->created_at)) ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('수정일', 'manager') ?></label>
                        <input type="text" class="form-control" readonly
                            value="<?php echo date('Y-m-d H:i:s', strtotime($dealer->updated_at)) ?>">
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="text-end">
                <a href="dealer-list" class="btn btn-outline-secondary me-2">
                    <?php echo __('취소', 'manager') ?>
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="ph-floppy-disk me-1"></i>
                    <?php echo $isNew ? __('추가', 'manager') : __('저장', 'manager') ?>
                </button>
            </div>
        </form>
    </div>
</div>

<?php if (!$isNew): ?>
<!-- 해당 대리점의 차량 목록 -->
<?php
$carsSql = "SELECT COUNT(*) as cnt FROM " . DB_PREFIX . "rent WHERE dealer_idx = :dealer_idx";
$carsCount = \ExpertNote\DB::getRow($carsSql, ['dealer_idx' => $idx])->cnt ?? 0;
?>
<div class="card mt-3">
    <div class="card-header d-flex align-items-center">
        <h6 class="mb-0">
            <i class="ph-car me-2"></i>
            <?php echo __('등록된 차량', 'manager') ?>
            <span class="badge bg-primary ms-2"><?php echo number_format($carsCount) ?></span>
        </h6>
        <div class="ms-auto">
            <a href="car-list?dealer_idx=<?php echo $idx ?>" class="btn btn-sm btn-outline-primary">
                <i class="ph-list me-1"></i><?php echo __('차량 목록 보기', 'manager') ?>
            </a>
        </div>
    </div>
</div>

<!-- 보험 조건 -->
<?php
$insuranceSql = "SELECT * FROM " . DB_PREFIX . "rent_insurance WHERE dealer_idx = :dealer_idx";
$insurance = \ExpertNote\DB::getRow($insuranceSql, ['dealer_idx' => $idx]);
?>
<div class="card mt-3">
    <div class="card-header d-flex align-items-center">
        <h6 class="mb-0">
            <i class="ph-shield-check me-2"></i>
            <?php echo __('보험 조건', 'manager') ?>
        </h6>
        <div class="ms-auto">
            <a href="insurance-edit?dealer_idx=<?php echo $idx ?>" class="btn btn-sm btn-outline-primary">
                <i class="ph-pencil-simple me-1"></i>
                <?php echo $insurance ? __('수정', 'manager') : __('설정', 'manager') ?>
            </a>
        </div>
    </div>
    <?php if ($insurance): ?>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <h6 class="text-muted"><?php echo __('책임한도', 'manager') ?></h6>
                <p class="mb-1"><?php echo __('대인', 'manager') ?>: <?php echo $insurance->liability_personal ?: '-' ?></p>
                <p class="mb-1"><?php echo __('대물', 'manager') ?>: <?php echo $insurance->liability_property ?: '-' ?></p>
                <p class="mb-0"><?php echo __('자손', 'manager') ?>: <?php echo $insurance->liability_self_injury ?: '-' ?></p>
            </div>
            <div class="col-md-4">
                <h6 class="text-muted"><?php echo __('면책금', 'manager') ?></h6>
                <p class="mb-1"><?php echo __('대인', 'manager') ?>: <?php echo $insurance->deductible_personal ?: '-' ?></p>
                <p class="mb-1"><?php echo __('대물', 'manager') ?>: <?php echo $insurance->deductible_property ?: '-' ?></p>
                <p class="mb-1"><?php echo __('자손', 'manager') ?>: <?php echo $insurance->deductible_self_injury ?: '-' ?></p>
                <p class="mb-0"><?php echo __('자차', 'manager') ?>: <?php echo $insurance->deductible_own_car ?: '-' ?></p>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>
