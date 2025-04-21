<?php


require_once "includes/config.php";

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(["success" => false, "error" => "No ID provided"]);
    exit;
}

$emp_id = $_GET['id'];

try {
    $stmt = $conn->prepare("SELECT name FROM users WHERE employee_id = ?");
    $stmt->execute([$emp_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo json_encode(["success" => true, "name" => $user['name']]);
    } else {
        echo json_encode(["success" => false, "error" => "User not found"]);
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>
