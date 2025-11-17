<?php 
$sql = "SELECT idx,updated_at FROM expertnote_rent WHERE dealer_idx='1' AND `status` IN ('active', 'rented') LIMIT $start, 1000";
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