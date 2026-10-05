<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host     = '127.0.0.1'; // <-- Set this to 127.0.0.1
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
     echo "Database Error: " . $e->getMessage();
     exit;
}

// The automatic SQLite CREATE TABLE code has been safely removed 
// because your tables are already live inside your MySQL Docker container.

?>
