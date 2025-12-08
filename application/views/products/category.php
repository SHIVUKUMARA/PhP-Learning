<?php
$data['title'] = "Create Category";
$this->load->view('partials/header', $data);
?>

<div class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">
        <?php $this->load->view('navbar/headernav'); ?>
        <?php $this->load->view('navbar/sidebar'); ?>

        <div class="container mt-4">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Create New Category</h5>
                        </div>
                        <div class="card-body">
                            <?php if ($this->session->flashdata('success')): ?>
                                <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
                            <?php endif; ?>

                            <form action="<?= base_url('products/category') ?>" method="post">
                                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>"
                                    value="<?= $this->security->get_csrf_hash(); ?>" />

                                <div class="mb-3">
                                    <label for="category_name" class="form-label">Category Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="category_name" name="category_name" placeholder="Enter category name" value="" required>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <div>
                                        <button type="submit" class="btn btn-success">Create Category</button>
                                        <a href="<?= base_url('products') ?>" class="btn btn-secondary">Cancel</a>
                                    </div>
                                    <div>
                                        <a href="<?= base_url('products/subcategory') ?>" class="btn btn-warning">
                                            Add Subcategory
                                        </a>
                                    </div>
                                </div>

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