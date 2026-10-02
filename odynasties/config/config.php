<?php
// Force error visibility inside config
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    // 1. Establish the global SQLite database file in the server's writable temp space
    $dbFile = "/tmp/odynasties.sqlite";
    $isNewDatabase = !file_exists($dbFile);

    // 2. Open the PDO file connection
    $pdo = new PDO("sqlite:" . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create a duplicate variable name so it is compatible with all script types
    $conn = $pdo;

    // 3. Automatically generate tables using your .sql file if this database is fresh
    if ($isNewDatabase) {
        $sqlPath = __DIR__ . "/database/odynasties.sql";
        if (file_exists($sqlPath)) {
            $sqlQueries = file_get_contents($sqlPath);
            $pdo->exec($sqlQueries);
        }
    }
} catch (PDOException $e) {
    echo "<div style='color:red; border:1px solid red; padding:15px; margin:20px; font-family:sans-serif;'>";
    echo "<strong>Database Connection Error:</strong> " . htmlspecialchars($e->getMessage());
    echo "</div>";
    die();
}
?>
