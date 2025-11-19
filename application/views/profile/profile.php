<?php 
$data['title'] = "My Profile | AdminLTE";
$data['body_class'] = "hold-transition sidebar-mini layout-fixed";  
$this->load->view('partials/header', $data); 
?>

<nav class="main-header navbar navbar-expand navbar-light bg-light shadow-sm mb-5">
    <div class="container-fluid">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a href="<?= site_url('dashboard/table'); ?>" class="nav-link">
                    <i class="fas fa-arrow-left"></i> Back to Table
                </a>
            </li>
        </ul>
        <span class="navbar-text ms-auto fw-bold">
            Welcome, <?= htmlspecialchars($user->fullname); ?>
        </span>
    </div>
</nav>

<div class="content-wrapper" style="margin-top: 4rem;">
    <div class="content">
        <div class="container d-flex justify-content-center align-items-center" style="min-height:70vh;">
            <div class="card shadow-lg border-0 rounded-lg text-center" style="width: 350px; position: relative; padding-top: 30px; background-color: #a0ce4b86;">

                <!-- Avatar -->
                <div class="avatar-wrapper" style="position: absolute; top: -50px; left: 50%; transform: translateX(-50%);">
                    <img src="<?= base_url('assets/images/user8-128x128.jpg'); ?>" 
                         class="rounded-circle border border-5 border-grey shadow-sm" 
                         alt="User Avatar"
                         style="width: 120px; height: 120px; object-fit: cover;">
                </div>

                <div class="card-body mt-5">
                    <h3 class="fw-bold mb-2">Full Name : <?= htmlspecialchars($user->fullname); ?></h3>

                    <!-- New input fields -->
                    <p class="mb-2"><strong>First Name:</strong> <?= htmlspecialchars($user->fname ?? 'Not set'); ?></p>
                    <p class="mb-2"><strong>Last Name:</strong> <?= htmlspecialchars($user->lname ?? 'Not set'); ?></p>
                    <p class="mb-2"><strong>Status:</strong> <?= htmlspecialchars($user->status ?? 'active'); ?></p>

                    <p class="mb-2"><i class="fas fa-envelope me-2"></i>Email : <?= htmlspecialchars($user->email); ?></p>
                    <small class="text-muted d-block mb-4">
                        Registered on : <?= date('d M Y', strtotime($user->created_at ?? 'now')); ?><br>
                        Last Updated : <?= date('d M Y H:i', strtotime($user->last_updated ?? $user->created_at)); ?>
                    </small>

                    <div class="d-flex justify-content-center gap-3">
                        <a href="<?= site_url('dashboard/edit_user/'.$user->id); ?>" class="btn btn-warning btn-lg shadow-sm">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <a href="<?= site_url('dashboard/delete_user/'.$user->id); ?>" 
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
    </div>
</div>

<?php $this->load->view('partials/footer'); ?>
