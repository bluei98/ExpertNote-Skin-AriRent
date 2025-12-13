<?php
$_menu_admin[998] = [
		"title" => __('렌트 관리', 'manager'),
        "permit" => ["SUPERADMIN", "ADMIN"],
		"submenu" => null,

];
$_menu_admin[999] = [
    "breadcrumb" => "rent",
    "title" => __('렌트 관리', 'manager'),
    "url" => "#",
    "icon" => "ph-car",
    "permit" => ["ADMIN", "SUPERADMIN"],
    "submenu" => [
        101 => [
            "breadcrumb" => "car-list",
            "title" => __('차량 목록', 'manager'),
            "url" => "/backoffice/rent/car-list",
            "permit" => ["ADMIN", "SUPERADMIN"],
        ],
        102 => [
            "breadcrumb" => "car-edit",
            "title" => __('차량 등록', 'manager'),
            "url" => "/backoffice/rent/car-edit",
            "permit" => ["ADMIN", "SUPERADMIN"],
        ],
        103 => [
            "breadcrumb" => "dealer-list",
            "title" => __('대리점 관리', 'manager'),
            "url" => "/backoffice/rent/dealer-list",
            "permit" => ["ADMIN", "SUPERADMIN"],
        ],
        104 => [
            "breadcrumb" => "insurance-edit",
            "title" => __('보험 설정', 'manager'),
            "url" => "/backoffice/rent/insurance-edit",
            "permit" => ["ADMIN", "SUPERADMIN"],
        ],
        105 => [
            "breadcrumb" => "wishlist",
            "title" => __('찜하기 목록', 'manager'),
            "url" => "/backoffice/rent/wishlist",
            "permit" => ["ADMIN", "SUPERADMIN"],
        ],
    ],
];