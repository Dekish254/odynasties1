<?php
// 1. Safe include for header with real-time crash tracking
try {
    if (file_exists('includes/header.php')) {
        include 'includes/header.php';
    }
} catch (Throwable $e) {
    echo "<div style='color:red; padding:10px; border:1px solid red;'><strong>Header Error:</strong> " . htmlspecialchars($e->getMessage()) . " in " . $e->getFile() . " on line " . $e->getLine() . "</div>";
}
?>

<!-- Your Login Form Layout Structure -->
<div class="login-container" style="max-width: 400px; margin: 50px auto; padding: 20px; font-family: sans-serif;">
    <h2>Sign In to ODynasties</h2>
    
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger" style="color: red; margin-bottom: 15px;"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group" style="margin-bottom: 15px;">
            <label>Login As:</label><br>
            <select name="login_as" class="form-control" style="width: 100%; padding: 8px; margin-top:5px;">
                <option value="member">Member</option>
                <option value="admin">Administrator</option>
            </select>
        </div>
        <div class="form-group" style="margin-bottom: 15px;">
            <label>Email Address:</label><br>
            <input type="email" name="email" class="form-control" required style="width: 100%; padding: 8px; margin-top:5px;">
        </div>
        <div class="form-group" style="margin-bottom: 15px;">
            <label>Password:</label><br>
            <input type="password" name="password" class="form-control" required style="width: 100%; padding: 8px; margin-top:5px;">
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 10px; background: #d9534f; color: white; border: none; cursor: pointer;">Login</button>
    </form>
</div>

<?php
// 2. Safe include for footer with real-time crash tracking
try {
    if (file_exists('includes/footer.php')) {
        include 'includes/footer.php';
    }
} catch (Throwable $e) {
    echo "<div style='color:red; padding:10px; border:1px solid red;'><strong>Footer Error:</strong> " . htmlspecialchars($e->getMessage()) . " in " . $e->getFile() . " on line " . $e->getLine() . "</div>";
}
?>
