<!DOCTYPE html>
<html>
<head>
    <title>Welcome</title>
</head>
<body>
    <h1>Welcome, <?= $fullname; ?>!</h1>
    <p>You have successfully logged in.</p>
    <a href="<?= site_url('auth/logout'); ?>">Logout</a>
</body>
</html>
