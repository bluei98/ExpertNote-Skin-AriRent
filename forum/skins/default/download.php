<?php
/**
 * 포럼 파일 다운로드 템플릿
 * 변수: $file, $need_login, $file_idx
 */
?>

<style>
.download-container {
    max-width: 800px;
    margin: 100px auto;
    padding: 2rem;
    text-align: center;
}

.download-card {
    background: white;
    border-radius: 12px;
    padding: 3rem 2rem;
}

.download-icon {
    font-size: 4rem;
    color: #0d6efd;
    margin-bottom: 1rem;
}

.download-filename {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    word-break: break-word;
}

.download-filesize {
    color: #6c757d;
    font-size: 0.9rem;
    margin-bottom: 2rem;
}

.countdown-wrapper {
    margin: 2rem 0;
}

.countdown-number {
    font-size: 4rem;
    font-weight: bold;
    color: #0d6efd;
    line-height: 1;
    margin-bottom: 1rem;
}

.countdown-text {
    font-size: 1.1rem;
    color: #6c757d;
}

.download-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-top: 2rem;
}

.btn-download {
    padding: 0.75rem 2rem;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-download-primary {
    background: #0d6efd;
    color: white;
}

.btn-download-primary:hover:not(:disabled) {
    background: #0b5ed7;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(13, 110, 253, 0.3);
}

.btn-download-primary:disabled {
    background: #6c757d;
    cursor: not-allowed;
}

.btn-download-secondary {
    background: #6c757d;
    color: white;
}

.btn-download-secondary:hover {
    background: #5a6268;
}

.login-required {
    background: #fff3cd;
    border: 1px solid #ffc107;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.login-required-icon {
    font-size: 2rem;
    color: #ffc107;
    margin-bottom: 0.5rem;
}

.login-required-text {
    font-size: 1.1rem;
    color: #856404;
    margin-bottom: 1rem;
}

.btn-login {
    display: inline-block;
    padding: 0.75rem 2rem;
    background: #ffc107;
    color: #000;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.2s;
}

.btn-login:hover {
    background: #e0a800;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
}
</style>

<div class="download-container">
    <div class="download-card">
        <?php if ($need_login): ?>
        <!-- 로그인 필요 -->
        <div class="login-required font-110">
            <div class="login-required-icon">
                <i class="bi bi-lock-fill"></i>
            </div>
            <div class="login-required-text">
                <?php echo __('이 파일을 다운로드하려면 로그인이 필요합니다.', 'skin') ?>
            </div>
            <a href="/login?returl=<?php echo urlencode($_SERVER['REQUEST_URI']) ?>" class="btn-login font-110">
                <i class="bi bi-box-arrow-in-right"></i>
                <?php echo __('로그인하기', 'skin') ?>
            </a>
        </div>

        <h1 class="font-140 mb-5"><?php echo ExpertNote\Core::getPageTitle()?></h1>

        <!-- 파일 정보 -->
        <div class="download-icon">
            <i class="bi bi-file-earmark-arrow-down"></i>
        </div>
        <div class="download-filename">
            <?php echo htmlspecialchars($file->real_name) ?>
        </div>
        <div class="download-filesize">
            <?php echo ExpertNote\Utils::convertFileSize($file->size) ?>
        </div>

        <?php else: ?>
        <!-- 다운로드 가능 -->
        <div class="download-icon">
            <i class="bi bi-file-earmark-arrow-down"></i>
        </div>
        <div class="download-filename">
            <?php echo htmlspecialchars($file->real_name) ?>
        </div>
        <div class="download-filesize">
            <?php echo ExpertNote\Utils::convertFileSize($file->size) ?> · <?php echo __('다운로드', 'skin') ?> <?php echo number_format($file->dncount) ?><?php echo __('회', 'skin') ?>
        </div>

        <!-- 카운트다운 -->
        <div class="countdown-wrapper" id="countdown-wrapper">
            <div class="countdown-number" id="countdown-number">10</div>
            <div class="countdown-text">
                <?php echo __('초 후 자동으로 다운로드가 시작됩니다.', 'skin') ?>
            </div>
        </div>

        <!-- 다운로드 완료 메시지 (숨김) -->
        <div class="countdown-wrapper" id="download-complete" style="display: none;">
            <div class="countdown-number">
                <i class="bi bi-check-circle-fill" style="color: #198754;"></i>
            </div>
            <div class="countdown-text">
                <?php echo __('다운로드가 시작되었습니다.', 'skin') ?>
            </div>
        </div>

        <!-- 다운로드 버튼 -->
        <div class="download-buttons">
            <button class="btn-download btn-download-primary" id="btn-download" disabled>
                <i class="bi bi-download"></i>
                <?php echo __('지금 다운로드', 'skin') ?>
            </button>
            <button class="btn-download btn-download-secondary" onclick="window.close() || history.back()">
                <i class="bi bi-x-circle"></i>
                <?php echo __('게시물 보기', 'skin') ?>
            </button>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if (!$need_login): ?>
<script>
let countdown = 10;
const countdownNumber = document.getElementById('countdown-number');
const countdownWrapper = document.getElementById('countdown-wrapper');
const downloadComplete = document.getElementById('download-complete');
const btnDownload = document.getElementById('btn-download');
const downloadUrl = '/forum/download/<?php echo $file_idx ?>?action=download';

// 카운트다운 타이머
const timer = setInterval(() => {
    countdown--;
    countdownNumber.textContent = countdown;

    if (countdown <= 0) {
        clearInterval(timer);
        startDownload();
    }
}, 1000);

// 다운로드 시작 함수
function startDownload() {
    // iframe을 사용한 다운로드 (페이지 이동 없이)
    const iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    iframe.src = downloadUrl;
    document.body.appendChild(iframe);

    // UI 업데이트
    countdownWrapper.style.display = 'none';
    downloadComplete.style.display = 'block';
    btnDownload.disabled = true;
    btnDownload.innerHTML = '<i class="bi bi-check-circle"></i> <?php echo __('다운로드 완료', 'skin') ?>';
}

// 직접 다운로드 버튼 활성화 및 이벤트
setTimeout(() => {
    btnDownload.disabled = false;
    btnDownload.addEventListener('click', () => {
        clearInterval(timer);
        startDownload();
    });
}, 0);
</script>
<?php endif; ?>
