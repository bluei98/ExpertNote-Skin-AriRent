<?php
$sitemapSkinIndex = [];

// 렌트 차량 사이트맵
$rentCount = ExpertNote\DB::getVar("SELECT count(idx) FROM " . DB_PREFIX . "rent WHERE dealer_idx=1 AND status IN ('active', 'rented')");
$rentPages = ceil($rentCount/1000);
for($i=1; $i<=$rentPages; $i++) {
    $sitemapSkinIndex[] = [
        "loc" => ExpertNote\Core::getBaseUrl() . "/sitemap-rent-{$i}.xml",
        "lastmod" => date("c", strtotime(ExpertNote\DB::getVar("SELECT MAX(updated_at) FROM " . DB_PREFIX . "rent WHERE status IN ('active', 'rented')"))) ?: date("c")
    ];
}

// YouTube 영상 사이트맵
$videoCount = ExpertNote\DB::getVar("SELECT count(idx) FROM " . DB_PREFIX . "youtube WHERE status = 'PUBLISHED'");
$videoPages = ceil($videoCount/1000);
for($i=1; $i<=$videoPages; $i++) {
    $sitemapSkinIndex[] = [
        "loc" => ExpertNote\Core::getBaseUrl() . "/sitemap-video-{$i}.xml",
        "lastmod" => date("c", strtotime(ExpertNote\DB::getVar("SELECT MAX(COALESCE(updated_at, published_at)) FROM " . DB_PREFIX . "youtube WHERE status = 'PUBLISHED'"))) ?: date("c")
    ];
}

$sitemapIndex = array_merge($sitemapIndex, $sitemapSkinIndex);