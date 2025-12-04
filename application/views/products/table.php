<?php
$data['title'] = "Products | AdminLTE";
$this->load->view('partials/header', $data);
?>

<div class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">
        <?php $this->load->view('navbar/headernav'); ?>
        <?php $this->load->view('navbar/sidebar'); ?>

        <main class="app-main">
            <div class="container-fluid mt-4">
                <div class="d-flex mb-3 align-items-center gap-2">
                    <form method="get" action="<?= base_url('products/table'); ?>" class="d-flex gap-2">

                        <select name="category_sub" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Select Category --</option>

                            <?php foreach ($nested_categories as $cat => $subcats): ?>
                                <optgroup label="<?= $cat; ?>">
                                    <?php foreach ($subcats as $sc): ?>
                                        <option value="<?= $cat . '||' . $sc['sub_category']; ?>"
                                            <?= ($selected_category == $cat && $selected_subcategory == $sc['sub_category']) ? 'selected' : ''; ?>>
                                            <?= $sc['sub_category']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endforeach; ?>
                        </select>

                    </form>
                </div>
                <div class="card shadow-sm">

                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0 text-center">Products List</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height:500px; overflow:auto; min-height:350px;">
                            <table class="table table-striped table-hover align-middle text-center table-bordered">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th>ID</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Subcategory</th>
                                        <th>Stock</th>
                                        <th>Price</th>
                                        <th>Availability</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($products)): ?>
                                        <?php foreach ($products as $p): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($p->id); ?></td>
                                                <td><?php
                                                    if ($p->image) {
                                                        $img_src = filter_var($p->image, FILTER_VALIDATE_URL) ? $p->image : base_url('assets/uploads/products/' . $p->image);
                                                    } else {
                                                        $img_src = base_url('assets/uploads/products/default.png');
                                                    }
                                                    ?>
                                                    <img src="<?= $img_src ?>" class="card-img-top" style="height:100px; object-fit:cover;">
                                                </td>
                                                <td><?= htmlspecialchars($p->name); ?></td>
                                                <td><?= htmlspecialchars($p->category); ?></td>
                                                <td><?= htmlspecialchars($p->sub_category ?? 'NA'); ?></td>
                                                <td><?= htmlspecialchars($p->stock); ?></td>
                                                <td><?= htmlspecialchars($p->price); ?></td>
                                                <td><?= htmlspecialchars(ucfirst($p->availability)); ?></td>
                                                <td><?= date('M d, Y - h:i A', strtotime($p->created_at)); ?></td>
                                                <td><?= date('M d, Y - h:i A', strtotime($p->updated_at)); ?></td>
                                                <td>
                                                    <a href="<?= base_url('products/view/' . $p->id); ?>" class="btn btn-sm btn-primary me-1"><i class="bi bi-eye"></i></a>
                                                    <?php if ($this->session->userdata('role') === 'admin'): ?>
                                                        <a href="<?= base_url('products/edit/' . $p->id); ?>" class="btn btn-sm btn-info me-1"><i class="bi bi-pencil-square"></i></a>
                                                        <a href="<?= base_url('products/delete/' . $p->id); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this product?');"><i class="bi bi-trash"></i></a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="10" class="text-center">No products found.</td>
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