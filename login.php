<?php
ExpertNote\Core::setPageTitle(__('로그인', 'skin'));
ExpertNote\Core::setPageSuffix(ExpertNote\SiteMeta::get('site_title')[$i18n->locale]);

// 로그인 상태 확인
if (ExpertNote\User\User::isLogin()) {
    header("Location: /");
    exit;
}

// 소셜 로그인 프로바이더 조회
$socialProviders = [];
try {
    $providers = \ExpertNote\SocialLogin::getProviders(['is_enabled' => 'Y'], ['display_order ASC']);
    foreach ($providers as $provider) {
        $socialProviders[] = [
            'provider' => $provider->provider,
            'display_name' => $provider->display_name,
            'icon_class' => $provider->icon_class,
            'button_color' => $provider->button_color ?: '#007bff',
            'display_order' => $provider->display_order
        ];
    }
} catch (Exception $e) {
    // 오류가 발생해도 일반 로그인은 가능하도록 함
}

// 소셜 로그인 요청 처리
if (isset($_GET['social_login'])) {
    $provider = $_GET['social_login'];
    $returl = $_GET['returl'] ?? '/';

    try {
        $validProvider = false;
        foreach ($socialProviders as $sp) {
            if ($sp['provider'] === $provider) {
                $validProvider = true;
                break;
            }
        }

        if (!$validProvider) {
            header("Location: /login.php?errcode=1200&returl=" . urlencode($returl));
            exit;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['social_login_returl'] = $returl;
        $_SESSION['social_login_provider'] = $provider;

        $authUrl = \ExpertNote\SocialLogin::getAuthUrl($provider);
        header("Location: " . $authUrl);
        exit;
    } catch (Exception $e) {
        \ExpertNote\Log::setLog('error', "소셜 로그인 요청 처리 실패 ({$provider}): " . $e->getMessage(), 'SocialLogin');
        header("Location: /login.php?errcode=1202&returl=" . urlencode($returl));
        exit;
    }
}

// 에러 메시지 처리
$_options_msg = [
    '99' => __('로그인이 필요합니다.', 'skin'),
    '1000' => __('아이디 또는 비밀번호가 올바르지 않습니다.', 'skin'),
    '1010' => __('아이디를 입력해주세요.', 'skin'),
    '1012' => __('비밀번호를 입력해주세요.', 'skin'),
    '1100' => __('reCAPTCHA 인증에 실패했습니다.', 'skin'),
    '1118' => __('탈퇴한 회원입니다.', 'skin'),
    '1200' => __('프로바이더 정보가 없습니다.', 'skin'),
    '1201' => __('소셜 로그인에 실패했습니다.', 'skin'),
    '1202' => __('소셜 로그인 처리 중 오류가 발생했습니다.', 'skin'),
    '1203' => __('소셜 로그인이 취소되었습니다.', 'skin')
];
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ExpertNote\Core::getPageTitle() ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        :root {
            --primary-color: #007bff;
            --primary-dark: #0056b3;
            --background-color: #f8f9fa;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: var(--background-color);
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 480px;
            width: 100%;
        }

        .login-header {
            text-align: center;
            padding: 40px 40px 20px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: #fff;
        }

        .login-header .logo {
            margin-bottom: 20px;
        }

        .login-header .logo img {
            max-height: 50px;
            filter: brightness(0) invert(1);
        }

        .login-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 10px;
            color: #fff;
        }

        .login-header p {
            font-size: 14px;
            opacity: 0.9;
            margin: 0;
        }

        .login-body {
            padding: 40px;
        }

        .alert {
            border-radius: 8px;
            border-left-width: 4px;
            margin-bottom: 20px;
        }

        .form-floating {
            position: relative;
            margin-bottom: 16px;
        }

        .form-floating.icon-input i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 4;
            font-size: 18px;
        }

        .form-floating.icon-input input {
            padding-left: 48px;
        }

        .form-floating.icon-input label {
            padding-left: 48px;
        }

        .form-floating input {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 16px;
            height: 56px;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .form-floating input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.1);
        }

        .form-floating label {
            padding: 16px;
            font-size: 14px;
            color: #6c757d;
        }

        .required-asterisk {
            color: #dc3545;
            margin-right: 2px;
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            background: var(--primary-color);
            border: none;
            color: #fff;
            transition: all 0.3s ease;
            margin-top: 8px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 123, 255, 0.3);
            background: var(--primary-dark);
        }

        .btn-login i {
            margin-right: 8px;
        }

        .social-login-section {
            margin-top: 32px;
        }

        .social-login-section .divider {
            position: relative;
            text-align: center;
            margin: 24px 0;
        }

        .social-login-section .divider span {
            position: relative;
            display: inline-block;
            padding: 0 20px;
            font-size: 14px;
            color: #6c757d;
            background: #fff;
            z-index: 1;
        }

        .social-login-section .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e9ecef;
        }

        .social-login-btn {
            border-radius: 8px;
            padding: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .social-login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .social-login-btn i {
            font-size: 18px;
            margin-right: 8px;
        }

        .links {
            text-align: center;
            margin-top: 24px;
            font-size: 14px;
            color: #6c757d;
        }

        .links a {
            color: var(--primary-color);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .links a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        .links .separator {
            margin: 0 12px;
            color: #dee2e6;
        }

        @media (max-width: 576px) {
            .login-container {
                padding: 0;
            }

            .login-card {
                border-radius: 0;
                min-height: 100vh;
            }

            .login-header,
            .login-body {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <section class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo">
                    <a href="/">
                        <h2 class="fw-bold mb-0" style="color: #fff; font-size: 32px;">ARI RENT</h2>
                    </a>
                </div>
                <h1><?php echo __('로그인', 'skin') ?></h1>
                <p><?php echo __('계정에 로그인하세요', 'skin') ?></p>
            </div>

            <div class="login-body">
                <form>
                    <input type="hidden" name="returl" value="<?php echo isset($_GET['returl']) ? $_GET['returl'] : "/" ?>">

                    <?php if (isset($_GET['errcode'])): ?>
                        <div class="alert alert-danger">
                            <?php echo $_options_msg[$_GET['errcode']] ?? __('오류가 발생했습니다.', 'skin') ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger">
                            <?php echo $_GET['error'] ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['infocode'])): ?>
                        <div class="alert alert-info">
                            <?php echo $_options_msg[$_GET['infocode']] ?? __('알림', 'skin') ?>
                        </div>
                    <?php endif; ?>

                    <!-- 아이디 -->
                    <div class="form-floating icon-input">
                        <i class="bi bi-person"></i>
                        <input type="text" name="user_id" class="form-control" id="userId" placeholder="<?php echo __('아이디', 'skin') ?>" required>
                        <label for="userId"><span class="required-asterisk">*</span> <?php echo __('아이디', 'skin') ?></label>
                    </div>

                    <!-- 비밀번호 -->
                    <div class="form-floating icon-input">
                        <i class="bi bi-lock"></i>
                        <input type="password" name="user_pass" class="form-control" id="userPass" placeholder="<?php echo __('비밀번호', 'skin') ?>" required>
                        <label for="userPass"><span class="required-asterisk">*</span> <?php echo __('비밀번호', 'skin') ?></label>
                    </div>

                    <!-- 로그인 버튼 -->
                    <button type="button" class="btn btn-login">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <?php echo __('로그인', 'skin') ?>
                    </button>

                    <!-- 소셜 로그인 영역 -->
                    <?php if (!empty($socialProviders)): ?>
                        <div class="social-login-section">
                            <div class="divider">
                                <span><?php echo __('또는', 'skin') ?></span>
                            </div>
                            <div class="social-login-buttons">
                                <?php foreach ($socialProviders as $provider): ?>
                                    <a href="/login?social_login=<?php echo urlencode($provider['provider']) ?>&returl=<?php echo urlencode($_GET['returl'] ?? '/') ?>"
                                        class="btn w-100 mb-2 social-login-btn"
                                        style="background-color: <?php echo htmlspecialchars($provider['button_color']) ?>; color: white; border-color: <?php echo htmlspecialchars($provider['button_color']) ?>;">
                                        <i class="<?php echo htmlspecialchars($provider['icon_class']) ?>"></i>
                                        <?php echo sprintf(__('%s로 로그인', 'skin'), htmlspecialchars($provider['display_name'])) ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="links">
                        <a href="/"><?php echo __('메인페이지', 'skin') ?></a>
                        <span class="separator">|</span>
                        <a href="/find-password"><?php echo __('아이디・비밀번호 찾기', 'skin') ?></a>
                        <span class="separator">|</span>
                        <a href="/signup"><?php echo __('회원가입', 'skin') ?></a>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- ExpertNote Util -->
    <script src="/assets/js/ExpertNote.Util.js"></script>
    <script src="/assets/js/ExpertNote.User.js"></script>

    <script>
    // 일반 로그인 버튼 이벤트
    document.querySelector(".btn-login").addEventListener("click", function() {
        var form = this.closest("form");
        var userId = form.querySelector('input[name="user_id"]').value;
        var userPass = form.querySelector('input[name="user_pass"]').value;

        if (!userId) {
            ExpertNote.Util.showMessage(
                '<?php echo __('아이디를 입력해주세요.', 'skin') ?>',
                '<?php echo __('입력 오류', 'skin'); ?>',
                [
                    { title: '<?php echo __('확인', 'skin')?>', class: 'btn btn-secondary', dismiss: true }
                ]
            );
            return;
        }
        if (!userPass) {
            ExpertNote.Util.showMessage(
                '<?php echo __('비밀번호를 입력해주세요.', 'skin') ?>',
                '<?php echo __('입력 오류', 'skin'); ?>',
                [
                    { title: '<?php echo __('확인', 'skin')?>', class: 'btn btn-secondary', dismiss: true }
                ]
            );
            return;
        }

        ExpertNote.User.login(userId, userPass, function(err, res) {
            if (res.result != "SUCCESS") {
                if (res.message) {
                    ExpertNote.Util.showMessage(
                        res.message,
                        '<?php echo __('로그인 실패', 'skin'); ?>',
                        [
                            { title: '<?php echo __('확인', 'skin')?>', class: 'btn btn-secondary', dismiss: true }
                        ]
                    );
                } else {
                    ExpertNote.Util.showMessage(
                        '<?php echo __('로그인에 실패하였습니다.', 'skin') ?>',
                        '<?php echo __('로그인 실패', 'skin'); ?>',
                        [
                            { title: '<?php echo __('확인', 'skin')?>', class: 'btn btn-secondary', dismiss: true }
                        ]
                    );
                }
            }
            else {
                location.href = form.querySelector('input[name="returl"]').value;
            }
        });
    });

    // Enter 키 처리
    document.querySelectorAll('input[name="user_id"], input[name="user_pass"]').forEach(function(input) {
        input.addEventListener("keypress", function(e) {
            if (e.key === "Enter") {
                e.preventDefault();
                document.querySelector(".btn-login").click();
            }
        });
    });
    </script>
</body>
</html>
