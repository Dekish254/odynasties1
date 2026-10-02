<?php
try {
    $dbFile = __DIR__ . "/odynasties.sqlite";
    $isNewDatabase = !file_exists($dbFile);

    // 1. Instantly creates the database file
    $conn = new PDO("sqlite:" . $dbFile);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. If the file is brand new, automatically import your SQL file structure
    if ($isNewDatabase) {
        $sqlPath = __DIR__ . "/database/odynasties.sql";
        
        if (file_exists($sqlPath)) {
            $sqlQueries = file_get_contents($sqlPath);
            $conn->exec($sqlQueries);
        }
    }

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
