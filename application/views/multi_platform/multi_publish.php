<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$data['title'] = "Omni Publish | AdminLTE";
$this->load->view('partials/header', $data);
$csrf_name = $this->security->get_csrf_token_name();
$csrf_hash = $this->security->get_csrf_hash();
?>

<div class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">

        <?php $this->load->view('navbar/headernav'); ?>
        <?php $this->load->view('navbar/sidebar'); ?>

        <main class="app-main">
            <div class="container-fluid mt-4">

                <!-- Publish Card -->
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Omni Channel Publish</h5>
                    </div>
                    <div class="card-body">

                        <form id="publishForm">

                            <div class="mb-3">
                                <label>Select Products</label>
                                <select id="products" name="products[]" multiple class="form-control"></select>
                            </div>

                            <div class="card card-outline card-secondary mb-2">
                                <div class="card-header p-2"><strong>Platforms</strong></div>
                                <div class="card-body p-2 d-flex flex-wrap gap-3">
                                    <label><input type="checkbox" name="platforms[]" value="Facebook"> Facebook</label>
                                    <label><input type="checkbox" name="platforms[]" value="Amazon"> Amazon</label>
                                    <label><input type="checkbox" name="platforms[]" value="Supabase"> Supabase</label>
                                    <label><input type="checkbox" name="platforms[]" value="Mock API"> Mock API</label>
                                </div>
                            </div>

                            <button class="btn btn-success" type="submit">Publish</button>
                        </form>

                        <div id="response" class="mt-3"></div>
                    </div>
                </div>

                <!-- Status -->
                <div class="card mt-4">
                    <div class="card-header bg-secondary text-white">
                        <strong>Publish Status</strong>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered align-middle text-nowrap" id="publishStatusTable">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Platform</th>
                                    <th>Status</th>
                                    <th>Platform ID</th>
                                    <th>Error</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="5" class="text-center">No data</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>

        <?php $this->load->view('navbar/footer'); ?>
    </div>
</div>

<?php $this->load->view('partials/footer'); ?>

<div class="modal fade" id="infoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="modalTitle">Item Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalContent">
                    <table class="table table-sm table-striped">
                        <tbody id="modalTableBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary d-none" id="saveEditBtn">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(function() {
        let csrfHash = '<?= $csrf_hash ?>';
        let csrfName = '<?= $csrf_name ?>';
        let publishTriggered = false;

        $('#products').select2({
            placeholder: "Search for products...",
            minimumInputLength: 1,
            ajax: {
                url: '<?= base_url("multi_platform/get_products") ?>',
                dataType: 'json',
                delay: 300,
                data: params => ({
                    q: params.term
                }),
                processResults: data => ({
                    results: data.results
                })
            }
        });

        function flashMessage(type, message) {
            const alertClass = type === 'success' ?
                'alert-success' :
                type === 'error' ?
                'alert-danger' :
                'alert-warning';

            $('#response').html(`
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `);
        }

        loadPublishStatus(false);

        function loadPublishStatus(fromPublish = false) {
            $.getJSON('<?= base_url("multi_platform/publish_status") ?>', function(res) {
                if (!res || !Array.isArray(res.data)) return;

                let html = '';
                let stillProcessing = false;
                let hasFailed = false;

                res.data.forEach(row => {
                    if (row.status === 'processing') stillProcessing = true;
                    if (row.status === 'failed') hasFailed = true;

                    let badge =
                        row.status === 'processing' ?
                        `<span class="badge bg-warning text-dark">Processing</span>` :
                        row.status === 'failed' ?
                        `<span class="badge bg-danger">Failed</span>` :
                        `<span class="badge bg-success">Success</span>`;

                    let rowData = encodeURIComponent(JSON.stringify(row));

                    html += `
                <tr>
                    <td>${row.product_name}</td>
                    <td>${row.platform}</td>
                    <td>${badge}</td>
                    <td style="max-width: 180px;">
                        <div style="
                            overflow-x: auto;
                            white-space: nowrap;
                            scrollbar-width: thin;
                            padding-bottom: 4px;
                        ">
                            <small class="text-muted">
                                ${row.platform_product_id || 'N/A'}
                            </small>
                        </div>
                    </td>
                    <td class="text-danger small">${row.error_message || ''}</td>
                    <td class="text-center">
                        ${
                            (row.status === 'deleted' || !row.platform_product_id)
                                ? `
                                    <button class="btn btn-sm btn-outline-secondary delete-history"
                                        data-id="${row.id}"
                                        title="Remove from history">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                  `
                                : `
                                    <button class="btn btn-sm btn-info view-item"
                                        data-platform="${row.platform}"
                                        data-platform-id="${row.platform_product_id}"
                                        data-info="${rowData}">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning edit-item"
                                        data-product-id="${row.product_id}"
                                        data-platform="${row.platform}"
                                        data-platform-id="${row.platform_product_id}"
                                        title="Sync ${row.platform}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-item"
                                        data-product-id="${row.product_id}"
                                        data-platform="${row.platform}"
                                        data-platform-id="${row.platform_product_id}"
                                        title="Delete from ${row.platform}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary delete-history"
                                        data-id="${row.id}" title="Remove from history">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                  `
                        }
                    </td>
                </tr>`;
                });

                $('#publishStatusTable tbody').html(
                    html || `<tr><td colspan="6" class="text-center">No activity</td></tr>`
                );

                if (fromPublish && publishTriggered && !stillProcessing) {
                    flashMessage(
                        hasFailed ? 'error' : 'success',
                        hasFailed ?
                        'Some products failed to publish.' :
                        'Publishing completed successfully.'
                    );

                    publishTriggered = false;
                }
            });
        }

        // $('#publishForm').on('submit', function(e) {
        //     e.preventDefault();

        //     let products = $('#products').val();
        //     if (!products || products.length === 0) {
        //         flashMessage('error', 'Please select at least one product.');
        //         return;
        //     }

        //     publishTriggered = true;

        //     flashMessage('processing', 'Publishing started. Sending products to platforms…');

        //     let data = $(this).serializeArray();
        //     data.push({
        //         name: csrfName,
        //         value: csrfHash
        //     });

        //     $.post('<?= base_url("multi_platform/publish") ?>', data, function(res) {

        //             if (res.new_csrf_hash) csrfHash = res.new_csrf_hash;

        //             if (res.status === 'success') {
        //                 flashMessage('processing', 'Products queued. Checking status shortly…');

        //                 setTimeout(function() {
        //                     loadPublishStatus(true);
        //                 }, 3000);

        //             } else {
        //                 publishTriggered = false;
        //                 flashMessage('error', res.message || 'Publish failed.');
        //             }

        //         }, 'json')
        //         .fail(function() {
        //             flashMessage(
        //                 'processing',
        //                 'Publishing request sent. Processing is continuing in background…'
        //             );

        //             setTimeout(function() {
        //                 loadPublishStatus(true);
        //             }, 3000);
        //         });
        // });
        // $('#publishForm').on('submit', function(e) {
        //     e.preventDefault();

        //     let products = $('#products').val();
        //     if (!products || products.length === 0) {
        //         flashMessage('error', 'Please select at least one product.');
        //         return;
        //     }

        //     let formData = $(this).serializeArray(); // ✅ renamed from "data"
        //     formData.push({
        //         name: csrfName,
        //         value: csrfHash
        //     });

        //     console.log('Submitting payload to server:', formData); // ✅ SAFE

        //     $.post(
        //         '<?= base_url("multi_platform/publish") ?>',
        //         formData,
        //         function(res) {
        //             console.log('Server response:', res); // ✅ SAFE

        //             if (res.platforms) {
        //                 Object.entries(res.platforms).forEach(([platform, info]) => {
        //                     console.group(`Platform: ${platform}`);
        //                     console.log('Payload sent:', info.payload_sent);
        //                     console.log('Response received:', info.response_received);
        //                     console.log('Success:', info.success);
        //                     if (info.error) console.error('Error:', info.error);
        //                     console.groupEnd();
        //                 });
        //             }

        //             if (res.new_csrf_hash) csrfHash = res.new_csrf_hash;
        //         },
        //         'json'
        //     );
        // });
        $('#publishForm').on('submit', function(e) {
            e.preventDefault();

            let products = $('#products').val();
            if (!products || products.length === 0) {
                flashMessage('error', 'Please select at least one product.');
                return;
            }

            publishTriggered = true;

            flashMessage(
                'processing',
                'Sending product details to selected platforms…'
            );

            let formData = $(this).serializeArray();
            formData.push({
                name: csrfName,
                value: csrfHash
            });

            console.log('Submitting payload to server:', formData);

            $.post(
                '<?= base_url("multi_platform/publish") ?>',
                formData,
                function(res) {

                    console.log('Server response:', res);

                    if (res.new_csrf_hash) csrfHash = res.new_csrf_hash;

                    flashMessage(
                        'processing',
                        'Processing products… Status will update shortly.'
                    );

                    // immediate load so table fills NOW
                    loadPublishStatus(true);

                    // delayed recheck to finalize status
                    setTimeout(function() {
                        loadPublishStatus(true);
                    }, 3000);
                },
                'json'
            ).fail(function() {

                flashMessage(
                    'processing',
                    'Publish request sent. Processing is continuing…'
                );

                loadPublishStatus(true);
            });
        });

        // View Logic
        // $(document).on('click', '.view-item', function() {
        //     const rowData = JSON.parse(decodeURIComponent($(this).data('info')));
        //     const handle = rowData.platform_product_id;

        //     $('#modalTitle').text('Meta Live Product Preview');
        //     $('#modalTableBody').html('<tr><td class="text-center"><div class="spinner-border text-primary" role="status"></div><br>Fetching live data from Meta...</td></tr>');
        //     $('#infoModal').modal('show');

        //     $.getJSON('<?= base_url("multi_platform/get_meta_details") ?>', {
        //         handle: handle
        //     }, function(res) {
        //         let html = '';

        //         // If we successfully fetched the live product from the catalog
        //         if (res.success && res.product_info) {
        //             const prod = res.product_info;

        //             // 1. Image Gallery Section
        //             html += `
        //         <tr>
        //             <td colspan="2" class="text-center bg-light p-3">
        //                 <div class="mb-2"><strong>Main Image</strong></div>
        //                 <img src="${prod.image_url}" class="img-fluid rounded shadow-sm mb-3" style="max-height: 250px; border: 1px solid #ddd;">

        //                 ${prod.additional_image_urls && prod.additional_image_urls.length > 0 ? `
        //                     <div class="mt-2">
        //                         <h6>Extra Images</h6>
        //                         <div class="d-flex flex-wrap justify-content-center gap-2">
        //                             ${prod.additional_image_urls.map(url => `
        //                                 <img src="${url}" class="img-thumbnail" style="width: 70px; height: 70px; object-fit: cover;">
        //                             `).join('')}
        //                         </div>
        //                     </div>
        //                 ` : ''}
        //             </td>
        //         </tr>
        //     `;

        //             // 2. Product Details Section
        //             html += `
        //         <tr class="table-secondary"><th colspan="2">Live Catalog Information</th></tr>
        //         <tr><td><b>Product Name</b></td><td>${prod.name}</td></tr>
        //         <tr><td><b>Price</b></td><td>${prod.currency} ${prod.price}</td></tr>
        //         <tr><td><b>Description</b></td><td><small>${prod.description || 'No description'}</small></td></tr>
        //         <tr><td><b>Visibility</b></td><td><span class="badge bg-info">${prod.visibility}</span></td></tr>
        //         <tr><td><b>Live Link</b></td><td><a href="${prod.url}" target="_blank" class="btn btn-xs btn-outline-primary">View on Facebook</a></td></tr>
        //     `;
        //         }

        //         // 3. Batch Process Section (The Handle Report)
        //         if (res.batch_status) {
        //             const statusClass = res.batch_status === 'finished' ? 'text-success' : 'text-warning';
        //             html += `
        //         <tr class="table-dark"><th colspan="2">Meta Batch Report</th></tr>
        //         <tr><td><b>Batch Status</b></td><td class="${statusClass}"><strong>${res.batch_status.toUpperCase()}</strong></td></tr>
        //         <tr><td><b>Batch Handle</b></td><td><small class="text-muted">${handle}</small></td></tr>
        //     `;
        //         }

        //         if (!res.success) {
        //             html = `<tr><td colspan="2" class="text-center text-danger p-4">
        //         <i class="bi bi-exclamation-triangle-fill display-4"></i><br>
        //         ${res.error || 'Could not find live product details yet.'}
        //     </td></tr>`;
        //         }

        //         $('#modalTableBody').html(html);
        //     });
        // });
        $(document).on('click', '.view-item', function() {

            const platform = $(this).data('platform');
            const platformId = $(this).data('platform-id');

            $('#modalTitle').text(`${platform} Product Details`);
            $('#modalTableBody').html(`
                <tr>
                    <td class="text-center p-4">
                        <div class="spinner-border text-primary"></div><br>
                        Fetching product details…
                    </td>
                </tr>
            `);

            $('#infoModal').modal('show');

            $.getJSON(
                '<?= base_url("multi_platform/get_platform_product_details") ?>', {
                    platform: platform,
                    platform_id: platformId
                },
                function(res) {

                    if (!res.success || !res.data) {
                        $('#modalTableBody').html(`
                    <tr>
                        <td class="text-danger text-center p-4">
                            ${res.error || 'Unable to fetch product details'}
                        </td>
                    </tr>
                `);
                        return;
                    }

                    const data = res.data;

                    /* ===============================
                       NORMALIZE MEDIA FIELDS
                    =============================== */

                    const mainImage =
                        data.main_image_url ||
                        data.image_url ||
                        data.primary_image ||
                        null;

                    let extraImages =
                        data.extra_image_urls ||
                        data.additional_image_urls || [];

                    // Handle comma-separated image strings
                    if (Array.isArray(extraImages) &&
                        extraImages.length === 1 &&
                        typeof extraImages[0] === 'string' &&
                        extraImages[0].includes(',')) {
                        extraImages = extraImages[0].split(',').map(v => v.trim());
                    }

                    const videoUrl =
                        data.video_url ||
                        data.video ||
                        null;

                    let html = '';

                    /* ===============================
                       MEDIA SECTION (TOP)
                    =============================== */

                    let mediaHtml = '';

                    if (mainImage) {
                        mediaHtml += `
                    <div class="mb-3 text-center">
                        <img src="${mainImage}"
                             class="img-fluid rounded shadow-sm"
                             style="max-height:260px;border:1px solid #ddd;">
                    </div>
                `;
                    }

                    if (Array.isArray(extraImages) && extraImages.length) {
                        mediaHtml += `
                    <div class="mb-3">
                        <strong>Extra Images</strong>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            ${extraImages.map(img => `
                                <img src="${img}"
                                     class="img-thumbnail"
                                     style="width:80px;height:80px;object-fit:cover;">
                            `).join('')}
                        </div>
                    </div>
                    `;
                    }

                    if (videoUrl) {
                        mediaHtml += `
                    <div class="mt-3">
                        <a href="${videoUrl}" target="_blank"
                           class="btn btn-outline-danger w-100">
                            ▶ Watch Video
                        </a>
                    </div>
                    `;
                    }

                    html += `
                <tr>
                    <td colspan="2">
                        ${mediaHtml || '<div class="text-muted text-center">No media available</div>'}
                    </td>
                </tr>
                `;

                    /* ===============================
                       DETAILS TABLE
                    =============================== */

                    Object.entries(data).forEach(([key, value]) => {

                        // Skip media keys already rendered
                        if ([
                                'main_image_url',
                                'image_url',
                                'primary_image',
                                'extra_image_urls',
                                'additional_image_urls',
                                'video_url',
                                'video'
                            ].includes(key)) {
                            return;
                        }

                        // OBJECT / ARRAY
                        if (typeof value === 'object' && value !== null) {
                            html += `
                        <tr>
                            <td><strong>${key}</strong></td>
                            <td>
                                <pre class="bg-light p-2 rounded small"
                                     style="max-height:200px;overflow:auto;">
                                     ${JSON.stringify(value, null, 2)}
                                </pre>
                            </td>
                        </tr>
                    `;
                            return;
                        }

                        // URL
                        if (typeof value === 'string' && value.startsWith('http')) {
                            html += `
                        <tr>
                            <td><strong>${key}</strong></td>
                            <td>
                                <a href="${value}" target="_blank"
                                   class="btn btn-sm btn-outline-primary">
                                    Open Link
                                </a>
                            </td>
                        </tr>
                    `;
                            return;
                        }

                        // NORMAL VALUE
                        html += `
                    <tr>
                        <td><strong>${key}</strong></td>
                        <td>${value}</td>
                    </tr>
                `;
                    });

                    $('#modalTableBody').html(html);
                }
            );
        });

        // Edit Logic
        // $(document).on('click', '.edit-item', function() {
        //     const rowData = JSON.parse(decodeURIComponent($(this).data('info')));

        //     if (confirm(`Do you want to sync the latest details for "${rowData.product_name}" to Meta?`)) {
        //         $.post('<?= base_url("multi_platform/update_meta_item") ?>', {
        //             id: rowData.product_id,
        //             [csrfName]: csrfHash
        //         }, function(res) {

        //             if (res.new_csrf_hash) csrfHash = res.new_csrf_hash;
        //             if (res.status === 'success') {
        //                 alert('Update started! Please wait a moment for Meta to process the new handle.');
        //                 loadPublishStatus();
        //             } else {
        //                 alert('Error: ' + res.message);
        //             }
        //         }, 'json');
        //     }
        // });
        // $(document).on('click', '.edit-item', function() {
        //     const rowData = JSON.parse(decodeURIComponent($(this).data('info')));

        //     if (!confirm(`Sync latest details for "${rowData.product_name}" to all platforms?`)) return;

        //     $.post('<?= base_url("multi_platform/update_platform_item") ?>', {
        //         id: rowData.product_id,
        //         [csrfName]: csrfHash
        //     }, function(res) {
        //         if (res.new_csrf_hash) csrfHash = res.new_csrf_hash;

        //         if (res.status === 'success') {
        //             alert('Sync started! Products are being updated on all platforms.');
        //             loadPublishStatus();
        //         } else {
        //             alert('Error: ' + (res.message || 'Unknown error'));
        //         }
        //     }, 'json');
        // });
        $(document).on('click', '.edit-item', function() {

            const productId = $(this).data('product-id');
            const platform = $(this).data('platform');
            const platformId = $(this).data('platform-id');

            if (!productId || !platform || !platformId) {
                alert('Invalid edit request');
                return;
            }

            if (!confirm(`Sync latest details to ${platform} only?`)) return;

            $.ajax({
                url: <?= json_encode(base_url('multi_platform/update_single_platform_item')) ?>,
                type: 'POST',
                dataType: 'json',
                data: {
                    product_id: productId,
                    platform: platform,
                    platform_product_id: platformId,
                    [csrfName]: csrfHash
                },
                success: function(res) {

                    if (res.new_csrf_hash) csrfHash = res.new_csrf_hash;

                    if (res.status === 'success') {
                        alert(`Update sent to ${platform}`);
                        loadPublishStatus();
                    } else {
                        alert(res.message || 'Update failed');
                    }
                },
                error: function() {
                    alert('Server error');
                }
            });
        });

        // Delete Logic
        // $(document).on('click', '.delete-item', function() {
        //     const productId = $(this).data('product-id');
        //     const row = $(this).closest('tr');

        //     if (!productId) {
        //         alert('Invalid product ID');
        //         return;
        //     }

        //     if (!confirm('This will REMOVE the product from Meta catalog. Continue?')) {
        //         return;
        //     }

        //     $.post('<?= base_url("multi_platform/delete_meta_item") ?>', {
        //         id: productId,
        //         [csrfName]: csrfHash
        //     }, function(res) {
        //         if (res.new_csrf_hash) csrfHash = res.new_csrf_hash;

        //         if (res.status === 'success') {
        //             row.find('td:eq(2)').html('<span class="badge bg-secondary">Deleted</span>');

        //             // Remove only Meta-related buttons, keep delete-history
        //             row.find('.view-item').remove();
        //             row.find('.edit-item').remove();
        //             row.find('.delete-item').remove();
        //         } else {
        //             alert('Delete failed: ' + (res.message || 'Unknown error'));
        //         }
        //     }, 'json');
        // });
        $(document).on('click', '.delete-item', function() {

            const productId = $(this).data('product-id');
            const platform = $(this).data('platform');
            const platformId = $(this).data('platform-id');
            const row = $(this).closest('tr');

            if (!productId || !platform || !platformId) {
                alert('Invalid delete request');
                return;
            }

            if (!confirm(`Delete this product from ${platform} only?`)) return;

            $.ajax({
                url: <?= json_encode(base_url('multi_platform/delete_platform_item')) ?>,
                type: 'POST',
                dataType: 'json',
                data: {
                    product_id: productId,
                    platform: platform,
                    platform_product_id: platformId,
                    [csrfName]: csrfHash
                },
                success: function(res) {
                    if (res.new_csrf_hash) csrfHash = res.new_csrf_hash;

                    if (res.status === 'success') {
                        row.find('td:eq(2)').html('<span class="badge bg-secondary">Deleted</span>');
                        row.find('.view-item,.edit-item,.delete-item').remove();
                    } else {
                        alert(res.message || 'Delete failed');
                    }
                },
                error: function() {
                    alert('Server error');
                }
            });
        });

        // Delete from history only
        $(document).on('click', '.delete-history', function() {
            const publishId = $(this).data('id');
            const row = $(this).closest('tr');

            if (!publishId) {
                alert('Invalid record ID');
                return;
            }

            if (!confirm('Remove this record from publish history?')) {
                return;
            }

            $.post('<?= base_url("multi_platform/delete_history") ?>', {
                id: publishId,
                [csrfName]: csrfHash
            }, function(res) {
                if (res.new_csrf_hash) csrfHash = res.new_csrf_hash;

                if (res.status === 'success') {
                    row.fadeOut(200, function() {
                        $(this).remove();
                    });
                } else {
                    alert('Delete failed: ' + (res.message || 'Unknown error'));
                }
            }, 'json');
        });
    });
</script>