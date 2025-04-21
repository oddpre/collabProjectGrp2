<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION["employee_id"]) || $_SESSION["role"] != 1) {
    header("Location: login.php");
    exit;
}

require_once "includes/config.php";

$user_id = $_GET['id'] ?? null;
if (!$user_id) {
    echo "User ID is missing.";
    exit;
}

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM users WHERE employee_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    echo "User not found.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit User #<?= $user['employee_id'] ?></title>
  <link rel="icon" href="/img/nkey.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;600;800&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="./img/favicon.png">
  <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<div class="container_edit_user">
  <div class="">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h4 class="ms-2">Edit User #<?= $user['employee_id'] ?></h4>
      <a href="users.php" class="btn-secondary">⬅ Back</a>
    </div>
    <div class="card_body">
      <ul class="list-group">
        <li class="list-group-item"><strong>ID:</strong> <?= $user['employee_id'] ?></li>

        <?php
        $fields = [
          'name' => 'Name',
          'email' => 'Email',
          'phone' => 'Phone',
          'rfid_id' => 'RFID',
          'department' => 'Department',
          'manager_email' => 'Manager Email',
          'role' => 'Role'
        ];
        foreach ($fields as $key => $label):
        ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <span><strong><?= $label ?>:</strong> <span id="<?= $key ?>Display">
            <?php
              if ($key === 'role') {
                $role = $user[$key] ?? 0;
                echo $role == 1 ? 'Admin' : ($role == 2 ? 'Manager' : 'User');
              } else {
                echo htmlspecialchars($user[$key] ?? '');
              }
            ?>
          </span></span>
          <button class="btn_save_users" onclick="editField('<?= $key ?>')">Edit</button>
        </li>
        <li class="list-group-item d-none" id="<?= $key ?>EditRow">
          <form onsubmit="return saveField(event, '<?= $key ?>')">
            <div>
              <?php if ($key === 'role'): ?>
                <select id="<?= $key ?>Input" class="form-select">
                  <option value="0" <?= ($user['role'] ?? 0) == 0 ? 'selected' : '' ?>>User</option>
                  <option value="1" <?= ($user['role'] ?? 0) == 1 ? 'selected' : '' ?>>Admin</option>
                  <option value="2" <?= ($user['role'] ?? 0) == 2 ? 'selected' : '' ?>>Manager</option>
                </select>
              <?php else: ?>
                <input type="text" name="<?= $key ?>" id="<?= $key ?>Input" class="form-control" value="<?= htmlspecialchars($user[$key] ?? '') ?>">
              <?php endif; ?>
              <button class="btn-success" type="submit">Save</button>
              <button class="btn-secondary" type="button" onclick="cancelEdit('<?= $key ?>')">Cancel</button>
            </div>
          </form>
        </li>
        <?php endforeach; ?>

      </ul>
    </div>
  </div>
</div>
<script>
function editField(field) {
  document.getElementById(field + "Display")?.parentElement.parentElement.classList.add("d-none");
  document.getElementById(field + "EditRow")?.classList.remove("d-none");
}

function cancelEdit(field) {
  document.getElementById(field + "EditRow")?.classList.add("d-none");
  document.getElementById(field + "Display")?.parentElement.parentElement.classList.remove("d-none");
}

function saveField(event, field) {
  event.preventDefault();
  let value = document.getElementById(field + "Input").value;
  fetch("update_profile.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded"
    },
    body: `field=${field}&value=${encodeURIComponent(value)}&target_id=<?= $user['employee_id'] ?>`
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      if (field === 'role') {
        const roleText = value == 1 ? 'Admin' : value == 2 ? 'Manager' : 'User';
        document.getElementById(field + "Display").innerText = roleText;
      } else {
        document.getElementById(field + "Display").innerText = value;
      }
      cancelEdit(field);
    } else {
      alert("Error: " + data.error);
    }
  })
  .catch(err => alert("Request failed: " + err));
}
</script>
</body>
</html>