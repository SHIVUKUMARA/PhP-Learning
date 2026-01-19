<?php
$segment1 = $this->uri->segment(1);
$segment2 = $this->uri->segment(2);
$segment3 = $this->uri->segment(3);
$segment4 = $this->uri->segment(4);
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

                <!-- <li class="nav-item <?=
                                            ($segment1 == 'dashboard' && ($segment2 == 'table' || $segment2 == 'api_table'))
                                                || ($segment1 == 'products' && $segment2 == 'table') ? 'menu-open' : ''
                                            ?>">
                    <a href="#" class="nav-link <?=
                                                ($segment1 == 'dashboard' && ($segment2 == 'table' || $segment2 == 'api_table'))
                                                    || ($segment1 == 'products' && $segment2 == 'table') ? 'active' : ''
                                                ?>">
                        <i class="nav-icon bi bi-table"></i>
                        <p>Tables <i class="nav-arrow bi bi-chevron-right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= base_url('dashboard/table') ?>"
                                class="nav-link <?= ($segment1 == 'dashboard' && $segment2 == 'table') ? 'active' : '' ?>">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Users Table</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('dashboard/api_table') ?>"
                                class="nav-link <?= ($segment1 == 'dashboard' && $segment2 == 'api_table') ? 'active' : '' ?>">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>API Table</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('products/table') ?>"
                                class="nav-link <?= ($segment1 == 'products' && $segment2 == 'table') ? 'active' : '' ?>">
                                <i class="nav-icon bi bi-boxes"></i>
                                <p>Products Table</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="<?= base_url('products') ?>"
                        class="nav-link <?= ($segment3 == 'products') ? 'active' : '' ?>">
                        <i class="nav-icon bi bi-box-seam"></i>
                        <p>Products</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= base_url('interaction') ?>"
                        class="nav-link <?= ($segment4 == 'interaction') ? 'active' : '' ?>">
                        <i class="nav-icon bi bi-translate"></i>
                        <p>Languages</p>
                    </a>
                </li> -->

                <?php
                $segment1 = $this->uri->segment(1);
                $segment2 = $this->uri->segment(2);

                $isOmniMenu =
                    ($segment1 === 'omni_products')
                    || ($segment1 === 'omni' && in_array($segment2, ['publish-view', 'published'], true));
                ?>

                <li class="nav-item <?= $isOmniMenu ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?= $isOmniMenu ? 'active' : '' ?>">
                        <i class="nav-icon bi bi-diagram-3"></i>
                        <p>
                            Omni Channel
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= base_url('omni_products') ?>"
                                class="nav-link <?= ($segment1 === 'omni_products') ? 'active' : '' ?>">
                                <i class="nav-icon bi bi-box-seam"></i>
                                <p>Products</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="<?= base_url('omni/publish-view') ?>"
                                class="nav-link <?= ($segment1 === 'omni' && $segment2 === 'publish-view') ? 'active' : '' ?>">
                                <i class="nav-icon bi bi-upload"></i>
                                <p>Publish (Omni)</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="<?= base_url('omni/published') ?>"
                                class="nav-link <?= ($segment1 === 'omni' && $segment2 === 'published') ? 'active' : '' ?>">
                                <i class="nav-icon bi bi-check-circle"></i>
                                <p>Published Products</p>
                            </a>
                        </li>
                    </ul>
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