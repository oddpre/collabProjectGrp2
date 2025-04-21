<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION["employee_id"]) || $_SESSION["role"] != 1) {

    header("Location: login.php");
    exit;
}

require_once "includes/config.php";

$stmt = $conn->query("SELECT employee_id, name, email, department, is_admin FROM users ORDER BY employee_id ASC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All Users | Admin Panel</title>
  <link rel="icon" href="/img/nkey.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;600;800&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="./img/favicon.png">
  <link rel="stylesheet" href="./css/style.css">
</head>
<body>

<div class="container_users">
  <div class="card shadow">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h4 class="mpage_H">All Users</h4>
      <div>
        <a href="add_user.php" class="btn_user_new"><span style="color: #E0DEEB;">➕</span>New User</a>
        <a href="dashboard.php" class="btn_user_dash">⬅ Back to Dashboard</a>
      </div>
    </div>

    <div class="card-body">
      <table class="table table-bordered table-hover">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Department</th>
            <th>Admin</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $user): ?>
            <tr>
              <td><?= $user['employee_id'] ?></td>
              <td><?= htmlspecialchars($user['name']) ?></td>
              <td><?= htmlspecialchars($user['email']) ?></td>
              <td><?= htmlspecialchars($user['department']) ?></td>
              <td>
  <?php
    if ($user['is_admin'] == 1) {
      echo "Admin";
    } elseif ($user['is_admin'] == 2) {
      echo "Manager";
    } else {
      echo "User";
    }
  ?>
</td>

              <td>
                <a href="edit_user.php?id=<?= $user['employee_id'] ?>" class="btn_view_edit">View/Edit</a>
                <a href="delete_user.php?id=<?= $user['employee_id'] ?>" class="btn_user_del" onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
