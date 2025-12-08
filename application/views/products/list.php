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

                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="categoryDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <?= $selected_category ? htmlspecialchars($selected_category) . ($selected_subcategory ? ' - ' . htmlspecialchars($selected_subcategory) : '') : 'Select Category' ?>
                        </button>
                        <ul class="dropdown-menu p-2" aria-labelledby="categoryDropdown" style="min-width: 220px;" data-bs-auto-close="outside">
                            <?php foreach ($nested_categories as $cat => $subcats): ?>
                                <?php if (!empty($subcats)): ?>
                                    <li class="category-accordion">
                                        <a href="#" class="dropdown-item category-toggle <?= ($selected_category === $cat && !$selected_subcategory) ? 'active' : '' ?>">
                                            <?= htmlspecialchars($cat) ?> <span class="arrow">▼</span>
                                        </a>
                                        <ul class="nested-menu list-unstyled ps-3 mb-0">
                                            <?php foreach ($subcats as $sub): ?>
                                                <li>
                                                    <a href="#" class="dropdown-item subcategory-item <?= ($selected_category === $cat && $selected_subcategory === $sub['sub_category']) ? 'active' : '' ?>"
                                                        data-category="<?= htmlspecialchars($cat) ?>"
                                                        data-subcategory="<?= htmlspecialchars($sub['sub_category']) ?>">
                                                        <?= htmlspecialchars($sub['sub_category']) ?>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </li>
                                <?php else: ?>
                                    <li>
                                        <a class="dropdown-item category-item <?= ($selected_category === $cat) ? 'active' : '' ?>" href="#" data-category="<?= htmlspecialchars($cat) ?>">
                                            <?= htmlspecialchars($cat) ?>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <?php if ($logged_user->role === 'admin'): ?>
                        <div class="d-flex gap-2">
                            <a href="<?= site_url('products/category'); ?>" class="btn btn-success">
                                <i class="bi bi-plus"></i>
                                Add Category
                            </a>
                            <a href="<?= site_url('products/add'); ?>" class="btn btn-primary">
                                <i class="bi bi-plus"></i>
                                Add Product
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($products): ?>
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                        <?php foreach ($products as $p): ?>
                            <div class="col">
                                <div class="card h-100 shadow-sm border-0 position-relative">
                                    <?php
                                    $img_src = $p->image
                                        ? (filter_var($p->image, FILTER_VALIDATE_URL) ? $p->image : base_url('assets/uploads/products/' . $p->image))
                                        : base_url('assets/uploads/products/default.png');
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

                <div class="mt-4"><?= $pagination ?></div>

                <form id="filterForm" method="GET" action="<?= site_url('products') ?>">
                    <input type="hidden" name="category_sub" id="category_sub">
                </form>

            </div>
        </main>

        <?php $this->load->view('navbar/footer'); ?>
    </div>
</div>

<?php $this->load->view('partials/footer'); ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        document.querySelectorAll('.category-item').forEach(function(el) {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('category_sub').value = this.dataset.category;
                document.getElementById('filterForm').submit();
            });
        });

        document.querySelectorAll('.subcategory-item').forEach(function(el) {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('category_sub').value = this.dataset.category + '||' + this.dataset.subcategory;
                document.getElementById('filterForm').submit();
            });
        });

        document.querySelectorAll('.category-toggle').forEach(function(el) {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const parent = this.parentElement;
                parent.classList.toggle('show');

                document.querySelectorAll('.category-accordion').forEach(function(other) {
                    if (other !== parent) other.classList.remove('show');
                });
            });
        });
    });
</script>