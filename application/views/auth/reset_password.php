<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reset Password | AdminLTE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body,
        html {
            height: 100%;
            margin: 0;
            background-color: #f4f4f4;
        }

        .reset-container {
            height: 100%;
        }

        .card {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>

<body>
    <div class="d-flex justify-content-center align-items-center reset-container">
        <div class="card">
            <h4 class="text-center mb-4">Reset Your Password</h4>
            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger"><?= $this->session->flashdata('error'); ?></div>
            <?php endif; ?>
            <?= form_open('auth/update_password'); ?>
            <input type="hidden" name="token" value="<?= $token; ?>">
            <div class="mb-3">
                <input type="password" class="form-control" name="password" placeholder="New Password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Update Password</button>
            <?= form_close(); ?>
        </div>
    </div>
</body>

</html>