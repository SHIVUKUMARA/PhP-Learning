<?php
$data['title'] = "AdminLTE 4 | Dashboard";
$this->load->view('partials/header', $data);
?>
<?php $current_role = $this->session->userdata('role'); ?>

<div class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">
        <?php $this->load->view('navbar/headernav', ['user' => $logged_user]); ?>
        <?php $this->load->view('navbar/sidebar'); ?>

        <main class="app-main">
            <div class="app-content-top-area">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6">
                            <div>App Content Top Area</div>
                        </div>
                        <div class="col-md-6 text-end">
                            <?php if ($current_role === 'admin'): ?>
                                <?= form_open('dashboard/create_users'); ?>
                                <button type="submit" class="btn btn-primary" name="save" value="create">
                                    <i class="bi bi-person-plus me-1"></i>
                                    Create Users
                                </button>
                                <?= form_close(); ?>
                            <?php else: ?>
                                <button type="button" class="btn btn-primary" id="no-access-btn">
                                    <i class="bi bi-person-plus me-1"></i>
                                    Create Users
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-8">
                            <h3 class="mb-0">Layout Custom Area</h3>
                        </div>
                        <div class="col-sm-4">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Fixed Layout</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Title</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse" title="Collapse">
                                            <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                                            <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-lte-toggle="card-remove" title="Remove">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">Start creating your amazing application!</div>
                                <div class="card-footer">Footer</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content-bottom-area">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6">
                            <div>App Content Bottom Area</div>
                        </div>
                        <div class="col-md-6 text-end">
                            <button type="submit" class="btn btn-secondary" name="save" value="create">Refresh</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <?php $this->load->view('navbar/footer'); ?>
    </div>
</div>

<?php $this->load->view('partials/footer'); ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const noAccessBtn = document.getElementById('no-access-btn');
        if (noAccessBtn) {
            noAccessBtn.addEventListener('click', function() {
                alert("You don't have admin access!");
            });
        }
    });
</script>