<?php 
$data['title'] = "AdminLTE 4 | Dashboard";
$this->load->view('partials/header', $data); 
?>

<div class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">
        
        <?php $this->load->view('navbar/headernav'); ?>
        <?php $this->load->view('navbar/sidebar'); ?>

        <main class="app-main">
          <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6"><h3 class="mb-0">Users Information</h3></div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="<?= base_url('greet'); ?>">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Users Info</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid mt-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0 text-center">Users List</h5>
                    </div>
                    <div class="card-body p-0">
    <div class="table-responsive" style="max-height:500px; overflow:auto;">
        <table class="table table-striped table-hover align-middle text-center table-bordered" style="min-width:1200px; white-space: nowrap;">
            <thead class="table-light sticky-top">
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Firstname</th>
                    <th>Lastname</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Registered on</th>
                    <th>Updated on</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($users)): ?>
                    <?php $i = $this->uri->segment(3) ? $this->uri->segment(3)+1 : 1; ?>
                    <?php foreach($users as $u): ?>
                        <tr>
                            <td><?= htmlspecialchars($u->id); ?></td>
                            <td><?= htmlspecialchars($u->fullname); ?></td>
                            <td><?= htmlspecialchars($u->fname ?? ''); ?></td>
                            <td><?= htmlspecialchars($u->lname ?? ''); ?></td>
                            <td><?= htmlspecialchars($u->email); ?></td>
                            <td>
                                <div class="form-check form-switch d-inline-block">
                                    <input class="form-check-input status-switch" type="checkbox" <?= $u->status == 'active' ? 'checked' : ''; ?>>
                                </div>
                            </td>
                            <td><?= date('M d, Y - h:i A', strtotime($u->created_at)); ?></td>
                            <td><?= date('M d, Y - h:i A', strtotime($u->last_updated)); ?></td>
                            <td>
                                <a href="<?= base_url('dashboard/view_user/'.$u->id); ?>" class="btn btn-sm btn-primary me-1"><i class="bi bi-eye"></i></a>
                                <a href="<?= base_url('dashboard/edit_user/'.$u->id); ?>" class="btn btn-sm btn-info me-1"><i class="bi bi-pencil-square"></i></a>
                                <a href="<?= base_url('dashboard/delete_user/'.$u->id); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?');"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">No users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

                    <div class="card-footer d-flex justify-content-center">
                        <?= $this->pagination->create_links(); ?>
                    </div>
                </div>
            </div>

        </main>

        <?php $this->load->view('navbar/footer'); ?>

    </div>
</div>

<?php $this->load->view('partials/footer'); ?>