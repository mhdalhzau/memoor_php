<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SPBU Management System</title>
    
    <!-- SB Admin Pro CSS -->
    <link href="/assets/css/styles.css" rel="stylesheet">
    <!-- Feather Icons -->
    <script data-search-pseudo-elements defer src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js" crossorigin="anonymous"></script>
</head>
<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container-xl px-4">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <!-- Basic login form-->
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header justify-content-center">
                                    <h3 class="fw-light my-4">
                                        <i data-feather="fuel" class="me-2"></i>
                                        SPBU Management System
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <?php if (isset($_SESSION['error'])): ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <i data-feather="alert-triangle" class="me-2"></i>
                                            <?= htmlspecialchars($_SESSION['error']) ?>
                                            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                        <?php unset($_SESSION['error']); ?>
                                    <?php endif; ?>
                                    
                                    <form method="POST" action="/login">
                                        <div class="mb-3">
                                            <label class="small mb-1" for="email">Email Address</label>
                                            <input class="form-control" id="email" name="email" type="email" placeholder="Enter email address" required />
                                        </div>
                                        <div class="mb-3">
                                            <label class="small mb-1" for="password">Password</label>
                                            <input class="form-control" id="password" name="password" type="password" placeholder="Enter password" required />
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" id="rememberPasswordCheck" type="checkbox" value="" />
                                                <label class="form-check-label" for="rememberPasswordCheck">Remember password</label>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <a class="small" href="#!">Forgot Password?</a>
                                            <button class="btn btn-primary" type="submit">Login</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center py-3">
                                    <div class="small">
                                        <strong>Demo Accounts:</strong><br>
                                        <div class="row mt-2 text-start">
                                            <div class="col-12">
                                                <div class="badge bg-primary-soft text-primary me-2 mb-2">Manager</div>
                                                <span class="small">manager@spbu.com / manager123</span>
                                            </div>
                                            <div class="col-12">
                                                <div class="badge bg-success-soft text-success me-2 mb-2">Admin</div>
                                                <span class="small">admin@spbu.com / admin123</span>
                                            </div>
                                            <div class="col-12">
                                                <div class="badge bg-warning-soft text-warning me-2 mb-2">Staff</div>
                                                <span class="small">putri@spbu.com / putri123</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div id="layoutAuthentication_footer">
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