<?php
require_once "../includes/config.php";

$employee_id = $_GET['id'] ?? null;

if ($employee_id) {
  $stmt = $conn->prepare("SELECT name, employee_id, profile_image FROM users WHERE employee_id = ?");
  $stmt->execute([$employee_id]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($user) {
    if (!empty($user['profile_image']) && strpos($user['profile_image'], '/') === false) {
        $user['profile_image'] = "../uploads/" . $user['profile_image'];
    }
    echo json_encode($user);
    ;
  } else {
    echo json_encode(["error" => "User not found"]);
  }
} else {
  echo json_encode(["error" => "No ID provided"]);
}
?>
