<?php
defined('BASEPATH') or exit('No direct script access allowed');

$data['title'] = isset($product) ? "Edit Omni Product | AdminLTE" : "Add Omni Product | AdminLTE";
$data['body_class'] = "hold-transition sidebar-mini layout-fixed";
$this->load->view('partials/header', $data);

$csrf_name = $this->security->get_csrf_token_name();
$csrf_hash = $this->security->get_csrf_hash();
$product = $product ?? null; // For Add/Edit form
$action = isset($product) ? site_url('omni/update/' . $product->id) : site_url('omni/store');
?>

<div class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">
        <?php $this->load->view('navbar/headernav'); ?>
        <?php $this->load->view('navbar/sidebar'); ?>

        <main class="app-main" style="min-height: calc(100vh - 56px); padding: 20px;">
            <div class="card shadow-lg border-0 rounded-lg w-100">
                <div class="card-header bg-primary text-white text-center">
                    <h3 class="mb-0"><?= isset($product) ? 'Edit' : 'Add' ?> Omni Product</h3>
                </div>
                <div class="card-body">
                    <div id="msg"></div>
                    <?= form_open_multipart($action, ['id' => 'omniForm']); ?>

                    <!-- Product Name & SKU -->
                    <div class="row g-3 mb-3">
                        <div class="form-floating col-md-6">
                            <input type="text" name="product_name" class="form-control" id="product_name" placeholder="Product Name"
                                value="<?= htmlspecialchars($product->product_name ?? '') ?>" required>
                            <label for="product_name">Product Name</label>
                        </div>
                        <div class="form-floating col-md-6">
                            <input type="text" name="sku" class="form-control" id="sku" placeholder="SKU"
                                value="<?= htmlspecialchars($product->sku ?? '') ?>" required>
                            <label for="sku">SKU</label>
                        </div>
                    </div>

                    <!-- Brand & Category -->
                    <div class="row g-3 mb-3">
                        <div class="form-floating col-md-6">
                            <input type="text" name="brand" class="form-control" id="brand" placeholder="Brand"
                                value="<?= htmlspecialchars($product->brand ?? '') ?>">
                            <label for="brand">Brand</label>
                        </div>
                        <div class="form-floating col-md-6">
                            <input type="text" name="category_name" class="form-control" id="category_name" placeholder="Category Name" required
                                value="<?= htmlspecialchars($product->category_name ?? '') ?>">
                            <label for="category_name">Category Name</label>
                        </div>
                    </div>

                    <!-- Category Code & Price -->
                    <div class="row g-3 mb-3">
                        <div class="form-floating col-md-6">
                            <input type="text" name="category_code" class="form-control" id="category_code" placeholder="Category Code"
                                value="<?= htmlspecialchars($product->category_code ?? '') ?>">
                            <label for="category_code">Category Code</label>
                        </div>
                        <div class="form-floating col-md-6">
                            <input type="number" step="0.01" name="price" class="form-control" id="price" placeholder="Price" required
                                value="<?= htmlspecialchars($product->price ?? '') ?>">
                            <label for="price">Price</label>
                        </div>
                    </div>

                    <!-- Sale Price & Currency -->
                    <div class="row g-3 mb-3">
                        <div class="form-floating col-md-6">
                            <input type="number" step="0.01" name="sale_price" class="form-control" id="sale_price" placeholder="Sale Price"
                                value="<?= htmlspecialchars($product->sale_price ?? '') ?>">
                            <label for="sale_price">Sale Price</label>
                        </div>
                        <div class="form-floating col-md-6">
                            <input type="text" name="currency" class="form-control" id="currency" placeholder="Currency"
                                value="<?= htmlspecialchars($product->currency ?? 'INR') ?>">
                            <label for="currency">Currency</label>
                        </div>
                    </div>

                    <!-- Stock & Dimensions -->
                    <div class="row g-3 mb-3">
                        <div class="form-floating col-md-3">
                            <input type="number" name="stock" class="form-control" id="stock" placeholder="Stock"
                                value="<?= htmlspecialchars($product->stock ?? 0) ?>">
                            <label for="stock">Stock</label>
                        </div>
                        <div class="form-floating col-md-3">
                            <input type="number" step="0.01" name="weight" class="form-control" id="weight" placeholder="Weight"
                                value="<?= htmlspecialchars($product->weight ?? '') ?>">
                            <label for="weight">Weight</label>
                        </div>
                        <div class="form-floating col-md-2">
                            <input type="number" step="0.01" name="length" class="form-control" id="length" placeholder="Length"
                                value="<?= htmlspecialchars($product->length ?? '') ?>">
                            <label for="length">Length</label>
                        </div>
                        <div class="form-floating col-md-2">
                            <input type="number" step="0.01" name="width" class="form-control" id="width" placeholder="Width"
                                value="<?= htmlspecialchars($product->width ?? '') ?>">
                            <label for="width">Width</label>
                        </div>
                        <div class="form-floating col-md-2">
                            <input type="number" step="0.01" name="height" class="form-control" id="height" placeholder="Height"
                                value="<?= htmlspecialchars($product->height ?? '') ?>">
                            <label for="height">Height</label>
                        </div>
                    </div>

                    <!-- Short Description & Description -->
                    <div class="mb-3">
                        <label for="short_description" class="form-label">Short Description</label>
                        <input type="text" name="short_description" class="form-control" id="short_description"
                            value="<?= htmlspecialchars($product->short_description ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-control" placeholder="Enter product description" required rows="5"><?= htmlspecialchars($product->description ?? '') ?></textarea>
                    </div>

                    <!-- Main Image & Extra Images -->
                    <div class="mb-3">
                        <label for="main_image_url" class="form-label">Main Image URL</label>
                        <input type="url" name="main_image_url" id="main_image_url" class="form-control" placeholder="https://example.com/image.jpg" required
                            value="<?= htmlspecialchars($product->main_image_url ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="extra_image_urls" class="form-label">Extra Image URLs (comma separated)</label>
                        <input type="text" name="extra_image_urls[]" id="extra_image_urls" class="form-control" placeholder="https://example.com/img1.jpg, https://example.com/img2.jpg"
                            value="<?= isset($product->extra_image_urls) ? implode(',', json_decode($product->extra_image_urls, true)) : '' ?>">
                    </div>

                    <!-- Condition, GTIN, MPN, Variant, Video -->
                    <div class="row g-3 mb-3">
                        <div class="form-floating col-md-3">
                            <select name="condition_type" class="form-select" id="condition_type">
                                <option value="new" <?= isset($product) && $product->condition_type === 'new' ? 'selected' : '' ?>>New</option>
                                <option value="used" <?= isset($product) && $product->condition_type === 'used' ? 'selected' : '' ?>>Used</option>
                                <option value="refurbished" <?= isset($product) && $product->condition_type === 'refurbished' ? 'selected' : '' ?>>Refurbished</option>
                            </select>
                            <label for="condition_type">Condition</label>
                        </div>
                        <div class="form-floating col-md-3">
                            <input type="text" name="gtin" class="form-control" id="gtin" placeholder="GTIN" value="<?= htmlspecialchars($product->gtin ?? '') ?>">
                            <label for="gtin">GTIN</label>
                        </div>
                        <div class="form-floating col-md-3">
                            <input type="text" name="mpn" class="form-control" id="mpn" placeholder="MPN" value="<?= htmlspecialchars($product->mpn ?? '') ?>">
                            <label for="mpn">MPN</label>
                        </div>
                        <div class="form-floating col-md-3">
                            <?php
                            $variants = json_decode($product->variant_attributes ?? '{}', true);
                            $variant_text = '';
                            if (is_array($variants) && !empty($variants)) {
                                $pairs = [];
                                foreach ($variants as $key => $value) {
                                    $pairs[] = $key . ':' . $value;
                                }
                                $variant_text = implode(', ', $pairs);
                            }
                            ?>
                            <input type="text"
                                name="variant_attributes"
                                class="form-control"
                                id="variant_attributes"
                                placeholder="Color:Black, Model Year:2026"
                                value="<?= htmlspecialchars($variant_text) ?>">
                            <label for="variant_attributes">Variant Attributes</label>
                        </div>

                    </div>

                    <div class="mb-3">
                        <label for="video_url" class="form-label">Video URL</label>
                        <input type="url" name="video_url" class="form-control" id="video_url" placeholder="https://example.com/video.mp4" value="<?= htmlspecialchars($product->video_url ?? '') ?>">
                    </div>

                    <!-- Status -->
                    <div class="form-floating mb-3">
                        <select name="status" class="form-select" id="status">
                            <?php
                            $statuses = ['DRAFT', 'READY', 'PUBLISHED', 'BLOCKED'];
                            foreach ($statuses as $status) {
                                $selected = (isset($product) && $product->status === $status) ? 'selected' : '';
                                echo "<option value='$status' $selected>$status</option>";
                            }
                            ?>
                        </select>
                        <label for="status">Status</label>
                    </div>

                    <!-- Submit -->
                    <div class="d-flex justify-content-center gap-2 mt-4">
                        <button type="submit" class="btn btn-success btn-lg shadow-sm">
                            <i class="bi bi-plus me-1"></i> <?= isset($product) ? 'Update' : 'Add' ?> Product
                        </button>
                        <a href="<?= site_url('omni') ?>" class="btn btn-secondary btn-lg shadow-sm">
                            <i class="bi bi-x-lg me-1"></i> Cancel
                        </a>
                    </div>

                    <?= form_close(); ?>
                </div>
            </div>
        </main>

        <?php $this->load->view('navbar/footer'); ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    $(document).ready(function() {
        const csrfName = '<?= $csrf_name ?>';
        let csrfHash = '<?= $csrf_hash ?>';

        $('#omniForm').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);

            // Convert variant_attributes to JSON
            const variantInput = $('#variant_attributes').val().trim();
            const variantJSON = {};
            if (variantInput) {
                variantInput.split(',').forEach(item => {
                    const [key, value] = item.split(':').map(s => s.trim());
                    if (key && value) variantJSON[key] = value;
                });
            }
            formData.set('variant_attributes', JSON.stringify(variantJSON));

            // Append CSRF token manually for AJAX
            formData.set(csrfName, csrfHash);

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                beforeSend: function() {
                    $('#msg').html('<div class="alert alert-info">Processing...</div>');
                },
                success: function(resp) {
                    // Update CSRF hash from server response if needed
                    if (resp.csrf) {
                        csrfHash = resp.csrf.hash;
                    }

                    if (resp.ok) {
                        $('#msg').html('<div class="alert alert-success">' + resp.message + '</div>');
                        // If updating, do not reset form; maybe redirect instead
                        if (!<?= isset($product) ? 'true' : 'false' ?>) {
                            $('#omniForm')[0].reset();
                        }
                    } else {
                        $('#msg').html('<div class="alert alert-danger">' + resp.message + '</div>');
                    }
                },
                error: function(xhr) {
                    const err = xhr.responseJSON?.message ?? 'Something went wrong!';
                    $('#msg').html('<div class="alert alert-danger">' + err + '</div>');
                }
            });
        });
    });
</script>