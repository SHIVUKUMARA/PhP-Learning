<?php
$data['title'] = "Create Subcategory";
$this->load->view('partials/header', $data);
?>

<div class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">
        <?php $this->load->view('navbar/headernav'); ?>
        <?php $this->load->view('navbar/sidebar'); ?>

        <div class="container mt-4">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card shadow-sm mb-3">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">Create New Subcategory</h5>
                        </div>
                        <div class="card-body">

                            <?php if ($this->session->flashdata('success_subcategory')): ?>
                                <div class="alert alert-success"><?= $this->session->flashdata('success_subcategory'); ?></div>
                            <?php endif; ?>

                            <?php if (!empty($error)): ?>
                                <div class="alert alert-danger"><?= $error; ?></div>
                            <?php endif; ?>

                            <form action="<?= base_url('products/subcategory') ?>" method="post">
                                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" />

                                <div class="mb-3">
                                    <label for="parent_category" class="form-label">Parent Category <span class="text-danger">*</span></label>
                                    <select class="form-control" id="parent_category" name="parent_category" required>
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?= $cat['cat_id'] ?>"><?= $cat['cat_name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="sub_category_name" class="form-label">Subcategory Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="sub_category_name" name="sub_category_name" placeholder="Enter subcategory name" required>
                                </div>

                                <button type="submit" class="btn btn-success w-100">Create Subcategory</button>
                                <a href="<?= base_url('products') ?>" class="btn btn-secondary w-100 mt-3">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php $this->load->view('navbar/footer'); ?>
    </div>
</div>

<?php $this->load->view('partials/footer'); ?>