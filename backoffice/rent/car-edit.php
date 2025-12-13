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
    $imagesSql = "SELECT * FROM " . DB_PREFIX . "rent_images WHERE rent_idx = :rent_idx ORDER BY sort_order, idx";
    $images = \ExpertNote\DB::getRows($imagesSql, ['rent_idx' => $idx]);
}
?>

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
    <div class="card-body">
        <div id="alertContainer"></div>

        <form id="carForm">
            <input type="hidden" name="idx" value="<?php echo $idx ?>">

            <!-- 탭 네비게이션 -->
            <ul class="nav nav-tabs mb-4" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-basic" type="button">
                        <i class="ph-info me-1"></i><?php echo __('기본 정보', 'manager') ?>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-options" type="button">
                        <i class="ph-wrench me-1"></i><?php echo __('옵션 정보', 'manager') ?>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-prices" type="button">
                        <i class="ph-currency-krw me-1"></i><?php echo __('가격 정보', 'manager') ?>
                        <span class="badge bg-primary ms-1" id="priceCount"><?php echo count($prices) ?></span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-images" type="button">
                        <i class="ph-images me-1"></i><?php echo __('이미지', 'manager') ?>
                        <span class="badge bg-primary ms-1" id="imageCount"><?php echo count($images) ?></span>
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                <!-- 기본 정보 탭 -->
                <div class="tab-pane fade show active" id="tab-basic">
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
                                <input type="number" name="monthly_price" class="form-control rounded-0"
                                    value="<?php echo $car->monthly_price ?? '' ?>"
                                    placeholder="500000">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?php echo __('대표 이미지 URL', 'manager') ?></label>
                        <input type="url" name="image" class="form-control rounded-0"
                            value="<?php echo htmlspecialchars($car->image ?? '') ?>"
                            placeholder="https://...">
                        <?php if (!empty($car->image)): ?>
                            <div class="mt-2">
                                <img src="<?php echo $car->image ?>" style="max-height: 100px;" class="rounded">
                            </div>
                        <?php endif; ?>
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

                <!-- 옵션 정보 탭 -->
                <div class="tab-pane fade" id="tab-options">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><?php echo __('외관 및 내장', 'manager') ?></label>
                                <textarea name="option_exterior" id="option_exterior" class="d-none"><?php echo htmlspecialchars($car->option_exterior ?? '') ?></textarea>
                                <div id="editor_option_exterior" class="border" style="height: 200px;"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><?php echo __('안전장치', 'manager') ?></label>
                                <textarea name="option_safety" id="option_safety" class="d-none"><?php echo htmlspecialchars($car->option_safety ?? '') ?></textarea>
                                <div id="editor_option_safety" class="border" style="height: 200px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><?php echo __('편의장치', 'manager') ?></label>
                                <textarea name="option_convenience" id="option_convenience" class="d-none"><?php echo htmlspecialchars($car->option_convenience ?? '') ?></textarea>
                                <div id="editor_option_convenience" class="border" style="height: 200px;"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><?php echo __('시트', 'manager') ?></label>
                                <textarea name="option_seat" id="option_seat" class="d-none"><?php echo htmlspecialchars($car->option_seat ?? '') ?></textarea>
                                <div id="editor_option_seat" class="border" style="height: 200px;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 가격 정보 탭 -->
                <div class="tab-pane fade" id="tab-prices">
                    <div class="mb-3">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="addPriceRow()">
                            <i class="ph-plus me-1"></i><?php echo __('가격 옵션 추가', 'manager') ?>
                        </button>
                    </div>
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
                                    <td><input type="number" name="prices[][monthly_rent_amount]" class="form-control form-control-sm rounded-0" value="<?php echo $price->monthly_rent_amount ?>"></td>
                                    <td><input type="number" name="prices[][yearly_mileage_limit]" class="form-control form-control-sm rounded-0" value="<?php echo $price->yearly_mileage_limit ?>"></td>
                                    <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm" onclick="removePriceRow(this)"><i class="ph-trash"></i></button></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <div class="text-muted small mt-2">
                        <i class="ph-info me-1"></i><?php echo __('가격 옵션이 없으면 기본 월렌트료가 표시됩니다.', 'manager') ?>
                    </div>
                </div>

                <!-- 이미지 탭 -->
                <div class="tab-pane fade" id="tab-images">
                    <div class="mb-3">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="addImageRow()">
                            <i class="ph-plus me-1"></i><?php echo __('이미지 추가', 'manager') ?>
                        </button>
                    </div>
                    <div id="imageContainer" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                        <?php if (!empty($images)): ?>
                            <?php foreach ($images as $image): ?>
                            <div class="col image-item">
                                <div class="card">
                                    <img src="<?php echo htmlspecialchars($image->image_url) ?>" class="card-img-top" style="height: 150px; object-fit: cover;">
                                    <div class="card-body p-2">
                                        <input type="hidden" name="images[][image_url]" value="<?php echo htmlspecialchars($image->image_url) ?>">
                                        <select name="images[][image_type]" class="form-select form-select-sm rounded-0 mb-2">
                                            <option value="exterior" <?php echo $image->image_type === 'exterior' ? 'selected' : '' ?>><?php echo __('외관', 'manager') ?></option>
                                            <option value="interior" <?php echo $image->image_type === 'interior' ? 'selected' : '' ?>><?php echo __('내부', 'manager') ?></option>
                                            <option value="detail" <?php echo $image->image_type === 'detail' ? 'selected' : '' ?>><?php echo __('상세', 'manager') ?></option>
                                        </select>
                                        <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="removeImageItem(this)">
                                            <i class="ph-trash me-1"></i><?php echo __('삭제', 'manager') ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="text-muted small mt-3">
                        <i class="ph-info me-1"></i><?php echo __('이미지 URL을 입력하거나 드래그하여 순서를 변경할 수 있습니다.', 'manager') ?>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex justify-content-between">
                <div>
                    <?php if (!$isNew): ?>
                    <button type="button" class="btn btn-outline-danger" onclick="deleteCar()">
                        <i class="ph-trash me-1"></i><?php echo __('차량 삭제', 'manager') ?>
                    </button>
                    <?php endif; ?>
                </div>
                <div>
                    <a href="car-list" class="btn btn-outline-secondary me-2">
                        <?php echo __('취소', 'manager') ?>
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ph-floppy-disk me-1"></i>
                        <?php echo $isNew ? __('추가', 'manager') : __('저장', 'manager') ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

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
    updateCounts();
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
        return '{\n  \n}';
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
    // 옵션 에디터 값 동기화
    const optionFields = ['option_exterior', 'option_safety', 'option_convenience', 'option_seat'];
    optionFields.forEach(function(field) {
        if (editors[field]) {
            const textarea = document.getElementById(field);
            let value = editors[field].getValue().trim();
            if (value === '{\n  \n}' || value === '{}') {
                textarea.value = '';
            } else {
                try {
                    const obj = JSON.parse(value);
                    textarea.value = JSON.stringify(obj);
                } catch (e) {
                    textarea.value = value;
                }
            }
        }
    });

    // 폼 데이터 수집
    const formData = new FormData(document.getElementById('carForm'));
    const data = {};

    // 기본 필드
    const basicFields = ['idx', 'dealer_idx', 'car_type', 'status', 'car_number', 'title', 'brand', 'model',
        'fuel_type', 'model_year', 'model_month', 'mileage_km', 'monthly_price', 'image',
        'option_exterior', 'option_safety', 'option_convenience', 'option_seat'];

    basicFields.forEach(field => {
        const value = formData.get(field);
        if (value !== null && value !== '') {
            data[field] = value;
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
        const typeSelect = item.querySelector('select[name="images[][image_type]"]');
        if (urlInput && urlInput.value) {
            data.images.push({
                image_url: urlInput.value,
                image_type: typeSelect ? typeSelect.value : 'exterior'
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
            showAlert('success', result.message || '<?php echo __('저장되었습니다.', 'manager') ?>');
            if (isNew && result.data && result.data.idx) {
                setTimeout(() => {
                    location.href = 'car-edit?idx=' + result.data.idx;
                }, 1000);
            }
        } else {
            showAlert('danger', result.message || '<?php echo __('저장에 실패했습니다.', 'manager') ?>');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', '<?php echo __('오류가 발생했습니다.', 'manager') ?>');
    });
}

function deleteCar() {
    if (!confirm('<?php echo __('정말로 이 차량을 삭제하시겠습니까? 관련된 가격, 이미지, 찜하기 정보도 모두 삭제됩니다.', 'manager') ?>')) {
        return;
    }

    fetch('/api/arirent/car?idx=' + carIdx, {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' }
    })
    .then(response => response.json())
    .then(result => {
        if (result.result === 'SUCCESS') {
            alert(result.message || '<?php echo __('삭제되었습니다.', 'manager') ?>');
            location.href = 'car-list';
        } else {
            alert(result.message || '<?php echo __('삭제에 실패했습니다.', 'manager') ?>');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('<?php echo __('오류가 발생했습니다.', 'manager') ?>');
    });
}

// 가격 행 추가
function addPriceRow() {
    const tbody = document.getElementById('priceTableBody');
    const row = document.createElement('tr');
    row.innerHTML = `
        <td><input type="number" name="prices[][deposit_amount]" class="form-control form-control-sm rounded-0" placeholder="0"></td>
        <td><input type="number" name="prices[][rental_period_months]" class="form-control form-control-sm rounded-0" placeholder="36"></td>
        <td><input type="number" name="prices[][monthly_rent_amount]" class="form-control form-control-sm rounded-0" placeholder="500000"></td>
        <td><input type="number" name="prices[][yearly_mileage_limit]" class="form-control form-control-sm rounded-0" placeholder="2"></td>
        <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm" onclick="removePriceRow(this)"><i class="ph-trash"></i></button></td>
    `;
    tbody.appendChild(row);
    updateCounts();
}

function removePriceRow(btn) {
    btn.closest('tr').remove();
    updateCounts();
}

// 이미지 추가
function addImageRow() {
    const url = prompt('<?php echo __('이미지 URL을 입력하세요', 'manager') ?>');
    if (!url) return;

    const container = document.getElementById('imageContainer');
    const col = document.createElement('div');
    col.className = 'col image-item';
    col.innerHTML = `
        <div class="card">
            <img src="${url}" class="card-img-top" style="height: 150px; object-fit: cover;" onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22><rect fill=%22%23ddd%22 width=%22100%22 height=%22100%22/><text fill=%22%23999%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22>No Image</text></svg>'">
            <div class="card-body p-2">
                <input type="hidden" name="images[][image_url]" value="${url}">
                <select name="images[][image_type]" class="form-select form-select-sm rounded-0 mb-2">
                    <option value="exterior"><?php echo __('외관', 'manager') ?></option>
                    <option value="interior"><?php echo __('내부', 'manager') ?></option>
                    <option value="detail"><?php echo __('상세', 'manager') ?></option>
                </select>
                <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="removeImageItem(this)">
                    <i class="ph-trash me-1"></i><?php echo __('삭제', 'manager') ?>
                </button>
            </div>
        </div>
    `;
    container.appendChild(col);
    updateCounts();
}

function removeImageItem(btn) {
    btn.closest('.image-item').remove();
    updateCounts();
}

function updateCounts() {
    document.getElementById('priceCount').textContent = document.querySelectorAll('#priceTableBody tr').length;
    document.getElementById('imageCount').textContent = document.querySelectorAll('.image-item').length;
}

function showAlert(type, message) {
    const container = document.getElementById('alertContainer');
    container.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show">
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>`;
    window.scrollTo(0, 0);
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
