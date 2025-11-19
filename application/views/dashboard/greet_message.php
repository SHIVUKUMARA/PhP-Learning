<?php 
$title = "Welcome | AdminLTE";
$body_class = "hold-transition login-page";  
$this->load->view('partials/header'); 
?>

<div class="login-box" style="max-width: 500px; width: 100%;">
<!-- <? $this->load->view('partials/navbar'); ?>
<? $this->load->view('partials/sidebar'); ?> -->
    <div class="card card-primary card-outline shadow-lg">
        <div class="card-header d-flex justify-content-center align-items-center">
            <h3 class="card-title m-0">
                <strong>Welcome 👋</strong>
            </h3>
        </div>

        <div class="card-body text-center">

            <div class="mb-3">
                <img
                  src="<?= base_url('assets/images/user8-128x128.jpg') ?>"
                  class="user-image rounded-circle shadow"
                  alt="User Image"
                />
            </div>
            <h4 class="mb-3">Hello, <strong><?= $fullname; ?></strong></h4>
            <p class="text-muted">You have successfully logged in.</p>
            <a href="<?= site_url('dashboard/dashboard'); ?>" 
               class="btn btn-success btn-lg w-100 mb-3">
                <i class="bi bi-speedometer2"></i> Go to Dashboard
            </a>
            <a href="<?= site_url('auth/logout'); ?>" 
               class="btn btn-danger btn-lg w-100">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>

        </div>
    </div>
    <!-- <? $this->load->view('partials/footer'); ?> -->
</div>

<?php $this->load->view('partials/footer'); ?>
