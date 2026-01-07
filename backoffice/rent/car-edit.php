<?php
// 차량 수정/추가 (가격, 이미지 통합 관리)
$breadcrumb = ["rent", "car-edit"];

$idx = $_GET['idx'] ?? null;
$isNew = empty($idx);

$_page_title = $isNew ? __('차량 추가', 'manager') : __('차량 수정', 'manager');

// 대리점 목록 조회
$dealersSql = "SELECT idx, dealer_code, dealer_name FROM " . DB_PREFIX . "rent_dealer ORDER BY dealer_name";
$dealers = \ExpertNote\DB::getRows($dealersSql);

$car = null;
$prices = [];
$images = [];

if (!$isNew) {
    $sql = "SELECT r.*, d.dealer_name FROM " . DB_PREFIX . "rent r
            LEFT JOIN " . DB_PREFIX . "rent_dealer d ON r.dealer_idx = d.idx
            WHERE r.idx = :idx";
    $car = \ExpertNote\DB::getRow($sql, ['idx' => $idx]);
    if (!$car) {
        echo "<script>alert('차량을 찾을 수 없습니다.'); location.href='car-list';</script>";
        exit;
    }

    // 가격 정보 조회
    $pricesSql = "SELECT * FROM " . DB_PREFIX . "rent_price WHERE rent_idx = :rent_idx ORDER BY deposit_amount, rental_period_months";
    $prices = \ExpertNote\DB::getRows($pricesSql, ['rent_idx' => $idx]);

    // 이미지 정보 조회
    $images = \AriRent\Rent::getImages($idx);
}
?>
<form id="carForm">
    <input type="hidden" name="idx" value="<?php echo $idx ?>">
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <h5 class="mb-0">
                <i class="ph-car me-2"></i>
                <?php echo $_page_title ?>
            </h5>
            <div class="ms-auto">
                <a href="car-list" class="btn btn-outline-secondary">
                    <i class="ph-list me-1"></i><?php echo __('목록', 'manager') ?>
                </a>
            </div>
        </div>
        <div class="card-header">
            <h5 class="mb-0 font-090"><i class="ph-car me-1"></i><?php echo __('기본 정보', 'manager') ?></h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('대리점', 'manager') ?> <span class="text-danger">*</span></label>
                        <select name="dealer_idx" class="form-select rounded-0" required>
                            <option value=""><?php echo __('선택하세요', 'manager') ?></option>
                            <?php foreach ($dealers as $d): ?>
                                <option value="<?php echo $d->idx ?>" <?php echo ($car->dealer_idx ?? '') == $d->idx ? 'selected' : '' ?>>
                                    <?php echo htmlspecialchars($d->dealer_name) ?> (<?php echo $d->dealer_code ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('차량유형', 'manager') ?> <span class="text-danger">*</span></label>
                        <select name="car_type" class="form-select rounded-0" required>
                            <option value="NEW" <?php echo ($car->car_type ?? 'NEW') === 'NEW' ? 'selected' : '' ?>><?php echo __('신차', 'manager') ?></option>
                            <option value="USED" <?php echo ($car->car_type ?? '') === 'USED' ? 'selected' : '' ?>><?php echo __('중고차', 'manager') ?></option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('상태', 'manager') ?></label>
                        <select name="status" class="form-select rounded-0">
                            <option value="active" <?php echo ($car->status ?? 'active') === 'active' ? 'selected' : '' ?>><?php echo __('판매중', 'manager') ?></option>
                            <option value="rented" <?php echo ($car->status ?? '') === 'rented' ? 'selected' : '' ?>><?php echo __('렌트중', 'manager') ?></option>
                            <option value="maintenance" <?php echo ($car->status ?? '') === 'maintenance' ? 'selected' : '' ?>><?php echo __('정비중', 'manager') ?></option>
                            <option value="deleted" <?php echo ($car->status ?? '') === 'deleted' ? 'selected' : '' ?>><?php echo __('삭제됨', 'manager') ?></option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('차량번호', 'manager') ?> <span class="text-danger">*</span></label>
                        <input type="text" name="car_number" class="form-control rounded-0" required
                            value="<?php echo htmlspecialchars($car->car_number ?? '') ?>"
                            placeholder="12가 3456">
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('차량명', 'manager') ?> <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control rounded-0" required
                            value="<?php echo htmlspecialchars($car->title ?? '') ?>"
                            placeholder="표시용 차량명">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('브랜드', 'manager') ?></label>
                        <input type="text" name="brand" class="form-control rounded-0"
                            value="<?php echo htmlspecialchars($car->brand ?? '') ?>"
                            placeholder="현대, 기아, BMW 등">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('모델', 'manager') ?></label>
                        <input type="text" name="model" class="form-control rounded-0"
                            value="<?php echo htmlspecialchars($car->model ?? '') ?>"
                            placeholder="모델명">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('연료타입', 'manager') ?></label>
                        <select name="fuel_type" class="form-select rounded-0">
                            <option value=""><?php echo __('선택하세요', 'manager') ?></option>
                            <option value="휘발유" <?php echo ($car->fuel_type ?? '') === '휘발유' ? 'selected' : '' ?>><?php echo __('휘발유', 'manager') ?></option>
                            <option value="경유" <?php echo ($car->fuel_type ?? '') === '경유' ? 'selected' : '' ?>><?php echo __('경유', 'manager') ?></option>
                            <option value="LPG" <?php echo ($car->fuel_type ?? '') === 'LPG' ? 'selected' : '' ?>>LPG</option>
                            <option value="전기" <?php echo ($car->fuel_type ?? '') === '전기' ? 'selected' : '' ?>><?php echo __('전기', 'manager') ?></option>
                            <option value="하이브리드" <?php echo ($car->fuel_type ?? '') === '하이브리드' ? 'selected' : '' ?>><?php echo __('하이브리드', 'manager') ?></option>
                            <option value="플러그인하이브리드" <?php echo ($car->fuel_type ?? '') === '플러그인하이브리드' ? 'selected' : '' ?>><?php echo __('플러그인하이브리드', 'manager') ?></option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('연식 (년)', 'manager') ?></label>
                        <input type="text" name="model_year" class="form-control rounded-0"
                            value="<?php echo htmlspecialchars($car->model_year ?? '') ?>"
                            placeholder="2024">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('연식 (월)', 'manager') ?></label>
                        <input type="text" name="model_month" class="form-control rounded-0"
                            value="<?php echo htmlspecialchars($car->model_month ?? '') ?>"
                            placeholder="01">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('주행거리 (km)', 'manager') ?></label>
                        <input type="number" name="mileage_km" class="form-control rounded-0"
                            value="<?php echo $car->mileage_km ?? '' ?>"
                            placeholder="0">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('월렌트료 (원)', 'manager') ?></label>
                        <input type="number" name="monthly_price" id="monthlyPrice" class="form-control rounded-0 bg-light"
                            value="<?php echo $car->monthly_price ?? '' ?>"
                            placeholder="가격 정보에서 자동 계산" readonly>
                        <small class="text-muted"><?php echo __('가격 정보의 최저가가 자동 입력됩니다', 'manager') ?></small>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label"><?php echo __('대표 이미지', 'manager') ?></label>
                <input type="hidden" name="featured_image" id="mainImageUrl" value="<?php echo htmlspecialchars($car->featured_image ?? '') ?>">
                <div id="mainImagePreview" class="border rounded p-2 d-inline-block" style="min-width: 150px; min-height: 100px;">
                    <?php if (!empty($car->featured_image)): ?>
                        <img src="<?php echo $car->featured_image ?>" style="max-height: 100px; max-width: 150px;" class="rounded">
                    <?php else: ?>
                        <div class="text-muted text-center py-4" style="font-size: 12px;"><?php echo __('아래 차량 이미지에서 선택하세요', 'manager') ?></div>
                    <?php endif; ?>
                </div>
                <div class="text-muted small mt-2">
                    <i class="ph-info me-1"></i><?php echo __('차량 이미지 섹션에서 대표 이미지로 설정할 이미지를 선택하세요.', 'manager') ?>
                </div>
            </div>

            <?php if (!$isNew): ?>
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('조회수', 'manager') ?></label>
                        <input type="text" class="form-control rounded-0" readonly value="<?php echo number_format($car->view_count) ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('찜 횟수', 'manager') ?></label>
                        <input type="text" class="form-control rounded-0" readonly value="<?php echo number_format($car->wish_count) ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('등록일', 'manager') ?></label>
                        <input type="text" class="form-control rounded-0" readonly value="<?php echo date('Y-m-d H:i:s', strtotime($car->created_at)) ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('수정일', 'manager') ?></label>
                        <input type="text" class="form-control rounded-0" readonly value="<?php echo date('Y-m-d H:i:s', strtotime($car->updated_at)) ?>">
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <div class="card-footer text-end">
            <div class="d-flex justify-content-between">
                <div>
                    <?php if (!$isNew): ?>
                    <button type="button" class="btn btn-sm btn-outline-danger rounded-0" onclick="deleteCar()">
                        <i class="ph-trash me-1"></i><?php echo __('차량 삭제', 'manager') ?>
                    </button>
                    <?php endif; ?>
                </div>
                <div>
                    <a href="car-list" class="btn btn-sm btn-outline-secondary me-2 rounded-0">
                        <?php echo __('취소', 'manager') ?>
                    </a>
                    <button type="submit" class="btn btn-sm btn-primary rounded-0">
                        <i class="ph-floppy-disk me-1"></i>
                        <?php echo $isNew ? __('추가', 'manager') : __('저장', 'manager') ?>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-header">
            <h5 class="mb-0 font-090"><i class="ph-list-checks me-1"></i><?php echo __('옵션 정보', 'manager') ?></h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('외관 및 내장', 'manager') ?></label>
                        <input type="text"
                                name="option_exterior"
                                value="<?php echo is_object($car) ? implode(",", json_decode($car->option_exterior)) : ''?>"
                                class="form-control tags-input"
                                placeholder="<?php echo __('쉼표로 구분하여 입력', 'manager')?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('안전장치', 'manager') ?></label>
                        <input type="text"
                                name="option_safety"
                                value="<?php echo is_object($car) ? implode(",", json_decode($car->option_safety)) : ''?>"
                                class="form-control tags-input"
                                placeholder="<?php echo __('쉼표로 구분하여 입력', 'manager')?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('편의장치', 'manager') ?></label>
                        <input type="text"
                                name="option_convenience"
                                value="<?php echo is_object($car) ? implode(",", json_decode($car->option_convenience)) : ''?>"
                                class="form-control tags-input"
                                placeholder="<?php echo __('쉼표로 구분하여 입력', 'manager')?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('시트', 'manager') ?></label>
                        <input type="text"
                                name="option_seat"
                                value="<?php echo is_object($car) ? implode(",", json_decode($car->option_seat)) : ''?>"
                                class="form-control tags-input"
                                placeholder="<?php echo __('쉼표로 구분하여 입력', 'manager')?>">
                    </div>
                </div>
            </div>
            <label class="form-label"><?php echo __('기타', 'manager') ?></label>
            <textarea id="editor" name="option_etc" rows="5" class="ckeditor bordered"
                data-uploadUrl="/backoffice/modules/upload-ckeditor?service_folder=rent"
                data-filebrowserImageUploadUrl="/backoffice/modules/upload-ckeditor?service_folder=rent"
                data-bodyClass="mx-5 my-5"
                data-contentsCss="/assets/css/common.min.css"><?php echo $car->option_etc?></textarea>
        </div>
        <div class="card-footer text-end">
            <div class="d-flex justify-content-between">
                <div>
                    <?php if (!$isNew): ?>
                    <button type="button" class="btn btn-sm btn-outline-danger rounded-0" onclick="deleteCar()">
                        <i class="ph-trash me-1"></i><?php echo __('차량 삭제', 'manager') ?>
                    </button>
                    <?php endif; ?>
                </div>
                <div>
                    <a href="car-list" class="btn btn-sm btn-outline-secondary me-2 rounded-0">
                        <?php echo __('취소', 'manager') ?>
                    </a>
                    <button type="submit" class="btn btn-sm btn-primary rounded-0">
                        <i class="ph-floppy-disk me-1"></i>
                        <?php echo $isNew ? __('추가', 'manager') : __('저장', 'manager') ?>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-header">
            <h5 class="mb-0 font-090 d-flex justify-content-between align-items-center">
                <div><i class="ph-currency-circle-dollar me-1"></i><?php echo __('가격 정보', 'manager') ?></div>
                <button type="button" class="btn btn-outline-primary btn-sm rounded-0" onclick="addPriceRow()">
                    <i class="ph-plus me-1"></i><?php echo __('가격 옵션 추가', 'manager') ?>
                </button>
            </h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered" id="priceTable">
                <thead class="bg-light">
                    <tr>
                        <th class="text-center" width="150"><?php echo __('보증금 (만원)', 'manager') ?></th>
                        <th class="text-center" width="150"><?php echo __('렌트기간 (개월)', 'manager') ?></th>
                        <th class="text-center" width="180"><?php echo __('월렌트비 (원)', 'manager') ?></th>
                        <th class="text-center" width="150"><?php echo __('연간주행 (만km)', 'manager') ?></th>
                        <th class="text-center" width="80"><?php echo __('삭제', 'manager') ?></th>
                    </tr>
                </thead>
                <tbody id="priceTableBody">
                    <?php if (!empty($prices)): ?>
                        <?php foreach ($prices as $price): ?>
                        <tr>
                            <td><input type="number" name="prices[][deposit_amount]" class="form-control form-control-sm rounded-0" value="<?php echo $price->deposit_amount ?>"></td>
                            <td><input type="number" name="prices[][rental_period_months]" class="form-control form-control-sm rounded-0" value="<?php echo $price->rental_period_months ?>"></td>
                            <td><input type="number" name="prices[][monthly_rent_amount]" class="form-control form-control-sm rounded-0" value="<?php echo $price->monthly_rent_amount ?>" onchange="updateMinPrice()" onkeyup="updateMinPrice()"></td>
                            <td><input type="number" name="prices[][yearly_mileage_limit]" class="form-control form-control-sm rounded-0" value="<?php echo $price->yearly_mileage_limit ?>"></td>
                            <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm rounded-0" onclick="removePriceRow(this)"><i class="ph-trash"></i></button></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <div class="text-muted small mt-2">
                <i class="ph-info me-1"></i><?php echo __('가격 옵션이 없으면 기본 월렌트료가 표시됩니다.', 'manager') ?>
            </div>
        </div>
        <div class="card-footer text-end">
            <div class="d-flex justify-content-between">
                <div>
                    <?php if (!$isNew): ?>
                    <button type="button" class="btn btn-sm btn-outline-danger rounded-0" onclick="deleteCar()">
                        <i class="ph-trash me-1"></i><?php echo __('차량 삭제', 'manager') ?>
                    </button>
                    <?php endif; ?>
                </div>
                <div>
                    <a href="car-list" class="btn btn-sm btn-outline-secondary me-2 rounded-0">
                        <?php echo __('취소', 'manager') ?>
                    </a>
                    <button type="submit" class="btn btn-sm btn-primary rounded-0">
                        <i class="ph-floppy-disk me-1"></i>
                        <?php echo $isNew ? __('추가', 'manager') : __('저장', 'manager') ?>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-header">
            <h5 class="mb-0 font-090"><i class="ph-images me-1"></i><?php echo __('차량 이미지', 'manager') ?></h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label for="imageFileInput" class="btn btn-outline-primary btn-sm">
                    <i class="ph-upload me-1"></i><?php echo __('이미지 업로드', 'manager') ?>
                </label>
                <input type="file" id="imageFileInput" class="d-none" accept="image/*" multiple>
                <small class="text-muted ms-2"><?php echo __('여러 파일을 선택할 수 있습니다', 'manager') ?></small>
            </div>
            <div id="uploadProgress" class="mb-3 d-none">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>
                    <span class="ms-2 text-muted small" id="uploadStatus"></span>
                </div>
            </div>
            <div id="imageContainer" class="row row-cols-1 row-cols-md-2 row-cols-lg-6 g-3">
                <?php if (!empty($images)): ?>
                    <?php foreach ($images as $image): ?>
                    <div class="col image-item" data-idx="<?php echo $image->idx ?>" data-url="<?php echo htmlspecialchars($image->image_url) ?>">
                        <div class="card <?php echo ($car->featured_image === $image->image_url) ? 'border-primary border-2' : '' ?>">
                            <img src="<?php echo htmlspecialchars($image->image_url) ?>" class="card-img-top" style="height: 150px; object-fit: contain;">
                            <div class="card-body p-2">
                                <input type="hidden" name="images[][image_url]" value="<?php echo htmlspecialchars($image->image_url) ?>">
                                <div class="btn-group w-100" role="group">
                                    <button type="button" class="btn btn-outline-primary btn-sm <?php echo ($car->featured_image === $image->image_url) ? 'active' : '' ?>" onclick="setMainImage(this, '<?php echo htmlspecialchars($image->image_url) ?>')">
                                        <i class="ph-star me-1"></i><?php echo __('대표', 'manager') ?>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeImageItem(this, <?php echo $image->idx ?>)">
                                        <i class="ph-trash me-1"></i><?php echo __('삭제', 'manager') ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="text-muted small mt-3">
                <i class="ph-info me-1"></i><?php echo __('JPG, PNG, GIF, WEBP 파일만 업로드 가능합니다. (최대 10MB)', 'manager') ?>
            </div>
        </div>
        <div class="card-footer text-end">
            <div class="d-flex justify-content-between">
                <div>
                    <?php if (!$isNew): ?>
                    <button type="button" class="btn btn-sm btn-outline-danger rounded-0" onclick="deleteCar()">
                        <i class="ph-trash me-1"></i><?php echo __('차량 삭제', 'manager') ?>
                    </button>
                    <?php endif; ?>
                </div>
                <div>
                    <a href="car-list" class="btn btn-sm btn-outline-secondary me-2 rounded-0">
                        <?php echo __('취소', 'manager') ?>
                    </a>
                    <button type="submit" class="btn btn-sm btn-primary rounded-0">
                        <i class="ph-floppy-disk me-1"></i>
                        <?php echo $isNew ? __('추가', 'manager') : __('저장', 'manager') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- CodeMirror CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/theme/monokai.min.css">

<!-- CodeMirror JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/javascript/javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/edit/matchbrackets.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/edit/closebrackets.min.js"></script>

<script>
const isNew = <?php echo $isNew ? 'true' : 'false' ?>;
const carIdx = <?php echo $idx ? $idx : 'null' ?>;
const editors = {};

document.addEventListener('DOMContentLoaded', function() {
    initCodeMirrorEditors();
    initFormSubmit();
    initImageUpload();
    updateMinPrice();
});

// CodeMirror 에디터 초기화
function initCodeMirrorEditors() {
    const editorOptions = {
        mode: { name: 'javascript', json: true },
        theme: 'monokai',
        lineNumbers: true,
        matchBrackets: true,
        autoCloseBrackets: true,
        tabSize: 2,
        indentWithTabs: false
    };

    const fields = ['option_exterior', 'option_safety', 'option_convenience', 'option_seat'];

    fields.forEach(function(field) {
        const textarea = document.getElementById(field);
        const editorDiv = document.getElementById('editor_' + field);

        if (textarea && editorDiv) {
            const formattedValue = formatJSON(textarea.value);
            editors[field] = CodeMirror(editorDiv, {
                ...editorOptions,
                value: formattedValue
            });
        }
    });
}

function formatJSON(str) {
    if (!str || str.trim() === '') {
        return '[\n  \n]';
    }
    try {
        const obj = JSON.parse(str);
        return JSON.stringify(obj, null, 2);
    } catch (e) {
        return str;
    }
}

// 폼 제출 처리
function initFormSubmit() {
    document.getElementById('carForm').addEventListener('submit', function(e) {
        e.preventDefault();
        saveCar();
    });
}

function saveCar() {

    if(typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.editor) {
        $('textarea[name=option_etc]').val(CKEDITOR.instances.editor.getData());
    }

    // 폼 데이터 수집
    const formData = new FormData(document.getElementById('carForm'));
    const data = {};

    // 기본 필드
    const basicFields = ['idx', 'dealer_idx', 'car_type', 'status', 'car_number', 'title', 'brand', 'model',
        'fuel_type', 'model_year', 'model_month', 'mileage_km', 'monthly_price', 'featured_image', 'option_etc'];

    // 옵션 필드 (콤마 → JSON 배열 변환)
    const optionFields = ['option_exterior', 'option_safety', 'option_convenience', 'option_seat'];

    basicFields.forEach(field => {
        const value = formData.get(field);
        if (value !== null && value !== '') {
            data[field] = value;
        }
    });

    // 옵션 필드는 콤마로 구분된 텍스트를 JSON 배열로 변환
    optionFields.forEach(field => {
        const value = formData.get(field);
        if (value !== null && value.trim() !== '') {
            data[field] = JSON.stringify(
                value.split(',').map(item => item.trim()).filter(item => item !== '')
            );
        } else {
            data[field] = '[]';
        }
    });

    // 가격 데이터 수집
    data.prices = [];
    const priceRows = document.querySelectorAll('#priceTableBody tr');
    priceRows.forEach(row => {
        const inputs = row.querySelectorAll('input');
        if (inputs.length >= 4) {
            data.prices.push({
                deposit_amount: inputs[0].value || null,
                rental_period_months: inputs[1].value || null,
                monthly_rent_amount: inputs[2].value || null,
                yearly_mileage_limit: inputs[3].value || null
            });
        }
    });

    // 이미지 데이터 수집
    data.images = [];
    const imageItems = document.querySelectorAll('.image-item');
    imageItems.forEach(item => {
        const urlInput = item.querySelector('input[name="images[][image_url]"]');
        if (urlInput && urlInput.value) {
            data.images.push({
                image_url: urlInput.value
            });
        }
    });

    // API 호출
    const method = isNew ? 'POST' : 'PUT';
    fetch('/api/arirent/car', {
        method: method,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.result === 'SUCCESS') {
            ExpertNote.Util.showMessage('<?php echo __('저장되었습니다.', 'manager') ?>','<?php echo __('성공', 'manager') ?>',
                [
                    {
                        title: '<?php echo __('확인', 'manager') ?>',
                        class: 'btn btn-sm btn-primary rounded-0',
                        dismiss: true
                    }
                ]
            );
            if (isNew && result.data && result.data.idx) {
                setTimeout(() => {
                    location.href = 'car-edit?idx=' + result.data.idx;
                }, 1000);
            }
        } else {
            ExpertNote.Util.showMessage('<?php echo __('저장에 실패했습니다.', 'manager') ?>','<?php echo __('오류', 'manager') ?>',
                [
                    {
                        title: '<?php echo __('확인', 'manager') ?>',
                        class: 'btn btn-sm btn-primary rounded-0',
                        dismiss: true
                    }
                ]
            );
        }
    })
    .catch(error => {
        console.error('Error:', error);
        ExpertNote.Util.showMessage('<?php echo __('오류가 발생했습니다.', 'manager') ?>','<?php echo __('오류', 'manager') ?>',
            [
                {
                    title: '<?php echo __('확인', 'manager') ?>',
                    class: 'btn btn-sm btn-primary rounded-0',
                    dismiss: true
                }
            ]
        );
    });
}

function deleteCar() {
    if (!confirm('<?php echo __('정말로 이 차량을 삭제하시겠습니까? 관련된 가격, 이미지, 찜하기 정보도 모두 삭제됩니다.', 'manager') ?>')) {
        return;
    }

    fetch('/api/arirent/car', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ idx: carIdx })
    })
    .then(response => response.json())
    .then(result => {
        if (result.result === 'SUCCESS') {
            ExpertNote.Util.showMessage('<?php echo __('삭제되었습니다.', 'manager') ?>','<?php echo __('성공', 'manager') ?>',
                [
                    {
                        title: '<?php echo __('확인', 'manager') ?>',
                        class: 'btn btn-sm btn-primary rounded-0',
                        dismiss: true
                    }
                ]
            );
                location.href = 'car-list';
        } else {
            ExpertNote.Util.showMessage(result.message || '<?php echo __('삭제에 실패했습니다.', 'manager') ?>','<?php echo __('오류', 'manager') ?>',
                [
                    {
                        title: '<?php echo __('확인', 'manager') ?>',
                        class: 'btn btn-sm btn-primary rounded-0',
                        dismiss: true
                    }
                ]
            );
        }
    })
    .catch(error => {
        console.error('Error:', error);
        ExpertNote.Util.showMessage('<?php echo __('오류가 발생했습니다.', 'manager') ?>','<?php echo __('오류', 'manager') ?>',
            [
                {
                    title: '<?php echo __('확인', 'manager') ?>',
                    class: 'btn btn-sm btn-primary rounded-0',
                    dismiss: true
                }
            ]
        );
    });
}

// 가격 행 추가
function addPriceRow() {
    const tbody = document.getElementById('priceTableBody');
    const row = document.createElement('tr');
    row.innerHTML = `
        <td><input type="number" name="prices[][deposit_amount]" class="form-control form-control-sm rounded-0" placeholder="0"></td>
        <td><input type="number" name="prices[][rental_period_months]" class="form-control form-control-sm rounded-0" placeholder="36"></td>
        <td><input type="number" name="prices[][monthly_rent_amount]" class="form-control form-control-sm rounded-0" placeholder="500000" onchange="updateMinPrice()" onkeyup="updateMinPrice()"></td>
        <td><input type="number" name="prices[][yearly_mileage_limit]" class="form-control form-control-sm rounded-0" placeholder="2"></td>
        <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm" onclick="removePriceRow(this)"><i class="ph-trash"></i></button></td>
    `;
    tbody.appendChild(row);
    updateMinPrice();
}

function removePriceRow(btn) {
    btn.closest('tr').remove();
    updateMinPrice();
}

// 월렌트료 최저가 자동 계산
function updateMinPrice() {
    const priceInputs = document.querySelectorAll('input[name="prices[][monthly_rent_amount]"]');
    let minPrice = null;

    priceInputs.forEach(input => {
        const value = parseInt(input.value);
        if (value > 0) {
            if (minPrice === null || value < minPrice) {
                minPrice = value;
            }
        }
    });

    const monthlyPriceInput = document.getElementById('monthlyPrice');
    if (monthlyPriceInput) {
        monthlyPriceInput.value = minPrice || '';
    }
}

// 이미지 업로드 초기화
function initImageUpload() {
    // 이미지 다중 업로드
    const imageFileInput = document.getElementById('imageFileInput');
    if (imageFileInput) {
        imageFileInput.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                uploadMultipleImages(e.target.files);
            }
        });
    }
}

// 다중 이미지 업로드
async function uploadMultipleImages(files) {
    const progressContainer = document.getElementById('uploadProgress');
    const progressBar = progressContainer.querySelector('.progress-bar');
    const statusText = document.getElementById('uploadStatus');

    progressContainer.classList.remove('d-none');

    const totalFiles = files.length;
    let uploadedCount = 0;

    for (const file of files) {
        statusText.textContent = `${uploadedCount + 1} / ${totalFiles}`;
        progressBar.style.width = ((uploadedCount / totalFiles) * 100) + '%';

        try {
            const result = await uploadSingleImage(file);
            if (result.result === 'SUCCESS') {
                addImageToContainer(result.data.url, result.data.idx);
            }
            uploadedCount++;
        } catch (error) {
            console.error('Upload error:', error);
        }
    }

    progressBar.style.width = '100%';
    statusText.textContent = `<?php echo __('완료', 'manager') ?>`;

    setTimeout(() => {
        progressContainer.classList.add('d-none');
        progressBar.style.width = '0%';
    }, 1500);

    // 파일 입력 초기화
    document.getElementById('imageFileInput').value = '';
    ExpertNote.Util.showMessage(`<?php echo __('개의 이미지가 업로드되었습니다.', 'manager') ?>`.replace('개', uploadedCount),'<?php echo __('성공', 'manager') ?>',
        [
            {
                title: '<?php echo __('확인', 'manager') ?>',
                class: 'btn btn-sm btn-primary rounded-0',
                dismiss: true
            }
        ]
    );
}

// 단일 이미지 업로드 (Promise)
function uploadSingleImage(file) {
    return new Promise((resolve, reject) => {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('rent_idx', document.querySelector('input[name="idx"]').value);

        fetch('/api/arirent/car-image', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => resolve(result))
        .catch(error => reject(error));
    });
}

// 이미지 컨테이너에 추가
function addImageToContainer(url, imageIdx) {
    const container = document.getElementById('imageContainer');
    const col = document.createElement('div');
    col.className = 'col image-item';
    col.setAttribute('data-idx', imageIdx);
    col.setAttribute('data-url', url);
    col.innerHTML = `
        <div class="card">
            <img src="${url}" class="card-img-top" style="height: 150px; object-fit: contain;" onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22><rect fill=%22%23ddd%22 width=%22100%22 height=%22100%22/><text fill=%22%23999%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22>No Image</text></svg>'">
            <div class="card-body p-2">
                <input type="hidden" name="images[][image_url]" value="${url}">
                <div class="btn-group w-100" role="group">
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="setMainImage(this, '${url}')">
                        <i class="ph-star me-1"></i><?php echo __('대표', 'manager') ?>
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeImageItem(this, ${imageIdx})">
                        <i class="ph-trash me-1"></i><?php echo __('삭제', 'manager') ?>
                    </button>
                </div>
            </div>
        </div>
    `;
    container.appendChild(col);
}

// 대표 이미지 설정
function setMainImage(btn, imageUrl) {
    // hidden input에 URL 설정
    document.getElementById('mainImageUrl').value = imageUrl;

    // 미리보기 업데이트
    const preview = document.getElementById('mainImagePreview');
    preview.innerHTML = `<img src="${imageUrl}" style="max-height: 100px; max-width: 150px;" class="rounded">`;

    // 모든 이미지 카드에서 선택 표시 제거
    document.querySelectorAll('#imageContainer .image-item .card').forEach(card => {
        card.classList.remove('border-primary', 'border-2');
    });
    document.querySelectorAll('#imageContainer .image-item .btn-outline-primary').forEach(btn => {
        btn.classList.remove('active');
    });

    // 현재 이미지 카드에 선택 표시
    const card = btn.closest('.card');
    card.classList.add('border-primary', 'border-2');
    btn.classList.add('active');

    ExpertNote.Util.showMessage('<?php echo __('대표 이미지가 설정되었습니다. 저장 버튼을 눌러 변경사항을 저장하세요.', 'manager') ?>','<?php echo __('알림', 'manager') ?>',
        [
            {
                title: '<?php echo __('확인', 'manager') ?>',
                class: 'btn btn-sm btn-primary rounded-0',
                dismiss: true
            }
        ]
    );
}

function removeImageItem(btn, imageIdx) {
    if (!confirm('<?php echo __('이미지를 삭제하시겠습니까?', 'manager') ?>')) {
        return;
    }

    const imageItem = btn.closest('.image-item');

    // DB에 저장된 이미지인 경우 API 호출하여 삭제
    if (imageIdx) {
        btn.disabled = true;
        btn.innerHTML = '<i class="ph-spinner ph-spin me-1"></i><?php echo __('삭제 중...', 'manager') ?>';

        fetch('/api/arirent/car-image?idx=' + imageIdx, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(result => {
            if (result.result === 'SUCCESS') {
                imageItem.remove();
            } else {
                ExpertNote.Util.showMessage(
                    result.message || '<?php echo __('이미지 삭제에 실패했습니다.', 'manager') ?>',
                    '<?php echo __('오류', 'manager') ?>',
                    [{ title: '<?php echo __('확인', 'manager') ?>', class: 'btn btn-secondary', dismiss: true }]
                );
                btn.disabled = false;
                btn.innerHTML = '<i class="ph-trash me-1"></i><?php echo __('삭제', 'manager') ?>';
            }
        })
        .catch(error => {
            console.error('Delete error:', error);
            ExpertNote.Util.showMessage(
                '<?php echo __('이미지 삭제 중 오류가 발생했습니다.', 'manager') ?>',
                '<?php echo __('오류', 'manager') ?>',
                [{ title: '<?php echo __('확인', 'manager') ?>', class: 'btn btn-secondary', dismiss: true }]
            );
            btn.disabled = false;
            btn.innerHTML = '<i class="ph-trash me-1"></i><?php echo __('삭제', 'manager') ?>';
        });
    } else {
        // 아직 DB에 저장되지 않은 새 이미지는 바로 DOM에서 제거
        imageItem.remove();
    }
}
</script>

<style>
.CodeMirror {
    height: 200px;
    font-size: 13px;
    border-radius: 0;
}
.nav-tabs .nav-link {
    color: #666;
}
.nav-tabs .nav-link.active {
    font-weight: bold;
}
.image-item .card {
    transition: transform 0.2s;
}
.image-item .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
</style>
