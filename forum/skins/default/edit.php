<div class="forum-container">
    <div class="forum-header">
        <h1><?= $isEdit ? __('게시글 수정', 'skin') : __('게시글 작성', 'skin') ?></h1>
    </div>

    <form id="forumForm" class="forum-form">
        <?php if ($isEdit): ?>
        <input type="hidden" name="idx" value="<?= $article->idx ?>">
        <?php endif; ?>

        <!-- 카테고리 선택 -->
        <?php if (!empty($categories)): ?>
        <div class="form-group">
            <label for="category"><?= __('카테고리', 'skin') ?></label>
            <select name="category" id="category" class="form-control">
                <option value=""><?= __('선택하세요', 'skin') ?></option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>" <?= ($isEdit && $article->category === $cat) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>

        <!-- 제목 -->
        <div class="form-group">
            <label for="title"><?= __('제목', 'skin') ?> <span class="required">*</span></label>
            <input type="text" name="title" id="title" class="form-control" required
                   value="<?= $isEdit ? htmlspecialchars($article->title) : '' ?>"
                   placeholder="<?= __('제목을 입력하세요', 'skin') ?>">
        </div>

        <!-- 내용 -->
        <div class="form-group">
            <label for="contents"><?= __('내용', 'skin') ?> <span class="required">*</span></label>
            <div id="editor-container">
                <textarea name="contents" id="contents" class="form-control" required><?= $isEdit ? htmlspecialchars($article->contents) : '' ?></textarea>
            </div>
        </div>

        <!-- 태그 -->
        <div class="form-group">
            <label for="tags"><?= __('태그', 'skin') ?></label>
            <input type="text" name="tags" id="tags" class="form-control"
                   value="<?= $isEdit ? htmlspecialchars($article->tags) : '' ?>"
                   placeholder="<?= __('태그를 쉼표(,)로 구분하여 입력하세요', 'skin') ?>">
            <small class="form-text"><?= __('예: 정보, 질문, 후기', 'skin') ?></small>
        </div>

        <!-- 관리자 옵션 -->
        <?php if (ExpertNote\User\User::isAdmin()): ?>
        <div class="admin-options">
            <h3><?= __('관리자 옵션', 'skin') ?></h3>

            <div class="form-check">
                <input type="checkbox" name="use_sticky" id="use_sticky" class="form-check-input" value="Y"
                       <?= ($isEdit && $article->use_sticky === 'Y') ? 'checked' : '' ?>>
                <label for="use_sticky" class="form-check-label">
                    <?= __('게시판 상단 고정', 'skin') ?>
                </label>
            </div>

            <div class="form-check">
                <input type="checkbox" name="use_all_sticky" id="use_all_sticky" class="form-check-input" value="Y"
                       <?= ($isEdit && $article->use_all_sticky === 'Y') ? 'checked' : '' ?>>
                <label for="use_all_sticky" class="form-check-label">
                    <?= __('전체 게시판 상단 고정', 'skin') ?>
                </label>
            </div>

            <div class="form-check">
                <input type="checkbox" name="use_representative" id="use_representative" class="form-check-input" value="Y"
                       <?= ($isEdit && $article->use_representative === 'Y') ? 'checked' : '' ?>>
                <label for="use_representative" class="form-check-label">
                    <?= __('대표 게시글', 'skin') ?>
                </label>
            </div>

            <div class="form-group">
                <label for="status"><?= __('상태', 'skin') ?></label>
                <select name="status" id="status" class="form-control">
                    <option value="PUBLISHED" <?= ($isEdit && $article->status === 'PUBLISHED') ? 'selected' : '' ?>>
                        <?= __('발행', 'skin') ?>
                    </option>
                    <option value="DRAFT" <?= ($isEdit && $article->status === 'DRAFT') ? 'selected' : '' ?>>
                        <?= __('임시저장', 'skin') ?>
                    </option>
                </select>
            </div>
        </div>
        <?php endif; ?>

        <!-- 버튼 -->
        <div class="form-actions">
            <button type="button" class="btn btn-secondary" onclick="history.back()">
                <?= __('취소', 'skin') ?>
            </button>
            <button type="submit" class="btn btn-primary">
                <?= $isEdit ? __('수정하기', 'skin') : __('작성하기', 'skin') ?>
            </button>
        </div>
    </form>
</div>

<!-- CKEditor 5 CDN -->
<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/translations/ko.js"></script>

<script>
let editor;

// CKEditor 초기화
ClassicEditor.create(document.querySelector('#contents'), {
    language: 'ko',
    placeholder: '<?= __('내용을 입력하세요', 'skin') ?>',
    toolbar: {
        items: [
            'heading', '|',
            'bold', 'italic', 'underline', 'strikethrough', '|',
            'link', 'uploadImage', '|',
            'bulletedList', 'numberedList', '|',
            'blockQuote', 'insertTable', '|',
            'undo', 'redo'
        ]
    }
})
.then(newEditor => {
    editor = newEditor;
})
.catch(error => {
    console.error('CKEditor 초기화 오류:', error);
});

// 폼 제출
document.getElementById('forumForm').addEventListener('submit', function(e) {
    e.preventDefault();

    // CKEditor 내용 가져오기
    const contents = editor.getData();

    if (!contents || !contents.trim()) {
        alert('<?= __('내용을 입력하세요.', 'skin') ?>');
        return;
    }

    const formData = new FormData(this);
    const data = {
        forum_code: '<?= $forumCode ?>',
        locale: '<?= $i18n->locale ?>',
        title: formData.get('title'),
        contents: contents,
        category: formData.get('category') || '',
        tags: formData.get('tags') || ''
    };

    <?php if ($isEdit): ?>
    data.idx = formData.get('idx');
    <?php endif; ?>

    <?php if (ExpertNote\User\User::isAdmin()): ?>
    data.use_sticky = formData.get('use_sticky') === 'Y' ? 'Y' : 'N';
    data.use_all_sticky = formData.get('use_all_sticky') === 'Y' ? 'Y' : 'N';
    data.use_representative = formData.get('use_representative') === 'Y' ? 'Y' : 'N';
    data.status = formData.get('status') || 'PUBLISHED';
    <?php endif; ?>

    const url = '/api/v1/forum/thread';
    const method = <?= $isEdit ? "'PUT'" : "'POST'" ?>;

    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(result => {
        if (result.result === 'SUCCESS') {
            alert('<?= $isEdit ? __('게시글이 수정되었습니다.', 'skin') : __('게시글이 작성되었습니다.', 'skin') ?>');
            // 게시글 상세 페이지로 이동
            const idx = result.data?.idx || result.idx || data.idx;
            location.href = `/forum/<?= urlencode($forumCode) ?>/view/${idx}`;
        } else {
            alert(result.message || '<?= __('오류가 발생했습니다.', 'skin') ?>');
        }
    })
    .catch(err => {
        console.error(err);
        alert('<?= __('오류가 발생했습니다.', 'skin') ?>');
    });
});
</script>
