<?php
// insert_user.php - Insert new user into DB

session_start();
require_once "includes/config.php";

// Only admin can add users
if (!isset($_SESSION['employee_id']) || $_SESSION['role'] != 1) {
  header("Location: login.php");
  exit;
}

// Read and validate POST data
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$rfid_id = $_POST['rfid_id'] ?? null;
$department = $_POST['department'] ?? '';
$manager_email = $_POST['manager_email'] ?? '';
$role = $_POST['role'] ?? 0;
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm'] ?? '';

// Basic password validation
if ($password !== $confirm || strlen($password) < 4) {
  die("Password mismatch or too short.");
}

// Hash password
$hash = password_hash($password, PASSWORD_DEFAULT);

// Default profile image
$profile_image = './uploads/user.png';

// Set default leave quota
$leave_quota = 25;

try {
  // Include leave_quota in insert statement
  $stmt = $conn->prepare("INSERT INTO users 
    (name, email, phone, rfid_id, department, manager_email, is_admin, password_hash, profile_image, leave_quota)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
  );

  $stmt->execute([
    $name,
    $email,
    $phone,
    $rfid_id,
    $department,
    $manager_email,
    $role,
    $hash,
    $profile_image,
    $leave_quota
  ]);

  header("Location: users.php");
  exit;

} catch (PDOException $e) {
  die("Database error: " . $e->getMessage());
}
?>
