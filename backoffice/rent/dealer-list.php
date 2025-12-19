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

// 기본 정렬 (PUBLISHED 먼저)
if (!$_GET['orderby'])
    $orderby = array("status DESC", "idx DESC");

// 검색 조건 처리
include ABSPATH . "/backoffice/modules/processSearch.php";

$searchWheres = $searchWheres ?: [];
$searchOrderBy = $searchOrderBy ?: ["status DESC", "idx DESC"];
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

    <table class="table table-hover table-striped table-bordered table-xs nowrap  font-080 w-100" data-paging="false" data-searching="false">
        <thead class="bg-dark text-light">
            <tr>
                <th class="text-center" width="80">IDX</th>
                <th class="text-center" width="150"><?php echo __('대리점 코드', 'manager') ?></th>
                <th class="text-center"><?php echo __('대리점명', 'manager') ?></th>
                <th class="text-center" width="80"><?php echo __('상태', 'manager') ?></th>
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
                            <?php
                            $status = $row->status ?? 'DRAFT';
                            $statusClass = $status === 'PUBLISHED' ? 'bg-success' : 'bg-warning';
                            $statusText = $status === 'PUBLISHED' ? __('발행', 'manager') : __('임시', 'manager');
                            ?>
                            <button type="button" class="badge <?php echo $statusClass ?> border-0 cursor-pointer"
                                onclick="toggleStatus(<?php echo $row->idx ?>, '<?php echo $status ?>')"
                                title="<?php echo __('클릭하여 상태 변경', 'manager') ?>">
                                <?php echo $statusText ?>
                            </button>
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
                            <div class="btn-group btn-group-sm" role="group">
                                <a class="dropdown-item" href="dealer-edit?idx=<?php echo $row->idx ?>" title="<?php echo __('수정', 'manager') ?>">
                                    <i class="ph-pen me-2"></i>
                                </a>
                                <button type="button" class="dropdown-item <?php echo $status === 'PUBLISHED' ? 'text-warning' : 'text-success' ?>" onclick="toggleStatus(<?php echo $row->idx ?>, '<?php echo $status ?>')" title="<?php echo $status === 'PUBLISHED' ? __('임시저장으로 변경', 'manager') : __('발행으로 변경', 'manager') ?>">
                                    <i class="ph-<?php echo $status === 'PUBLISHED' ? 'eye-slash' : 'eye' ?> me-2"></i>
                                </button>
                                <button type="button" class="dropdown-item text-danger" onclick="deleteDealer('<?php echo $row->idx ?>', '<?php echo addslashes($row->dealer_name) ?>')" title="<?php echo __('삭제', 'manager') ?>">
                                    <i class="ph-trash me-2"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="7" class="text-center py-5">
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
// 상태 변경
function toggleStatus(idx, currentStatus) {
    const newStatus = currentStatus === 'PUBLISHED' ? 'DRAFT' : 'PUBLISHED';
    const statusText = newStatus === 'PUBLISHED' ? '<?php echo __('발행', 'manager') ?>' : '<?php echo __('임시저장', 'manager') ?>';

    ExpertNote.Util.showMessage(
        '<?php echo __('상태를', 'manager') ?> "' + statusText + '"<?php echo __('으로 변경하시겠습니까?', 'manager') ?>',
        '<?php echo __('상태 변경', 'manager') ?>',
        [
            { title: '<?php echo __('취소', 'manager') ?>', class: 'btn btn-secondary', dismiss: true },
            {
                title: '<?php echo __('확인', 'manager') ?>',
                class: 'btn btn-primary',
                dismiss: true,
                click: `executeToggleStatus(${idx}, '${newStatus}')`
            }
        ]
    );
}

function executeToggleStatus(idx, newStatus) {
    fetch('/api/arirent/dealer', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ idx: idx, status: newStatus })
    })
    .then(response => response.json())
    .then(data => {
        if (data.result === 'SUCCESS') {
            location.reload();
        } else {
            ExpertNote.Util.showMessage(
                data.message || '<?php echo __('상태 변경에 실패했습니다.', 'manager') ?>',
                '<?php echo __('오류', 'manager') ?>',
                [
                    { title: '<?php echo __('확인', 'manager') ?>', class: 'btn btn-secondary', dismiss: true }
                ]
            );
        }
    })
    .catch(error => {
        console.error('Error:', error);
        ExpertNote.Util.showMessage(
            '<?php echo __('오류가 발생했습니다.', 'manager') ?>',
            '<?php echo __('오류', 'manager') ?>',
            [
                { title: '<?php echo __('확인', 'manager') ?>', class: 'btn btn-secondary', dismiss: true }
            ]
        );
    });
}

// 대리점 삭제
function deleteDealer(idx, name) {
    ExpertNote.Util.showMessage(
        '<?php echo __('대리점', 'manager') ?> "' + name + '"<?php echo __('을(를) 삭제하시겠습니까?', 'manager') ?>\n\n<?php echo __('주의: 해당 대리점의 모든 차량 정보도 함께 삭제됩니다.', 'manager') ?>',
        '<?php echo __('대리점 삭제', 'manager') ?>',
        [
            { title: '<?php echo __('취소', 'manager') ?>', class: 'btn btn-secondary', dismiss: true },
            {
                title: '<?php echo __('삭제', 'manager') ?>',
                class: 'btn btn-danger',
                dismiss: true,
                click: `executeDeleteDealer(${idx})`
            }
        ]
    );
}

function executeDeleteDealer(idx) {
    fetch('/api/arirent/dealer?idx=' + idx, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.result === 'SUCCESS') {
            ExpertNote.Util.showMessage(
                '<?php echo __('대리점이 삭제되었습니다.', 'manager') ?>',
                '<?php echo __('성공', 'manager') ?>',
                [
                    {
                        title: '<?php echo __('확인', 'manager') ?>',
                        class: 'btn btn-primary',
                        dismiss: true
                    }
                ],
                function() {
                    location.reload();
                }
            );
        } else {
            ExpertNote.Util.showMessage(
                data.message || '<?php echo __('삭제에 실패했습니다.', 'manager') ?>',
                '<?php echo __('오류', 'manager') ?>',
                [
                    { title: '<?php echo __('확인', 'manager') ?>', class: 'btn btn-secondary', dismiss: true }
                ]
            );
        }
    })
    .catch(error => {
        console.error('Error:', error);
        ExpertNote.Util.showMessage(
            '<?php echo __('오류가 발생했습니다.', 'manager') ?>',
            '<?php echo __('오류', 'manager') ?>',
            [
                { title: '<?php echo __('확인', 'manager') ?>', class: 'btn btn-secondary', dismiss: true }
            ]
        );
    });
}
</script>
