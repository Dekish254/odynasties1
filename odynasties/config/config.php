<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    // 1. Open a clean SQLite file connection
    $dbFile = "/tmp/odynasties.sqlite";
    $host     = 'db'; // Must match your MySQL docker service name
$db       = 'my_application_db';
$user     = 'app_user';
$password = 'app_password';
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
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn = $pdo;

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
