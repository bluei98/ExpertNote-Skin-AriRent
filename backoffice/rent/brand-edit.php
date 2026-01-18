<?php
// 브랜드 수정/추가
$breadcrumb = ["rent", "brand-edit"];

$idx = $_GET['idx'] ?? null;
$isNew = empty($idx);

$_page_title = $isNew ? __('브랜드 추가', 'manager') : __('브랜드 수정', 'manager');

$brand = null;
if (!$isNew) {
    $brand = \AriRent\Rent::getBrand($idx);
    if (!$brand) {
        echo "<script>document.addEventListener('DOMContentLoaded', function() { ExpertNote.Util.showMessage('" . __('브랜드를 찾을 수 없습니다.', 'manager') . "', '" . __('오류', 'manager') . "', [{ title: '" . __('확인', 'manager') . "', class: 'btn btn-secondary', dismiss: true }], function() { location.href='brand-list'; }); });</script>";
        exit;
    }
}

// 국가 목록
$countries = [
    'KR' => '한국',
    'DE' => '독일',
    'JP' => '일본',
    'US' => '미국',
    'GB' => '영국',
    'IT' => '이탈리아',
    'SE' => '스웨덴',
    'FR' => '프랑스'
];

// POST 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $brandName = trim($_POST['brand_name'] ?? '');
    $brandNameEn = trim($_POST['brand_name_en'] ?? '');
    $countryCode = $_POST['country_code'] ?? 'KR';
    $logoUrl = trim($_POST['logo_url'] ?? '');
    $sortOrder = intval($_POST['sort_order'] ?? 0);
    $isActive = isset($_POST['is_active']) ? 1 : 0;

    if (empty($brandName)) {
        $error = __('브랜드명은 필수입니다.', 'manager');
    } else {
        $data = [
            'brand_name' => $brandName,
            'brand_name_en' => $brandNameEn,
            'country_code' => $countryCode,
            'logo_url' => $logoUrl,
            'sort_order' => $sortOrder,
            'is_active' => $isActive
        ];

        if (!$isNew) {
            $data['idx'] = $idx;
        }

        try {
            $result = \AriRent\Rent::setBrand($data);
            if ($isNew) {
                $newIdx = $result;
                $redirectUrl = "brand-edit?idx={$newIdx}";
                $successMessage = __('브랜드가 추가되었습니다.', 'manager');
                echo "<script>document.addEventListener('DOMContentLoaded', function() { ExpertNote.Util.showMessage('{$successMessage}', '" . __('성공', 'manager') . "', [{ title: '" . __('확인', 'manager') . "', class: 'btn btn-primary', dismiss: true }], function() { location.href='{$redirectUrl}'; }); });</script>";
            } else {
                $success = __('브랜드 정보가 수정되었습니다.', 'manager');
                // 수정된 정보 다시 조회
                $brand = \AriRent\Rent::getBrand($idx);
            }
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }
    }
}
?>

<div class="card">
    <div class="card-header d-flex align-items-center">
        <h5 class="mb-0">
            <i class="ph-trademark-registered me-2"></i>
            <?php echo $_page_title ?>
        </h5>
        <div class="ms-auto">
            <a href="brand-list" class="btn btn-outline-secondary">
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
                        <label class="form-label"><?php echo __('브랜드명', 'manager') ?> <span class="text-danger">*</span></label>
                        <input type="text" name="brand_name" class="form-control form-control-sm rounded-0" required
                            value="<?php echo htmlspecialchars($brand->brand_name ?? '') ?>"
                            placeholder="<?php echo __('브랜드명 (한글)', 'manager') ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('브랜드명(영문)', 'manager') ?></label>
                        <input type="text" name="brand_name_en" class="form-control form-control-sm rounded-0"
                            value="<?php echo htmlspecialchars($brand->brand_name_en ?? '') ?>"
                            placeholder="<?php echo __('브랜드명 (영문)', 'manager') ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('국가', 'manager') ?></label>
                        <?php $currentCountry = $brand->country_code ?? 'KR'; ?>
                        <select name="country_code" class="form-select form-select-sm">
                            <?php foreach ($countries as $code => $name): ?>
                            <option value="<?php echo $code ?>" <?php echo $currentCountry === $code ? 'selected' : '' ?>>
                                <?php echo $name ?> (<?php echo $code ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('정렬순서', 'manager') ?></label>
                        <input type="number" name="sort_order" class="form-control form-control-sm rounded-0"
                            value="<?php echo intval($brand->sort_order ?? 0) ?>"
                            placeholder="0">
                        <small class="text-muted"><?php echo __('낮은 숫자가 먼저 표시됩니다', 'manager') ?></small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('상태', 'manager') ?></label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive"
                                <?php echo ($brand->is_active ?? 1) == 1 ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isActive"><?php echo __('활성화', 'manager') ?></label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('로고 URL', 'manager') ?></label>
                        <input type="text" name="logo_url" class="form-control form-control-sm rounded-0"
                            value="<?php echo htmlspecialchars($brand->logo_url ?? '') ?>"
                            placeholder="https://example.com/logo.png">
                        <small class="text-muted"><?php echo __('브랜드 로고 이미지 URL을 입력하세요', 'manager') ?></small>
                    </div>
                </div>
            </div>

            <?php if (!$isNew): ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('등록일', 'manager') ?></label>
                        <input type="text" class="form-control form-control-sm rounded-0" readonly
                            value="<?php echo date('Y-m-d H:i:s', strtotime($brand->created_at)) ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('수정일', 'manager') ?></label>
                        <input type="text" class="form-control form-control-sm rounded-0" readonly
                            value="<?php echo date('Y-m-d H:i:s', strtotime($brand->updated_at)) ?>">
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="text-end">
                <a href="brand-list" class="btn btn-outline-secondary me-2">
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
<!-- 해당 브랜드의 모델 목록 -->
<?php
$modelCount = \AriRent\Rent::getModelCount(['brand_idx' => $idx]);
?>
<div class="card mt-3">
    <div class="card-header d-flex align-items-center">
        <h6 class="mb-0">
            <i class="ph-car me-2"></i>
            <?php echo __('등록된 모델', 'manager') ?>
            <span class="badge bg-primary ms-2"><?php echo number_format($modelCount) ?></span>
        </h6>
        <div class="ms-auto">
            <a href="model-edit?brand_idx=<?php echo $idx ?>" class="btn btn-sm btn-primary me-1">
                <i class="ph-plus me-1"></i><?php echo __('모델 추가', 'manager') ?>
            </a>
            <a href="model-list?brand_idx=<?php echo $idx ?>" class="btn btn-sm btn-outline-primary">
                <i class="ph-list me-1"></i><?php echo __('모델 목록', 'manager') ?>
            </a>
        </div>
    </div>
</div>
<?php endif; ?>
