<?php
defined('BASEPATH') or exit('No direct script access allowed');

$data['title'] = "Omni Products | AdminLTE";
$this->load->view('partials/header', $data);

$csrf_name = $this->security->get_csrf_token_name();
$csrf_hash = $this->security->get_csrf_hash();
?>

<div class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">

        <?php $this->load->view('navbar/headernav'); ?>
        <?php $this->load->view('navbar/sidebar'); ?>

        <main class="app-main">
            <div class="container-fluid mt-4">

                <!-- HEADER -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Omni Channel Products</h5>
                    <a href="<?= site_url('omni/create') ?>" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add Product
                    </a>
                </div>

                <!-- TABLE CARD -->
                <div class="card shadow-sm">

                    <div class="card-header bg-primary text-white text-center">
                        <h5 class="mb-0">Products List</h5>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive"
                            style="max-height:500px; overflow:auto; min-height:350px;">

                            <table class="table table-striped table-hover align-middle text-center table-bordered">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th>ID</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>SKU</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Updated</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>

                                <tbody id="omniProductsTable">
                                    <?php if (!empty($products)): ?>
                                        <?php foreach ($products as $p): ?>

                                            <?php
                                            $img = filter_var($p->main_image_url, FILTER_VALIDATE_URL)
                                                ? $p->main_image_url
                                                : base_url('assets/images/no-image.png');
                                            ?>

                                            <tr>
                                                <td><?= $p->id ?></td>

                                                <td>
                                                    <img src="<?= $img ?>"
                                                        style="height:80px; object-fit:cover;"
                                                        class="rounded border">
                                                </td>

                                                <td><?= htmlspecialchars($p->product_name) ?></td>
                                                <td><?= htmlspecialchars($p->sku) ?></td>
                                                <td><?= htmlspecialchars($p->category_name) ?></td>
                                                <td><?= number_format($p->price, 2) ?> <?= $p->currency ?></td>
                                                <td><?= $p->stock ?></td>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        <?= $p->status ?>
                                                    </span>
                                                </td>

                                                <td><?= date('M d, Y', strtotime($p->created_at)) ?></td>
                                                <td><?= date('M d, Y', strtotime($p->updated_at)) ?></td>

                                                <td>
                                                    <button class="btn btn-sm btn-primary viewOmni"
                                                        data-id="<?= $p->id ?>">
                                                        <i class="bi bi-eye"></i>
                                                    </button>

                                                    <a href="<?= site_url('omni/edit/' . $p->id) ?>" class="btn btn-sm btn-info">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>

                                                    <button class="btn btn-sm btn-danger deleteOmni"
                                                        data-id="<?= $p->id ?>">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>

                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="11" class="text-center">
                                                No omni products found
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>

                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </main>

        <?php $this->load->view('navbar/footer'); ?>
    </div>
</div>

<?php $this->load->view('partials/footer'); ?>

<div class="modal fade" id="viewProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow-lg border-0">

            <!-- HEADER -->
            <div class="modal-header bg-primary text-white">
                <div>
                    <h4 class="fw-bold mb-0" id="prod_name"></h4>
                    <small>SKU: <span id="prod_sku"></span></small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body p-4">
                <div class="row g-4">

                    <!-- LEFT : IMAGES -->
                    <div class="col-lg-5">
                        <div class="border rounded p-3 bg-light text-center">
                            <img id="productMainImage"
                                class="img-fluid rounded"
                                style="max-height:320px; object-fit:contain;">
                        </div>

                        <div id="prod_extra_images"
                            class="d-flex flex-wrap gap-2 justify-content-center mt-3">
                        </div>
                    </div>

                    <!-- RIGHT : DETAILS -->
                    <div class="col-lg-7">

                        <!-- PRICE -->
                        <div class="mb-3">
                            <span class="fs-3 fw-bold text-danger" id="prod_price"></span>
                            <span class="text-muted text-decoration-line-through ms-2"
                                id="prod_sale_price"></span>
                        </div>

                        <!-- BADGES -->
                        <div class="mb-3">
                            <span class="badge bg-info me-2" id="prod_category"></span>
                            <span class="badge bg-secondary me-2" id="prod_brand"></span>
                            <span class="badge bg-success" id="prod_status"></span>
                        </div>

                        <!-- QUICK STATS -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <div class="border rounded p-3 text-center">
                                    <small class="text-muted">Stock</small>
                                    <div class="fw-bold fs-5" id="prod_stock"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3 text-center">
                                    <small class="text-muted">Weight (kg)</small>
                                    <div class="fw-bold fs-5" id="prod_weight"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3 text-center">
                                    <small class="text-muted">Dimensions</small>
                                    <div class="fw-bold fs-6" id="prod_dimensions"></div>
                                </div>
                            </div>
                        </div>

                        <!-- DESCRIPTIONS -->
                        <div class="mb-3">
                            <h6 class="fw-bold">Short Description</h6>
                            <p class="text-muted small" id="prod_short_desc"></p>

                            <h6 class="fw-bold">Full Description</h6>
                            <p id="prod_desc"></p>
                        </div>

                        <!-- ATTRIBUTES -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <strong>Condition:</strong>
                                    <div id="prod_condition"></div>

                                    <strong class="mt-2 d-block">Variants:</strong>
                                    <div id="prod_variants"></div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <strong>GTIN:</strong>
                                    <div id="prod_gtin"></div>

                                    <strong class="mt-2 d-block">MPN:</strong>
                                    <div id="prod_mpn"></div>
                                </div>
                            </div>
                        </div>

                        <!-- VIDEO -->
                        <div class="mb-3">
                            <h6 class="fw-bold">Product Video</h6>
                            <div id="prod_video"></div>
                        </div>

                        <!-- META -->
                        <div class="border-top pt-3 text-muted small">
                            <div>ID: <span id="prod_id"></span></div>
                            <div>Created: <span id="prod_created"></span></div>
                            <div>Updated: <span id="prod_updated"></span></div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="modal-footer bg-light">
                <button class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {

        // =============================
        // DELETE PRODUCT (AJAX)
        // =============================
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.deleteOmni');
            if (!btn) return;

            if (!confirm('Delete this omni product?')) return;

            const fd = new FormData();
            fd.append('<?= $csrf_name ?>', '<?= $csrf_hash ?>');

            fetch('<?= base_url("omni/delete/") ?>' + btn.dataset.id, {
                    method: 'POST',
                    body: fd
                })
                .then(res => res.json())
                .then(res => {
                    if (res.ok) {
                        location.reload();
                    } else {
                        alert(res.message);
                    }
                })
                .catch(() => alert('Something went wrong while deleting the product.'));
        });

        // View Product Details
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.viewOmni');
            if (!btn) return;

            fetch('<?= site_url("omni/get_product/") ?>' + btn.dataset.id)
                .then(res => res.json())
                .then(resp => {
                    if (!resp.ok) {
                        alert(resp.message);
                        return;
                    }

                    const p = resp.data;

                    document.getElementById('productMainImage').src = p.main_image_url || '';
                    document.getElementById('prod_id').textContent = p.id;
                    document.getElementById('prod_name').textContent = p.product_name;
                    document.getElementById('prod_sku').textContent = p.sku;
                    document.getElementById('prod_brand').textContent = p.brand;
                    document.getElementById('prod_category').textContent = p.category_name;
                    document.getElementById('prod_price').textContent = p.price + ' ' + p.currency;
                    document.getElementById('prod_sale_price').textContent = p.sale_price + ' ' + p.currency;
                    document.getElementById('prod_stock').textContent = p.stock;
                    document.getElementById('prod_weight').textContent = p.weight;
                    document.getElementById('prod_dimensions').textContent = `${p.length} × ${p.width} × ${p.height}`;
                    document.getElementById('prod_short_desc').textContent = p.short_description;
                    document.getElementById('prod_desc').textContent = p.description;

                    // Extra images
                    let extraHTML = '';
                    let images = [];

                    try {
                        let parsed = JSON.parse(p.extra_image_urls);
                        if (Array.isArray(parsed)) {
                            parsed.forEach(item => {
                                if (typeof item === 'string') {
                                    images.push(
                                        ...item.split(',').map(u => u.trim()).filter(Boolean)
                                    );
                                }
                            });
                        }
                    } catch (e) {
                        images = p.extra_image_urls ?
                            p.extra_image_urls.split(',').map(u => u.trim()) : [];
                    }

                    images.forEach(url => {
                        extraHTML += `
                        <img src="${url}"
                            style="height:80px;object-fit:cover"
                            class="rounded border m-1">`;
                    });

                    document.getElementById('prod_extra_images').innerHTML =
                        extraHTML || '<span class="text-muted">No extra images</span>';

                    const variantsBox = document.getElementById('prod_variants');
                    variantsBox.innerHTML = '';

                    if (p.variant_attributes) {
                        let variantsObj = null;

                        try {
                            variantsObj = JSON.parse(p.variant_attributes);
                        } catch (e) {
                            variantsObj = null;
                        }

                        if (
                            variantsObj &&
                            typeof variantsObj === 'object' &&
                            !Array.isArray(variantsObj) &&
                            Object.keys(variantsObj).length > 0
                        ) {
                            let html = '<ul class="list-unstyled mb-0">';
                            for (const key in variantsObj) {
                                html += `
                <li>
                    <strong>${key}:</strong>
                    <span class="text-muted">${variantsObj[key]}</span>
                </li>`;
                            }
                            html += '</ul>';
                            variantsBox.innerHTML = html;
                        } else {
                            variantsBox.innerHTML = '<span class="text-muted">No variants</span>';
                        }
                    } else {
                        variantsBox.innerHTML = '<span class="text-muted">No variants</span>';
                    }

                    document.getElementById('prod_condition').textContent = p.condition_type;
                    document.getElementById('prod_gtin').textContent = p.gtin;
                    document.getElementById('prod_mpn').textContent = p.mpn;
                    document.getElementById('prod_video').innerHTML = p.video_url ? `<a href="${p.video_url}" target="_blank">Watch Video</a>` : '';
                    document.getElementById('prod_status').textContent = p.status;
                    document.getElementById('prod_created').textContent = p.created_at;
                    document.getElementById('prod_updated').textContent = p.updated_at;

                    // Show modal
                    const viewModal = new bootstrap.Modal(document.getElementById('viewProductModal'));
                    viewModal.show();
                })
                .catch(() => alert('Failed to fetch product details.'));
        });

    });
</script>