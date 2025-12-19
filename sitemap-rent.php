<?php 
$sql = "SELECT r.idx, r.updated_at FROM expertnote_rent r
        INNER JOIN expertnote_rent_dealer d ON r.dealer_idx = d.idx AND d.status = 'PUBLISHED'
        WHERE r.status IN ('active', 'rented') ORDER BY r.idx ASC LIMIT $start, 1000";
$items = ExpertNote\DB::getRows($sql);
if (is_array($items)) {
    foreach($items as $item) {
        $urls[] = [
            "loc" => ExpertNote\Core::getBaseUrl()."/item/{$item->idx}",
            "changefreq" => "daily",
            "priority" => 0.9,
            "lastmod" => date("c", strtotime($item->updated_at))
        ];
    }
}