<?php
// Start the session
session_start();

// Check if user is logged in
if (!isset($_SESSION["employee_id"])) {
    // Redirect to login if not logged in
    header("Location: login.php");
    exit;
}

// Load user data from session
$name = $_SESSION["name"];
$employee_id = $_SESSION["employee_id"];
$role = $_SESSION["role"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>WORK EASE</title>
  <link rel="icon" href="/img/favicon.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;600;800&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="./img/favicon.png">
  <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<div class="container_dashboard">
  <!-- Header -->
  <header class="d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
      <img class="logo" src="./img/logo.png" alt="Logo">

    </div>
    <div class="logout_employer">
      <span class="emploee_name"><?= htmlspecialchars($name) ?> (ID: <?= $employee_id ?>)</span>
      <img src="<?= file_exists("./uploads/{$employee_id}.jpg") ? "./uploads/{$employee_id}.jpg" : "./uploads/user.png" ?>" 
      alt="Profile" width="47" height="44" class="rounded-circle">
    </div>
  </header>

  <div class="d-flex nav_dashboard">
    <!-- Sidebar -->
    <nav>
      <ul class="nav flex-column">
        <li  class="nav-link"><a href="dashboard.php">HOME</a></li>
        <li  class="nav-link"><a href="my_page.php">MY PAGE</a></li>
        <li  class="nav-link"><a href="my_timesheet.php">MY TIMESHEET</a></li>
        <li  class="nav-link"><a href="vacation.php">MY VACATION</a></li>

        <?php if ($role == 1): ?>
        <!-- Admin menu -->
        <hr>
        <li class="nav-link"><a href="users.php">USERS</a></li>
        <li class="nav-link"><a href="admin_timesheet.php">TIMESHEET</a></li>
        <li class="nav-link"><a href="admin_vacation.php">VACATION</a></li>
        <?php elseif ($role == 2): ?>
          <!-- Manager menu -->
          <hr>
          <li class="nav-link"><a href="admin_vacation.php">VACATION</a></li>
          <li class="nav-link"><a href="idcard/idcard_printer.php">ID CARD</a></li>
        <?php endif; ?>
      </ul>
      <img src="./img/1_leaf.png" alt="Leafs Top" class="leafs_top_dash">
        <img src="./img/2_ leaf.png" alt="Leafs Top" class="leafs_middle_dash">
        <img src="./img/3_leaf.png" alt="Leafs Top" class="leafs_bottom_dash">
    </nav>

    <!-- Main content -->
    <main class="main_dashboard">
      <div class=container_dash>
        <h2 class="dashboard_H2">Welcome to Employee Work Management System</h2>
        <p class="dashboard_p">You are logged in as <?= htmlspecialchars($name) ?>!</p>
        <?php if ($role == 1): ?>
          <p><strong></strong></p>
        <?php endif; ?>
      </div>
      <a href="logout.php">
          <button  class="login_Button_dashboard">LOG OUT</button>
      </a>
      <img src="./img/main_image.png" alt="main_image" class="main_image_main">
    </main>
  </div>
  </div>
</body>
</html>
