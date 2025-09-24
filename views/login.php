<?php
$title = 'Login - SPBU Management System';
ob_start();
?>

<div style="max-width: 400px; margin: 5rem auto;">
    <div class="card">
        <h2 style="text-align: center; margin-bottom: 2rem; color: #667eea;">Login ke Sistem</h2>
        
        <form method="POST" action="/login">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn" style="width: 100%;">Login</button>
        </form>
        
        <div style="margin-top: 2rem; text-align: center; color: #666;">
            <p><strong>Akun Demo:</strong></p>
            <p>Manager: manager@spbu.com / manager123</p>
            <p>Admin: admin@spbu.com / admin123</p>
            <p>Staff: putri@spbu.com / putri123</p>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>