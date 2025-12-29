<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Profil Saya</h3>
                <p class="text-subtitle text-muted">Ubah informasi password akun Anda.</p>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Ubah Password</h4>
            </div>
            <div class="card-body">
                
                <!-- Pesan Error/Sukses -->
                <div id="alert-container" class="mb-3"></div>

                <!-- Form Change Password -->
                <form id="changePasswordForm" action="<?= site_url('profile/update') ?>" method="POST">
                    <!-- CSRF Token -->
                    <?= csrf_field() ?>

                    <!-- Username (Read Only) -->
                    <div class="form-group mb-3">
                        <label class="form-label text-muted">Username</label>
                        <input type="text" class="form-control form-control-sm" value="<?= $user['username'] ?>" disabled>
                    </div>

                    <!-- Full Name (Read Only) -->
                    <div class="form-group mb-3">
                        <label class="form-label text-muted">Full Name</label>
                        <input type="text" class="form-control form-control-sm" name="full_name" value="<?= $user['full_name'] ?>" required>
                        <p><small class="text-muted"><i>*) Abaikan jika tidak ada perubahan nama</i></small></p>
                    </div>

                    <hr>

                    <!-- Current Password -->
                    <div class="form-group position-relative has-icon-left mb-3">
                        <input type="password" class="form-control" name="current_password" placeholder="Password Lama" required>
                        <div class="form-control-icon">
                            <i class="bi bi-lock"></i>
                        </div>
                    </div>

                    <!-- New Password -->
                    <div class="form-group position-relative has-icon-left mb-3">
                        <input type="password" class="form-control" name="new_password" placeholder="Password Baru" required>
                        <div class="form-control-icon">
                            <i class="bi bi-key"></i>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group position-relative has-icon-left mb-3">
                        <input type="password" class="form-control" name="confirm_password" placeholder="Konfirmasi Password Baru" required>
                        <div class="form-control-icon">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary shadow-lg btn-block">
                        <i class="bi bi-save me-2"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </section>
</div>