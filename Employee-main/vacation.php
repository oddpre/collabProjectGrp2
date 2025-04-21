<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit;
}

require_once "includes/config.php";

$employee_id = $_SESSION['employee_id'];
$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $from = $_POST['from_date'] ?? '';
    $to = $_POST['to_date'] ?? '';
    $reason = $_POST['reason'] ?? '';
    $today = date('Y-m-d');

    if ($from >= $today && $to >= $from && !empty($reason)) {
        $stmt = $conn->prepare("INSERT INTO leaves (employee_id, from_date, to_date, reason, status, date_requested) VALUES (?, ?, ?, ?, 'pending', datetime('now'))");
        $stmt->execute([$employee_id, $from, $to, $reason]);
        $message = "✅ Vacation request submitted!";
    } else {
        $message = "❌ Invalid date range or missing reason.";
    }
}

// Fetch previous requests
$stmt = $conn->prepare("SELECT from_date, to_date, reason, status, date_requested FROM leaves WHERE employee_id = ? ORDER BY date_requested DESC");
$stmt->execute([$employee_id]);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Vacation</title>
  <link rel="icon" href="/img/nkey.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;600;800&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="./img/favicon.png">
  <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<div class="container_vacation">
<div class="mb-4 text-center">
  <h2 class="mb-3">Apply for Vacation</h2>
  
  <div class="d-inline-flex flex-wrap justify-content-center gap-2">
    <button class="btn btn-secondary" onclick="showVacationInfo()"> Vacation Days</button>
    <a href="dashboard.php" class="btn btn-secondary">⬅ Back to Dashboard</a>
  </div>
</div>

  <!-- Vacation Info Popup -->
  <div id="vacationInfoPopup" class="d-none w-100  d-flex justify-content-center align-items-center py-4">
  <div  style="width: 100%; max-width: 618px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="Vacation_Overview" style="margin: 0 auto;font-size: 29px;font-weight: bold;">Vacation Overview</h4>
      <button class="btn btn-close" onclick="closeVacationInfo()"></button>
    </div>
    <iframe id="vacationInfoFrame" src="" frameborder="0" style="width: 100%; height: 300px;">    <div class="mt-0">
      <button class="btn_close_vac_info" onclick="closeVacationInfo()">Close</button>
    </div></iframe>

  </div>
</div>

  <script>
  function showVacationInfo() {
    const iframe = document.getElementById('vacationInfoFrame');
    iframe.src = 'vacation_info.php?employee_id=<?= $employee_id ?>';
    document.getElementById('vacationInfoPopup').classList.remove('d-none');
  }

  function closeVacationInfo() {
    document.getElementById('vacationInfoPopup').classList.add('d-none');
    document.getElementById('vacationInfoFrame').src = '';
  }
  </script>

  <?php if (!empty($message)): ?>
    <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <form method="post" class="card p-4 mb-5">
    <div class="row mb-3">
      <div class="col-md-4">
        <label for="from_date" class="form-label">From Date</label>
        <input type="date" name="from_date" id="from_date" class="form-control" required>
      </div>
      <div class="col-md-4">
        <label for="to_date" class="form-label">To Date</label>
        <input type="date" name="to_date" id="to_date" class="form-control" required>
      </div>
      <div class="col-md-4">
        <label for="reason" class="form-label">Reason</label>
        <input type="text" name="reason" id="reason" class="form-control" required>
      </div>
    </div>
    <button type="submit" class="btn_reqest">Send Request</button>
  </form>

  <h4 class="mb-3">My Vacation Requests</h4>
  <table class="table table-bordered">
    <thead class="table-light">
      <tr>
        <th>From</th>
        <th>To</th>
        <th>Reason</th>
        <th>Status</th>
        <th>Requested</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($requests as $row):
      $badge = 'secondary';
      if ($row['status'] === 'approved') $badge = 'success';
      elseif ($row['status'] === 'pending') $badge = 'warning';
      elseif ($row['status'] === 'declined') $badge = 'danger';
    ?>
      <tr>
        <td><?= htmlspecialchars($row['from_date']) ?></td>
        <td><?= htmlspecialchars($row['to_date']) ?></td>
        <td><?= htmlspecialchars($row['reason']) ?></td>
        <td><span class="badge bg-<?= $badge ?>"><?= ucfirst($row['status']) ?></span></td>
        <td><?= htmlspecialchars($row['date_requested']) ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body>
</html>
