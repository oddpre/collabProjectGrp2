<?php
// config.php - Database connection for the NKEY System
// config.php - Подключение к базе данных для системы NKEY

// Define path to the SQLite database
// Определить путь к базе данных SQLite
define('DB_PATH', __DIR__ . '/../db/users.db');

// Create a new SQLite connection using PDO
// Создать новое соединение SQLite с использованием PDO
try {
    $conn = new PDO('sqlite:' . DB_PATH);

    // Set PDO error mode to exception
    // Установить режим ошибок PDO в режим исключений
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Connection successful
    // Подключение успешно
} catch (PDOException $e) {
    // If connection fails, show JSON error message
    // Если подключение не удалось, показать сообщение об ошибке в формате JSON
    echo json_encode([
        "success" => false,
        "error" => "Connection failed: " . $e->getMessage()
    ]);
    exit;
}
?>
