<?php
$data['title'] = "Products | AdminLTE";
$data['body_class'] = "hold-transition sidebar-mini layout-fixed";
$this->load->view('partials/header', $data);
?>

<div class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">
        <?php $this->load->view('navbar/headernav'); ?>
        <?php $this->load->view('navbar/sidebar'); ?>

        <main class="app-main py-4">
            <div class="container">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-2">

                    <form method="GET" action="<?= site_url('products'); ?>" class="d-flex gap-2 align-items-center">
                        <select name="category_sub" class="form-select">
                            <option value="">All Categories</option>
                            <?php foreach ($nested_categories as $cat => $subcats): ?>
                                <optgroup label="<?= htmlspecialchars($cat) ?>">
                                    <?php if (!empty($subcats)): ?>
                                        <?php foreach ($subcats as $sub): ?>
                                            <?php
                                            $value = $cat . '||' . $sub['sub_category'];
                                            $selected = ($selected_category == $cat && $selected_subcategory == $sub['sub_category']) ? 'selected' : '';
                                            ?>
                                            <option value="<?= $value ?>" <?= $selected ?>>
                                                <?= htmlspecialchars($sub['sub_category']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="<?= $cat ?>" <?= ($selected_category == $cat && !$selected_subcategory) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat) ?>
                                        </option>
                                    <?php endif; ?>
                                </optgroup>
                            <?php endforeach; ?>
                        </select>

                        <button type="submit" class="btn btn-outline-primary">Filter</button>
                    </form>

                    <?php if ($logged_user->role === 'admin'): ?>
                        <a href="<?= site_url('products/add'); ?>" class="btn btn-primary">
                            <i class="bi bi-plus"></i>
                            Add Product
                        </a>
                    <?php endif; ?>
                </div>

                <?php if ($products): ?>
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">

                        <?php foreach ($products as $p): ?>
                            <div class="col">
                                <div class="card h-100 shadow-sm border-0 position-relative">

                                    <?php
                                    if ($p->image) {
                                        $img_src = filter_var($p->image, FILTER_VALIDATE_URL) ? $p->image : base_url('assets/uploads/products/' . $p->image);
                                    } else {
                                        $img_src = base_url('assets/uploads/products/default.png');
                                    }
                                    ?>
                                    <img src="<?= $img_src ?>" class="card-img-top" style="height:200px; object-fit:cover;">

                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title fw-bold mb-2"><?= htmlspecialchars($p->name) ?></h5>

                                        <p class="card-text text-muted mb-3" style="line-height:1.3;">
                                            <?php
                                            $desc = strip_tags($p->description);
                                            echo (strlen($desc) > 25) ? substr($desc, 0, 25) . '...' : $desc;
                                            ?>
                                        </p>

                                        <ul class="list-unstyled mt-auto mb-0 small">
                                            <li><strong>Stock:</strong> <?= $p->stock ?> (<?= $p->availability ?>)</li>
                                            <li><strong>Price:</strong> ₹<?= $p->price ?></li>
                                        </ul>

                                        <a href="<?= site_url('products/view/' . $p->id); ?>" class="stretched-link"></a>
                                    </div>

                                </div>
                            </div>
                        <?php endforeach; ?>

                    </div>
                <?php else: ?>
                    <p class="text-center fs-5 mt-5">No products found.</p>
                <?php endif; ?>
                <div class="mt-4">
                    <?= $pagination ?>
                </div>

            </div>
        </main>

        <?php $this->load->view('navbar/footer'); ?>
    </div>
</div>

<?php $this->load->view('partials/footer'); ?>