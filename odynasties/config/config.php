<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
  // Force-override the host to route through your computer's gateway
$host     = '127.0.0.1'; 
$db       = 'my_application_db'; // Change to your actual database name
$user     = 'app_user';          // Change to your actual database user
$password = 'app_password';      // Change to your actual database password
$charset  = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset;port=3306";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $password, $options);
} catch (\PDOException $e) {
     echo "Database Error: " . $e->getMessage();
     exit;
}

    // 2. Natively build the core users and sessions tables (Bypassing MySQL syntax issues)
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        password_hash TEXT NOT NULL,
        role TEXT NOT NULL DEFAULT 'member',
        blood_group TEXT,
        status TEXT NOT NULL DEFAULT 'active',
        profile_picture TEXT
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS login_sessions (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        session_token TEXT NOT NULL,
        role TEXT NOT NULL,
        last_seen TEXT NOT NULL
    )");

} catch (PDOException $e) {
    echo "Database Error: " . htmlspecialchars($e->getMessage());
    die();
}
?>
