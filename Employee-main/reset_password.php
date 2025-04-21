<?php
// reset/reset_password.php
// 🔐 Handle reset request based on employee ID and send email

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once "./includes/config.php";

$emp1 = $_POST["emp_id_1"] ?? null;
$emp2 = $_POST["emp_id_2"] ?? null;

function sendResponse($message, $type = "info") {
  echo "<html><head>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>
  </head><body>
    <div class='close-btn' onclick='closeIframe()' style='font-size: 24px; cursor: pointer;'>&times;</div>
    <div class='alert alert-{$type} mt-5 text-center'>{$message}</div>
    <div class='text-center'>
      <button class='btn btn-secondary mt-3' onclick='closeIframe()'>Close</button>
    </div>
    <script>
      function closeIframe() {
        if (window.parent !== window) {
          const resetContainer = window.parent.document.getElementById(\"resetContainer\");
          if (resetContainer) {
            resetContainer.classList.add(\"d-none\");
          }
        } else {
          window.location.href = \"./index.html\";
        }
      }
    </script>
  </body></html>";
  exit;
}


if (!$emp1 || !$emp2 || $emp1 !== $emp2) {
  sendResponse("IDs do not match. Please try again.", "danger");
}

try {
  $stmt = $conn->prepare("SELECT name, email FROM users WHERE employee_id = ?");
  $stmt->execute([$emp1]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$user) {
    sendResponse("User not found.", "danger");
  }

  if (!$user["email"]) {
    sendResponse("No email on file. Please contact an admin to reset your password.", "warning");
  }

  // Simulate email sending
  // $to = $user["email"];
  // $subject = "Test Endring";
  // $message = "Test: You requested a password reset.";
  // $headers = "From: noreply@workease.no";

  // mail($to, $subject, $message, $headers);

  sendResponse("Check your email: <strong>{$user["email"]}</strong>", "success");

} catch (Exception $e) {
  sendResponse("Something went wrong. Please try again later.", "danger");
}
?>
