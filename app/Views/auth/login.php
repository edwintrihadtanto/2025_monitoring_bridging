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
</head>

<body>
    <script src="<?= base_url('public/assets/dist/assets/static/js/initTheme.js') ?>"></script>
    <div id="auth" style="background: url('<?= base_url('public/img/monitoring_AI.png') ?>') center/cover no-repeat fixed; min-height: 100vh; display: flex; align-items: center;">
        
        <div class="container-fluid h-100">
            <div class="row h-100 align-items-center">
                
                <div class="col-lg-5 col-12 h-100" style="background-color: #000000c7; min-height: 100vh; display: flex; flex-direction: column; justify-content: center;">
                    
                    <div id="auth-left" style="width: 100%; padding: 0 2rem;">
                        
                        <!-- HEADER: LOGO & JUDUL -->
                        <div class="d-flex align-items-center mb-5">
                            <a href="<?= base_url() ?>">
                                <img src="<?= base_url('public/img/rssm.png') ?>" alt="Logo" style="height: 7rem;">
                            </a>
                            <h1 class="auth-title ms-3 mb-0 text-white">Log in.</h1>
                        </div>

                        <!-- ALERT / ERROR MESSAGES -->
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                        <?php endif; ?>

                        <?php if (isset($validation) && $validation->getErrors()): ?>
                            <div class="alert alert-danger">
                                <?= implode('<br>', $validation->getErrors()) ?>
                            </div>
                        <?php endif; ?>

                        <!-- FORM LOGIN -->
                        <form action="<?= site_url('auth/attemptLogin') ?>" method="POST">
                            <?= csrf_field() ?>
                            
                            <div class="form-group position-relative has-icon-left mb-4">
                                <input type="text" class="form-control form-control-xl" name="username" placeholder="Username" value="<?= old('username') ?>" required autofocus>
                                <div class="form-control-icon">
                                    <i class="bi bi-person"></i>
                                </div>
                            </div>
                            <div class="form-group position-relative has-icon-left mb-4">
                                <input type="password" class="form-control form-control-xl" name="password" placeholder="Password" required>
                                <div class="form-control-icon">
                                    <i class="bi bi-shield-lock"></i>
                                </div>
                            </div>
                            <div class="form-group position-relative has-icon-left mb-4">
                                <!-- Input ini untuk jawaban angka -->
                                <input type="number" class="form-control form-control-xl" name="captcha" placeholder="Jawaban" required autocomplete="off">
                                
                                <div class="form-control-icon">
                                    <i class="bi bi-calculator"></i>
                                </div>
                                
                                <!-- Tampilan Pertanyaan Matematika di kanan input -->
                                <div style="position: absolute; right: 35px; top: 10px; font-weight: bold; font-size: 1.1rem; color: #435ebe;">
                                    = <?= $captcha_question ?>
                                </div>
                            </div>
                            <div class="form-check form-check-lg d-flex align-items-end">
                                <input class="form-check-input me-2" type="checkbox" value="" id="flexCheckDefault">
                                <label class="form-check-label text-gray-600" for="flexCheckDefault">
                                    Keep me logged in
                                </label>
                            </div>
                            <button class="btn btn-primary btn-block btn-lg shadow-lg mt-5">Log in</button>
                        </form>
                        
                    </div>
                </div>
                
            </div>
        </div>

    </div>
</body>
</html>