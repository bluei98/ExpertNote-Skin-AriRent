<?php
// 모델 수정/추가
$breadcrumb = ["rent", "model-edit"];

$idx = $_GET['idx'] ?? null;
$isNew = empty($idx);

$_page_title = $isNew ? __('모델 추가', 'manager') : __('모델 수정', 'manager');

$model = null;
if (!$isNew) {
    $model = \AriRent\Rent::getModel($idx);
    if (!$model) {
        echo "<script>document.addEventListener('DOMContentLoaded', function() { ExpertNote.Util.showMessage('" . __('모델을 찾을 수 없습니다.', 'manager') . "', '" . __('오류', 'manager') . "', [{ title: '" . __('확인', 'manager') . "', class: 'btn btn-secondary', dismiss: true }], function() { location.href='model-list'; }); });</script>";
        exit;
    }
}

// 브랜드 목록
$brands = \AriRent\Rent::getBrands(['is_active' => 1], ['sort_order' => 'ASC', 'brand_name' => 'ASC']);

// 기본 브랜드 선택 (URL 파라미터)
$defaultBrandIdx = $_GET['brand_idx'] ?? ($model->brand_idx ?? '');

// 세그먼트 목록
$segments = [
    'ECONOMY' => '이코노미',
    'COMPACT' => '컴팩트',
    'MID_SIZE' => '중형',
    'FULL_SIZE' => '대형',
    'LUXURY' => '럭셔리',
    'SUV' => 'SUV',
    'VAN' => '밴/MPV',
    'SPORTS' => '스포츠',
    'ELECTRIC' => '전기차'
];

// POST 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $brandIdx = intval($_POST['brand_idx'] ?? 0);
    $modelName = trim($_POST['model_name'] ?? '');
    $modelNameEn = trim($_POST['model_name_en'] ?? '');
    $segment = $_POST['segment'] ?? '';
    $sortOrder = intval($_POST['sort_order'] ?? 0);
    $isActive = isset($_POST['is_active']) ? 1 : 0;

    if (empty($brandIdx) || empty($modelName)) {
        $error = __('브랜드와 모델명은 필수입니다.', 'manager');
    } else {
        // 브랜드 존재 여부 확인
        $brand = \AriRent\Rent::getBrand($brandIdx);
        if (!$brand) {
            $error = __('존재하지 않는 브랜드입니다.', 'manager');
        } else {
            $data = [
                'brand_idx' => $brandIdx,
                'model_name' => $modelName,
                'model_name_en' => $modelNameEn,
                'segment' => $segment,
                'sort_order' => $sortOrder,
                'is_active' => $isActive
            ];

            if (!$isNew) {
                $data['idx'] = $idx;
            }

            try {
                $result = \AriRent\Rent::setModel($data);
                if ($isNew) {
                    $newIdx = $result;
                    $redirectUrl = "model-edit?idx={$newIdx}";
                    $successMessage = __('모델이 추가되었습니다.', 'manager');
                    echo "<script>document.addEventListener('DOMContentLoaded', function() { ExpertNote.Util.showMessage('{$successMessage}', '" . __('성공', 'manager') . "', [{ title: '" . __('확인', 'manager') . "', class: 'btn btn-primary', dismiss: true }], function() { location.href='{$redirectUrl}'; }); });</script>";
                } else {
                    $success = __('모델 정보가 수정되었습니다.', 'manager');
                    // 수정된 정보 다시 조회
                    $model = \AriRent\Rent::getModel($idx);
                }
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }
    }
}
?>

<div class="card">
    <div class="card-header d-flex align-items-center">
        <h5 class="mb-0">
            <i class="ph-car me-2"></i>
            <?php echo $_page_title ?>
        </h5>
        <div class="ms-auto">
            <a href="model-list<?php echo $defaultBrandIdx ? '?brand_idx=' . $defaultBrandIdx : '' ?>" class="btn btn-outline-secondary">
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
                        <label class="form-label"><?php echo __('브랜드', 'manager') ?> <span class="text-danger">*</span></label>
                        <select name="brand_idx" class="form-select form-select-sm" required>
                            <option value=""><?php echo __('브랜드 선택', 'manager') ?></option>
                            <?php foreach ($brands as $b): ?>
                            <option value="<?php echo $b->idx ?>" <?php echo $defaultBrandIdx == $b->idx ? 'selected' : '' ?>>
                                <?php echo htmlspecialchars($b->brand_name) ?> (<?php echo $b->country_code ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('세그먼트', 'manager') ?></label>
                        <?php $currentSegment = $model->segment ?? ''; ?>
                        <select name="segment" class="form-select form-select-sm">
                            <option value=""><?php echo __('세그먼트 선택', 'manager') ?></option>
                            <?php foreach ($segments as $code => $name): ?>
                            <option value="<?php echo $code ?>" <?php echo $currentSegment === $code ? 'selected' : '' ?>>
                                <?php echo $name ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('모델명', 'manager') ?> <span class="text-danger">*</span></label>
                        <input type="text" name="model_name" class="form-control form-control-sm rounded-0" required
                            value="<?php echo htmlspecialchars($model->model_name ?? '') ?>"
                            placeholder="<?php echo __('모델명 (한글)', 'manager') ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('모델명(영문)', 'manager') ?></label>
                        <input type="text" name="model_name_en" class="form-control form-control-sm rounded-0"
                            value="<?php echo htmlspecialchars($model->model_name_en ?? '') ?>"
                            placeholder="<?php echo __('모델명 (영문)', 'manager') ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('정렬순서', 'manager') ?></label>
                        <input type="number" name="sort_order" class="form-control form-control-sm rounded-0"
                            value="<?php echo intval($model->sort_order ?? 0) ?>"
                            placeholder="0">
                        <small class="text-muted"><?php echo __('낮은 숫자가 먼저 표시됩니다', 'manager') ?></small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('상태', 'manager') ?></label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive"
                                <?php echo ($model->is_active ?? 1) == 1 ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isActive"><?php echo __('활성화', 'manager') ?></label>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!$isNew): ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('등록일', 'manager') ?></label>
                        <input type="text" class="form-control form-control-sm rounded-0" readonly
                            value="<?php echo date('Y-m-d H:i:s', strtotime($model->created_at)) ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('수정일', 'manager') ?></label>
                        <input type="text" class="form-control form-control-sm rounded-0" readonly
                            value="<?php echo date('Y-m-d H:i:s', strtotime($model->updated_at)) ?>">
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="text-end">
                <a href="model-list<?php echo $defaultBrandIdx ? '?brand_idx=' . $defaultBrandIdx : '' ?>" class="btn btn-outline-secondary me-2">
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

<?php if (!$isNew && $model): ?>
<!-- 브랜드 정보 표시 -->
<div class="card mt-3">
    <div class="card-header d-flex align-items-center">
        <h6 class="mb-0">
            <i class="ph-trademark-registered me-2"></i>
            <?php echo __('브랜드 정보', 'manager') ?>
        </h6>
        <div class="ms-auto">
            <a href="brand-edit?idx=<?php echo $model->brand_idx ?>" class="btn btn-sm btn-outline-primary">
                <i class="ph-pencil-simple me-1"></i><?php echo __('브랜드 수정', 'manager') ?>
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <strong><?php echo __('브랜드명', 'manager') ?>:</strong>
                <?php echo htmlspecialchars($model->brand_name) ?>
            </div>
            <div class="col-md-4">
                <strong><?php echo __('브랜드명(영문)', 'manager') ?>:</strong>
                <?php echo htmlspecialchars($model->brand_name_en ?: '-') ?>
            </div>
            <div class="col-md-4">
                <strong><?php echo __('국가', 'manager') ?>:</strong>
                <span class="badge bg-secondary"><?php echo $model->country_code ?></span>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
