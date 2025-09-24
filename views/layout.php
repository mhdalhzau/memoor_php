<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'SPBU Management System' ?></title>
    
    <!-- SB Admin Pro CSS -->
    <link href="/assets/css/styles.css" rel="stylesheet">
    <!-- Feather Icons -->
    <script data-search-pseudo-elements defer src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js" crossorigin="anonymous"></script>
</head>
<body class="nav-fixed">
    <!-- Top Navigation -->
    <nav class="topnav navbar navbar-expand shadow justify-content-between justify-content-sm-start navbar-light bg-white" id="sidenavAccordion">
        <!-- Sidenav Toggle Button-->
        <button class="btn btn-icon btn-transparent-dark order-1 order-lg-0 me-2 ms-lg-2 me-lg-0" id="sidebarToggle"><i data-feather="menu"></i></button>
        <!-- Navbar Brand-->
        <!-- * * Tip * * You can use text or an image for your navbar brand.-->
        <!-- * * * * * * When using an image, we recommend 40px in height.-->
        <a class="navbar-brand pe-3 ps-4 ps-lg-2" href="/dashboard">SPBU Management</a>
        <!-- Navbar Search Input-->
        <!-- * * Note: * * Visible only on and above the lg breakpoint-->
        <form class="form-inline me-auto d-none d-lg-block me-3">
            <div class="input-group input-group-joined input-group-solid">
                <input class="form-control pe-0" type="search" placeholder="Search" aria-label="Search" />
                <div class="input-group-text"><i data-feather="search"></i></div>
            </div>
        </form>
        <!-- Navbar Items-->
        <ul class="navbar-nav align-items-center ms-auto">
            <!-- Documentation Dropdown-->
            <li class="nav-item dropdown no-caret d-none d-md-block me-3">
                <a class="nav-link dropdown-toggle" id="navbarDropdownDocs" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <div class="fw-500">Documentation</div>
                    <i class="fas fa-chevron-right dropdown-arrow"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end py-0 me-sm-n15 me-lg-0 o-hidden animated--fade-in-up" aria-labelledby="navbarDropdownDocs">
                    <a class="dropdown-item py-3" href="#" target="_blank">
                        <div class="icon-stack bg-primary-soft text-primary me-4">
                            <i data-feather="book"></i>
                        </div>
                        <div>
                            <div class="small text-gray-500">Documentation</div>
                            Usage instructions and examples
                        </div>
                    </a>
                </div>
            </li>
            <!-- User Dropdown-->
            <?php if (isset($_SESSION['user'])): ?>
            <li class="nav-item dropdown no-caret dropdown-user me-3 me-lg-4">
                <a class="btn btn-icon btn-transparent-dark dropdown-toggle" id="navbarDropdownUserImage" href="javascript:void(0);" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="img-fluid" src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['user']['name']) ?>&background=0061f2&color=fff" />
                </a>
                <div class="dropdown-menu dropdown-menu-end border-0 shadow animated--fade-in-up" aria-labelledby="navbarDropdownUserImage">
                    <h6 class="dropdown-header d-flex align-items-center">
                        <img class="dropdown-user-img" src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['user']['name']) ?>&background=0061f2&color=fff" />
                        <div class="dropdown-user-details">
                            <div class="dropdown-user-details-name"><?= htmlspecialchars($_SESSION['user']['name']) ?></div>
                            <div class="dropdown-user-details-email"><?= htmlspecialchars($_SESSION['user']['email']) ?></div>
                        </div>
                    </h6>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="/settings">
                        <div class="dropdown-item-icon"><i data-feather="settings"></i></div>
                        Account
                    </a>
                    <a class="dropdown-item" href="/logout">
                        <div class="dropdown-item-icon"><i data-feather="log-out"></i></div>
                        Logout
                    </a>
                </div>
            </li>
            <?php endif; ?>
        </ul>
    </nav>
    
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sidenav shadow-right sidenav-light">
                <div class="sidenav-menu">
                    <div class="nav accordion" id="accordionSidenav">
                        <!-- Sidenav Menu Heading (Core)-->
                        <div class="sidenav-menu-heading">Dashboard</div>
                        <a class="nav-link <?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>" href="/dashboard">
                            <div class="nav-link-icon"><i data-feather="activity"></i></div>
                            Dashboard
                        </a>
                        
                        <!-- Sidenav Menu Heading (Operations)-->
                        <div class="sidenav-menu-heading">Operations</div>
                        <a class="nav-link <?= ($currentPage ?? '') === 'attendance' ? 'active' : '' ?>" href="/attendance">
                            <div class="nav-link-icon"><i data-feather="clock"></i></div>
                            Absensi
                        </a>
                        <a class="nav-link <?= ($currentPage ?? '') === 'sales' ? 'active' : '' ?>" href="/sales">
                            <div class="nav-link-icon"><i data-feather="shopping-cart"></i></div>
                            Penjualan
                        </a>
                        
                        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] !== 'staff'): ?>
                        <!-- Sidenav Menu Heading (Management)-->
                        <div class="sidenav-menu-heading">Management</div>
                        <a class="nav-link <?= ($currentPage ?? '') === 'stores' ? 'active' : '' ?>" href="/stores">
                            <div class="nav-link-icon"><i data-feather="home"></i></div>
                            Toko
                        </a>
                        <a class="nav-link <?= ($currentPage ?? '') === 'users' ? 'active' : '' ?>" href="/users">
                            <div class="nav-link-icon"><i data-feather="users"></i></div>
                            Karyawan
                        </a>
                        <a class="nav-link <?= ($currentPage ?? '') === 'payroll' ? 'active' : '' ?>" href="/payroll">
                            <div class="nav-link-icon"><i data-feather="dollar-sign"></i></div>
                            Gaji
                        </a>
                        <a class="nav-link <?= ($currentPage ?? '') === 'cashflow' ? 'active' : '' ?>" href="/cashflow">
                            <div class="nav-link-icon"><i data-feather="credit-card"></i></div>
                            Kas
                        </a>
                        <?php endif; ?>
                        
                        <!-- Sidenav Menu Heading (Interface)-->
                        <div class="sidenav-menu-heading">Interface</div>
                        <a class="nav-link <?= ($currentPage ?? '') === 'proposals' ? 'active' : '' ?>" href="/proposals">
                            <div class="nav-link-icon"><i data-feather="message-square"></i></div>
                            Saran
                        </a>
                        <a class="nav-link <?= ($currentPage ?? '') === 'settings' ? 'active' : '' ?>" href="/settings">
                            <div class="nav-link-icon"><i data-feather="settings"></i></div>
                            Settings
                        </a>
                    </div>
                </div>
                <!-- Sidenav Footer-->
                <div class="sidenav-footer">
                    <div class="sidenav-footer-content">
                        <div class="sidenav-footer-subtitle">Logged in as:</div>
                        <div class="sidenav-footer-title"><?= htmlspecialchars($_SESSION['user']['name'] ?? 'Guest') ?></div>
                    </div>
                </div>
            </nav>
        </div>
        
        <div id="layoutSidenav_content">
            <main>
                <header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
                    <div class="container-xl px-4">
                        <div class="page-header-content">
                            <div class="row align-items-center justify-content-between pt-3">
                                <div class="col-auto mb-3">
                                    <h1 class="page-header-title">
                                        <div class="page-header-icon"><i data-feather="activity"></i></div>
                                        <?= $title ?? 'SPBU Management System' ?>
                                    </h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>
                
                <!-- Main page content-->
                <div class="container-xl px-4">
                    <!-- Alerts -->
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-primary alert-dismissible fade show" role="alert">
                            <i data-feather="check" class="alert-feather"></i>
                            <?= htmlspecialchars($_SESSION['success']) ?>
                            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i data-feather="alert-triangle" class="alert-feather"></i>
                            <?= htmlspecialchars($_SESSION['error']) ?>
                            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>
                    
                    <?= $content ?>
                </div>
            </main>
            
            <footer class="footer-admin mt-auto footer-light">
                <div class="container-xl px-4">
                    <div class="row">
                        <div class="col-md-6 small">Copyright &copy; SPBU Management System 2025</div>
                        <div class="col-md-6 text-md-end small">
                            <a href="#!">Privacy Policy</a>
                            &middot;
                            <a href="#!">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <!-- SB Admin Pro Scripts -->
    <script src="/assets/js/scripts.js"></script>
</body>
</html>