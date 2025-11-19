<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title><?= $title ?? "AdminLTE 4"; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/adminlte.min.css'); ?>">

    <!-- Overlay Scrollbars -->
    <link rel="stylesheet" href="<?= base_url('assets/css/overlayscrollbars.min.css'); ?>">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap-icons.min.css'); ?>">

    <link rel="stylesheet" href="<?= base_url('assets/css/index.css'); ?>">

    <link rel="stylesheet" href="<?= base_url('assets/css/custom.css') ?>">
</head>

<body class="<?= $body_class ?? 'hold-transition sidebar-mini layout-fixed'; ?>">