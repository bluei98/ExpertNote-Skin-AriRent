<?php
// 차량 목록 관리
$breadcrumb = ["rent", "car-list"];
$_page_title = __('차량 목록', "manager");

// 대리점 목록 조회
$dealersSql = "SELECT idx, dealer_code, dealer_name FROM " . DB_PREFIX . "rent_dealer ORDER BY dealer_name";
$dealers = \ExpertNote\DB::getRows($dealersSql);
$dealerMap = [];
foreach ($dealers as $d) {
    $dealerMap[$d->idx] = $d->dealer_name;
}

// 검색 옵션 설정
$searchText = [
    "car_number" => __('차량번호', 'manager'),
    "title" => __('차량명', 'manager'),
    "brand" => __('브랜드', 'manager'),
    "model" => __('모델', 'manager')
];

$dealerItems = ['ALL' => __('전체', 'manager')];
foreach ($dealers as $d) {
    $dealerItems[$d->idx] = $d->dealer_name;
}

$searchOption = [
    "dealer_idx" => [
        "title" => __('대리점', 'manager'),
        "item" => $dealerItems
    ],
    "car_type" => [
        "title" => __('차량유형', 'manager'),
        "item" => [
            'NEW' => __('신차', 'manager'),
            'USED' => __('중고차', 'manager')
        ]
    ],
    "status" => [
        "title" => __('상태', 'manager'),
        "item" => [
            'active' => __('판매중', 'manager'),
            'rented' => __('렌트중', 'manager'),
            'maintenance' => __('정비중', 'manager'),
            'deleted' => __('삭제됨', 'manager')
        ]
    ]
];

$searchSort = [
    "idx" => __('ID', 'manager'),
    "title" => __('차량명', 'manager'),
    "monthly_price" => __('월렌트료', 'manager'),
    "view_count" => __('조회수', 'manager'),
    "created_at" => __('등록일', 'manager')
];

$navigationButtons = array(
    array(
        "title" => __('차량 추가', 'manager'),
        "url" => "car-edit",
        "class" => "btn btn-primary",
        "icon" => "ph-plus"
    )
);

// 기본 정렬
if (!$_GET['orderby'])
    $orderby = array("idx DESC");

// 검색 조건 처리
include ABSPATH . "/backoffice/modules/processSearch.php";

$searchWheres = $searchWheres ?: [];
$searchOrderBy = $searchOrderBy ?: ["idx DESC"];
$params = [];

// GET 파라미터로 대리점 필터
if (isset($_GET['dealer_idx']) && !empty($_GET['dealer_idx']) && $_GET['dealer_idx'] !== 'ALL') {
    $searchWheres[] = "r.dealer_idx = :dealer_idx_filter";
    $params['dealer_idx_filter'] = $_GET['dealer_idx'];
}

// 목록 조회
$countSql = "SELECT COUNT(*) as cnt FROM " . DB_PREFIX . "rent r";
if (count($searchWheres) > 0) {
    $countSql .= " WHERE " . implode(" AND ", $searchWheres);
}
$cntResult = \ExpertNote\DB::getRow($countSql, $params);
$cnt = $cntResult->cnt ?? 0;

list($paging, $pageRecord) = createPaging($cnt, $page, $pageCount, 20);

$listSql = "SELECT r.*, d.dealer_name,
    (SELECT COUNT(*) FROM " . DB_PREFIX . "rent_images WHERE rent_idx = r.idx) as image_count,
    (SELECT COUNT(*) FROM " . DB_PREFIX . "rent_price WHERE rent_idx = r.idx) as price_count
FROM " . DB_PREFIX . "rent r
LEFT JOIN " . DB_PREFIX . "rent_dealer d ON r.dealer_idx = d.idx";

if (count($searchWheres) > 0) {
    $listSql .= " WHERE " . implode(" AND ", $searchWheres);
}

if (count($searchOrderBy) > 0) {
    $orderByWithPrefix = [];
    foreach ($searchOrderBy as $order) {
        if (strpos($order, '.') === false && strpos($order, '(') === false) {
            $order = preg_replace('/^(\w+)/', 'r.$1', $order);
        }
        $orderByWithPrefix[] = $order;
    }
    $listSql .= " ORDER BY " . implode(", ", $orderByWithPrefix);
} else {
    $listSql .= " ORDER BY r.idx DESC";
}

$listSql .= " LIMIT {$paging['viewStarRow']}, {$paging['viewCount']}";

$res = \ExpertNote\DB::getRows($listSql, $params);
?>

<div class="card">
    <div class="card-body">
        <?php include ABSPATH . "/backoffice/modules/printSearch.php"; ?>
    </div>
    <div class="card-body">
        <?php include ABSPATH . "/backoffice/modules/printNavigation.php"; ?>
    </div>

    <table class="table table-hover table-striped table-bordered table-xs nowrap datatables font-080 w-100"
        data-paging="false" data-searching="false">
        <thead class="bg-dark text-light">
            <tr>
                <th class="text-center" width="80"><?php echo __('이미지', 'manager') ?></th>
                <th class="text-center" width="80"><?php echo __('유형', 'manager') ?></th>
                <th class="text-center"><?php echo __('차량명', 'manager') ?></th>
                <th class="text-center" width="120"><?php echo __('차량번호', 'manager') ?></th>
                <th class="text-center" width="120"><?php echo __('대리점', 'manager') ?></th>
                <th class="text-center" width="100"><?php echo __('월렌트료', 'manager') ?></th>
                <th class="text-center" width="150"><?php echo __('상태', 'manager') ?></th>
                <th class="text-center" width="80"><?php echo __('조회수', 'manager') ?></th>
                <th class="text-center" width="120"><?php echo __('관리', 'manager') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($res) && count($res) > 0):
                foreach ($res as $row): ?>
                    <tr>
                        <td class="text-center p-1">
                            <?php if ($row->featured_image): ?>
                                <img src="<?php echo $row->featured_image ?>" style="width: 60px; height: 45px; object-fit: cover;" class="rounded">
                            <?php else: ?>
                                <div class="bg-light d-flex align-items-center justify-content-center rounded" style="width: 60px; height: 45px;">
                                    <i class="ph-car text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php
                            $typeClass = $row->car_type === 'NEW' ? 'bg-success' : 'bg-warning';
                            $typeText = $row->car_type === 'NEW' ? __('신차', 'manager') : __('중고', 'manager');
                            ?>
                            <span class="badge <?php echo $typeClass ?>"><?php echo $typeText ?></span>
                        </td>
                        <td class="text-start">
                            <div class="fw-bold text-truncate" style="max-width: 250px;" title="<?php echo htmlspecialchars($row->title) ?>">
                                <?php echo htmlspecialchars($row->title) ?>
                            </div>
                            <small class="text-muted">
                                <?php echo htmlspecialchars($row->brand) ?> <?php echo htmlspecialchars($row->model) ?>
                            </small>
                        </td>
                        <td class="text-center">
                            <code><?php echo htmlspecialchars($row->car_number) ?></code>
                        </td>
                        <td class="text-center">
                            <?php echo htmlspecialchars($row->dealer_name) ?>
                        </td>
                        <td class="text-end">
                            <?php if ($row->monthly_price): ?>
                                <?php echo number_format($row->monthly_price) ?><?php echo __('원', 'manager') ?>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <select class="form-select form-select-sm rounded-0 status-select"
                                data-idx="<?php echo $row->idx ?>"
                                onchange="changeStatus(this)">
                                <option value="active" <?php echo $row->status === 'active' ? 'selected' : '' ?>><?php echo __('판매중', 'manager') ?></option>
                                <option value="rented" <?php echo $row->status === 'rented' ? 'selected' : '' ?>><?php echo __('판매완료', 'manager') ?></option>
                                <option value="maintenance" <?php echo $row->status === 'maintenance' ? 'selected' : '' ?>><?php echo __('정비중', 'manager') ?></option>
                                <option value="deleted" <?php echo $row->status === 'deleted' ? 'selected' : '' ?>><?php echo __('삭제됨', 'manager') ?></option>
                            </select>
                        </td>
                        <td class="text-end">
                            <?php echo number_format($row->view_count) ?>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="car-edit?idx=<?php echo $row->idx ?>" class="btn btn-outline-primary"
                                    title="<?php echo __('수정', 'manager') ?>">
                                    <i class="ph-pencil-simple"></i>
                                </a>
                                <a href="images?rent_idx=<?php echo $row->idx ?>" class="btn btn-outline-secondary"
                                    title="<?php echo __('이미지', 'manager') ?> (<?php echo $row->image_count ?>)">
                                    <i class="ph-images"></i>
                                </a>
                                <a href="price?rent_idx=<?php echo $row->idx ?>" class="btn btn-outline-info"
                                    title="<?php echo __('가격', 'manager') ?> (<?php echo $row->price_count ?>)">
                                    <i class="ph-currency-krw"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger"
                                    onclick="deleteCar('<?php echo $row->idx ?>', '<?php echo addslashes($row->title) ?>')"
                                    title="<?php echo __('삭제', 'manager') ?>">
                                    <i class="ph-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="9" class="text-center py-5">
                        <div class="text-muted mb-2"><i class="ph-car fs-1"></i></div>
                        <?php echo __('등록된 차량이 없습니다.', 'manager') ?>
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
function changeStatus(selectEl) {
    const idx = selectEl.dataset.idx;
    const status = selectEl.value;

    fetch('/api/arirent/car', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ idx: idx, status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.result === 'SUCCESS') {
            // 상태에 따라 select 배경색 변경
            const colors = {
                'active': '#d1e7dd',
                'rented': '#cff4fc',
                'maintenance': '#fff3cd',
                'deleted': '#f8d7da'
            };
            selectEl.style.backgroundColor = colors[status] || '';
        } else {
            alert(data.message || '<?php echo __('상태 변경에 실패했습니다.', 'manager') ?>');
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('<?php echo __('오류가 발생했습니다.', 'manager') ?>');
        location.reload();
    });
}

function deleteCar(idx, name) {
    if (!confirm('차량 "' + name + '"을(를) 삭제하시겠습니까?')) {
        return;
    }

    fetch('/api/arirent/car?idx=' + idx, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.result === 'SUCCESS') {
            location.reload();
        } else {
            alert(data.message || '삭제에 실패했습니다.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('오류가 발생했습니다.');
    });
}

// 페이지 로드 시 현재 상태에 따른 배경색 적용
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.status-select').forEach(function(select) {
        const colors = {
            'active': '#d1e7dd',
            'rented': '#cff4fc',
            'maintenance': '#fff3cd',
            'deleted': '#f8d7da'
        };
        select.style.backgroundColor = colors[select.value] || '';
    });
});
</script>
