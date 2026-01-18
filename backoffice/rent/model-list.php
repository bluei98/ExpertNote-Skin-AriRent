<?php
// 모델 목록 관리
$breadcrumb = ["rent", "model-list"];
$_page_title = __('모델 목록', 'manager');

// 검색 옵션 설정
$searchText = [
    "ALL" => __('전체 검색', 'manager'),
    "model_name" => __('모델명', 'manager'),
    "model_name_en" => __('모델명(영문)', 'manager')
];

$searchSort = [
    "idx" => __('ID', 'manager'),
    "model_name" => __('모델명', 'manager'),
    "brand_idx" => __('브랜드', 'manager'),
    "segment" => __('세그먼트', 'manager'),
    "sort_order" => __('정렬순서', 'manager'),
    "created_at" => __('등록일', 'manager')
];

$navigationButtons = array(
    array(
        "title" => __('모델 추가', 'manager'),
        "url" => "model-edit",
        "class" => "btn btn-primary",
        "icon" => "ph-plus"
    )
);

// 기본 정렬
if (!$_GET['orderby'])
    $orderby = array("sort_order ASC", "idx ASC");

// 검색 조건 처리
include ABSPATH . "/backoffice/modules/processSearch.php";

$searchWheres = $searchWheres ?: [];
$searchOrderBy = $searchOrderBy ?: ["sort_order ASC", "idx ASC"];
$params = [];

// 브랜드 필터
$brandFilter = $_GET['brand_idx'] ?? '';
if ($brandFilter) {
    $searchWheres[] = "m.brand_idx = :brand_idx";
    $params['brand_idx'] = intval($brandFilter);
}

// 세그먼트 필터
$segmentFilter = $_GET['segment'] ?? '';
if ($segmentFilter) {
    $searchWheres[] = "m.segment = :segment";
    $params['segment'] = $segmentFilter;
}

// 국가 필터
$countryFilter = $_GET['country_code'] ?? '';
if ($countryFilter) {
    $searchWheres[] = "b.country_code = :country_code";
    $params['country_code'] = $countryFilter;
}

// 목록 조회
$countSql = "SELECT COUNT(*) as cnt FROM " . DB_PREFIX . "rent_model m
             LEFT JOIN " . DB_PREFIX . "rent_brand b ON m.brand_idx = b.idx";
if (count($searchWheres) > 0) {
    $countSql .= " WHERE " . implode(" AND ", $searchWheres);
}
$cntResult = \ExpertNote\DB::getRow($countSql, $params);
$cnt = $cntResult->cnt ?? 0;

list($paging, $pageRecord) = createPaging($cnt, $page, $pageCount, 20);

$listSql = "SELECT m.*, b.brand_name, b.brand_name_en, b.country_code
FROM " . DB_PREFIX . "rent_model m
LEFT JOIN " . DB_PREFIX . "rent_brand b ON m.brand_idx = b.idx";

if (count($searchWheres) > 0) {
    $listSql .= " WHERE " . implode(" AND ", $searchWheres);
}

if (count($searchOrderBy) > 0) {
    $orderByWithPrefix = [];
    foreach ($searchOrderBy as $order) {
        if (strpos($order, '.') === false && strpos($order, '(') === false) {
            $order = preg_replace('/^(\w+)/', 'm.$1', $order);
        }
        $orderByWithPrefix[] = $order;
    }
    $listSql .= " ORDER BY " . implode(", ", $orderByWithPrefix);
} else {
    $listSql .= " ORDER BY m.sort_order ASC, m.idx ASC";
}

$listSql .= " LIMIT {$paging['viewStarRow']}, {$paging['viewCount']}";

$res = \ExpertNote\DB::getRows($listSql, $params);

// 브랜드 목록 (필터용)
$brands = \AriRent\Rent::getBrands([], ['sort_order' => 'ASC', 'brand_name' => 'ASC']);

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
?>

<div class="card">
    <div class="card-body">
        <?php include ABSPATH . "/backoffice/modules/printSearch.php"; ?>
        <!-- 필터 영역 -->
        <div class="d-flex flex-wrap gap-2 mt-2">
            <!-- 브랜드 필터 -->
            <div class="dropdown">
                <button class="btn btn-sm <?php echo !empty($brandFilter) ? 'btn-primary' : 'btn-outline-secondary' ?> dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="ph-trademark-registered me-1"></i>
                    <?php
                    if ($brandFilter) {
                        foreach ($brands as $b) {
                            if ($b->idx == $brandFilter) {
                                echo htmlspecialchars($b->brand_name);
                                break;
                            }
                        }
                    } else {
                        echo __('브랜드', 'manager');
                    }
                    ?>
                </button>
                <ul class="dropdown-menu" style="max-height: 300px; overflow-y: auto;">
                    <li><a class="dropdown-item <?php echo empty($brandFilter) ? 'active' : '' ?>" href="?<?php echo http_build_query(array_merge($_GET, ['brand_idx' => ''])) ?>"><?php echo __('전체', 'manager') ?></a></li>
                    <li><hr class="dropdown-divider"></li>
                    <?php foreach ($brands as $b): ?>
                    <li><a class="dropdown-item <?php echo $brandFilter == $b->idx ? 'active' : '' ?>" href="?<?php echo http_build_query(array_merge($_GET, ['brand_idx' => $b->idx])) ?>"><?php echo htmlspecialchars($b->brand_name) ?> (<?php echo $b->country_code ?>)</a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- 국가 필터 -->
            <div class="dropdown">
                <button class="btn btn-sm <?php echo !empty($countryFilter) ? 'btn-primary' : 'btn-outline-secondary' ?> dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="ph-flag me-1"></i>
                    <?php echo !empty($countryFilter) ? ($countries[$countryFilter] ?? $countryFilter) : __('국가', 'manager') ?>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item <?php echo empty($countryFilter) ? 'active' : '' ?>" href="?<?php echo http_build_query(array_merge($_GET, ['country_code' => ''])) ?>"><?php echo __('전체', 'manager') ?></a></li>
                    <li><hr class="dropdown-divider"></li>
                    <?php foreach ($countries as $code => $name): ?>
                    <li><a class="dropdown-item <?php echo $countryFilter === $code ? 'active' : '' ?>" href="?<?php echo http_build_query(array_merge($_GET, ['country_code' => $code])) ?>"><?php echo $name ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- 세그먼트 필터 -->
            <div class="dropdown">
                <button class="btn btn-sm <?php echo !empty($segmentFilter) ? 'btn-primary' : 'btn-outline-secondary' ?> dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="ph-car me-1"></i>
                    <?php echo !empty($segmentFilter) ? ($segments[$segmentFilter] ?? $segmentFilter) : __('세그먼트', 'manager') ?>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item <?php echo empty($segmentFilter) ? 'active' : '' ?>" href="?<?php echo http_build_query(array_merge($_GET, ['segment' => ''])) ?>"><?php echo __('전체', 'manager') ?></a></li>
                    <li><hr class="dropdown-divider"></li>
                    <?php foreach ($segments as $code => $name): ?>
                    <li><a class="dropdown-item <?php echo $segmentFilter === $code ? 'active' : '' ?>" href="?<?php echo http_build_query(array_merge($_GET, ['segment' => $code])) ?>"><?php echo $name ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="card-body">
        <?php include ABSPATH . "/backoffice/modules/printNavigation.php"; ?>
    </div>

    <table class="table table-hover table-striped table-bordered table-xs nowrap font-080 w-100" data-paging="false" data-searching="false">
        <thead class="bg-dark text-light">
            <tr>
                <th class="text-center" width="60">IDX</th>
                <th class="text-center" width="80"><?php echo __('국가', 'manager') ?></th>
                <th class="text-center" width="120"><?php echo __('브랜드', 'manager') ?></th>
                <th class="text-center"><?php echo __('모델명', 'manager') ?></th>
                <th class="text-center" width="150"><?php echo __('모델명(영문)', 'manager') ?></th>
                <th class="text-center" width="100"><?php echo __('세그먼트', 'manager') ?></th>
                <th class="text-center" width="60"><?php echo __('정렬', 'manager') ?></th>
                <th class="text-center" width="80"><?php echo __('상태', 'manager') ?></th>
                <th class="text-center" width="140"><?php echo __('등록일', 'manager') ?></th>
                <th class="text-center" width="100"><?php echo __('관리', 'manager') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($res) && count($res) > 0):
                foreach ($res as $row): ?>
                <tr>
                    <td class="text-center"><?php echo $row->idx ?></td>
                    <td class="text-center">
                        <span class="badge bg-secondary"><?php echo $row->country_code ?></span>
                    </td>
                    <td class="text-center">
                        <a href="brand-edit?idx=<?php echo $row->brand_idx ?>" class="text-decoration-none">
                            <?php echo htmlspecialchars($row->brand_name) ?>
                        </a>
                    </td>
                    <td class="text-start">
                        <strong><?php echo htmlspecialchars($row->model_name) ?></strong>
                    </td>
                    <td class="text-center">
                        <?php echo htmlspecialchars($row->model_name_en) ?>
                    </td>
                    <td class="text-center">
                        <?php echo $segments[$row->segment] ?? $row->segment ?>
                    </td>
                    <td class="text-center"><?php echo $row->sort_order ?></td>
                    <td class="text-center">
                        <?php
                        $isActive = $row->is_active == 1;
                        $statusClass = $isActive ? 'bg-success' : 'bg-warning';
                        $statusText = $isActive ? __('활성', 'manager') : __('비활성', 'manager');
                        ?>
                        <button type="button" class="badge <?php echo $statusClass ?> border-0 cursor-pointer"
                            onclick="toggleActive(<?php echo $row->idx ?>, <?php echo $row->is_active ?>)"
                            title="<?php echo __('클릭하여 상태 변경', 'manager') ?>">
                            <?php echo $statusText ?>
                        </button>
                    </td>
                    <td class="text-center">
                        <?php echo date('Y-m-d H:i', strtotime($row->created_at)) ?>
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm" role="group">
                            <a class="dropdown-item" href="model-edit?idx=<?php echo $row->idx ?>" title="<?php echo __('수정', 'manager') ?>">
                                <i class="ph-pen me-2"></i>
                            </a>
                            <button type="button" class="dropdown-item text-danger" onclick="deleteModel(<?php echo $row->idx ?>, '<?php echo addslashes($row->model_name) ?>')" title="<?php echo __('삭제', 'manager') ?>">
                                <i class="ph-trash me-2"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; else: ?>
            <tr>
                <td colspan="10" class="text-center py-5">
                    <div class="text-muted mb-2"><i class="ph-car fs-1"></i></div>
                    <?php echo __('등록된 모델이 없습니다.', 'manager') ?>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="card-body">
        <?php include ABSPATH . "/backoffice/modules/printNavigation.php"; ?>
    </div>
</div>

<script>
// 활성 상태 변경
function toggleActive(idx, currentActive) {
    const newActive = currentActive == 1 ? 0 : 1;
    const statusText = newActive == 1 ? '<?php echo __('활성', 'manager') ?>' : '<?php echo __('비활성', 'manager') ?>';

    ExpertNote.Util.showMessage(
        '<?php echo __('상태를', 'manager') ?> "' + statusText + '"<?php echo __('으로 변경하시겠습니까?', 'manager') ?>',
        '<?php echo __('상태 변경', 'manager') ?>',
        [
            { title: '<?php echo __('취소', 'manager') ?>', class: 'btn btn-secondary', dismiss: true },
            {
                title: '<?php echo __('확인', 'manager') ?>',
                class: 'btn btn-primary',
                dismiss: true,
                click: `executeToggleActive(${idx}, ${newActive})`
            }
        ]
    );
}

function executeToggleActive(idx, newActive) {
    fetch('/api/arirent/model', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ idx: idx, is_active: newActive })
    })
    .then(response => response.json())
    .then(data => {
        if (data.result === 'SUCCESS') {
            location.reload();
        } else {
            ExpertNote.Util.showMessage(data.message || '<?php echo __('상태 변경에 실패했습니다.', 'manager') ?>', '<?php echo __('오류', 'manager') ?>', [{ title: '<?php echo __('확인', 'manager') ?>', class: 'btn btn-secondary', dismiss: true }]);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        ExpertNote.Util.showMessage('<?php echo __('오류가 발생했습니다.', 'manager') ?>', '<?php echo __('오류', 'manager') ?>', [{ title: '<?php echo __('확인', 'manager') ?>', class: 'btn btn-secondary', dismiss: true }]);
    });
}

// 모델 삭제
function deleteModel(idx, name) {
    ExpertNote.Util.showMessage(
        '<?php echo __('모델', 'manager') ?> "' + name + '"<?php echo __('을(를) 삭제하시겠습니까?', 'manager') ?>',
        '<?php echo __('모델 삭제', 'manager') ?>',
        [
            { title: '<?php echo __('취소', 'manager') ?>', class: 'btn btn-secondary', dismiss: true },
            {
                title: '<?php echo __('삭제', 'manager') ?>',
                class: 'btn btn-danger',
                dismiss: true,
                click: `executeDeleteModel(${idx})`
            }
        ]
    );
}

function executeDeleteModel(idx) {
    fetch('/api/arirent/model?idx=' + idx, {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.result === 'SUCCESS') {
            ExpertNote.Util.showMessage('<?php echo __('모델이 삭제되었습니다.', 'manager') ?>', '<?php echo __('성공', 'manager') ?>', [{ title: '<?php echo __('확인', 'manager') ?>', class: 'btn btn-primary', dismiss: true }], function() { location.reload(); });
        } else {
            ExpertNote.Util.showMessage(data.message || '<?php echo __('삭제에 실패했습니다.', 'manager') ?>', '<?php echo __('오류', 'manager') ?>', [{ title: '<?php echo __('확인', 'manager') ?>', class: 'btn btn-secondary', dismiss: true }]);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        ExpertNote.Util.showMessage('<?php echo __('오류가 발생했습니다.', 'manager') ?>', '<?php echo __('오류', 'manager') ?>', [{ title: '<?php echo __('확인', 'manager') ?>', class: 'btn btn-secondary', dismiss: true }]);
    });
}
</script>
