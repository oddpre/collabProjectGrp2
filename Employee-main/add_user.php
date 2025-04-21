<?php


session_start();

if (!isset($_SESSION['employee_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add New User | WORK EASE</title>
  <link rel="icon" href="/img/nkey.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;600;800&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="../img/favicon.png">
  <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<div class="container_add_user">
  <div class="card mx-auto shadow">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h4 class="mpage_H">Add New User</h4>
      <a href="users.php" class="btn_add_back">⬅ Back</a>
    </div>
    <div class="card-body">
  <form action="insert_user.php" method="post">
    <div class="">
      <label class="form-label">Name</label>
      <input type="text" name="name" class="form-control" placeholder="Enter full name" required>
    </div>
    <div class="">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" placeholder="Enter email address" required>
    </div>
    <div class="">
      <label class="form-label">Phone</label>
      <input type="text" name="phone" class="form-control" placeholder="Enter phone number">
    </div>
    <div class="">
      <label class="form-label">RFID ID</label>
      <input type="text" name="rfid_id" class="form-control" placeholder="Enter RFID ID">
    </div>
    <div class="">
      <label class="form-label">Department</label>
      <input type="text" name="department" class="form-control" placeholder="Enter department">
    </div>
    <div class="">
      <label class="form-label">Manager E-mail</label>
      <input type="email" name="manager_email" class="form-control" value="manager@nkey.no" placeholder="Enter manager's email">
    </div>
    <div class="">
    <label class="form-label">Role</label>
<select name="role" class="form-select">
  <option value="0">User</option>
  <option value="1">Admin</option>
  <option value="2">Manager</option>
      </select>
    </div>
    <div class="">
      <label class="form-label">Password</label>
      <input type="text" name="password" class="form-control"  placeholder="Enter password" required>
    </div>
    <div class="">
      <label class="form-label">Confirm Password</label>
      <input type="text" name="confirm" class="form-control"  placeholder="Re-enter password" required>
    </div>
    <button type="submit" class="btn_add-users">➕ Add User</button>
  </form>
</div>
  </div>
</div>
</body>
</html>