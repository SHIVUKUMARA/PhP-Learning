<?php
$data['title'] = "Edit Product | AdminLTE";
$data['body_class'] = "hold-transition sidebar-mini layout-fixed";
$this->load->view('partials/header', $data);
?>

<div class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">
        <?php $this->load->view('navbar/headernav'); ?>
        <?php $this->load->view('navbar/sidebar'); ?>

        <main class="app-main" style="padding: 20px;">
            <div class="container-fluid">

                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-white">
                        <h3 class="mb-0">Edit Product</h3>
                    </div>

                    <div class="card-body">

                        <div id="formMessage"></div>

                        <?= form_open_multipart('products/edit/' . $product->id, ['id' => 'editProductForm']); ?>

                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control"
                                    value="<?= $product->name; ?>" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Category</label>
                                <input type="text" name="category" class="form-control"
                                    value="<?= $product->category; ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Sub Category</label>
                                <input type="text" name="sub_category" class="form-control"
                                    value="<?= $product->sub_category; ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Stock</label>
                                <input type="number" name="stock" class="form-control"
                                    value="<?= $product->stock; ?>">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label>Description</label>
                                <textarea name="description" class="form-control"><?= $product->description; ?></textarea>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Availability</label>
                                <select name="availability" class="form-control">
                                    <option value="In Stock" <?= $product->availability == 'In Stock' ? 'selected' : ''; ?>>
                                        In Stock
                                    </option>
                                    <option value="Out of Stock" <?= $product->availability == 'Out of Stock' ? 'selected' : ''; ?>>
                                        Out of Stock
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Price</label>
                                <input type="number" step="0.01" name="price" class="form-control"
                                    value="<?= $product->price; ?>" required>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="image_input" class="form-label">Upload Image</label>
                                    <input type="file" name="image" id="image_input" class="form-control">
                                    <div class="mt-2">
                                        <img id="image_preview"
                                            src="<?= $product->image ? (filter_var($product->image, FILTER_VALIDATE_URL) ? $product->image : base_url('assets/uploads/products/' . $product->image)) : base_url('assets/uploads/products/default.png'); ?>"
                                            class="img-thumbnail" style="height:120px; width:auto;">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="image_url" class="form-label">Or enter Image URL</label>
                                    <input type="url" name="image_url" id="image_url" class="form-control" placeholder="https://example.com/image.jpg">
                                </div>
                            </div>

                        </div>

                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save me-1"></i> Update Product
                        </button>

                        <a href="<?= site_url('products'); ?>" class="btn btn-secondary ms-2">
                            <i class="bi bi-x-lg me-1"></i>Cancel
                        </a>

                        <?= form_close(); ?>

                    </div>
                </div>

            </div>
        </main>

        <?php $this->load->view('navbar/footer'); ?>

    </div>
</div>

<?php $this->load->view('partials/footer'); ?>

<script>
    $(document).ready(function() {

        $('#editProductForm').on('submit', function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            var msgContainer = $('#formMessage');
            msgContainer.html('');

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                dataType: 'json',
                contentType: false,
                processData: false,
                success: function(data) {
                    if (data.success) {
                        msgContainer.html('<div class="alert alert-success">' + data.message + '</div>');
                    } else {
                        msgContainer.html('<div class="alert alert-danger">' + data.message + '</div>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr, status, error);
                    msgContainer.html('<div class="alert alert-danger">Something went wrong! Please try again.</div>');
                }
            });
        });

        $('#image_input').on('change', function() {
            const [file] = this.files;
            if (file) {
                $('#image_preview').attr('src', URL.createObjectURL(file));
                $('#image_url').val('');
            }
        });

        $('#image_url').on('input', function() {
            const url = $(this).val();
            if (url) {
                $('#image_preview').attr('src', url);
                $('#image_input').val('');
            }
        });

    });
</script>