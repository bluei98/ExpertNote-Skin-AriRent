<?php
$sitemapSkinIndex = [];

$rentCount = ExpertNote\DB::getVar("SELECT count(idx) FROM " . DB_PREFIX . "rent WHERE status IN ('active', 'rented')");
$rentPages = ceil($rentCount/1000);
for($i=1; $i<=$rentPages; $i++) {
    $sitemapSkinIndex[] = [
        "loc" => ExpertNote\Core::getBaseUrl() . "/sitemap-rent-{$i}.xml",
        "lastmod" => date("c", strtotime(ExpertNote\DB::getVar("SELECT MAX(updated_at) FROM " . DB_PREFIX . "rent WHERE status IN ('active', 'rented')"))) ?: date("c")
    ];
}

$sitemapIndex = array_merge($sitemapIndex, $sitemapSkinIndex);