<!-- Header Navbar -->
<nav class="app-header navbar navbar-expand bg-body">
    <div class="container-fluid">
        <!-- Start Navbar Links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="javascript:void(0);" role="button">
                    <i class="bi bi-list"></i>
                </a>
            </li>
            <li class="nav-item d-none d-md-block">
                <a href="javascript:void(0);" onclick="history.back();" class="nav-link">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </li>
            <li class="nav-item d-none d-md-block"><a href="<?= base_url('greet') ?>" class="nav-link">Home</a></li>
            <li class="nav-item d-none d-md-block"><a href="#" class="nav-link">Contact</a></li>
        </ul>
        <!-- End Navbar Links -->

        <!-- Right Navbar Links -->
        <ul class="navbar-nav ms-auto">
            <!-- Navbar Search -->
            <li class="nav-item">
                <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                    <i class="bi bi-search"></i>
                </a>
            </li>

            <!-- Messages Dropdown -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-bs-toggle="dropdown" href="#">
                    <i class="bi bi-chat-text"></i>
                    <span class="navbar-badge badge text-bg-danger">3</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                    <!-- Message 1 -->
                    <a href="#" class="dropdown-item">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <img src="<?= base_url('assets/images/user8-128x128.jpg') ?>" alt="User Avatar" class="img-size-50 rounded-circle me-3" />
                            </div>
                            <div class="flex-grow-1">
                                <h3 class="dropdown-item-title">
                                    Brad Diesel
                                    <span class="float-end fs-7 text-danger"><i class="bi bi-star-fill"></i></span>
                                </h3>
                                <p class="fs-7">Call me whenever you can...</p>
                                <p class="fs-7 text-secondary"><i class="bi bi-clock-fill me-1"></i> 4 Hours Ago</p>
                            </div>
                        </div>
                    </a>
                    <div class="dropdown-divider"></div>

                    <!-- Message 2 -->
                    <a href="#" class="dropdown-item">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <img src="<?= base_url('assets/images/user8-128x128.jpg') ?>" alt="User Avatar" class="img-size-50 rounded-circle me-3" />
                            </div>
                            <div class="flex-grow-1">
                                <h3 class="dropdown-item-title">
                                    John Pierce
                                    <span class="float-end fs-7 text-secondary"><i class="bi bi-star-fill"></i></span>
                                </h3>
                                <p class="fs-7">I got your message bro</p>
                                <p class="fs-7 text-secondary"><i class="bi bi-clock-fill me-1"></i> 4 Hours Ago</p>
                            </div>
                        </div>
                    </a>
                    <div class="dropdown-divider"></div>

                    <!-- Message 3 -->
                    <a href="#" class="dropdown-item">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <img src="<?= base_url('assets/images/user8-128x128.jpg') ?>" alt="User Avatar" class="img-size-50 rounded-circle me-3" />
                            </div>
                            <div class="flex-grow-1">
                                <h3 class="dropdown-item-title">
                                    Nora Silvester
                                    <span class="float-end fs-7 text-warning"><i class="bi bi-star-fill"></i></span>
                                </h3>
                                <p class="fs-7">The subject goes here</p>
                                <p class="fs-7 text-secondary"><i class="bi bi-clock-fill me-1"></i> 4 Hours Ago</p>
                            </div>
                        </div>
                    </a>
                    <div class="dropdown-divider"></div>

                    <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
                </div>
            </li>

            <!-- Notifications Dropdown -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-bs-toggle="dropdown" href="#">
                    <i class="bi bi-bell-fill"></i>
                    <span class="navbar-badge badge text-bg-warning">15</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                    <span class="dropdown-item dropdown-header">15 Notifications</span>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="bi bi-envelope me-2"></i> 4 new messages
                        <span class="float-end text-secondary fs-7">3 mins</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="bi bi-people-fill me-2"></i> 8 friend requests
                        <span class="float-end text-secondary fs-7">12 hours</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="bi bi-file-earmark-fill me-2"></i> 3 new reports
                        <span class="float-end text-secondary fs-7">2 days</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
                </div>
            </li>

            <!-- Fullscreen Toggle -->
            <li class="nav-item">
                <a class="nav-link" href="#" data-lte-toggle="fullscreen">
                    <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                    <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none"></i>
                </a>
            </li>

            <!-- User Menu -->
            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <img src="<?= $logged_user->avatar_url ?? base_url('assets/uploads/user_default.png'); ?>"
                        class="user-image rounded-circle shadow"
                        alt="User Image" />
                    <span class="d-none d-md-inline"><?= htmlspecialchars(ucwords(strtolower($logged_user->fullname))); ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                    <li class="user-header text-bg-primary">
                        <img src="<?= $logged_user->avatar_url ?? base_url('assets/uploads/user_default.png'); ?>"
                            class="rounded-circle shadow"
                            alt="User Image" />
                        <p>
                            <?= htmlspecialchars(ucwords(strtolower($logged_user->fullname))); ?> - Web Developer
                            <small>Member since <?= date('M Y', strtotime($logged_user->created_at ?? 'now')); ?></small>
                        </p>
                    </li>
                    <li class="user-body">
                        <div class="row">
                            <div class="col-4 text-center"><a href="#">Followers</a></div>
                            <div class="col-4 text-center"><a href="#">Sales</a></div>
                            <div class="col-4 text-center"><a href="#">Friends</a></div>
                        </div>
                    </li>
                    <li class="user-footer">
                        <a href="<?= site_url('profile/profile'); ?>" class="btn btn-default btn-flat">Profile</a>
                        <a href="<?= site_url('auth/logout'); ?>" class="btn btn-default btn-flat float-end">Sign out</a>
                    </li>
                </ul>
            </li>

        </ul>
    </div>
</nav>