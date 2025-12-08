<?php
$data['title'] = "Product Details | AdminLTE";
$data['body_class'] = "hold-transition sidebar-mini layout-fixed";
$this->load->view('partials/header', $data);
?>

<div class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">
        <?php $this->load->view('navbar/headernav'); ?>
        <?php $this->load->view('navbar/sidebar'); ?>

        <main class="app-main py-4">
            <div class="container">

                <div class="card border-0 shadow-lg rounded-5 overflow-hidden">
                    <div class="row g-0">

                        <div class="col-md-6 position-relative">
                            <?php
                            $img_src = $product->image
                                ? (filter_var($product->image, FILTER_VALIDATE_URL) ? $product->image : base_url('assets/uploads/products/' . $product->image))
                                : base_url('assets/uploads/products/default.png');
                            ?>
                            <div class="overflow-hidden rounded-start-5">
                                <img src="<?= $img_src ?>" class="img-fluid w-100 h-100" style="object-fit: cover; transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                            </div>
                        </div>

                        <div class="col-md-6 p-4 d-flex flex-column justify-content-between">
                            <div>
                                <h1 class="fw-bold mb-3"><?= htmlspecialchars($product->name) ?></h1>

                                <div class="mb-3">
                                    <span class="badge bg-primary me-1"><?= htmlspecialchars($product->category_name ?? 'N/A') ?></span>
                                    <?php if (!empty($product->sub_category_name)): ?>
                                        <span class="badge bg-secondary"><?= htmlspecialchars($product->sub_category_name) ?></span>
                                    <?php endif; ?>
                                </div>

                                <p class="text-muted mb-2">
                                    <i class="bi bi-check-circle-fill text-success me-1"></i>
                                    <?= $product->availability ?>
                                </p>

                                <p class="text-muted mb-2">
                                    <i class="bi bi-box-seam me-1"></i>
                                    Stock: <?= $product->stock ?>
                                </p>

                                <h3 class="text-success fw-bold mb-3">₹<?= number_format($product->price, 2) ?></h3>

                                <div class="card bg-light p-3 rounded-4 mb-3 shadow-sm">
                                    <h5 class="fw-bold mb-2"><i class="bi bi-card-text me-2"></i>Description</h5>
                                    <p class="mb-0" style="white-space: pre-line; line-height:1.5;">
                                        <?= nl2br(htmlspecialchars($product->description)) ?>
                                    </p>
                                </div>
                            </div>

                            <div class="d-flex gap-2 flex-wrap mt-3">
                                <a href="<?= site_url('products'); ?>" class="btn btn-outline-dark btn-lg flex-fill">
                                    <i class="bi bi-arrow-left"></i> Back
                                </a>

                                <?php if ($logged_user->role === 'admin'): ?>
                                    <a href="<?= site_url('products/edit/' . $product->id); ?>" class="btn btn-primary btn-lg flex-fill">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    <a href="<?= site_url('products/delete/' . $product->id); ?>" class="btn btn-danger btn-lg flex-fill"
                                        onclick="return confirm('Are you sure you want to delete this product?');">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                <?php endif; ?>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </main>

        <?php $this->load->view('navbar/footer'); ?>
    </div>
</div>

<?php $this->load->view('partials/footer'); ?>