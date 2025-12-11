<?php
/**
 * YouTube 영상 사이트맵
 */

$sql = "SELECT y.idx, y.youtube_video_id, y.published_at, y.updated_at,
        COALESCE(l1.title, l2.title, y.channel_title) as title
    FROM " . DB_PREFIX . "youtube y
    LEFT JOIN " . DB_PREFIX . "youtubeLocale l1
        ON y.idx = l1.youtube_idx
        AND l1.locale = SUBSTRING(y.default_audio_language, 1, 2)
    LEFT JOIN " . DB_PREFIX . "youtubeLocale l2
        ON y.idx = l2.youtube_idx
        AND l2.locale = 'en'
    WHERE y.status = 'PUBLISHED'
    ORDER BY y.published_at DESC
    LIMIT $start, 1000 ";
$items = ExpertNote\DB::getRows($sql);

if (is_array($items)) {
    foreach($items as $item) {
        // 영상 제목으로 퍼머링크 생성
        $permalink = \ExpertNote\Utils::getPermaLink($item->title, true);

        $urls[] = [
            "loc" => ExpertNote\Core::getBaseUrl() . "/video/{$item->idx}/{$permalink}",
            "changefreq" => "weekly",
            "priority" => 0.8,
            "lastmod" => date("c", strtotime($item->updated_at ?: $item->published_at))
        ];
    }
}
