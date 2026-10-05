<?php
// Include your existing database connection file here
// Replace 'db.php' with the actual name of your connection file
include('db.php'); 

try {
    // This asks the database to list all tables it currently holds
    $query = $pdo->query("SHOW TABLES");
    $tables = $query->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h3>Tables found in your live database:</h3>";
    if (empty($tables)) {
        echo "No tables found! Your database is empty.";
    } else {
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>" . htmlspecialchars($table) . "</li>";
        }
        echo "</ul>";
    }
} catch (Exception $e) {
    echo "Error checking tables: " . $e->getMessage();
}
?>
