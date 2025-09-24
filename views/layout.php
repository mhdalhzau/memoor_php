<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'SPBU Management System' ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header h1 {
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .nav {
            background: white;
            padding: 0 2rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .nav ul {
            list-style: none;
            display: flex;
            gap: 2rem;
        }
        
        .nav a {
            display: block;
            padding: 1rem 0;
            text-decoration: none;
            color: #666;
            font-weight: 500;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }
        
        .nav a:hover, .nav a.active {
            color: #667eea;
            border-bottom-color: #667eea;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        .card {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-weight: 500;
            transition: background 0.3s ease;
        }
        
        .btn:hover {
            background: #5a6fd8;
        }
        
        .btn-danger {
            background: #e74c3c;
        }
        
        .btn-danger:hover {
            background: #c0392b;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #555;
        }
        
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .table th, .table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #555;
        }
        
        .table tr:hover {
            background: #f8f9fa;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-card h3 {
            color: #667eea;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .stat-card p {
            color: #666;
            font-weight: 500;
        }
        
        .user-menu {
            float: right;
            margin-top: -0.5rem;
        }
        
        .user-menu span {
            margin-right: 1rem;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 0 1rem;
            }
            
            .nav ul {
                flex-direction: column;
                gap: 0;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .user-menu {
                float: none;
                margin-top: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <h1>SPBU Management System</h1>
            <?php if (isset($_SESSION['user'])): ?>
                <div class="user-menu">
                    <span>Halo, <?= htmlspecialchars($_SESSION['user']['name']) ?></span>
                    <a href="/logout" class="btn btn-danger">Logout</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if (isset($_SESSION['user'])): ?>
    <div class="nav">
        <div class="container">
            <ul>
                <li><a href="/dashboard" <?= ($currentPage ?? '') === 'dashboard' ? 'class="active"' : '' ?>>Dashboard</a></li>
                <li><a href="/attendance" <?= ($currentPage ?? '') === 'attendance' ? 'class="active"' : '' ?>>Absensi</a></li>
                <li><a href="/sales" <?= ($currentPage ?? '') === 'sales' ? 'class="active"' : '' ?>>Penjualan</a></li>
                <?php if ($_SESSION['user']['role'] !== 'staff'): ?>
                    <li><a href="/stores" <?= ($currentPage ?? '') === 'stores' ? 'class="active"' : '' ?>>Toko</a></li>
                    <li><a href="/users" <?= ($currentPage ?? '') === 'users' ? 'class="active"' : '' ?>>Karyawan</a></li>
                    <li><a href="/payroll" <?= ($currentPage ?? '') === 'payroll' ? 'class="active"' : '' ?>>Gaji</a></li>
                    <li><a href="/cashflow" <?= ($currentPage ?? '') === 'cashflow' ? 'class="active"' : '' ?>>Kas</a></li>
                <?php endif; ?>
                <li><a href="/proposals" <?= ($currentPage ?? '') === 'proposals' ? 'class="active"' : '' ?>>Saran</a></li>
                <li><a href="/settings" <?= ($currentPage ?? '') === 'settings' ? 'class="active"' : '' ?>>Settings</a></li>
            </ul>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['success']) ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($_SESSION['error']) ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <?= $content ?>
    </div>
</body>
</html>