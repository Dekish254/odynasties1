<?php
try {
    // FIX: Using the global /tmp folder ensures read/write permission locks are bypassed
    $dbFile = "/tmp/odynasties.sqlite";
    $isNewDatabase = !file_exists($dbFile);

    $conn = new PDO("sqlite:" . $dbFile);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // If the database is freshly created in /tmp, automatically load your tables
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
