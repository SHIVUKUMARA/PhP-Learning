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

                    <!-- Product Name -->
                    <div class="form-floating mb-3">
                        <input type="text" name="name" class="form-control" id="name" placeholder="Product Name" required>
                        <label for="name">Product Name</label>
                    </div>

                    <!-- Category & Subcategory -->
                    <div class="row g-3 mb-3">
                        <div class="form-floating col-md-6">
                            <select name="category" class="form-select" id="category" required>
                                <option value="">Select Category</option>
                                <?php foreach ($this->Product_model->get_all_categories() as $cat): ?>
                                    <option value="<?= $cat['cat_id'] ?>"><?= $cat['cat_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="category">Category</label>
                        </div>
                        <div class="form-floating col-md-6">
                            <select name="sub_category" class="form-select" id="sub_category" required>
                                <option value="">Select Subcategory</option>
                            </select>
                            <label for="sub_category">Subcategory</label>
                        </div>
                    </div>

                    <!-- Stock & Availability -->
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
                            <label for="availability">Availability</label>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-control"></textarea>
                    </div>

                    <!-- Price -->
                    <div class="form-floating mb-3">
                        <input type="number" step="0.01" name="price" class="form-control" id="price" placeholder="Price" required>
                        <label for="price">Price (₹)</label>
                    </div>

                    <!-- Image Upload / URL -->
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

                    <!-- Submit / Cancel -->
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

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/41.0.0/classic/ckeditor.js"></script>

<script>
    var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';
    let descriptionEditor;

    // Initialize CKEditor
    ClassicEditor
        .create(document.querySelector('#description'))
        .then(editor => {
            descriptionEditor = editor;
        })
        .catch(error => console.error(error));

    // Category -> Subcategory AJAX
    $('#category').on('change', function() {
        var cat_id = $(this).val();
        if (cat_id) {
            var postData = {};
            postData['cat_id'] = cat_id;
            postData[csrfName] = csrfHash;

            $.ajax({
                url: "<?= site_url('products/get_subcategories_ajax') ?>",
                method: "POST",
                data: postData,
                dataType: "json",
                success: function(response) {
                    csrfHash = response.csrfHash; // update CSRF hash
                    $('#sub_category').html('<option value="">-- Select Subcategory --</option>');
                    $.each(response.data, function(i, item) {
                        $('#sub_category').append('<option value="' + item.sub_cat_id + '">' + item.sub_cat_name + '</option>');
                    });
                },
                error: function(xhr) {
                    console.error('AJAX error', xhr);
                }
            });
        } else {
            $('#sub_category').html('<option value="">-- Select Subcategory --</option>');
        }
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
    $('#addProductForm').on('submit', function(e) {
        e.preventDefault();

        // Update textarea with CKEditor content
        if (descriptionEditor) {
            $('#description').val(descriptionEditor.getData());
        }

        let formData = new FormData(this);
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
            success: function(response) {
                csrfHash = '<?= $this->security->get_csrf_hash(); ?>'; // update CSRF hash

                if (response.success) {
                    $('#msg').html('<div class="alert alert-success">' + response.message + '</div>');
                    $('#addProductForm')[0].reset();
                    $('#image_preview').attr('src', '<?= base_url('assets/uploads/products/default.png'); ?>');

                    // Reset CKEditor
                    if (descriptionEditor) descriptionEditor.setData('');
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
</script>