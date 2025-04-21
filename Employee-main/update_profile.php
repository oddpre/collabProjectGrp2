<?php

session_start();

require_once "includes/config.php";

header('Content-Type: application/json');

if (!isset($_SESSION["employee_id"])) {
  echo json_encode(["success" => false, "error" => "Not logged in"]);
  exit;
}

$field = $_POST['field'] ?? '';
$value = $_POST['value'] ?? '';
$target_id = $_POST['target_id'] ?? $_SESSION['employee_id'];

// Only admins can edit other users
if ($target_id != $_SESSION['employee_id'] && $_SESSION['role'] != 1) {
  echo json_encode(["success" => false, "error" => "Unauthorized"]);
  exit;
}

$allowed_fields = ["name", "phone", "rfid_id", "password_hash", "email", "department", "manager_email", "role"];

if (!in_array($field, $allowed_fields)) {
  echo json_encode(["success" => false, "error" => "Invalid field"]);
  exit;
}

// Translate frontend "role" to DB column "is_admin"
if ($field === "role") {
  $field = "is_admin";
}

try {
  if ($field === "password_hash") {
    if (strlen($value) < 4) throw new Exception("Password too short");
    $value = password_hash($value, PASSWORD_DEFAULT);
}


  $stmt = $conn->prepare("UPDATE users SET $field = :value WHERE employee_id = :id");
  $stmt->execute([
    ":value" => $value,
    ":id" => $target_id
  ]);

  echo json_encode(["success" => true]);
} catch (Exception $e) {
  echo json_encode(["success" => false, "error" => $e->getMessage()]);
  exit;
}
