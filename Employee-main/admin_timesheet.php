<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['employee_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit;
}

require_once "includes/config.php";

$selectedUser = isset($_GET['user_id']) ? (int)$_GET['user_id'] : $_SESSION['employee_id'];
$selectedMonth = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
$selectedDate = isset($_GET['date']) ? $_GET['date'] : null;

$users = $conn->query("SELECT employee_id, name FROM users ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

list($year, $month) = explode('-', $selectedMonth);
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

$holidays = ['01.01', '01.05', '17.05', '25.12', '26.12'];

$stmt = $conn->prepare("SELECT date(clock_in) as day, clock_in, clock_out FROM timesheet WHERE employee_id = ? AND strftime('%Y-%m', clock_in) = ?");
$stmt->execute([$selectedUser, $selectedMonth]);
$clockData = $stmt->fetchAll(PDO::FETCH_ASSOC);
$clockMap = [];
foreach ($clockData as $entry) {
    $clockMap[$entry['day']] = $entry;
}

$stmt = $conn->prepare("SELECT from_date, to_date, status FROM leaves WHERE employee_id = ?");
$stmt->execute([$selectedUser]);
$leaves = $stmt->fetchAll(PDO::FETCH_ASSOC);

$leaveDays = [];
foreach ($leaves as $leave) {
    $start = new DateTime($leave['from_date']);
    $end = new DateTime($leave['to_date']);
    while ($start <= $end) {
        $leaveDays[$start->format('Y-m-d')] = $leave['status'];
        $start->modify('+1 day');
    }
}

$today = new DateTime();
$todayDateOnly = $today->format('Y-m-d');

function isWeekend($date) {
    $day = date('w', strtotime($date));
    return $day == 0 ? 'table-danger' : ($day == 6 ? 'table-warning' : '');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Timesheet</title>
  <link rel="icon" href="/img/nkey.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;600;800&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="../img/favicon.png">
  <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<div class="container_timesheet">
  <div class="d-flex justify-content-between align-items-center">
    <h2 class="tm-H">Admin Timesheet Overview</h2>
    <a href="dashboard.php" class="btn_time">⬅ Back to Dashboard</a>
  </div>
  <form method="get" class="d-flex gap-3 align-items-center mb-3">
    <select name="user_id" class="form-select w-auto">
      <option value="" disabled <?= !isset($_GET['user_id']) ? 'selected' : '' ?>>- User -</option>
        <?php foreach ($users as $user): ?>
      <option value="<?= $user['employee_id'] ?>" <?= $user['employee_id'] == $selectedUser ? 'selected' : '' ?>>
        <?= htmlspecialchars($user['name']) ?> (ID: <?= $user['employee_id'] ?>)
      </option>
        <?php endforeach; ?>
    </select>
      <input class="text-center px-2" style="padding-top: 0.45rem !important; border: none; padding-bottom: 0.45rem !important; border-radius: 3px;" type="month" name="month" value="<?= $selectedMonth ?>">
        <button type="submit" class="btn btn-secondary m-0">View Month</button>
        <input type="hidden" name="user_id" value="<?= $selectedUser ?>">
    <input type="date" name="date" value="<?= $selectedDate ?>" class="form-control w-auto">
    <button type="submit" class="btn btn-secondary m-0">View Date</button>
  </form>
  <h4 class="text-center mb-3"><?= htmlspecialchars($users[array_search($selectedUser, array_column($users, 'employee_id'))]['name']) ?> (ID: <?= $selectedUser ?>)</h4>

  <table class="table table-bordered">
    <thead class="table-light">
      <tr>
        <th>Date</th>
        <th>Status</th>
        <th>Check In</th>
        <th>Check Out</th>
        <th>Pause</th>
        <th>Unavailable</th>
        <th>Total Hours</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $start = $selectedDate ? new DateTime($selectedDate) : new DateTime("$selectedMonth-01");
      $end = $selectedDate ? new DateTime($selectedDate) : (new DateTime("$selectedMonth-01"))->modify('last day of this month');
      while ($start <= $end):
        $dateStr = $start->format('Y-m-d');
        $formatted = $start->format('d.m');
        $rowClass = isWeekend($dateStr);

        $status = "Absent";
        $in = $out = $pause = $unavailable = $total = "-";

        if (isset($clockMap[$dateStr])) {
            $status = "Clocked In";
            $in = date("H:i", strtotime($clockMap[$dateStr]['clock_in']));
            $clockOutRaw = $clockMap[$dateStr]['clock_out'];
            if (!empty($clockOutRaw) && strtotime($clockOutRaw) > strtotime($clockMap[$dateStr]['clock_in'])) {
                $out = date("H:i", strtotime($clockOutRaw));
                $rawHours = (strtotime($clockOutRaw) - strtotime($clockMap[$dateStr]['clock_in'])) / 3600;
                $pause = ($rawHours >= 6) ? "0.5h" : "0.0h";
                $total = round($rawHours - ($rawHours >= 6 ? 0.5 : 0), 2);
            } elseif ($dateStr < $todayDateOnly) {
                $estimated = new DateTime($clockMap[$dateStr]['clock_in']);
                $estimated->add(new DateInterval("PT8H"));
                $out = $estimated->format("H:i") . " (auto)";
                $pause = "0.5h";
                $total = 7.5;
            }
        } elseif (isset($leaveDays[$dateStr])) {
            $status = "Vacation (" . $leaveDays[$dateStr] . ")";
        } elseif (in_array($formatted, $holidays)) {
            $status = "Holiday";
        } elseif ($start > $today) {
            $status = "Upcoming";
        }
      ?>
        <tr class="<?= $rowClass ?>">
          <td><?= $start->format("d.m.Y") ?></td>
          <td><?= $status ?></td>
          <td><?= $in ?></td>
          <td><?= $out ?></td>
          <td><?= $pause ?></td>
          <td><?= $unavailable ?></td>
          <td><?= $total ?></td>
        </tr>
      <?php $start->modify('+1 day'); endwhile; ?>
    </tbody>
  </table>
</div>
</body>
</html>