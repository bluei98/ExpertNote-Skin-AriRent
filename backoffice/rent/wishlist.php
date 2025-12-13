<?php
// 찜하기 목록 관리
$breadcrumb = ["rent", "wishlist"];
$_page_title = __('찜하기 목록', "manager");

// 검색 옵션 설정
$searchText = [
    "ALL" => __('전체 검색', 'manager'),
    "ip_address" => __('IP 주소', 'manager'),
    "user_id" => __('사용자 ID', 'manager')
];

$searchSort = [
    "idx" => __('ID', 'manager'),
    "created_at" => __('등록일', 'manager')
];

// 기본 정렬
if (!$_GET['orderby'])
    $orderby = array("idx DESC");

// 검색 조건 처리
include ABSPATH . "/backoffice/modules/processSearch.php";

$searchWheres = $searchWheres ?: [];
$searchOrderBy = $searchOrderBy ?: ["idx DESC"];
$params = [];

// 특정 차량 필터
if (isset($_GET['rent_idx']) && !empty($_GET['rent_idx'])) {
    $searchWheres[] = "w.rent_idx = :rent_idx";
    $params['rent_idx'] = $_GET['rent_idx'];
}

// 목록 조회
$countSql = "SELECT COUNT(*) as cnt FROM " . DB_PREFIX . "rent_wishlist w";
if (count($searchWheres) > 0) {
    $countSql .= " WHERE " . implode(" AND ", $searchWheres);
}
$cntResult = \ExpertNote\DB::getRow($countSql, $params);
$cnt = $cntResult->cnt ?? 0;

list($paging, $pageRecord) = createPaging($cnt, $page, $pageCount, 20);

$listSql = "SELECT w.*, r.title as car_title, r.car_number, r.image as car_image
FROM " . DB_PREFIX . "rent_wishlist w
LEFT JOIN " . DB_PREFIX . "rent r ON w.rent_idx = r.idx";

if (count($searchWheres) > 0) {
    $listSql .= " WHERE " . implode(" AND ", $searchWheres);
}

if (count($searchOrderBy) > 0) {
    $orderByWithPrefix = [];
    foreach ($searchOrderBy as $order) {
        if (strpos($order, '.') === false && strpos($order, '(') === false) {
            $order = preg_replace('/^(\w+)/', 'w.$1', $order);
        }
        $orderByWithPrefix[] = $order;
    }
    $listSql .= " ORDER BY " . implode(", ", $orderByWithPrefix);
} else {
    $listSql .= " ORDER BY w.idx DESC";
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
                <th class="text-center" width="80"><?php echo __('이미지', 'manager') ?></th>
                <th class="text-center"><?php echo __('차량', 'manager') ?></th>
                <th class="text-center" width="150"><?php echo __('IP 주소', 'manager') ?></th>
                <th class="text-center" width="120"><?php echo __('사용자 ID', 'manager') ?></th>
                <th class="text-center" width="150"><?php echo __('등록일', 'manager') ?></th>
                <th class="text-center" width="80"><?php echo __('관리', 'manager') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($res) && count($res) > 0):
                foreach ($res as $row): ?>
                    <tr>
                        <td class="text-center"><?php echo $row->idx ?></td>
                        <td class="text-center p-1">
                            <?php if ($row->car_image): ?>
                                <img src="<?php echo $row->car_image ?>" style="width: 60px; height: 45px; object-fit: cover;" class="rounded">
                            <?php else: ?>
                                <div class="bg-light d-flex align-items-center justify-content-center rounded" style="width: 60px; height: 45px;">
                                    <i class="ph-car text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="text-start">
                            <div class="fw-bold"><?php echo htmlspecialchars($row->car_title ?? '-') ?></div>
                            <small class="text-muted"><?php echo htmlspecialchars($row->car_number ?? '-') ?></small>
                        </td>
                        <td class="text-center">
                            <code><?php echo htmlspecialchars($row->ip_address) ?></code>
                        </td>
                        <td class="text-center">
                            <?php echo $row->user_id ? htmlspecialchars($row->user_id) : '<span class="text-muted">-</span>' ?>
                        </td>
                        <td class="text-center">
                            <?php echo date('Y-m-d H:i', strtotime($row->created_at)) ?>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-outline-danger btn-sm"
                                onclick="deleteWishlist('<?php echo $row->idx ?>')"
                                title="<?php echo __('삭제', 'manager') ?>">
                                <i class="ph-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div class="text-muted mb-2"><i class="ph-heart fs-1"></i></div>
                        <?php echo __('찜하기 기록이 없습니다.', 'manager') ?>
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
function deleteWishlist(idx) {
    if (!confirm('이 찜하기 기록을 삭제하시겠습니까?')) {
        return;
    }

    fetch('/api/v1/rent/wishlist?idx=' + idx, {
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
