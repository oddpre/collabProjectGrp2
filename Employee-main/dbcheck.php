<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('DB_PATH', __DIR__ . '/db/users.db');

try {

    $conn = new PDO('sqlite:' . DB_PATH);

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->query("SELECT name FROM sqlite_master WHERE type='table'");

    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "<h3>✅ Database connection successful!</h3>";
    echo "<p>Found tables: " . implode(', ', $tables) . "</p>";

} catch (PDOException $e) {

    echo "<h3>❌ Connection failed:</h3>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
?>
