<?php
$listParams = [
    "/forum",
    htmlspecialchars($article->forum_code),
];
$listQueryParams = [];
if($_GET['category']) {
    $listParams[] = "category";
    $listParams[] = ExpertNote\Forum\Thread::slugify($_GET['category']);
}
if(isset($_GET['q'])) $listQueryParams['q'] = $_GET['q'];
if(isset($_GET['page'])) $listQueryParams['page'] = $_GET['page'];
$listPathStr = implode("/", $listParams);
$listPathQueryStr = count($listQueryParams) > 0 ? "?".http_build_query($listQueryParams) : "";

// 연관 포스트 검색 (현재 글 제목 기반)
$sql = "SELECT f.idx, f.forum_code, f.title,
        MATCH(f.title, f.contents) AGAINST(:search_term IN NATURAL LANGUAGE MODE) AS score
        FROM expertnote_forum f
        WHERE f.forum_code = :forum_code
            AND f.locale = :locale
            AND f.status = 'PUBLISHED'
            AND f.idx != :current_idx
            AND MATCH(f.title, f.contents) AGAINST(:search_term IN NATURAL LANGUAGE MODE)
        ORDER BY score DESC
        LIMIT 5";

$realtedThreads = ExpertNote\DB::getRows($sql, [
    'search_term' => $article->title,
    'forum_code' => $article->forum_code,
    'locale' => $article->locale,
    'current_idx' => $article->idx
]);
?>
<style>
/* 소셜 공유 버튼 스타일 */
.social-share-buttons {
    display: flex;
    gap: 8px;
    align-items: center;
}

.share-btn {
    width: 40px;
    height: 40px;
    border: 1px solid #E6E9EB;
    background: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 18px;
    color: #202429;
}

.share-btn:hover {
    background: #f8f9fa;
    border-color: #D85D4E;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.share-btn:active {
    transform: translateY(0);
}

/* 각 소셜 버튼 색상 */
.share-btn:hover .bi-facebook {
    color: #1877F2;
}

.share-btn:hover .bi-twitter-x {
    color: #000000;
}

.share-btn:hover .bi-linkedin {
    color: #0A66C2;
}

.share-btn:hover .bi-chat-fill {
    color: #FEE500 !important;
}

.share-btn:hover .bi-link-45deg {
    color: #D85D4E;
}

/* 반응형 디자인 */
@media (max-width: 768px) {
    .post-footer {
        flex-wrap: wrap;
        gap: 12px;
    }

    .social-share-buttons {
        order: 2;
        width: 100%;
        justify-content: center;
    }

    .share-btn {
        width: 36px;
        height: 36px;
        font-size: 16px;
    }
}
</style>

<script type="application/ld+json"><?php echo json_encode($structuredData, JSON_PRETTY_PRINT)?></script>
<div class="container-xl">
    <!-- Breadcrumb -->
    <div class="forum-breadcrumb mt-3">
        <span class="me-2"><i class="bi bi-house-door-fill"></i></span>
        <a href="/"><?php echo  __('홈', 'skin') ?></a>
        <span class="mx-2"><i class="bi bi-chevron-double-right"></i></span>
        <a href="/forum/<?php echo  urlencode($article->forum_code) ?>"><?php echo  htmlspecialchars($forumConfig->forum_title) ?></a>
        <span class="mx-2"><i class="bi bi-chevron-double-right"></i></span>
        <span><?php echo  __('게시글', 'skin') ?></span>
    </div>

    <!-- 게시글 -->
    <div class="forum-post">
        <div class="post-header">
            <h1 class="post-title"><?php echo  $article->title ?></h1>
            <div class="post-meta">
                <div class="post-author">
                    <strong><?php echo  htmlspecialchars($article->nickname ?: $article->username) ?></strong>
                    <span>•</span>
                    <span><?php echo  date('Y-m-d H:i', strtotime($article->write_time)) ?></span>
                </div>
                <div class="post-stats">
                    <span><i class="bi bi-eye-fill me-2"></i> <?php echo  number_format($article->cnt_view) ?></span>
                    <span><i class="bi bi-hand-thumbs-up-fill me-2"></i> <?php echo  number_format($article->cnt_like) ?></span>
                    <span><i class="bi bi-chat-fill me-2"></i> <?php echo  number_format($article->cnt_comments) ?></span>
                </div>
            </div>
<?php if(count($files) > 0):?>
                <div class="d-flex mt-2">
                    <div class="me-3">
                        <?php echo __('첨부파일', 'skin')?>
                    </div>
                    <div class="file-list">
                        <?php foreach ($files as $file):?>
                            <div class="file-item d-flex align-items-center">
                                <i class="bi bi-file-earmark me-2"></i>
                                <a href="/forum/download/<?php echo $file->idx ?>" class="text-decoration-none">
                                    <?php echo htmlspecialchars($file->real_name) ?>
                                </a>
                                <small class="text-muted ms-2">
                                    (<?php echo ExpertNote\Utils::convertFileSize($file->size) ?>, <?php echo __('다운로드', 'skin')?> <?php echo sprintf(__('%s회', 'skin'), number_format($file->dncount))?>)
                                </small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
<?php endif;?>
        </div>

        <div class="post-body">
            <?php echo  $article->contents ?>
            <?php if(count($realtedThreads) > 0):?>
            <h2><?php echo __('연관 포스트', 'skin')?></h2>
            <ul>
                <?php foreach($realtedThreads as $row):?>
                <li><a href="<?php echo "/forum/{$row->forum_code}/".ExpertNote\Forum\Thread::getPermalink($row->idx, $row->title)?>"><?php echo $row->title?></a></li>
                <?php endforeach;?>
            </ul>
            <?php endif;?>
        </div>

        <div class="post-actions justify-content-center my-3 mb-5">
            <button class="btn-like" onclick="toggleLike(<?php echo  $idx ?>, 'LIKE')">
                <i class="bi bi-hand-thumbs-up-fill me-2"></i><?php echo  __('좋아요', 'skin') ?> (<span id="like-count-<?php echo  $idx ?>"><?php echo  number_format($article->cnt_like) ?></span>)
            </button>
            <button class="btn-dislike" onclick="toggleLike(<?php echo  $idx ?>, 'DISLIKE')">
                <i class="bi bi-hand-thumbs-down-fill me-2"></i><?php echo  __('싫어요', 'skin') ?> (<span id="dislike-count-<?php echo  $idx ?>"><?php echo  number_format($article->cnt_dislike) ?></span>)
            </button>
        </div>

        <div class="post-footer">
            <div class="btn-group">
                <a href="<?php echo $listPathStr?><?php echo $listPathQueryStr?>" class="btn">
                    <?php echo  __('목록', 'skin') ?>
                </a>
            </div>

            <!-- 소셜 공유 버튼 -->
            <div class="social-share-buttons">
                <button class="share-btn" onclick="shareToFacebook()" title="<?php echo __('페이스북에 공유', 'skin') ?>">
                    <i class="bi bi-facebook"></i>
                </button>
                <button class="share-btn" onclick="shareToTwitter()" title="<?php echo __('트위터에 공유', 'skin') ?>">
                    <i class="bi bi-twitter-x"></i>
                </button>
                <button class="share-btn" onclick="shareToLinkedIn()" title="<?php echo __('링크드인에 공유', 'skin') ?>">
                    <i class="bi bi-linkedin"></i>
                </button>
                <button class="share-btn" onclick="shareToKakao()" title="<?php echo __('카카오톡에 공유', 'skin') ?>">
                    <i class="bi bi-chat-fill" style="color: #FEE500;"></i>
                </button>
                <button class="share-btn" onclick="copyUrl()" title="<?php echo __('URL 복사', 'skin') ?>">
                    <i class="bi bi-link-45deg"></i>
                </button>
            </div>

            <div class="btn-group">
                <?php if ($isAuthor || $isAdmin): ?>
                    <a href="/forum/<?php echo  urlencode($article->forum_code) ?>/edit/<?php echo  $article->idx ?>" class="btn btn-primary">
                        <?php echo  __('수정', 'skin') ?>
                    </a>
                    <button onclick="deletePost(<?php echo  $article->idx ?>)" class="btn btn-danger">
                        <?php echo  __('삭제', 'skin') ?>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- 댓글 섹션 -->
    <?php if ($forumConfig->use_comment === 'Y'): ?>
        <div class="comments-section">
            <div class="comments-header">
                <?php echo  __('댓글', 'skin') ?> (<span id="comment-count">0</span>)
            </div>

            <!-- 댓글 목록 -->
            <div id="comments-list"></div>

            <!-- 더 읽어오기 버튼 -->
            <div id="load-more-container" style="display: none; text-align: center; padding: 1rem;">
                <button id="load-more-comments" class="btn btn-primary" onclick="loadMoreComments()">
                    <?php echo  __('댓글 더 보기', 'skin') ?>
                </button>
            </div>

            <?php
            // 댓글 작성 권한 체크 (permit_member_comment, permit_guest_comment 사용)
            $canComment = false;
            if ($isGuest && $forumConfig->permit_guest_comment === 'Y') {
                $canComment = true;
            }
            elseif ($isMember && $forumConfig->permit_member_comment === 'Y') {
                $canComment = true;
            }
            elseif (ExpertNote\User\User::isAdmin()) {
                $canComment = true;
            }

            if ($canComment): ?>
                <form class="comment-form" onsubmit="return submitComment(event)">
                    <textarea name="content" id="comment-editor" placeholder="<?php echo  __('댓글을 입력하세요', 'skin') ?>" required></textarea>
                    <button type="submit" class="btn btn-primary">
                        <?php echo  __('댓글 작성', 'skin') ?>
                    </button>
                </form>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script>
// 소셜 공유 함수들
function getShareUrl() {
    return decodeURIComponent(window.location.href);
}

function getShareTitle() {
    return document.querySelector('.post-title').textContent;
}

function shareToFacebook() {
    const url = encodeURIComponent(getShareUrl());
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, 'facebook-share', 'width=600,height=400');
}

function shareToTwitter() {
    const url = encodeURIComponent(getShareUrl());
    const text = encodeURIComponent(getShareTitle());
    window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, 'twitter-share', 'width=600,height=400');
}

function shareToLinkedIn() {
    const url = encodeURIComponent(getShareUrl());
    window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${url}`, 'linkedin-share', 'width=600,height=400');
}

function shareToKakao() {
    const url = getShareUrl();
    const title = getShareTitle();

    // 카카오 SDK가 로드되지 않았으면 URL 복사로 대체
    if (typeof Kakao === 'undefined' || !Kakao.isInitialized()) {
        alert('<?php echo __('카카오톡 공유를 사용할 수 없습니다. URL을 복사합니다.', 'skin') ?>');
        copyUrl();
        return;
    }

    Kakao.Share.sendDefault({
        objectType: 'feed',
        content: {
            title: title,
            description: '<?php echo htmlspecialchars($forumConfig->forum_title) ?>',
            imageUrl: '<?php echo ExpertNote\SiteMeta::get('site_logo') ?>',
            link: {
                mobileWebUrl: url,
                webUrl: url
            }
        }
    });
}

function copyUrl() {
    const url = getShareUrl();

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(url).then(function() {
            alert('<?php echo __('URL이 복사되었습니다.', 'skin') ?>');
        }).catch(function(err) {
            fallbackCopyUrl(url);
        });
    } else {
        fallbackCopyUrl(url);
    }
}

function fallbackCopyUrl(url) {
    const textarea = document.createElement('textarea');
    textarea.value = url;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();
    try {
        document.execCommand('copy');
        alert('<?php echo __('URL이 복사되었습니다.', 'skin') ?>');
    } catch (err) {
        alert('<?php echo __('URL 복사에 실패했습니다.', 'skin') ?>');
    }
    document.body.removeChild(textarea);
}

function toggleLike(idx, actionType) {
    fetch(`/api/v1/forum/like`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            forum_idx: idx,
            action_type: actionType
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.result === 'SUCCESS' && data.data) {
            // 좋아요 수 업데이트
            const likeCount = document.getElementById(`like-count-${idx}`);
            if (likeCount) {
                likeCount.textContent = data.data.cnt_like || 0;
            }

            // 싫어요 수 업데이트
            const dislikeCount = document.getElementById(`dislike-count-${idx}`);
            if (dislikeCount) {
                dislikeCount.textContent = data.data.cnt_dislike || 0;
            }

            // 버튼 스타일 토글 (선택 사항)
            const likeBtn = document.querySelector(`.btn-like`);
            const dislikeBtn = document.querySelector(`.btn-dislike`);

            if (actionType === 'LIKE' && likeBtn) {
                likeBtn.classList.toggle('liked');
            } else if (actionType === 'DISLIKE' && dislikeBtn) {
                dislikeBtn.classList.toggle('disliked');
            }
        } else {
            alert(data.message || '<?php echo  __('처리에 실패했습니다.', 'skin') ?>');
        }
    })
    .catch(err => {
        console.error(err);
        alert('<?php echo  __('오류가 발생했습니다.', 'skin') ?>');
    });
}

function deletePost(idx) {
    if (!confirm('<?php echo  __('정말 삭제하시겠습니까?', 'skin') ?>')) return;

    location.href = `/forum/<?php echo  urlencode($article->forum_code) ?>/delete/${idx}`;
}

function editComment(commentIdx, currentContent) {
    const contentDiv = document.getElementById('comment-content-' + commentIdx);
    const commentItem = document.getElementById('comment-' + commentIdx);

    // 이미 수정 폼이 있으면 중복 생성 방지
    if (commentItem.querySelector('.comment-edit-form')) {
        return;
    }

    const editForm = document.createElement('div');
    editForm.className = 'comment-edit-form';
    editForm.innerHTML = `
        <textarea id="edit-textarea-${commentIdx}">${currentContent}</textarea>
        <div>
            <button class="btn btn-sm btn-primary" onclick="saveComment(${commentIdx})"><?php echo  __('저장', 'skin') ?></button>
            <button class="btn btn-sm" onclick="cancelEdit(${commentIdx})"><?php echo  __('취소', 'skin') ?></button>
        </div>
    `;

    contentDiv.style.display = 'none';
    contentDiv.parentNode.insertBefore(editForm, contentDiv.nextSibling);
}

function cancelEdit(commentIdx) {
    const contentDiv = document.getElementById('comment-content-' + commentIdx);
    const commentItem = document.getElementById('comment-' + commentIdx);
    const editForm = commentItem.querySelector('.comment-edit-form');

    if (editForm) {
        editForm.remove();
    }
    contentDiv.style.display = 'block';
}

function saveComment(commentIdx) {
    const textarea = document.getElementById('edit-textarea-' + commentIdx);
    const contents = textarea.value;

    if (!contents.trim()) {
        alert('<?php echo  __('댓글 내용을 입력하세요.', 'skin') ?>');
        return;
    }

    fetch('/api/v1/forum/comment', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            idx: commentIdx,
            contents: contents
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.result === 'SUCCESS') {
            alert('<?php echo  __('댓글이 수정되었습니다.', 'skin') ?>');
            // 댓글 목록 새로고침
            document.getElementById('comments-list').innerHTML = '';
            currentPage = 1;
            loadComments(1);
        } else {
            alert(data.message || '<?php echo  __('댓글 수정에 실패했습니다.', 'skin') ?>');
        }
    })
    .catch(err => {
        console.error(err);
        alert('<?php echo  __('오류가 발생했습니다.', 'skin') ?>');
    });
}

function deleteComment(commentIdx) {
    if (!confirm('<?php echo  __('정말 삭제하시겠습니까?', 'skin') ?>')) {
        return;
    }

    fetch('/api/v1/forum/comment', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            idx: commentIdx
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.result === 'SUCCESS') {
            alert('<?php echo  __('댓글이 삭제되었습니다.', 'skin') ?>');
            // 댓글 목록 새로고침
            document.getElementById('comments-list').innerHTML = '';
            currentPage = 1;
            loadComments(1);
        } else {
            alert(data.message || '<?php echo  __('댓글 삭제에 실패했습니다.', 'skin') ?>');
        }
    })
    .catch(err => {
        console.error(err);
        alert('<?php echo  __('오류가 발생했습니다.', 'skin') ?>');
    });
}

function replyComment(commentIdx, authorName) {
    const commentItem = document.getElementById('comment-' + commentIdx);

    // 이미 답글 폼이 있으면 중복 생성 방지
    if (commentItem.querySelector('.comment-reply-form')) {
        return;
    }

    // 기존에 열려있는 답글 폼들 닫기
    document.querySelectorAll('.comment-reply-form').forEach(form => form.remove());

    const replyForm = document.createElement('div');
    replyForm.className = 'comment-reply-form';
    replyForm.innerHTML = `
        <div style="margin-bottom: 0.5rem; color: var(--bs-secondary, #6c757d); font-size: 0.9rem;">
            <strong>${authorName}</strong><?php echo  __('님에게 답글 작성', 'skin') ?>
        </div>
        <textarea id="reply-textarea-${commentIdx}" placeholder="<?php echo  __('답글을 입력하세요', 'skin') ?>"></textarea>
        <div>
            <button class="btn btn-sm btn-primary" onclick="submitReply(${commentIdx})"><?php echo  __('답글 작성', 'skin') ?></button>
            <button class="btn btn-sm" onclick="cancelReply(${commentIdx})"><?php echo  __('취소', 'skin') ?></button>
        </div>
    `;

    commentItem.appendChild(replyForm);
    document.getElementById('reply-textarea-' + commentIdx).focus();
}

function cancelReply(commentIdx) {
    const commentItem = document.getElementById('comment-' + commentIdx);
    const replyForm = commentItem.querySelector('.comment-reply-form');

    if (replyForm) {
        replyForm.remove();
    }
}

function submitReply(replyToIdx) {
    const textarea = document.getElementById('reply-textarea-' + replyToIdx);
    const contents = textarea.value;

    if (!contents.trim()) {
        alert('<?php echo  __('답글 내용을 입력하세요.', 'skin') ?>');
        return;
    }

    fetch('/api/v1/forum/comment', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            parent_idx: <?php echo  $idx ?>,
            reply_idx: replyToIdx,
            contents: contents
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.result === 'SUCCESS') {
            alert('<?php echo  __('답글이 작성되었습니다.', 'skin') ?>');
            // 댓글 목록 새로고침
            document.getElementById('comments-list').innerHTML = '';
            currentPage = 1;
            loadComments(1);
        } else {
            alert(data.message || '<?php echo  __('답글 작성에 실패했습니다.', 'skin') ?>');
        }
    })
    .catch(err => {
        console.error(err);
        alert('<?php echo  __('오류가 발생했습니다.', 'skin') ?>');
    });
}

// 댓글 로딩 관련 전역 변수
let currentPage = 1;
let totalComments = 0;
let isLoading = false;

// 댓글 로드 함수
function loadComments(page = 1) {
    if (isLoading) return;
    isLoading = true;

    fetch(`/api/v1/forum/comments?idx=<?php echo  $idx ?>&page=${page}&limit=20`)
        .then(res => res.json())
        .then(data => {
            if (data.result === 'SUCCESS' || data.data) {
                totalComments = data.total || 0;
                document.getElementById('comment-count').textContent = totalComments;

                if (data.data && data.data.length > 0) {
                    const commentsList = document.getElementById('comments-list');

                    data.data.forEach(comment => {
                        commentsList.appendChild(renderComment(comment));
                    });

                    // 더 읽어오기 버튼 표시 여부
                    if (data.currentPage < data.totalPage) {
                        document.getElementById('load-more-container').style.display = 'block';
                    } else {
                        document.getElementById('load-more-container').style.display = 'none';
                    }
                }
            }
            isLoading = false;
        })
        .catch(err => {
            console.error('댓글 로드 오류:', err);
            isLoading = false;
        });
}

// 댓글 HTML 생성 함수
function renderComment(comment) {
    const div = document.createElement('div');
    div.className = `comment-item depth-${Math.min(comment.depth || 0, 3)}`;
    div.id = `comment-${comment.idx}`;

    const isAuthor = <?php echo  ExpertNote\User\User::isLogin() ? "'" . $_SESSION['user_id'] . "'" : 'null' ?> === comment.user_id;
    const isAdmin = <?php echo  ExpertNote\User\User::isAdmin() ? 'true' : 'false' ?>;
    const canReply = <?php echo  $canComment ? 'true' : 'false' ?>;

    const authorName = comment.nickname || comment.username;
    const depthLabel = comment.depth > 0 ? '<span style="color: var(--bs-secondary, #6c757d); font-size: 0.9em;">↳ <?php echo  __('답글', 'skin') ?></span>' : '';

    // 소셜 로그인 아이콘
    let socialIcon = '';
    if (comment.registered_by && comment.registered_by !== 'DIRECT') {
        socialIcon = `<span class="social-icon"><i class="sicon-${comment.registered_by.toLowerCase()} sicon-16"></i></span>`;
    }

    let actionsHtml = '';
    if (canReply && (comment.depth || 0) < 3) {
        actionsHtml += `<button class="btn btn-sm" onclick="replyComment(${comment.idx}, '${authorName}')"><?php echo  __('답글', 'skin') ?></button>`;
    }
    if (isAuthor || isAdmin) {
        actionsHtml += `<button class="btn btn-sm" onclick="editComment(${comment.idx}, '${(comment.contents || '').replace(/'/g, "\\'").replace(/\n/g, '\\n')}')"><?php echo  __('수정', 'skin') ?></button>`;
        actionsHtml += `<button class="btn btn-sm btn-danger" onclick="deleteComment(${comment.idx})"><?php echo  __('삭제', 'skin') ?></button>`;
    }

    div.innerHTML = `
        <div class="comment-author">
            ${socialIcon}${authorName}
            ${depthLabel}
        </div>
        <div class="comment-meta">
            ${new Date(comment.write_time).toLocaleString('ko-KR')}
        </div>
        <div class="comment-content" id="comment-content-${comment.idx}">
            ${(comment.contents || '').replace(/\n/g, '<br>')}
        </div>
        <div class="comment-actions">
            ${actionsHtml}
        </div>
    `;

    return div;
}

// 더 읽어오기 버튼 클릭
function loadMoreComments() {
    currentPage++;
    loadComments(currentPage);
}

function submitComment(e) {
    e.preventDefault();
    const form = e.target;

    // CKEditor에서 내용 가져오기
    let contents = '';
    if (commentEditor) {
        contents = commentEditor.getData();
    } else {
        contents = form.content.value;
    }

    if (!contents || !contents.trim()) {
        alert('<?php echo  __('댓글 내용을 입력하세요.', 'skin') ?>');
        return false;
    }

    fetch('/api/v1/forum/comment', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            parent_idx: <?php echo  $idx ?>,
            reply_idx: 0,
            contents: contents
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.result === 'SUCCESS') {
            alert('<?php echo  __('댓글이 작성되었습니다.', 'skin') ?>');
            // CKEditor 내용 리셋
            if (commentEditor) {
                commentEditor.setData('');
            } else {
                form.reset();
            }
            // 댓글 목록 새로고침
            document.getElementById('comments-list').innerHTML = '';
            currentPage = 1;
            loadComments(1);
        } else {
            alert(data.message || '<?php echo  __('댓글 작성에 실패했습니다.', 'skin') ?>');
        }
    })
    .catch(err => {
        console.error(err);
        alert('<?php echo  __('오류가 발생했습니다.', 'skin') ?>');
    });

    return false;
}

// CKEditor 초기화
let commentEditor;
document.addEventListener('DOMContentLoaded', function() {
    <?php if ($forumConfig->use_comment === 'Y' && $canComment): ?>
    // CKEditor 5 초기화
    if (typeof ClassicEditor !== 'undefined') {
        ClassicEditor.create(document.querySelector('#comment-editor'), {
            toolbar: {
                items: [
                    'bold', 'italic', '|',
                    'link', '|',
                    'bulletedList', 'numberedList', '|',
                    'undo', 'redo'
                ]
            },
            language: 'ko',
            placeholder: '<?php echo  __('댓글을 입력하세요', 'skin') ?>'
        })
        .then(editor => {
            commentEditor = editor;
        })
        .catch(error => {
            console.error('CKEditor 초기화 오류:', error);
        });
    }
    <?php endif; ?>

    // 댓글 자동 로드
    <?php if ($forumConfig->use_comment === 'Y'): ?>
    loadComments(1);
    <?php endif; ?>
});
</script>

<!-- CKEditor 5 CDN -->
<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/translations/ko.js"></script>
