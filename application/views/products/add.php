<?php
$data['title'] = "Add Product | AdminLTE";
$data['body_class'] = "hold-transition sidebar-mini layout-fixed";
$this->load->view('partials/header', $data);
?>

<div class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">

        <?php $this->load->view('navbar/headernav'); ?>
        <?php $this->load->view('navbar/sidebar'); ?>

        <main class="app-main d-flex justify-content-center align-items-center" style="min-height: calc(100vh - 56px); padding: 20px;">
            <div class="card shadow-lg border-0 rounded-lg w-100">
                <div class="card-header bg-primary text-white text-center">
                    <h3 class="mb-0">Add Product</h3>
                </div>
                <div class="card-body">

                    <div id="msg"></div>

                    <?= form_open_multipart('products/add', ['id' => 'addProductForm']); ?>
                    <div class="form-floating mb-3">
                        <input type="text" name="name" class="form-control" id="name" placeholder="Product Name" required>
                        <label for="name">Product Name</label>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="form-floating col-md-6">
                            <select name="category" class="form-select" id="category" required>
                                <option value="">-- Select Category --</option>
                                <option value="Beauty">Beauty</option>
                                <option value="Electronics">Electronics</option>
                                <option value="Clothing">Clothing</option>
                                <option value="Home & Kitchen">Home & Kitchen</option>
                                <option value="Books">Books</option>
                                <option value="Toys">Toys</option>
                                <option value="Sports">Sports</option>
                                <option value="Automotive">Automotive</option>
                                <option value="Health">Health</option>
                                <option value="Grocery">Grocery</option>
                            </select>
                        </div>
                        <div class="form-floating col-md-6">
                            <input type="text" name="sub_category" class="form-control" id="sub_category" placeholder="Sub Category">
                            <label for="sub_category">Sub Category</label>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="form-floating col-md-6">
                            <input type="number" name="stock" class="form-control" id="stock" value="0" placeholder="Stock">
                            <label for="stock">Stock</label>
                        </div>
                        <div class="form-floating col-md-6">
                            <select name="availability" class="form-select" id="availability">
                                <option value="">Availability</option>
                                <option value="In Stock">In Stock</option>
                                <option value="Out of Stock">Out of Stock</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-floating mb-3">
                        <textarea name="description" class="form-control" id="description" placeholder="Product Description" style="height:100px;"></textarea>
                        <label for="description">Description</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="number" step="0.01" name="price" class="form-control" id="price" placeholder="Price" required>
                        <label for="price">Price (₹)</label>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="image_input" class="form-label">Upload Image</label>
                            <input type="file" name="image" id="image_input" class="form-control">
                            <div class="mt-2">
                                <img id="image_preview" src="<?= base_url('assets/uploads/products/default.png'); ?>" class="img-thumbnail" style="height:120px; width:auto;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="image_url" class="form-label">Or Image URL</label>
                            <input type="url" name="image_url" id="image_url" class="form-control" placeholder="https://example.com/image.jpg">
                        </div>
                    </div>

                    <div class="d-flex justify-content-center gap-2 mt-4">
                        <button type="submit" class="btn btn-success btn-lg shadow-sm">
                            <i class="bi bi-plus me-1"></i> Add Product
                        </button>
                        <a href="<?= site_url('products'); ?>" class="btn btn-secondary btn-lg shadow-sm">
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

<script>
    $(document).ready(function() {
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

        $('#addProductForm').on('submit', function(e) {
            e.preventDefault();

            let formData = new FormData(this);

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
                success: function(response) {
                    if (response.success) {
                        $('#msg').html('<div class="alert alert-success">' + response.message + '</div>');
                        $('#addProductForm')[0].reset();
                        $('#image_preview').attr('src', '<?= base_url('assets/uploads/products/default.png'); ?>');
                    } else {
                        $('#msg').html('<div class="alert alert-danger">' + response.message + '</div>');
                    }
                },
                error: function(xhr) {
                    let err = xhr.responseJSON?.message ?? 'Something went wrong!';
                    $('#msg').html('<div class="alert alert-danger">' + err + '</div>');
                }
            });
        });

    });
</script>