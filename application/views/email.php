<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>CodeIgniter Email Example</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0 text-center">Send Test Email</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($this->session->flashdata('email_sent')): ?>
                            <div class="alert alert-info"><?= $this->session->flashdata('email_sent'); ?></div>
                        <?php endif; ?>

                        <?= form_open('email/send_mail'); ?>
                        <div class="mb-3">
                            <label for="email" class="form-label">Recipient Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter recipient email" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Send Email</button>
                        <?= form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>