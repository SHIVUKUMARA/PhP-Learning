<?php
defined('BASEPATH') or exit('No direct script access allowed');

$data['title'] = "Published Products | AdminLTE";
$this->load->view('partials/header', $data);
?>

<div class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">

        <?php $this->load->view('navbar/headernav'); ?>
        <?php $this->load->view('navbar/sidebar'); ?>

        <main class="app-main">
            <div class="container-fluid mt-4">

                <!-- HEADER -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Published Products</h5>

                    <select id="platformSelect" class="form-select w-auto">
                        <option value="">Select Platform</option>
                        <option value="Supabase">Supabase</option>
                        <option value="Facebook">Facebook</option>
                        <option value="CrudCrud">CrudCrud</option>
                        <option value="Mock API">Mock API</option>
                    </select>
                </div>

                <!-- TABLE CARD -->
                <div class="card shadow-sm">

                    <div class="card-header bg-success text-white text-center">
                        <h5 class="mb-0">Published Products List</h5>
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
                                        <th>Published At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>

                                <tbody id="publishedProductsTable">
                                    <tr>
                                        <td colspan="10" class="text-muted text-center">
                                            Select a platform to load products
                                        </td>
                                    </tr>
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

<div class="modal fade" id="viewProductModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow-lg border-0">

            <div class="modal-header bg-primary text-white">
                <div>
                    <h4 class="fw-bold mb-0" id="prod_name"></h4>
                    <small>SKU: <span id="prod_sku"></span></small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <div class="row g-4">

                    <div class="col-lg-5 text-center">
                        <img id="productMainImage"
                            class="img-fluid rounded"
                            style="max-height:320px;object-fit:contain;">
                    </div>

                    <div class="col-lg-7">
                        <div class="fs-3 fw-bold text-danger mb-2" id="prod_price"></div>

                        <div class="mb-3">
                            <span class="badge bg-info me-2" id="prod_category"></span>
                            <span class="badge bg-secondary me-2" id="prod_brand"></span>
                            <span class="badge bg-success" id="prod_status"></span>
                        </div>

                        <p id="prod_desc"></p>

                        <div class="row g-3">
                            <div class="col-md-4"><strong>Stock:</strong> <span id="prod_stock"></span></div>
                            <div class="col-md-4"><strong>Weight:</strong> <span id="prod_weight"></span></div>
                            <div class="col-md-4"><strong>Dimensions:</strong> <span id="prod_dimensions"></span></div>
                        </div>

                        <hr>

                        <div><strong>GTIN:</strong> <span id="prod_gtin"></span></div>
                        <div><strong>MPN:</strong> <span id="prod_mpn"></span></div>

                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="editProductModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header bg-warning">
                <h5 class="modal-title">Edit Published Product</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="editForm" class="row g-3">
                    <input type="hidden" id="edit_doc_id">

                    <div class="col-md-6">
                        <label>Product Name</label>
                        <input class="form-control" id="edit_product_name">
                    </div>

                    <div class="col-md-6">
                        <label>SKU</label>
                        <input class="form-control" id="edit_sku">
                    </div>

                    <div class="col-md-4">
                        <label>Price</label>
                        <input type="number" class="form-control" id="edit_price">
                    </div>

                    <div class="col-md-4">
                        <label>Sale Price</label>
                        <input type="number" class="form-control" id="edit_sale_price">
                    </div>

                    <div class="col-md-4">
                        <label>Stock</label>
                        <input type="number" class="form-control" id="edit_stock">
                    </div>

                    <div class="col-md-6">
                        <label>Brand</label>
                        <input class="form-control" id="edit_brand">
                    </div>

                    <div class="col-md-6">
                        <label>Category</label>
                        <input class="form-control" id="edit_category">
                    </div>

                    <div class="col-md-12">
                        <label>Description</label>
                        <textarea class="form-control" id="edit_description"></textarea>
                    </div>

                    <div class="col-md-12">
                        <label>Main Image URL</label>
                        <input class="form-control" id="edit_image">
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button class="btn btn-success" id="saveEdit">Update</button>
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>

        </div>
    </div>
</div>

<script>
    document.getElementById('platformSelect').addEventListener('change', function() {

        const platform = this.value;
        if (!platform) return;

        fetch(`<?= site_url('omni/published/api') ?>?platform=${platform}`)
            .then(res => res.json())
            .then(resp => {

                console.log('API RESPONSE:', resp);

                const tbody = document.getElementById('publishedProductsTable');
                tbody.innerHTML = '';

                if (!resp || resp.ok !== true || !Array.isArray(resp.data)) {
                    tbody.innerHTML = `
                <tr>
                    <td colspan="10" class="text-danger text-center">
                        Invalid API response
                    </td>
                </tr>`;
                    return;
                }

                if (resp.data.length === 0) {
                    tbody.innerHTML = `
                <tr>
                    <td colspan="10" class="text-muted text-center">
                        No products published to this platform
                    </td>
                </tr>`;
                    return;
                }

                resp.data.forEach(p => {
                    // Use '_id' for CrudCrud, 'id' for Beeceptor
                    const docId = p._id ?? p.id ?? '';

                    tbody.innerHTML += `
                <tr>
                    <td>${p.product_id ?? '-'}</td>
                    <td><img src="${p.main_image_url}" height="80"></td>
                    <td>${p.product_name}</td>
                    <td>${p.sku}</td>
                    <td>${p.category_name}</td>
                    <td>${Number(p.price).toFixed(2)} ${p.currency}</td>
                    <td>${p.stock}</td>
                    <td><span class="badge bg-success">PUBLISHED</span></td>
                    <td>${p.published_at ?? '-'}</td>
                    <td>
                        <button class="btn btn-sm btn-primary viewPublished"
                            data-product='${JSON.stringify(p)}'>
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-warning editPublished"
                                data-id="${docId}">
                                <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger deletePublished ms-1"
                                data-id="${docId}">
                                <i class="bi bi-trash"></i>
                        </button>
                    </td>
                    </tr>`;
                });

            })
            .catch(err => {
                console.error(err);
                alert('Failed to load products');
            });
    });

    // Edit Published Product
    let currentEditProduct = null;

    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.editPublished');
        if (!btn) return;

        const row = btn.closest('tr');
        const product = JSON.parse(
            row.querySelector('.viewPublished').dataset.product
        );

        currentEditProduct = product;

        document.getElementById('edit_doc_id').value = product._id || product.id;
        document.getElementById('edit_product_name').value = product.product_name;
        document.getElementById('edit_sku').value = product.sku;
        document.getElementById('edit_price').value = product.price;
        document.getElementById('edit_sale_price').value = product.sale_price;
        document.getElementById('edit_stock').value = product.stock;
        document.getElementById('edit_brand').value = product.brand;
        document.getElementById('edit_category').value = product.category_name;
        document.getElementById('edit_description').value = product.description;
        document.getElementById('edit_image').value = product.main_image_url;

        new bootstrap.Modal(
            document.getElementById('editProductModal')
        ).show();
    });

    document.getElementById('saveEdit').addEventListener('click', function() {
        if (!currentEditProduct) return;

        const platform = document.getElementById('platformSelect').value;
        const docId = document.getElementById('edit_doc_id').value;

        // clone object safely
        const updated = JSON.parse(JSON.stringify(currentEditProduct));

        // ❌ REMOVE ID FIELDS (CrudCrud requirement)
        delete updated._id;
        delete updated.id;

        // ✅ apply edited fields
        updated.product_name = document.getElementById('edit_product_name').value;
        updated.sku = document.getElementById('edit_sku').value;
        updated.price = Number(document.getElementById('edit_price').value);
        updated.sale_price = Number(document.getElementById('edit_sale_price').value);
        updated.stock = Number(document.getElementById('edit_stock').value);
        updated.brand = document.getElementById('edit_brand').value;
        updated.category_name = document.getElementById('edit_category').value;
        updated.description = document.getElementById('edit_description').value;
        updated.main_image_url = document.getElementById('edit_image').value;

        fetch(`<?= site_url('omni/published/api/update') ?>/${docId}?platform=${platform}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(updated)
            })
            .then(res => res.json())
            .then(resp => {
                if (!resp || resp.ok !== true) {
                    throw new Error(resp?.message || 'Update failed');
                }

                alert('Product updated successfully');
                location.reload();
            })
            .catch(err => {
                console.error(err);
                alert(err.message || 'Update failed');
            });
    });

    document.addEventListener('click', function(e) {

        const btn = e.target.closest('.viewPublished');
        if (!btn) return;

        const p = JSON.parse(btn.dataset.product);

        document.getElementById('prod_name').textContent = p.product_name;
        document.getElementById('prod_sku').textContent = p.sku;
        document.getElementById('prod_brand').textContent = p.brand;
        document.getElementById('prod_category').textContent = p.category_name;
        document.getElementById('prod_price').textContent = p.price + ' ' + p.currency;
        document.getElementById('prod_stock').textContent = p.stock;
        document.getElementById('prod_weight').textContent = p.weight;
        document.getElementById('prod_dimensions').textContent =
            `${p.length} × ${p.width} × ${p.height}`;
        document.getElementById('prod_desc').textContent = p.description;
        document.getElementById('prod_gtin').textContent = p.gtin;
        document.getElementById('prod_mpn').textContent = p.mpn;
        document.getElementById('prod_status').textContent = 'PUBLISHED';
        document.getElementById('productMainImage').src = p.main_image_url || '';

        new bootstrap.Modal(document.getElementById('viewProductModal')).show();
    });

    document.addEventListener('click', function(e) {
        const delBtn = e.target.closest('.deletePublished');
        if (!delBtn) return;

        const id = delBtn.dataset.id;
        const platform = document.getElementById('platformSelect').value;

        if (!id) return alert('Missing product ID');
        if (!platform) return alert('Select platform first');
        if (!confirm('Delete this published product?')) return;

        fetch(`<?= site_url('omni/published/api/delete') ?>/${id}?platform=${platform}`, {
                method: 'DELETE'
            })
            .then(res => res.json())
            .then(resp => {
                if (!resp || resp.ok !== true) {
                    alert(resp?.message || 'Delete failed');
                    return;
                }

                delBtn.closest('tr').remove();
                alert(`Deleted from ${platform}`);
            })
            .catch(() => alert('Server error'));
    });
</script>