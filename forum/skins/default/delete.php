<div class="delete-container">
    <div class="delete-card">
        <div class="delete-header">
            <h1><?php echo __('게시글 삭제', 'skin') ?></h1>
        </div>

        <div class="delete-body">
            <div class="warning-icon">⚠️</div>

            <div class="delete-message">
                <h2><?php echo __('이 게시글을 삭제하시겠습니까?', 'skin') ?></h2>
                <p><?php echo __('삭제된 게시글은 복구할 수 없습니다.', 'skin') ?></p>
            </div>

            <div class="article-info">
                <div class="article-info-item">
                    <span class="article-info-label"><?php echo __('제목', 'skin') ?>:</span>
                    <span class="article-info-value"><?php echo htmlspecialchars($article->title) ?></span>
                </div>
                <div class="article-info-item">
                    <span class="article-info-label"><?php echo __('작성자', 'skin') ?>:</span>
                    <span class="article-info-value"><?php echo htmlspecialchars($article->nickname ?: $article->username) ?></span>
                </div>
                <div class="article-info-item">
                    <span class="article-info-label"><?php echo __('작성일', 'skin') ?>:</span>
                    <span class="article-info-value"><?php echo date('Y-m-d H:i', strtotime($article->write_time)) ?></span>
                </div>
                <?php if ($article->cnt_comments > 0): ?>
                <div class="article-info-item">
                    <span class="article-info-label"><?php echo __('댓글', 'skin') ?>:</span>
                    <span class="article-info-value"><?php echo $article->cnt_comments ?><?php echo __('개', 'skin') ?></span>
                </div>
                <?php endif; ?>
            </div>

            <?php if ($article->cnt_comments > 0): ?>
            <div class="warning-message">
                <strong><?php echo __('경고', 'skin') ?>:</strong>
                <?php echo __('이 게시글에는 댓글이 있습니다. 게시글을 삭제하면 모든 댓글도 함께 삭제됩니다.', 'skin') ?>
            </div>
            <?php endif; ?>

            <div class="delete-actions">
                <a href="/forum/<?php echo urlencode($forumCode) ?>/<?php echo urlencode($article->permlink) ?>-<?php echo $idx ?>" class="btn btn-secondary">
                    <?php echo __('취소', 'skin') ?>
                </a>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                    <?php echo __('삭제하기', 'skin') ?>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete() {
    if (!confirm('<?php echo __('정말 삭제하시겠습니까?', 'skin') ?>')) {
        return;
    }

    const deleteStatus = <?php echo $isAdmin ? "'DEL_ADMIN'" : "'DEL_USER'" ?>;

    fetch('/api/v1/forum/thread', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            idx: <?php echo $idx ?>,
            status: deleteStatus
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.result === 'SUCCESS') {
            alert('<?php echo __('게시글이 삭제되었습니다.', 'skin') ?>');
            location.href = '/forum/<?php echo urlencode($forumCode) ?>';
        } else {
            alert(data.message || '<?php echo __('삭제에 실패했습니다.', 'skin') ?>');
        }
    })
    .catch(err => {
        console.error(err);
        alert('<?php echo __('오류가 발생했습니다.', 'skin') ?>');
    });
}
</script>
