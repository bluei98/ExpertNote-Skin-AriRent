<?php
// 브랜드 목록 관리
$breadcrumb = ["rent", "brand-list"];
$_page_title = __('브랜드 목록', "manager");

// 검색 옵션 설정
$searchText = [
    "ALL" => __('전체 검색', 'manager'),
    "brand_name" => __('브랜드명', 'manager'),
    "brand_name_en" => __('브랜드명(영문)', 'manager')
];

$searchSort = [
    "idx" => __('ID', 'manager'),
    "brand_name" => __('브랜드명', 'manager'),
    "country_code" => __('국가', 'manager'),
    "sort_order" => __('정렬순서', 'manager'),
    "created_at" => __('등록일', 'manager')
];

$navigationButtons = array(
    array(
        "title" => __('브랜드 추가', 'manager'),
        "url" => "brand-edit",
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

// 국가 필터
$countryFilter = $_GET['country_code'] ?? '';
if ($countryFilter) {
    $searchWheres[] = "country_code = :country_code";
    $params['country_code'] = $countryFilter;
}

// 목록 조회
$countSql = "SELECT COUNT(*) as cnt FROM " . DB_PREFIX . "rent_brand";
if (count($searchWheres) > 0) {
    $countSql .= " WHERE " . implode(" AND ", $searchWheres);
}
$cntResult = \ExpertNote\DB::getRow($countSql, $params);
$cnt = $cntResult->cnt ?? 0;

list($paging, $pageRecord) = createPaging($cnt, $page, $pageCount, 20);

$listSql = "SELECT b.*,
    (SELECT COUNT(*) FROM " . DB_PREFIX . "rent_model WHERE brand_idx = b.idx) as model_count
FROM " . DB_PREFIX . "rent_brand b";

if (count($searchWheres) > 0) {
    $listSql .= " WHERE " . implode(" AND ", $searchWheres);
}

if (count($searchOrderBy) > 0) {
    $orderByWithPrefix = [];
    foreach ($searchOrderBy as $order) {
        if (strpos($order, '.') === false && strpos($order, '(') === false) {
            $order = preg_replace('/^(\w+)/', 'b.$1', $order);
        }
        $orderByWithPrefix[] = $order;
    }
    $listSql .= " ORDER BY " . implode(", ", $orderByWithPrefix);
} else {
    $listSql .= " ORDER BY b.sort_order ASC, b.idx ASC";
}

$listSql .= " LIMIT {$paging['viewStarRow']}, {$paging['viewCount']}";

$res = \ExpertNote\DB::getRows($listSql, $params);

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
?>

<div class="card">
    <div class="card-body">
        <?php include ABSPATH . "/backoffice/modules/printSearch.php"; ?>
        <!-- 국가 필터 -->
        <div class="d-flex gap-2 mt-2">
            <a href="?<?php echo http_build_query(array_merge($_GET, ['country_code' => ''])) ?>"
               class="btn btn-sm <?php echo empty($countryFilter) ? 'btn-primary' : 'btn-outline-secondary' ?>">
                <?php echo __('전체', 'manager') ?>
            </a>
            <?php foreach ($countries as $code => $name): ?>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['country_code' => $code])) ?>"
               class="btn btn-sm <?php echo $countryFilter === $code ? 'btn-primary' : 'btn-outline-secondary' ?>">
                <?php echo $name ?>
            </a>
            <?php endforeach; ?>
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
                <th class="text-center"><?php echo __('브랜드명', 'manager') ?></th>
                <th class="text-center" width="150"><?php echo __('브랜드명(영문)', 'manager') ?></th>
                <th class="text-center" width="80"><?php echo __('모델 수', 'manager') ?></th>
                <th class="text-center" width="80"><?php echo __('정렬', 'manager') ?></th>
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
                    <td class="text-start">
                        <strong><?php echo htmlspecialchars($row->brand_name) ?></strong>
                    </td>
                    <td class="text-center">
                        <?php echo htmlspecialchars($row->brand_name_en) ?>
                    </td>
                    <td class="text-center">
                        <a href="model-list?brand_idx=<?php echo $row->idx ?>" class="badge bg-primary">
                            <?php echo number_format($row->model_count) ?>
                        </a>
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
                            <a class="dropdown-item" href="brand-edit?idx=<?php echo $row->idx ?>" title="<?php echo __('수정', 'manager') ?>">
                                <i class="ph-pen me-2"></i>
                            </a>
                            <a class="dropdown-item" href="model-list?brand_idx=<?php echo $row->idx ?>" title="<?php echo __('모델 목록', 'manager') ?>">
                                <i class="ph-car me-2"></i>
                            </a>
                            <button type="button" class="dropdown-item text-danger" onclick="deleteBrand(<?php echo $row->idx ?>, '<?php echo addslashes($row->brand_name) ?>', <?php echo $row->model_count ?>)" title="<?php echo __('삭제', 'manager') ?>">
                                <i class="ph-trash me-2"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; else: ?>
            <tr>
                <td colspan="9" class="text-center py-5">
                    <div class="text-muted mb-2"><i class="ph-car-profile fs-1"></i></div>
                    <?php echo __('등록된 브랜드가 없습니다.', 'manager') ?>
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
    fetch('/api/arirent/brand', {
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

// 브랜드 삭제
function deleteBrand(idx, name, modelCount) {
    if (modelCount > 0) {
        ExpertNote.Util.showMessage(
            '<?php echo __('해당 브랜드에 모델이', 'manager') ?> ' + modelCount + '<?php echo __('개 등록되어 있어 삭제할 수 없습니다.', 'manager') ?>\n<?php echo __('먼저 모델을 삭제해주세요.', 'manager') ?>',
            '<?php echo __('삭제 불가', 'manager') ?>',
            [{ title: '<?php echo __('확인', 'manager') ?>', class: 'btn btn-secondary', dismiss: true }]
        );
        return;
    }

    ExpertNote.Util.showMessage(
        '<?php echo __('브랜드', 'manager') ?> "' + name + '"<?php echo __('을(를) 삭제하시겠습니까?', 'manager') ?>',
        '<?php echo __('브랜드 삭제', 'manager') ?>',
        [
            { title: '<?php echo __('취소', 'manager') ?>', class: 'btn btn-secondary', dismiss: true },
            {
                title: '<?php echo __('삭제', 'manager') ?>',
                class: 'btn btn-danger',
                dismiss: true,
                click: `executeDeleteBrand(${idx})`
            }
        ]
    );
}

function executeDeleteBrand(idx) {
    fetch('/api/arirent/brand?idx=' + idx, {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.result === 'SUCCESS') {
            ExpertNote.Util.showMessage('<?php echo __('브랜드가 삭제되었습니다.', 'manager') ?>', '<?php echo __('성공', 'manager') ?>', [{ title: '<?php echo __('확인', 'manager') ?>', class: 'btn btn-primary', dismiss: true }], function() { location.reload(); });
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
