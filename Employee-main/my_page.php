<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION["employee_id"])) {
    header("Location: login.php");
    exit();
}

require_once "includes/config.php";

$employee_id = $_SESSION["employee_id"];
$stmt = $conn->prepare("SELECT * FROM users WHERE employee_id = ?");
$stmt->execute([$employee_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>MY PROFILE | WORK EASE</title>
  <link rel="icon" href="/img/nkey.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;600;800&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="./img/favicon.png">
  <link rel="stylesheet" href="./css/style.css">
</head>
<body>
  <div class="container_profile">
    <div class="header_profile">
      <form action="./upload_image.php" method="post" enctype="multipart/form-data" id="uploadForm">
        <input type="hidden" name="employee_id" value="<?= $employee_id ?>">
        <h4 class="mpage_H"><b>Hello, <?= htmlspecialchars($user['name']) ?></b></h4> 
          <div class="img_info">
          <img src="<?= htmlspecialchars($user['profile_image'] ?: './uploads/user.png') ?>" alt="" class="" width="122" height="140">
          <button type="button" class="btn_upload_img" id="cameraBtn">Use Camera</button>
          <button type="submit" class="btn_upload_img" id="uploadBtn" disabled>Upload New Image</button>
          <input type="file" name="profile_image" class="input_img" id="profileInput">
          <span id="imageError" class="text-danger"></span>
          </div>
        <div id="cameraContainer" class="d-none" style="text-align: center;">
  <video id="video" autoplay width="320" height="240" style="border:1px solid #ccc;"></video><br>
  <button id="captureBtn" class="btn btn-success">📸 Take Picture</button>
  <button type="button" id="cancelCameraBtn" class="btn btn-secondary m-0">Cancel</button>
  <canvas id="canvas" width="320" height="240" style="display:none;"></canvas>
  
        </div>
      </form> 
    <div>
  </div>    
      <ul>
        <li class="list-group-item"><span><b>ID: </b><?= $user['employee_id'] ?></li></span>
        <li class="list-group-item"><b>Name: </b> <?= htmlspecialchars($user['name']) ?><button class="btn_edit" onclick="editField('name')">Edit</button>
        </li>
        <li  id="nameEditRow" class="d-none">
          <form onsubmit="return saveField(event, 'name')">
              <input type="text" name="name" id="nameInput" class="form_control_page" value="<?= htmlspecialchars($user['name']) ?>">
              <button class="btn-success" type="submit">Save</button>
              <button class="btn-secondary" type="button" onclick="cancelEdit('name')">Cancel</button>
          </form>
        </li>
        <li class="list-group-item"><b>Phone: </b><?= htmlspecialchars($user['phone']) ?><button class="btn_edit_phone" onclick="editField('phone')">Edit</button>  
        </li>
        <li class="d-none" id="phoneEditRow">
          <form onsubmit="return saveField(event, 'phone')">
              <input type="text" name="phone" id="phoneInput" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>">
              <button class="btn_save" type="submit">Save</button>
              <button class="btn_cansel" type="button" onclick="cancelEdit('phone')">Cancel</button>
          </form>
        </li>
        <li class="list-group-item">
          <span class="list-group-item"><b>Department: </b><?= htmlspecialchars($user['department']) ?></span>
        </li>
        <li class="list-group-item">
          <span><b>Email: </b> <?= htmlspecialchars($user['manager_email']) ?></span> 
        </li>
        <li>
          <span class="list-group-item"><b>RFID: </b><?= htmlspecialchars($user['rfid_id'] ?? '') ?><button class="btn_edit_RFID" onclick="editField('rfid_id')">Edit</button></span>
        </li>
        <li class="list-group-item d-none" id="rfid_idEditRow">
          <form onsubmit="return saveField(event, 'rfid_id')">
              <input type="text" name="rfid_id" id="rfid_idInput" class="form-control" value="<?= htmlspecialchars($user['rfid_id'] ?? '') ?>">
              <button class="btn-success" type="submit">Save</button>
              <button class="btn-secondary" type="button" onclick="cancelEdit('rfid_id')">Cancel</button>
          </form>
        </li>
        <li class="list-group-item"><b>Password: </b> ******** <button class="btn_edit_pas" onclick="editField('password')">Edit</button>          
        </li>
        <li class="list-group-item d-none" id="passwordEditRow">
          <form onsubmit="return saveField(event, 'password')">
              <input type="password" name="password" id="passwordInput" class="form-control" placeholder="New Password">
              <input type="password" name="confirm" id="confirmInput" class="form-control" placeholder="Confirm Password">
              <button class="btn-success" type="submit">Save</button>
              <button class="btn-secondary" type="button" onclick="cancelEdit('password')">Cancel</button>
          </form>
          <span id="status-password" class="text-success"></span>

        </li>
      </ul>  
  </div>
  <a href="./dashboard.php" class="btn_dashboard">⬅ Back to Dashboard</a>
<script>
  let cameraStream;

  document.getElementById("cameraBtn").addEventListener("click", function () {
  const container = document.getElementById("cameraContainer");
  container.classList.remove("d-none");

  navigator.mediaDevices.getUserMedia({ video: true })
    .then(stream => {
      cameraStream = stream;
      document.getElementById("video").srcObject = stream;
    })
    .catch(err => alert("Camera error: " + err));
});

document.getElementById("cancelCameraBtn").addEventListener("click", function () {
  
  if (cameraStream) {
    cameraStream.getTracks().forEach(track => track.stop());
    cameraStream = null;
  }
  
  document.getElementById("cameraContainer").classList.add("d-none");
});

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
  let value;
  let originalField = field;

  if (field === "password") {
    const pass = document.getElementById("passwordInput").value;
    const confirm = document.getElementById("confirmInput").value;
    if (pass !== confirm || pass.length < 4) {
      alert("Passwords do not match or are too short.");
      return;
    }
    value = pass;
    field = "password_hash";
  } else {
    value = document.getElementById(field + "Input").value;
  }

  fetch("update_profile.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded"
    },
    body: `field=${field}&value=${encodeURIComponent(value)}`
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      if (field !== "password_hash") {
        const displayEl = document.getElementById(field + "Display");
        if (displayEl) {
          displayEl.innerText = value;
        }
      } else {
        document.getElementById("status-password").innerText = "Password updated successfully.";
      }
      cancelEdit(originalField);
    } else {
      alert("Error updating field: " + data.error);
    }
  })
  .catch(error => alert("Request failed: " + error));
}

document.getElementById("profileInput").addEventListener("change", function() {
  const file = this.files[0];
  const uploadBtn = document.getElementById("uploadBtn");
  const errorMsg = document.getElementById("imageError");

  if (!file) {
    uploadBtn.disabled = true;
    errorMsg.innerText = "";
    return;
  }

  const allowedTypes = ["image/jpeg", "image/png", "image/gif"];
  if (!allowedTypes.includes(file.type)) {
    errorMsg.innerText = "Invalid file type. Only JPG, PNG or GIF allowed.";
    this.value = "";
    uploadBtn.disabled = true;
  } else {
    errorMsg.innerText = "";
    uploadBtn.disabled = false;
  }
});

document.getElementById("cameraBtn").addEventListener("click", function () {
  document.getElementById("cameraContainer").classList.remove("d-none");
  navigator.mediaDevices.getUserMedia({ video: true })
    .then(stream => {
      document.getElementById("video").srcObject = stream;
    })
    .catch(err => alert("Camera error: " + err));
});

document.getElementById("captureBtn").addEventListener("click", function () {
  const canvas = document.getElementById("canvas");
  const video = document.getElementById("video");
  canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

  canvas.toBlob(blob => {
    const formData = new FormData();
    formData.append("employee_id", "<?= $employee_id ?>");
    formData.append("profile_image", blob, "photo.jpg");

    fetch("upload_image.php", {
      method: "POST",
      body: formData
    })
    .then(response => response.text())
    .then(() => window.location.href = "my_page.php")
    .catch(err => alert("Upload failed: " + err));
  }, 'image/jpeg');
});
</script>
</body>
</html>