<?php
$data['title'] = "Edit Profile | AdminLTE";
$data['body_class'] = "hold-transition sidebar-mini layout-fixed";
$this->load->view('partials/header', $data);
?>

<nav class="main-header navbar navbar-expand navbar-light bg-light shadow-sm mb-3">
    <div class="container-fluid">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a href="<?= site_url('profile/profile'); ?>" class="nav-link">
                    <i class="fas fa-arrow-left"></i> Back to Profile
                </a>
            </li>
        </ul>
        <span class="navbar-text ms-auto fw-bold">
            Edit Profile
        </span>
    </div>
</nav>

<div class="content-wrapper" style="margin-top: 4rem;">
    <div class="content">
        <div class="container d-flex justify-content-center align-items-center" style="min-height:70vh;">

            <div class="card shadow-lg border-0 rounded-lg text-center" style="width: 450px; background-color: #f8ea687a;">
                <div class="position-relative">
                    <img src="<?= base_url('assets/images/user8-128x128.jpg'); ?>"
                        class="rounded-circle border border-5 border-grey shadow-sm"
                        alt="User Avatar"
                        style="width: 120px; height: 120px; object-fit: cover; position: absolute; top: -50px; left: 50%; transform: translateX(-50%);">
                </div>

                <div class="card-body mt-5 p-4">
                    <?php if ($this->session->flashdata('success')): ?>
                        <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
                    <?php endif; ?>
                    <?php if ($this->session->flashdata('error')): ?>
                        <div class="alert alert-danger"><?= $this->session->flashdata('error'); ?></div>
                    <?php endif; ?>

                    <?= form_open('profile/update') ?>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="fullname" name="fullname"
                            placeholder="Full Name" value="<?= set_value('fullname', $user->fullname); ?>" required>
                        <label for="fullname">Full Name</label>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="fname" name="fname"
                                    placeholder="First Name" value="<?= set_value('fname', $user->fname); ?>">
                                <label for="fname">First Name</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="lname" name="lname"
                                    placeholder="Last Name" value="<?= set_value('lname', $user->lname); ?>">
                                <label for="lname">Last Name</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-floating mb-3">
                        <select id="status" name="status" class="form-select">
                            <option value="active" <?= ($user->status === 'active') ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?= ($user->status === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                        <label for="status">Status</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="email" name="email"
                            placeholder="Email" value="<?= set_value('email', $user->email); ?>" readonly>
                        <label for="email">Email</label>
                    </div>
                    <div class="d-flex justify-content-center gap-3 mt-4">
                        <button type="submit" class="btn btn-success btn-lg shadow-sm">
                            <i class="fas fa-save me-1"></i> Save Changes
                        </button>
                        <a href="<?= site_url('profile/profile'); ?>" class="btn btn-secondary btn-lg shadow-sm">
                            <i class="fas fa-times me-1"></i> Cancel
                        </a>
                    </div>

                    <?= form_close(); ?>
                </div>

                <div class="card-footer text-center" style="background-color: #f8ea687a;">
                    <small class="text-muted">Update your profile information anytime.</small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('partials/footer'); ?>