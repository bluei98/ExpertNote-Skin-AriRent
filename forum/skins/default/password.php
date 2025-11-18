<?php
/**
 * Forum Password Template - V2 마이그레이션
 * 비밀글 비밀번호 입력 UI 템플릿
 */

if (!defined('SECURITY_CODE')) exit;
?>

<div class="container-xl my-4">
    <!-- 브레드크럼 -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">홈</a></li>
            <li class="breadcrumb-item"><a href="/forum/list?forum=<?php echo $article->forum_code ?>"><?php echo htmlspecialchars($forumConfig->forum_name ?? '') ?></a></li>
            <li class="breadcrumb-item active" aria-current="page">비밀글 확인</li>
        </ol>
    </nav>

    <!-- 비밀번호 입력 폼 -->
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lock"></i> 비밀글 확인
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="bi bi-shield-lock fs-1 text-warning"></i>
                        <p class="mt-3 text-muted">
                            이 게시물은 비밀글입니다.<br>
                            비밀번호를 입력해주세요.
                        </p>
                    </div>

                    <!-- 게시물 정보 (제목만) -->
                    <div class="mb-3 p-3 bg-light rounded">
                        <h6 class="mb-1"><?php echo htmlspecialchars($article->title) ?></h6>
                        <small class="text-muted">
                            작성자: <?php echo htmlspecialchars($article->nickname ?? $article->username) ?> |
                            작성일: <?php echo date('Y.m.d', strtotime($article->create_time)) ?>
                        </small>
                    </div>

                    <form method="post" action="/api/v1/forum/password" class="password-form">
                        <input type="hidden" name="forum_idx" value="<?php echo $article->idx ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo \ExpertNote\Core::generateCSRFToken() ?>">
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">비밀번호</label>
                            <input type="password" name="password" id="password" class="form-control" 
                                   placeholder="비밀번호를 입력하세요" required autofocus>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-unlock"></i> 확인
                            </button>
                            <a href="/forum/list?forum=<?php echo $article->forum_code ?>" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> 목록으로
                            </a>
                        </div>
                    </form>

                    <!-- 오류 메시지 -->
                    <?php if (isset($_GET['error']) && $_GET['error'] == 'wrong_password'): ?>
                    <div class="alert alert-danger mt-3">
                        <i class="bi bi-exclamation-triangle"></i>
                        비밀번호가 일치하지 않습니다.
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 도움말 -->
            <div class="alert alert-info mt-3">
                <h6><i class="bi bi-info-circle"></i> 안내</h6>
                <ul class="mb-0 small">
                    <li>작성자가 설정한 비밀번호를 입력해주세요.</li>
                    <li>작성자 본인은 로그인 후 비밀번호 없이 볼 수 있습니다.</li>
                    <li>관리자는 별도 권한으로 접근할 수 있습니다.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
.card-header.bg-warning {
    background-color: #ffc107 !important;
}

.password-form input[type="password"] {
    font-size: 1.1rem;
    padding: 12px;
}

.btn-warning:hover {
    background-color: #e0a800;
    border-color: #d39e00;
}

.alert-info ul {
    padding-left: 1rem;
}

.alert-info li {
    margin-bottom: 0.25rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 폼 제출 처리
    document.querySelector('.password-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // 로딩 표시
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> 확인 중...';
        submitBtn.disabled = true;
        
        fetch('/api/v1/forum/password', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.result === 'success') {
                // 성공 시 게시물 페이지로 이동
                window.location.href = '/forum/view?idx=<?php echo $article->idx ?>';
            } else {
                // 실패 시 오류 표시
                let errorAlert = document.querySelector('.alert-danger');
                if (!errorAlert) {
                    errorAlert = document.createElement('div');
                    errorAlert.className = 'alert alert-danger mt-3';
                    document.querySelector('.card-body').appendChild(errorAlert);
                }
                errorAlert.innerHTML = '<i class="bi bi-exclamation-triangle"></i> ' + (data.message || '비밀번호가 일치하지 않습니다.');
                
                // 비밀번호 필드 초기화 및 포커스
                document.getElementById('password').value = '';
                document.getElementById('password').focus();
                
                // 버튼 복구
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('오류가 발생했습니다. 다시 시도해주세요.');
            
            // 버튼 복구
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
    
    // Enter 키 처리
    document.getElementById('password').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.querySelector('.password-form').dispatchEvent(new Event('submit'));
        }
    });
});
</script>