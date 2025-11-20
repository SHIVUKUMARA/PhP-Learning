<?php
$data['title'] = "My Profile | AdminLTE";
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
                <div class="card shadow-lg border-0 rounded-lg text-center" style="width: 400px; background-color: #a0ce4b86; position: relative; padding-top: 10px;">

                    <div class="position-absolute" style="top: -50px; left: 50%; transform: translateX(-50%);">
                        <img src="<?= base_url('assets/images/user8-128x128.jpg'); ?>"
                            class="rounded-circle border border-5 border-grey shadow-sm"
                            alt="User Avatar"
                            style="width: 100px; height: 100px; object-fit: cover;">
                    </div>

                    <div class="card-body mt-4">
                        <h3 class="fw-bold mb-2">
                            Full Name: <?= htmlspecialchars($user->fullname); ?>
                        </h3>

                        <p class="mb-2"><strong>First Name:</strong> <?= htmlspecialchars($user->fname ?? 'Not set'); ?></p>
                        <p class="mb-2"><strong>Last Name:</strong> <?= htmlspecialchars($user->lname ?? 'Not set'); ?></p>
                        <p class="mb-2"><strong>Status:</strong> <?= htmlspecialchars($user->status ?? 'active'); ?></p>
                        <p class="mb-2"><i class="fas fa-envelope me-2"></i>Email: <?= htmlspecialchars($user->email); ?></p>

                        <small class="text-muted d-block mb-4">
                            Registered on: <?= date('d M Y H:i A', strtotime($user->created_at ?? 'now')); ?><br>
                            Last Updated on: <?= date('d M Y H:i A', strtotime($user->last_updated ?? $user->created_at)); ?>
                        </small>

                        <div class="d-flex justify-content-center gap-2">
                            <a href="<?= site_url('dashboard/edit_user/' . $user->id); ?>" class="btn btn-warning btn-lg shadow-sm">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>

                            <a href="<?= site_url('dashboard/delete_user/' . $user->id); ?>"
                                class="btn btn-danger btn-lg shadow-sm"
                                onclick="return confirm('Are you sure you want to delete this user?');">
                                <i class="fas fa-trash me-1"></i> Delete
                            </a>
                        </div>
                    </div>

                    <div class="card-footer text-center" style="background-color: #a0ce4b86;">
                        <small class="text-muted">You can update your info anytime.</small>
                    </div>

                </div>
            </div>
        </main>

        <?php $this->load->view('navbar/footer'); ?>

    </div>
</div>

<?php $this->load->view('partials/footer'); ?>