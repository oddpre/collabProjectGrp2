<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['employee_id']) || ($_SESSION['role'] != 1 && $_SESSION['role'] != 2)) {

    header("Location: login.php");
    exit;
}

require_once "includes/config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['leave_id'])) {
    $action = $_POST['action'];
    $leave_id = (int)$_POST['leave_id'];
    if (in_array($action, ['approved', 'declined'])) {
        $stmt = $conn->prepare("UPDATE leaves SET status = ? WHERE leave_id = ?");
        $stmt->execute([$action, $leave_id]);
    }
}

$stmt = $conn->query("SELECT l.leave_id, l.employee_id, l.from_date, l.to_date, l.reason, l.status, l.date_requested, u.name FROM leaves l JOIN users u ON l.employee_id = u.employee_id ORDER BY l.date_requested DESC");
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Vacation Requests</title>
  <link rel="icon" href="/img/nkey.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;600;800&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="../img/favicon.png">
  <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<div class="container_admin_vacation">
  <div class="d-flex justify-content-between align-items-center mb-4">
  <h2 class="me-1">Vacation Requests (Admin)</h2>
    <a href="dashboard.php" class="btn btn-secondary">⬅ Back to Dashboard</a>
  </div>

  <table class="table table-bordered">
    <thead class="table-light">
      <tr>
        <th>Employee</th>
        <th>From</th>
        <th>To</th>
        <th>Reason</th>
        <th>Status</th>
        <th>Requested</th>
        <th>Actions</th>
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
        <td>
          <button class="btn btn-link p-0" onclick="showVacationInfo(<?= $row['employee_id'] ?>)">
            <?= htmlspecialchars($row['name']) ?>
          </button>
        </td>
        <td><?= htmlspecialchars($row['from_date']) ?></td>
        <td><?= htmlspecialchars($row['to_date']) ?></td>
        <td><?= htmlspecialchars($row['reason']) ?></td>
        <td><span class="badge bg-<?= $badge ?>"><?= ucfirst($row['status']) ?></span></td>
        <td><?= htmlspecialchars($row['date_requested']) ?></td>
        <td>
          <?php if ($row['status'] === 'pending'): ?>
            <form method="post" class="d-inline">
              <input type="hidden" name="leave_id" value="<?= $row['leave_id'] ?>">
              <button type="submit" name="action" value="approved" class="btn btn-success btn-sm"  <span class="badge bg-success" style="
    padding: 10px 10px 10px 10px;">Approved</span>
            </form>
            <form method="post" class="d-inline-block">
              <input type="hidden" name="leave_id" value="<?= $row['leave_id'] ?>">
            <button type="submit" name="action" value="declined" class="btn btn-danger" style="padding: 11px 10px 10px 14px; min-width: 78px;">Decline
            </button>
            </form>
          <?php else: ?>
            <em>No actions</em>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- Vacation Info Popup -->
<div id="vacationInfoPopup" 
  class="d-none position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center" 
  style="
    z-index: 1050;
      background: rgba(224, 222, 235, 0.8);
      backdrop-filter: blur(4px);
      box-shadow:
      -8px 4px 12px rgba(0, 0, 0, 0.4),
      -4px 2px 6px rgba(0, 0, 0, 0.2),
      4px 2px 8px rgba(0, 0, 0, 0.1),
      0 6px 10px rgba(0, 0, 0, 0.15),
      0 -6px 10px rgba(0, 0, 0, 0.15);
  ">
  <div class="bg-white rounded shadow p-4" style="width: 618px; height: 400px; display: flex; flex-direction: column; position: relative;">

    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="Vacation_Overview m-0 fs-4 fw-bold text-center flex-grow-1">Vacation Overview</h4>
      <button class="btn btn-close position-absolute top-0 end-0 m-3" onclick="closeVacationInfo()"></button>
    </div>

    <!-- iframe -->
    <iframe id="vacationInfoFrame" src="" frameborder="0" class="flex-grow-1" style="width: 100%; border: none; border-radius: 8px;"></iframe>

  </div>
</div>
<script>
function showVacationInfo(employeeId) {
  const iframe = document.getElementById('vacationInfoFrame');
  iframe.src = `vacation_info.php?employee_id=${employeeId}`;
  document.getElementById('vacationInfoPopup').classList.remove('d-none');
}

function closeVacationInfo() {
  document.getElementById('vacationInfoPopup').classList.add('d-none');
  document.getElementById('vacationInfoFrame').src = '';
}
</script>
</body>
</html>