<?php
$segment1 = $this->uri->segment(1);
$segment2 = $this->uri->segment(2);
$segment3 = $this->uri->segment(3);
?>

<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
        <a href="<?= base_url('dashboard/dashboard') ?>" class="brand-link">
            <img src="<?= base_url('assets/images/AdminLTELogo.png'); ?>" alt="AdminLTE Logo" class="brand-image opacity-75 shadow" />
            <span class="brand-text fw-light">AdminLTE 4</span>
        </a>
    </div>

    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation">

                <li class="nav-item">
                    <a href="<?= base_url('dashboard/dashboard') ?>"
                        class="nav-link <?= ($segment2 == 'dashboard') ? 'active' : '' ?>">
                        <i class="nav-icon bi bi-speedometer"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-item <?= ($segment2 == 'table') ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?= ($segment2 == 'table') ? 'active' : '' ?>">
                        <i class="nav-icon bi bi-table"></i>
                        <p>Tables <i class="nav-arrow bi bi-chevron-right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= base_url('dashboard/table') ?>"
                                class="nav-link <?= ($segment2 == 'table') ? 'active' : '' ?>">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Users Table</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="<?= base_url('interaction') ?>"
                        class="nav-link <?= ($segment3 == 'interaction') ? 'active' : '' ?>">
                        <i class="nav-icon bi bi-translate"></i>
                        <p>Languages</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="../docs/faq.html" class="nav-link">
                        <i class="nav-icon bi bi-question-circle-fill"></i>
                        <p>FAQ</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>