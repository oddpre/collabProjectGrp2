<?php
session_start();
require_once "includes/config.php";

$employee_id = $_GET['employee_id'] ?? null;

if (!$employee_id) {
    echo "Invalid employee ID.";
    exit;
}

// Fetch vacation data, including dynamic leave_used calculation
$stmt = $conn->prepare("
    SELECT 
        leave_quota, 
        (SELECT COALESCE(SUM(julianday(to_date) - julianday(from_date) + 1), 0)
         FROM leaves 
         WHERE employee_id = ? AND status = 'approved') AS leave_used,
        (SELECT COALESCE(SUM(julianday(to_date) - julianday(from_date) + 1), 0)
         FROM leaves 
         WHERE employee_id = ? AND status = 'pending') AS pending_days
    FROM users 
    WHERE employee_id = ?
");
$stmt->execute([$employee_id, $employee_id, $employee_id]);


$data = $stmt->fetch(PDO::FETCH_ASSOC);

$leave_quota = $data['leave_quota'] ?? 0;
$leave_used = $data['leave_used'] ?? 0;
$pending_days = $data['pending_days'] ?? 0;
$leave_remaining = $leave_quota - $leave_used;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Vacation Info</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;600;800&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="./img/favicon.png">
  <link rel="stylesheet" href="./css/style.css">
</head>
<body>
  <div class="vacation_Info">
    <h5>Vacation Details</h5>
    <ul class="list-group">
      <li class="list-group-item"><b>Total Leave Quota:</b> <?= $leave_quota ?> days</li>
      <li class="list-group-item"><b>Leave Used:</b> <?= $leave_used ?> days</li>
      <li class="list-group-item"><b>Pending Leave Requests:</b> <?= $pending_days ?> days</li>
      <li class="list-group-item"><b>Leave Remaining (before pending):</b> <?= $leave_remaining ?> days</li>
    </ul>
  </div>
</body>
</html>