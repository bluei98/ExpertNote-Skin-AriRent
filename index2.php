<?php
ExpertNote\Core::setLayout('v2');

// 페이지 메타 설정
$pageTitle = '아리렌트';
$pageSuffix = '무심사 저신용 신차 • 중고차 장기렌트';
$pageDescription = '아리렌트 무심사 장기렌트, 저신용, 6~10등급, 개인회생, 신용불량자도 초기 부담 걱정없이 출고 가능한 신차,중고차 전문업체';
$pageKeywords = implode(",", [
    '아리렌트',
    '저신용 신차 장기렌트',
    '무심사 신차 할부',
    '저신용 중고차 장기렌트',
    '무심사 중고차 할부',
    '저신용 렌트카',
    '신용불량자 무보증 장기렌트카'
]);

ExpertNote\Core::setPageTitle($pageTitle);
ExpertNote\Core::setPageSuffix($pageSuffix);
ExpertNote\Core::setPageDescription($pageDescription);
ExpertNote\Core::setPageKeywords($pageKeywords);

// Open Graph 메타 태그
\ExpertNote\Core::addMetaTag('og:type', ["property"=>"og:type", "content"=>"website"]);
\ExpertNote\Core::addMetaTag('og:title', ["property"=>"og:title", "content"=>$pageTitle . " - " . $pageSuffix]);
\ExpertNote\Core::addMetaTag('og:description', ["property"=>"og:description", "content"=>$pageDescription]);
\ExpertNote\Core::addMetaTag('og:url', ["property"=>"og:url", "content"=>ExpertNote\Core::getBaseUrl()]);
\ExpertNote\Core::addMetaTag('og:site_name', ["property"=>"og:site_name", "content"=>"아리렌트"]);

// 대표 이미지 (있는 경우)
// $ogImage = ExpertNote\Core::getBaseUrl() . "/skins/arirent/assets/images/og-image.jpg"; // 실제 이미지 경로로 변경 필요
// \ExpertNote\Core::addMetaTag('og:image', ["property"=>"og:image", "content"=>$ogImage]);
// \ExpertNote\Core::addMetaTag('og:image:width', ["property"=>"og:image:width", "content"=>"1200"]);
// \ExpertNote\Core::addMetaTag('og:image:height', ["property"=>"og:image:height", "content"=>"630"]);

// 트위터 카드 메타 태그
\ExpertNote\Core::addMetaTag('twitter:card', ["name"=>"twitter:card", "content"=>"summary_large_image"]);
\ExpertNote\Core::addMetaTag('twitter:title', ["name"=>"twitter:title", "content"=>$pageTitle . " - " . $pageSuffix]);
\ExpertNote\Core::addMetaTag('twitter:description', ["name"=>"twitter:description", "content"=>$pageDescription]);
\ExpertNote\Core::addMetaTag('twitter:url', ["name"=>"twitter:url", "content"=>ExpertNote\Core::getBaseUrl()]);
// \ExpertNote\Core::addMetaTag('twitter:image', ["name"=>"twitter:image", "content"=>$ogImage]);
?>