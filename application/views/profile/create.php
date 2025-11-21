<?php
$data['title'] = "Create Admin | AdminLTE";
$data['body_class'] = "hold-transition sidebar-mini layout-fixed";
$this->load->view('partials/header', $data);
?>

<div class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">

        <?php $this->load->view('navbar/headernav', ['user' => (object)[
            'fullname' => $this->session->userdata('fullname'),
            'created_at' => $this->session->userdata('created_at') ?? date('Y-m-d')
        ]]); ?>

        <?php $this->load->view('navbar/sidebar'); ?>

        <main class="app-main d-flex justify-content-center align-items-center" style="min-height: calc(100vh - 56px); padding: 20px;">
            <div class="d-flex justify-content-center align-items-center w-100" style="min-height: 100%;">
                <div class="card shadow-lg border-0 rounded-lg text-center" style="width: 400px; background-color: #f8ea687a;">
                    <div class="position-relative mt-5">
                        <img src="<?= base_url('assets/images/user8-128x128.jpg'); ?>"
                            class="rounded-circle border border-5 border-grey shadow-sm"
                            alt="User Avatar"
                            style="width: 100px; height: 100px; object-fit: cover; position: absolute; top: -40px; left: 50%; transform: translateX(-50%);">
                    </div>

                    <div class="card-body mt-5 p-3">
                        <?php if ($this->session->flashdata('success')): ?>
                            <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
                        <?php endif; ?>
                        <?php if ($this->session->flashdata('error')): ?>
                            <div class="alert alert-danger"><?= $this->session->flashdata('error'); ?></div>
                        <?php endif; ?>

                        <?= form_open('dashboard/create_admin'); ?>
                        <div class="form-floating mb-2">
                            <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Full Name" required autocomplete="off">
                            <label for="fullname">Full Name</label>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="fname" name="fname" placeholder="First Name" autocomplete="off">
                                    <label for="fname">First Name</label>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="lname" name="lname" placeholder="Last Name" autocomplete="off">
                                    <label for="lname">Last Name</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-floating mb-2">
                            <input type="email" class="form-control" id="email" name="email" placeholder="Email" required autocomplete="off">
                            <label for="email">Email</label>
                        </div>

                        <div class="form-floating mb-2">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required autocomplete="off">
                            <label for="password">Password</label>
                        </div>

                        <div class="form-floating mb-2">
                            <select class="form-select" id="role" name="role">
                                <option value="customer" <?= (isset($role) && $role == 'customer') ? 'selected' : ''; ?>>Customer</option>
                                <option value="manager" <?= (isset($role) && $role == 'manager') ? 'selected' : ''; ?>>Manager</option>
                                <option value="admin" <?= (isset($role) && $role == 'admin') ? 'selected' : ''; ?>>Admin</option>
                            </select>
                            <label for="role">Role</label>
                        </div>

                        <div class="form-check mb-2 text-start">
                            <input class="form-check-input" type="checkbox" name="agree_terms" value="1" id="agree_terms" required>
                            <label class="form-check-label" for="agree_terms">
                                Agree to Terms
                            </label>
                        </div>

                        <div class="d-flex justify-content-center gap-2 mt-3">
                            <button type="submit" class="btn btn-success btn-lg shadow-sm" name="save" value="create">
                                <i class="fas fa-save me-1"></i> Create
                            </button>
                            <a href="<?= site_url('dashboard'); ?>" class="btn btn-secondary btn-lg shadow-sm">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                        </div>
                        <?= form_close(); ?>
                    </div>

                    <div class="card-footer text-center" style="background-color: #f8ea687a;">
                        <small class="text-muted">You can create a new admin anytime.</small>
                    </div>
                </div>
            </div>
        </main>

        <?php $this->load->view('navbar/footer'); ?>

    </div>
</div>

<?php $this->load->view('partials/footer'); ?>