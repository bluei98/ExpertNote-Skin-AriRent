<?php
// 대리점 목록 관리
$breadcrumb = ["rent", "dealer-list"];
$_page_title = __('대리점 목록', "manager");

// 검색 옵션 설정
$searchText = [
    "ALL" => __('전체 검색', 'manager'),
    "dealer_code" => __('대리점 코드', 'manager'),
    "dealer_name" => __('대리점명', 'manager')
];

$searchSort = [
    "idx" => __('ID', 'manager'),
    "dealer_code" => __('대리점 코드', 'manager'),
    "dealer_name" => __('대리점명', 'manager'),
    "created_at" => __('등록일', 'manager')
];

$navigationButtons = array(
    array(
        "title" => __('대리점 추가', 'manager'),
        "url" => "dealer-edit",
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

// 목록 조회
$countSql = "SELECT COUNT(*) as cnt FROM " . DB_PREFIX . "rent_dealer";
if (count($searchWheres) > 0) {
    $countSql .= " WHERE " . implode(" AND ", $searchWheres);
}
$cntResult = \ExpertNote\DB::getRow($countSql, $params);
$cnt = $cntResult->cnt ?? 0;

list($paging, $pageRecord) = createPaging($cnt, $page, $pageCount, 20);

$listSql = "SELECT d.*,
    (SELECT COUNT(*) FROM " . DB_PREFIX . "rent WHERE dealer_idx = d.idx) as car_count
FROM " . DB_PREFIX . "rent_dealer d";

if (count($searchWheres) > 0) {
    $listSql .= " WHERE " . implode(" AND ", $searchWheres);
}

if (count($searchOrderBy) > 0) {
    $orderByWithPrefix = [];
    foreach ($searchOrderBy as $order) {
        if (strpos($order, '.') === false && strpos($order, '(') === false) {
            $order = preg_replace('/^(\w+)/', 'd.$1', $order);
        }
        $orderByWithPrefix[] = $order;
    }
    $listSql .= " ORDER BY " . implode(", ", $orderByWithPrefix);
} else {
    $listSql .= " ORDER BY d.idx DESC";
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
                <th class="text-center" width="80">IDX</th>
                <th class="text-center" width="150"><?php echo __('대리점 코드', 'manager') ?></th>
                <th class="text-center"><?php echo __('대리점명', 'manager') ?></th>
                <th class="text-center" width="100"><?php echo __('차량 수', 'manager') ?></th>
                <th class="text-center" width="150"><?php echo __('등록일', 'manager') ?></th>
                <th class="text-center" width="120"><?php echo __('관리', 'manager') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($res) && count($res) > 0):
                foreach ($res as $row): ?>
                    <tr>
                        <td class="text-center"><?php echo $row->idx ?></td>
                        <td class="text-center">
                            <code><?php echo htmlspecialchars($row->dealer_code) ?></code>
                        </td>
                        <td class="text-start">
                            <strong><?php echo htmlspecialchars($row->dealer_name) ?></strong>
                        </td>
                        <td class="text-center">
                            <a href="car-list?dealer_idx=<?php echo $row->idx ?>" class="badge bg-primary">
                                <?php echo number_format($row->car_count) ?>
                            </a>
                        </td>
                        <td class="text-center">
                            <?php echo date('Y-m-d H:i', strtotime($row->created_at)) ?>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="dealer-edit?idx=<?php echo $row->idx ?>" class="btn btn-outline-primary"
                                    title="<?php echo __('수정', 'manager') ?>">
                                    <i class="ph-pencil-simple"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger"
                                    onclick="deleteDealer('<?php echo $row->idx ?>', '<?php echo addslashes($row->dealer_name) ?>')"
                                    title="<?php echo __('삭제', 'manager') ?>">
                                    <i class="ph-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="text-muted mb-2"><i class="ph-buildings fs-1"></i></div>
                        <?php echo __('등록된 대리점이 없습니다.', 'manager') ?>
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
function deleteDealer(idx, name) {
    if (!confirm('대리점 "' + name + '"을(를) 삭제하시겠습니까?\n\n주의: 해당 대리점의 모든 차량 정보도 함께 삭제됩니다.')) {
        return;
    }

    fetch('/api/v1/rent/dealer?idx=' + idx, {
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
</script>
