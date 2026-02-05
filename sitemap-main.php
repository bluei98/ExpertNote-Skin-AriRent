<?php
$urls = [
    [
        "loc" => ExpertNote\Core::getBaseUrl()."/company",
        "changefreq" => "daily",
        "priority" => 0.9,
        "lastmod" => date("c", strtotime($item->updated_at))
    ],
    [
        "loc" => ExpertNote\Core::getBaseUrl()."/how-to-contract",
        "changefreq" => "daily",
        "priority" => 0.9,
        "lastmod" => date("c", strtotime($item->updated_at))
    ],
    [
        "loc" => ExpertNote\Core::getBaseUrl()."/faq",
        "changefreq" => "daily",
        "priority" => 0.9,
        "lastmod" => date("c", strtotime($item->updated_at))
    ],
    [
        "loc" => ExpertNote\Core::getBaseUrl()."/terms",
        "changefreq" => "daily",
        "priority" => 0.9,
        "lastmod" => date("c", strtotime($item->updated_at))
    ],
    [
        "loc" => ExpertNote\Core::getBaseUrl()."/privacy",
        "changefreq" => "daily",
        "priority" => 0.9,
        "lastmod" => date("c", strtotime($item->updated_at))
    ],
    [
        "loc" => ExpertNote\Core::getBaseUrl()."/car/new",
        "changefreq" => "daily",
        "priority" => 0.9,
        "lastmod" => date("c", strtotime($item->updated_at))
    ],
    [
        "loc" => ExpertNote\Core::getBaseUrl()."/car/new/updated",
        "changefreq" => "daily",
        "priority" => 0.9,
        "lastmod" => date("c", strtotime($item->updated_at))
    ],
    [
        "loc" => ExpertNote\Core::getBaseUrl()."/car/table/new",
        "changefreq" => "daily",
        "priority" => 0.9,
        "lastmod" => date("c", strtotime($item->updated_at))
    ],
    [
        "loc" => ExpertNote\Core::getBaseUrl()."/car/used/updated",
        "changefreq" => "daily",
        "priority" => 0.9,
        "lastmod" => date("c", strtotime($item->updated_at))
    ],
    [
        "loc" => ExpertNote\Core::getBaseUrl()."/car/table/used",
        "changefreq" => "daily",
        "priority" => 0.9,
        "lastmod" => date("c", strtotime($item->updated_at))
    ],
];