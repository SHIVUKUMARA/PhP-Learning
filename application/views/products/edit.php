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

                            <!-- Product Name -->
                            <div class="col-md-6 mb-3">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control"
                                    value="<?= htmlspecialchars($product->name); ?>" required>
                            </div>

                            <!-- Category -->
                            <div class="col-md-6 mb-3">
                                <label>Category</label>
                                <select name="category" id="category" class="form-control">
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['cat_id'] ?>"
                                            <?= ($product->category == $cat['cat_id'] ? 'selected' : '') ?>>
                                            <?= htmlspecialchars($cat['cat_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Subcategory -->
                            <div class="col-md-6 mb-3">
                                <label>Sub Category</label>
                                <select name="sub_category" id="sub_category" class="form-control">
                                    <option value="">Select Subcategory</option>
                                    <?php foreach ($subcategories as $sub): ?>
                                        <option value="<?= $sub['sub_cat_id'] ?>"
                                            <?= ($product->sub_category == $sub['sub_cat_id'] ? 'selected' : '') ?>>
                                            <?= htmlspecialchars($sub['sub_cat_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Stock -->
                            <div class="col-md-6 mb-3">
                                <label>Stock</label>
                                <input type="number" name="stock" class="form-control"
                                    value="<?= $product->stock; ?>">
                            </div>

                            <!-- Description -->
                            <div class="col-md-12 mb-3">
                                <label>Description</label>
                                <textarea name="description" id="description"
                                    class="form-control"><?= htmlspecialchars($product->description); ?></textarea>
                            </div>

                            <!-- Availability -->
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

                            <!-- Price -->
                            <div class="col-md-6 mb-3">
                                <label>Price</label>
                                <input type="number" step="0.01" name="price" class="form-control"
                                    value="<?= $product->price; ?>" required>
                            </div>

                            <!-- Image Upload / URL -->
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
                                    <input type="url" name="image_url" id="image_url" class="form-control"
                                        placeholder="https://example.com/image.jpg">
                                </div>
                            </div>

                        </div>

                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save me-1"></i> Update Product
                        </button>

                        <a href="<?= site_url('products'); ?>" class="btn btn-secondary ms-2">
                            <i class="bi bi-x-lg me-1"></i> Cancel
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

<!-- CKEditor -->
<script src="https://cdn.ckeditor.com/ckeditor5/41.0.0/classic/ckeditor.js"></script>

<script>
    $(document).ready(function() {
        var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
        var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';

        // CKEditor
        ClassicEditor.create(document.querySelector('#description'))
            .then(editor => {
                window.descriptionEditor = editor;
            })
            .catch(error => console.error(error));

        // Category -> Subcategory AJAX
        $('#category').on('change', function() {
            var cat_id = $(this).val();
            if (!cat_id) {
                $('#sub_category').html('<option value="">Select Subcategory</option>');
                return;
            }

            var postData = {};
            postData['cat_id'] = cat_id;
            postData[csrfName] = csrfHash;

            $.ajax({
                url: "<?= site_url('products/get_subcategories_ajax') ?>",
                method: "POST",
                data: postData,
                dataType: "json",
                success: function(response) {
                    csrfHash = response.csrfHash;
                    $('#sub_category').html('<option value="">Select Subcategory</option>');
                    $.each(response.data, function(i, item) {
                        $('#sub_category').append('<option value="' + item.sub_cat_id + '">' + item.sub_cat_name + '</option>');
                    });
                }
            });
        });

        // Image preview
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

        // Form submission
        $('#editProductForm').on('submit', function(e) {
            e.preventDefault();
            if (window.descriptionEditor) $('#description').val(window.descriptionEditor.getData());

            var formData = new FormData(this);
            formData.set(csrfName, csrfHash);

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#formMessage').html('<div class="alert alert-success">' + response.message + '</div>');
                    } else {
                        $('#formMessage').html('<div class="alert alert-danger">' + response.message + '</div>');
                    }
                },
                error: function(xhr) {
                    $('#formMessage').html('<div class="alert alert-danger">Something went wrong! Please try again.</div>');
                }
            });
        });
    });
</script>