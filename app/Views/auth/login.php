<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title . ' - ' : '' ?><?php echo SITE_NAME; ?></title>
    
    <link rel="shortcut icon" href="<?= base_url('public/img/rssmico.png') ?>" type="image/png">
    <link rel="stylesheet" href="<?= base_url('public/assets/dist/assets/compiled/css/app.css') ?>">
    <link rel="stylesheet" href="<?= base_url('public/assets/dist/assets/compiled/css/app-dark.css') ?>">
    <link rel="stylesheet" href="<?= base_url('public/assets/dist/assets/compiled/css/auth.css') ?>">

    <style>
        /* WRAPPER */
        .auth-wrapper {
            height: 100vh;
            overflow: hidden;
            position: relative;
            background: #0f172a;
        }

        /* BACKGROUND IMAGE */
        .auth-bgx {
            position: absolute;
            inset: 0;
            background: url('<?= base_url('public/img/monitoring_AI.png') ?>') center no-repeat #00ffff;
            background-size: contain;
            opacity: 0.15;
        }

        .auth-bg {
          position: absolute;
          inset: 0;
          background:
              radial-gradient(circle at 20% 30%, rgba(67,94,190,0.4), transparent 40%),
              radial-gradient(circle at 80% 70%, rgba(0,198,255,0.3), transparent 40%),
              url('public/img/monitoring_AI.png') center/cover no-repeat;
          animation: bgMove 20s ease-in-out infinite alternate;
          filter: brightness(0.7) saturate(1.2);
        }

        /* BACKGROUND ANIMATION */
        @keyframes bgMove {
          0% { transform: scale(1) translate(0,0); }
          100% { transform: scale(1.05) translate(-20px, 10px); }
        }
        /* RIGHT IMAGE */
        .right-image {
            background: url('<?= base_url('public/img/monitoring_AI.png') ?>') center no-repeat;
            background-size: contain;
            background-color: #ffffff;
        }

        /* GLASS CARD */
        .glass-card {
            width: 100%;
            max-width: 420px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            backdrop-filter: blur(15px);
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            border: 1px solid rgba(255,255,255,0.15);
            animation: fadeInUp 0.8s ease;
        }

        /* INPUT */
        .custom-input {
            background: rgba(255,255,255,0.1);
            border: none;
            color: #fff;
            height: 48px;
            border-radius: 10px;
            padding-left: 15px;
        }

        .custom-input::placeholder {
            color: rgba(255,255,255,0.6);
        }

        /* CAPTCHA */
        .captcha-box {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: #fff;
            padding: 3px 10px;
            border-radius: 8px;
            font-weight: bold;
            color: #333;
        }

        /* BUTTON */
        .btn-modern {
            background: linear-gradient(135deg, #435ebe, #6a82fb);
            border: none;
            height: 48px;
            border-radius: 10px;
            color: #fff;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(67,94,190,0.5);
            color: #fff;
        }

        /* FLOATING SHAPES */
        .floating-shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.3;
            animation: float 8s infinite ease-in-out;
        }

        .shape1 {
            width: 300px;
            height: 300px;
            background: #43a4be;
            top: -50px;
            left: -50px;
        }

        .shape2 {
            width: 300px;
            height: 300px;
            background: #00c6ff;
            bottom: -50px;
            right: -50px;
        }

        /* ANIMATION */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(30px); }
            100% { transform: translateY(0px); }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <script src="<?= base_url('public/assets/dist/assets/static/js/initTheme.js') ?>"></script>
    <div id="auth" style="background: url('<?= base_url('public/img/monitoring_AI.png') ?>') center/cover no-repeat fixed; min-height: 100vh; display: flex; align-items: center;">
        
        <div class="container-fluid h-100">
            <div class="row h-100 align-items-center">
            <div id="auth" class="auth-wrapper">

                <!-- BACKGROUND -->
                <div class="auth-bg"></div>

                <!-- FLOATING ELEMENT -->
                <div class="floating-shape shape1"></div>
                <div class="floating-shape shape2"></div>

                <div class="container-fluid h-100 position-relative">
                    <div class="row h-100">

                        <!-- LEFT: GLASS LOGIN -->
                        <div class="col-lg-5 col-12 d-flex align-items-center justify-content-center">

                            <div class="glass-card p-4 p-lg-5">

                                <!-- LOGO -->
                                <div class="d-flex align-items-center mb-4">
                                    <img src="<?= base_url('public/img/rssm.png') ?>" style="height:60px;">
                                    <div class="ms-3">
                                        <h5 class="mb-0 fw-bold text-white">RSU Dr. Soedono</h5>
                                        <small class="text-light opacity-75">Integrasi Bridging Obat Farmasi</small>
                                    </div>
                                </div>

                                <h3 class="text-white fw-bold mb-4">Login Sistem</h3>

                                <!-- ERROR -->
                                <?php if (session()->getFlashdata('error')): ?>
                                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                                <?php endif; ?>

                                <?php if (isset($validation) && $validation->getErrors()): ?>
                                    <div class="alert alert-danger">
                                        <?= implode('<br>', $validation->getErrors()) ?>
                                    </div>
                                <?php endif; ?>

                                <!-- FORM -->
                                <form action="<?= site_url('auth/attemptLogin') ?>" method="POST">
                                    <?= csrf_field() ?>

                                    <div class="form-group mb-3 position-relative has-icon-left mb-4">
                                        <input type="text" class="form-control custom-input"
                                            name="username" placeholder="Username"
                                            value="<?= old('username') ?>" required>
                                        <div class="form-control-icon">
                                            <i class="bi bi-person"></i>
                                        </div>
                                    </div>

                                    <div class="form-group mb-3 position-relative has-icon-left mb-4">
                                        <input type="password" class="form-control custom-input"
                                            name="password" placeholder="Password" required>
                                        <div class="form-control-icon">
                                            <i class="bi bi-shield-lock"></i>
                                        </div>
                                    </div>

                                    <div class="form-group mb-3 position-relative">
                                        <input type="number" class="form-control custom-input"
                                            name="captcha" placeholder="Jawaban" required>

                                        <div class="captcha-box">
                                            = <?= $captcha_question ?>
                                        </div>
                                    </div>

                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="remember">
                                        <label class="form-check-label text-light" for="remember">
                                            Keep me logged in
                                        </label>
                                    </div>

                                    <button class="btn btn-modern w-100">
                                        Login
                                    </button>
                                </form>

                            </div>
                        </div>

                        <!-- RIGHT: IMAGE -->
                        <div class="col-lg-7 d-none d-lg-block right-image"></div>

                    </div>
                </div>
            </div>

            </div>
        </div>

    </div>
</body>
</html>