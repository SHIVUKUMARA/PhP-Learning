<?php
$data['title'] = "Languages | AdminLTE";
$data['body_class'] = "hold-transition sidebar-mini layout-fixed";
$this->load->view('partials/header', $data);
?>

<div class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">

        <?php $this->load->view('navbar/headernav', ['logged_user' => $user]); ?>

        <?php $this->load->view('navbar/sidebar'); ?>

        <main class="app-main d-flex justify-content-center align-items-center"
            style="padding: 20px; min-height: calc(100vh - 56px);">

            <div class="card card-primary card-outline shadow-lg" style="width: 450px;">
                <div class="card-header text-center">
                    <h3 class="card-title m-0"><strong><?= $welcome ?></strong></h3>
                </div>
                <div class="card-body text-center">
                    <p class="mb-3"><?= $description ?></p>

                    <h5 class="mb-3"><?= $select_language ?></h5>
                    <div class="btn-group" role="group">
                        <a href="<?= site_url('interaction/lang/english') ?>" class="btn btn-primary">English</a>
                        <a href="<?= site_url('interaction/lang/kannada') ?>" class="btn btn-primary">Kannada</a>
                        <a href="<?= site_url('interaction/lang/hindi') ?>" class="btn btn-primary">Hindi</a>
                    </div>
                </div>
            </div>

        </main>

        <?php $this->load->view('navbar/footer'); ?>
    </div>
</div>

<?php $this->load->view('partials/footer'); ?>