<?php
// 보험 조건 수정
$breadcrumb = ["rent", "insurance-edit"];
$_page_title = __('보험 조건 설정', 'manager');

$dealerIdx = $_GET['dealer_idx'] ?? null;

if (empty($dealerIdx)) {
    echo "<script>alert('대리점을 선택해주세요.'); location.href='dealer-list';</script>";
    exit;
}

// 대리점 정보 조회
$dealerSql = "SELECT * FROM " . DB_PREFIX . "rent_dealer WHERE idx = :idx";
$dealer = \ExpertNote\DB::getRow($dealerSql, ['idx' => $dealerIdx]);
if (!$dealer) {
    echo "<script>alert('대리점을 찾을 수 없습니다.'); location.href='dealer-list';</script>";
    exit;
}

// 보험 조건 조회
$insuranceSql = "SELECT * FROM " . DB_PREFIX . "rent_insurance WHERE dealer_idx = :dealer_idx";
$insurance = \ExpertNote\DB::getRow($insuranceSql, ['dealer_idx' => $dealerIdx]);

// POST 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'dealer_idx' => $dealerIdx,
        'liability_personal' => trim($_POST['liability_personal'] ?? ''),
        'liability_property' => trim($_POST['liability_property'] ?? ''),
        'liability_self_injury' => trim($_POST['liability_self_injury'] ?? ''),
        'deductible_personal' => trim($_POST['deductible_personal'] ?? ''),
        'deductible_property' => trim($_POST['deductible_property'] ?? ''),
        'deductible_self_injury' => trim($_POST['deductible_self_injury'] ?? ''),
        'deductible_own_car' => trim($_POST['deductible_own_car'] ?? ''),
        'insurance_etc' => trim($_POST['insurance_etc'] ?? ''),
    ];

    if ($insurance) {
        // 업데이트
        $sets = [];
        foreach (array_keys($data) as $key) {
            if ($key !== 'dealer_idx') {
                $sets[] = "{$key} = :{$key}";
            }
        }
        $sql = "UPDATE " . DB_PREFIX . "rent_insurance SET " . implode(', ', $sets) . " WHERE dealer_idx = :dealer_idx";
        \ExpertNote\DB::query($sql, $data);
    } else {
        // 신규 등록
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO " . DB_PREFIX . "rent_insurance ({$columns}) VALUES ({$placeholders})";
        \ExpertNote\DB::query($sql, $data);
    }

    $success = '보험 조건이 저장되었습니다.';
    // 다시 조회
    $insurance = \ExpertNote\DB::getRow($insuranceSql, ['dealer_idx' => $dealerIdx]);
}
?>

<div class="card">
    <div class="card-header d-flex align-items-center">
        <h5 class="mb-0">
            <i class="ph-shield-check me-2"></i>
            <?php echo $_page_title ?>
            <small class="text-muted ms-2">(<?php echo htmlspecialchars($dealer->dealer_name) ?>)</small>
        </h5>
        <div class="ms-auto">
            <a href="dealer-edit?idx=<?php echo $dealerIdx ?>" class="btn btn-outline-secondary">
                <i class="ph-arrow-left me-1"></i><?php echo __('대리점으로', 'manager') ?>
            </a>
        </div>
    </div>
    <div class="card-body">
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="mb-3"><?php echo __('책임한도', 'manager') ?></h6>
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('대인', 'manager') ?></label>
                        <input type="text" name="liability_personal" class="form-control rounded-0"
                            value="<?php echo htmlspecialchars($insurance->liability_personal ?? '') ?>"
                            placeholder="무한">
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('대물', 'manager') ?></label>
                        <input type="text" name="liability_property" class="form-control rounded-0"
                            value="<?php echo htmlspecialchars($insurance->liability_property ?? '') ?>"
                            placeholder="1억원">
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('자손', 'manager') ?></label>
                        <input type="text" name="liability_self_injury" class="form-control rounded-0"
                            value="<?php echo htmlspecialchars($insurance->liability_self_injury ?? '') ?>"
                            placeholder="1억원">
                    </div>
                </div>
                <div class="col-md-6">
                    <h6 class="mb-3"><?php echo __('면책금', 'manager') ?></h6>
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('대인', 'manager') ?></label>
                        <input type="text" name="deductible_personal" class="form-control rounded-0"
                            value="<?php echo htmlspecialchars($insurance->deductible_personal ?? '') ?>"
                            placeholder="없음">
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('대물', 'manager') ?></label>
                        <input type="text" name="deductible_property" class="form-control rounded-0"
                            value="<?php echo htmlspecialchars($insurance->deductible_property ?? '') ?>"
                            placeholder="20만원">
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('자손', 'manager') ?></label>
                        <input type="text" name="deductible_self_injury" class="form-control rounded-0"
                            value="<?php echo htmlspecialchars($insurance->deductible_self_injury ?? '') ?>"
                            placeholder="없음">
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('자차', 'manager') ?></label>
                        <input type="text" name="deductible_own_car" class="form-control rounded-0"
                            value="<?php echo htmlspecialchars($insurance->deductible_own_car ?? '') ?>"
                            placeholder="50만원">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <h6 class="mb-3"><?php echo __('보험 기타 설명', 'manager') ?></h6>
                </div>
                <div class="mb-3">
                    <textarea id="editor" name="insurance_etc" class="ckeditor bordered"
                        data-uploadUrl="/backoffice/modules/upload-ckeditor?service_folder=rent"
                        data-filebrowserImageUploadUrl="/backoffice/modules/upload-ckeditor?service_folder=rent"
                        data-bodyClass="mx-5 my-3"
                        data-height="60"
                        data-contentsCss="/assets/css/common.min.css"><?php echo $insurance->insurance_etc?></textarea>
                </div>
            </div>

            <div class="text-end">
                <a href="dealer-edit?idx=<?php echo $dealerIdx ?>" class="btn btn-sm btn-outline-secondary me-2 rounded-0">
                    <?php echo __('취소', 'manager') ?>
                </a>
                <button type="submit" class="btn btn-sm btn-primary rounded-0">
                    <i class="ph-floppy-disk me-1"></i><?php echo __('저장', 'manager') ?>
                </button>
            </div>
        </form>
    </div>
</div>
