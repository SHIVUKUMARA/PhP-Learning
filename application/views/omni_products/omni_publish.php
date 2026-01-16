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

                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Omni Channel Publish</h5>
                    </div>

                    <div class="card-body">

                        <form id="omniPublishForm">

                            <input type="hidden" name="<?= $csrf_name ?>" value="<?= $csrf_hash ?>">

                            <!-- PRODUCT -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Select Product</label>
                                <select name="product_id" class="form-select" required>
                                    <option value="">-- Select Product --</option>
                                    <?php foreach ($products as $p): ?>
                                        <option value="<?= $p->id ?>">
                                            <?= htmlspecialchars($p->product_name) ?> (<?= $p->sku ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- ECOMMERCE -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">E-commerce Platforms</label>
                                <div class="row">
                                    <?php foreach (['Amazon', 'Flipkart', 'Meesho', 'Shopify'] as $e): ?>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input"
                                                    type="checkbox"
                                                    name="ecommerce_platforms[]"
                                                    value="<?= $e ?>">
                                                <label class="form-check-label"><?= $e ?></label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- SOCIAL -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Social Media Platforms</label>
                                <div class="row">
                                    <?php foreach (['Beecreptor', 'CrudCrud', 'Mock API', 'Supabase', 'Instagram', 'Facebook', 'TikTok', 'YouTube'] as $s): ?>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input"
                                                    type="checkbox"
                                                    name="social_platforms[]"
                                                    value="<?= $s ?>">
                                                <label class="form-check-label"><?= $s ?></label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="text-end">
                                <button class="btn btn-success px-4">
                                    Publish Product
                                </button>
                            </div>

                        </form>

                        <div id="publishResult" class="mt-3"></div>

                    </div>
                </div>

            </div>
        </main>

        <?php $this->load->view('navbar/footer'); ?>
    </div>
</div>

<?php $this->load->view('partials/footer'); ?>

<script>
    document.getElementById('omniPublishForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = e.target;
        const resultBox = document.getElementById('publishResult');

        fetch('<?= site_url("omni/publish/store") ?>', {
                method: 'POST',
                body: new FormData(form)
            })
            .then(r => r.json())
            .then(res => {
                if (res.ok) {
                    console.log('Publish Response:', res);
                    resultBox.innerHTML = `<div class="alert alert-success">${res.message}</div>`;
                    form.reset();
                } else {
                    console.error('Publish Error:', res);
                    resultBox.innerHTML = `<div class="alert alert-danger">${res.message}</div>`;
                }
            })
            .catch(() => {
                resultBox.innerHTML =
                    `<div class="alert alert-danger">Request failed</div>`;
            });
    });
</script>