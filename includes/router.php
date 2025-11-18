<?php
ExpertNote\Router::set("/item/{idx}", "/car-view.php");


/**
 * Forum 라우팅 설정
 */
// 파일 다운로드: /forum/download/{file_idx}
ExpertNote\Router::set('/forum/download/{file_idx}', '/forum/download.php');
// 게시글 수정: /forum/{code}/edit/{idx} (먼저 체크)
// ExpertNote\Router::set('/forum/{code}/edit/{idx}', '/forum/edit.php');
// 게시글 삭제: /forum/{code}/delete/{idx}
// ExpertNote\Router::set('/forum/{code}/delete/{idx}', '/forum/delete.php');
// 카테고리 목록: /forum/{code}/category/{category-permlink}
ExpertNote\Router::set('/forum/{code}/category/{category}', '/forum/list.php');
// 페이징 목록: /forum/{code}/category/{category-permlink}
ExpertNote\Router::set('/forum/{code}/page/{page}', '/forum/list.php');
// 게시글 읽기: /forum/{code}/{permlink}-{idx}
ExpertNote\Router::set('/forum/{code}/{slug}', '/forum/view.php');
// 포럼 목록: /forum/{code} (마지막에 체크)
ExpertNote\Router::set('/forum/{code}', '/forum/list.php');