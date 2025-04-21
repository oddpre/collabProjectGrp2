<?php
session_start();
if (!isset($_SESSION['employee_id']) || $_SESSION['role'] != 2) {
  header("Location: ../login.php");
  exit;
}

require_once "../includes/config.php";

$stmt = $conn->query("SELECT employee_id, name FROM users ORDER BY name ASC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;600;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;600;800&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="./img/favicon.png">
  <link rel="stylesheet" href="../css/style.css">
  <style>
    body {
      font-family: "Inter", sans-serif;
      text-align: center;
    }
    .card {
      width: 53.98mm;
      height: 85.6mm;
      background: white;
      border: 2px solid #000;
      border-radius: 5mm;
      padding: 5mm;
      margin: 20px auto;
      box-sizing: border-box;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: start;
      overflow: hidden;
    }
    .photo {
      width: 32mm;
      height: 32mm;
      object-fit: cover;
      border-radius: 2mm;
    }
    .name {
      font-size: 14pt;
      font-weight: bold;
      margin-top: 5mm;
      text-align: center;
    }
    .id, .dept {
      font-size: 12pt;
      margin-top: 2mm;
      text-align: center;
    }
    .qr img {
      width: 100%;
      height: auto;
      max-width: 20mm;
      max-height: 20mm;
      object-fit: contain;
    }
    @media print {
      body { background: none; padding: 0; }
      .card { box-shadow: none; }
      h2, select, button { display: none; }
    }

    .wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background-color: #E0DEEB;
    box-shadow: -8px 4px 12px rgba(0, 0, 0, 0.4), -4px 2px 6px rgba(0, 0, 0, 0.2), 4px 2px 8px rgba(0, 0, 0, 0.1), 0 6px 10px rgba(0, 0, 0, 0.15), 0 -6px 10px rgba(0, 0, 0, 0.15);
    padding:20px;
    margin:40px;
}

h2 {
  font-size: 24px;
  margin-bottom: 10px;
}

select {
  font-size: 16px;
  padding: 6px 10px;
  margin-bottom: 20px;
}

.card {
  margin-bottom: 20px;
}

.btn-group {
  display: flex;
  gap: 10px;
  justify-content: center;
}

    .card.mirrored { transform: scaleX(-1) !important; transform-style: preserve-3d; }
    .card.mirrored * { transform: scaleX(-1) !important; display: inline-block; }
    .card.mirrored #cardPhoto { transform: scaleX(-1) !important; }
  </style>
  <title>ID Card Printer</title>
</head>
<body>
  <div class=container_PrintCard>
  <div class="wrapper">
    <h2>Print ID Card</h2>

    <select class="user_select" id="userSelect">
      <option value="">-- Select Employee --</option>
      <?php foreach ($users as $user): ?>
        <option value="<?= $user['employee_id'] ?>">
          <?= htmlspecialchars($user['name']) ?> (ID: <?= $user['employee_id'] ?>)
        </option>
      <?php endforeach; ?>
    </select>

    <div class="card mirrored" id="card">
      <img id="cardPhoto" class="photo" src="../uploads/user.png" alt="User Photo">
      <div class="name" id="cardName"></div>
      <div class="id" id="cardID"></div>
      <div class="qr"><canvas id="qrcode"></canvas></div>
    </div>

    <div class="btn-group">
      <button class="btn_print" onclick="window.print()">🖨 Print</button>
      <a class="btn_dashboard" href="../dashboard.php" style="padding-right: 15px; padding-left: 13px; position: relative;">
  ⬅ Back to Home
</a>
    </div>
  </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
  <script>
    document.getElementById("userSelect").addEventListener("change", async function () {
      const id = this.value;
      if (!id) return;

      const response = await fetch(`get_user.php?id=${id}`);
      const data = await response.json();

      if (data && !data.error) {
        document.getElementById("cardName").innerText = data.name;
        document.getElementById("cardID").innerText = "ID: " + data.employee_id;

        const cleanPath = data.profile_image?.replace(/^\.?\/?uploads\//, "") || "user.png";
        document.getElementById("cardPhoto").src = `../uploads/${cleanPath}`;

        const qrText = `ID:${data.employee_id}, Name:${data.name}`;
        QRCode.toCanvas(document.getElementById("qrcode"), qrText, { width: 100 }, function (error) {
          if (error) console.error(error);
        });
      }
    });
  </script>
</body>
