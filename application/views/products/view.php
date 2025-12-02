<?php
$data['title'] = "Product Details | AdminLTE";
$data['body_class'] = "hold-transition sidebar-mini layout-fixed";
$this->load->view('partials/header', $data);
?>

<div class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">
        <?php $this->load->view('navbar/headernav'); ?>
        <?php $this->load->view('navbar/sidebar'); ?>

        <main class="app-main" style="padding: 20px;">
            <div class="container mt-2">

                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0 text-center"><?= $product->name ?></h3>
                    </div>
                    <div class="card-body row">

                        <div class="col-md-6 text-center">
                            <?php if ($product->image): ?>
                                <?php if (filter_var($product->image, FILTER_VALIDATE_URL)): ?>
                                    <img src="<?= $product->image ?>" class="img-fluid" style="max-height:400px; object-fit:cover;">
                                <?php else: ?>
                                    <img src="<?= base_url('assets/uploads/products/' . $product->image) ?>" class="img-fluid" style="max-height:400px; object-fit:cover;">
                                <?php endif; ?>
                            <?php else: ?>
                                <img src="<?= base_url('assets/uploads/products/default.png') ?>" class="img-fluid" style="max-height:400px; object-fit:cover;">
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <p><strong>Name:</strong> <?= $product->name ?></p>
                            <p><strong>Category:</strong> <?= $product->category ?> / <?= $product->sub_category ?></p>
                            <p><strong>Availability: </strong><?= $product->availability ?></p>
                            <p><strong>Stock:</strong> <?= $product->stock ?></p>
                            <p><strong>Price:</strong> ₹<?= $product->price ?></p>
                            <p><strong>Description:</strong><br><?= $product->description ?></p>
                        </div>

                    </div>
                    <div class="card-footer text-center">
                        <a href="<?= site_url('products'); ?>" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>

                        <?php if ($logged_user->role === 'admin'): ?>
                            <a href="<?= site_url('products/edit/' . $product->id); ?>" class="btn btn-primary">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                            <a href="<?= site_url('products/delete/' . $product->id); ?>" class="btn btn-danger"
                                onclick="return confirm('Are you sure you want to delete this product?');">
                                <i class="bi bi-trash"></i> Delete
                            </a>
                        <?php endif; ?>
                    </div>

                </div>

            </div>
        </main>

        <?php $this->load->view('navbar/footer'); ?>
    </div>
</div>

<?php $this->load->view('partials/footer'); ?>