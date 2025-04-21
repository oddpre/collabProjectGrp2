<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);


session_start();


require_once "includes/functions.php";
require_once "includes/config.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $employee_id = $_POST["employee_id"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM users WHERE employee_id = ?");
    $stmt->execute([$employee_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user["password_hash"])) {

        $_SESSION["employee_id"] = $user["employee_id"];
        $_SESSION["name"] = $user["name"];
        $_SESSION["role"] = $user["is_admin"];
        header("Location: dashboard.php");
        exit;

    } else {
        $message = "Invalid employee ID or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login | WORK SYSTEM</title>
  <link rel="icon" href="./img/favicon.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;600;800&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="./img/favicon.png">
  <link rel="stylesheet" href="./css/style.css">
</head>
<body>


  <div class="container_login_form">
    <div>
        <a href="index.html" class="close-link">
    &#10005;
        </a>
      <h3 class="text-center login_H">Log In</h3>

      <?php if ($message): ?>
       <div class="alert alert-danger"><?= $message ?></div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="input-control">
          <input type="number" class="form_control" name="employee_id" placeholder="Username ID" min="1" required>
          <input type="password" class="form_control" name="password" placeholder="Password" required>
        </div>
    <button type="submit" class="button_login_form">Login</button>    
      <p class="btn_forgot">
        <a class="btn_forgot" href="#" onclick="showResetForm()">Forgot your password?</a>
      </p>
    </div>
    </div>
  <div id="resetContainer" class="d-none reset_Containers">
    <iframe src="./reset_password.html"></iframe>
  </div> 
  <script>
    function showResetForm() {
      document.getElementById("resetContainer").classList.remove("d-none");
    }
  </script>
</body>
</html>
