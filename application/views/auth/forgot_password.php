<?php 
$title = "AdminLTE 4 | Forgot Password"; 
$body_class = "hold-transition login-page"; // Correct AdminLTE class
$this->load->view('partials/header'); 
?>

<div class="login-box">
    <div class="login-logo">
        <a href="#"><b>Admin</b>LTE</a>
    </div>

    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Forgot your password? Enter your email to reset it.</p>
            
            <form action="<?= site_url('auth/forgot_password_submit'); ?>" method="post">
                <div class="input-group mb-3">
                    <input type="email" class="form-control" name="email" placeholder="Email" required>
                    <div class="input-group-text"><span class="bi bi-envelope"></span></div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">Send Reset Link</button>
                    </div>
                </div>
            </form>

            <p class="mt-3 mb-1">
                <a href="<?= site_url('auth/login'); ?>">Back to login</a>
            </p>
        </div>
    </div>
</div>
<!-- /.login-box -->

<?php $this->load->view('partials/footer'); ?>
