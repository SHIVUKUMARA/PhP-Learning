<?php
$data['title'] = "Welcome | AdminLTE";
$data['body_class'] = "hold-transition sidebar-mini layout-fixed";
$this->load->view('partials/header', $data);
?>

<div class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">

        <?php $this->load->view('navbar/headernav', ['user' => (object)[
            'avatar_url' => $user->avatar_url,
            'fullname' => $fullname,
            'created_at' => $this->session->userdata('created_at') ?? date('Y-m-d')
        ]]); ?>

        <?php $this->load->view('navbar/sidebar'); ?>

        <main class="app-main d-flex justify-content-center align-items-center"
            style="padding: 20px; min-height: calc(100vh - 56px);">
            <div class="card card-primary card-outline shadow-lg" style="width: 450px;">
                <div class="card-header text-center">
                    <h3 class="card-title m-0"><strong>Welcome 👋</strong></h3>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <img src="<?= $user->avatar_url ?>"
                            class="user-image rounded-circle shadow"
                            alt="User Avatar"
                            style="width:120px;height:120px;object-fit:cover;">
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
        </main>
        <?php $this->load->view('navbar/footer'); ?>

    </div>
</div>

<?php $this->load->view('partials/footer'); ?>