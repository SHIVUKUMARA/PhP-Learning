<?php
$data['title'] = "Update Profile | AdminLTE";
$data['body_class'] = "hold-transition sidebar-mini layout-fixed";
$this->load->view('partials/header', $data);
?>

<div class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">

        <!-- Header Nav -->
        <?php $this->load->view('navbar/headernav', ['user' => (object)[
            'fullname' => $this->session->userdata('fullname'),
            'created_at' => $this->session->userdata('created_at') ?? date('Y-m-d')
        ]]); ?>

        <!-- Sidebar -->
        <?php $this->load->view('navbar/sidebar'); ?>

        <main class="app-main d-flex justify-content-center align-items-center" style="min-height: calc(100vh - 56px); padding: 20px;">
            <div class="d-flex justify-content-center align-items-center w-100" style="min-height: 100%;">
                <div class="card shadow-lg border-0 rounded-lg text-center" style="width: 450px; background-color: #f8ea687a; position: relative; padding-top: 10px;">

                    <div class="position-relative">
                        <img src="<?= $user->avatar_url; ?>" class="rounded-circle border border-5 border-grey shadow-sm"
                            style="width:120px;height:120px;object-fit:cover;">
                    </div>

                    <div class="card-body p-4">

                        <?php if ($this->session->flashdata('success')): ?>
                            <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
                        <?php endif; ?>
                        <?php if ($this->session->flashdata('error')): ?>
                            <div class="alert alert-danger"><?= $this->session->flashdata('error'); ?></div>
                        <?php endif; ?>

                        <?= form_open_multipart('profile/update') ?>
                        <input type="hidden" name="user_id" value="<?= $user->id ?>">
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

                        <div class="mb-3 text-start">
                            <label class="form-label fw-bold">Profile Image</label>
                            <input type="file" name="userfile" class="form-control">
                        </div>

                        <div class="d-flex justify-content-center gap-2 mt-4">
                            <button type="submit" class="btn btn-success btn-lg shadow-sm">
                                <i class="fas fa-save me-1"></i> Save Changes
                            </button>
                            <a href="<?= site_url('dashboard/table'); ?>" class="btn btn-danger btn-lg shadow-sm">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                        </div>

                        <?= form_close(); ?>

                    </div>

                    <div class="card-footer text-center" style="background-color: #f8ea687a;">
                        <small class="text-muted">Update user information anytime.</small>
                    </div>

                </div>
            </div>
        </main>

        <?php $this->load->view('navbar/footer'); ?>

    </div>
</div>

<?php $this->load->view('partials/footer'); ?>

<script>
    document.querySelector('input[name="userfile"]').addEventListener('change', function(event) {
        const [file] = event.target.files;
        if (file) {
            const img = document.querySelector('.position-relative img');
            img.src = URL.createObjectURL(file);
        }
    });
</script>