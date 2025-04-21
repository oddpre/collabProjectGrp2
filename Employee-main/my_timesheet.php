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

$month = isset($_GET['month']) ? (int)$_GET['month'] : date('m');
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

$holidays = ['01.01', '01.05', '17.05', '25.12', '26.12'];

// Fetch clock in/out data
$stmt = $conn->prepare("SELECT date(clock_in) as day, clock_in, clock_out, note FROM timesheet WHERE employee_id = ? AND strftime('%Y', clock_in) = ? AND strftime('%m', clock_in) = ?");
$stmt->execute([$employee_id, $year, str_pad($month, 2, '0', STR_PAD_LEFT)]);
$clockData = $stmt->fetchAll(PDO::FETCH_ASSOC);
$clockMap = [];
foreach ($clockData as $entry) {
    $clockMap[$entry['day']] = $entry;
}

// Fetch leaves
$stmt = $conn->prepare("SELECT from_date, to_date, status FROM leaves WHERE employee_id = ? AND ((strftime('%Y', from_date) = ? AND strftime('%m', from_date) = ?) OR (strftime('%Y', to_date) = ? AND strftime('%m', to_date) = ?))");
$stmt->execute([$employee_id, $year, str_pad($month, 2, '0', STR_PAD_LEFT), $year, str_pad($month, 2, '0', STR_PAD_LEFT)]);
$leaves = $stmt->fetchAll(PDO::FETCH_ASSOC);

$leaveDays = [];
foreach ($leaves as $leave) {
    $start = new DateTime($leave['from_date']);
    $end = new DateTime($leave['to_date']);
    while ($start <= $end) {
        $key = $start->format('Y-m-d');
        $leaveDays[$key] = $leave['status'];
        $start->modify('+1 day');
    }
}

$prevMonth = $month - 1;
$prevYear = $year;
if ($prevMonth < 1) {
    $prevMonth = 12;
    $prevYear--;
}
$nextMonth = $month + 1;
$nextYear = $year;
if ($nextMonth > 12) {
    $nextMonth = 1;
    $nextYear++;
}

$currentMonth = date('m');
$currentYear = date('Y');
$disableNext = ($month == $currentMonth && $year == $currentYear);
$today = new DateTime();
$todayDateOnly = $today->format('Y-m-d');
$todayClockIn = isset($clockMap[$todayDateOnly]) ? $clockMap[$todayDateOnly]['clock_in'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Timesheet</title>
  <link rel="icon" href="/img/nkey.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;600;800&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="./img/favicon.png">
  <link rel="stylesheet" href="./css/style.css">
</head>
<body>
  <div class="container_timesheet">
    <div class="d-flex justify-content-between align-items-center">
      <h2 class="tm-H"> My Timesheet - <?= date('F Y', strtotime("$year-$month-01")) ?></h2>
      <a href="dashboard.php" class="btn_time">⬅ Back to Dashboard</a>
    </div>

    <?php if ($todayClockIn): ?>
      <div class="alert alert-info">
        You checked in today at <strong><?= date("H:i", strtotime($todayClockIn)) ?></strong>.<br>
        <span id="timeRemaining">Time remaining: calculating...</span>
      </div>
    <?php endif; ?>

    <div class="choose_time">
      <label for="viewSelect" class="view_label">Select View:</label>
      <select id="viewSelect" class="form_select  d-inline-block">
        <option value="weekly">Weekly</option>
        <option value="monthly" selected>Monthly</option>
        <option value="yearly">Yearly</option>
      </select>
    </div>

    <div class="d-flex justify-content-between">
      <a href="?month=<?= $prevMonth ?>&year=<?= $prevYear ?>" class="btn_previous">⬅ Previous</a>
      <?php if (!$disableNext): ?>
        <a href="?month=<?= $nextMonth ?>&year=<?= $nextYear ?>" class="btn_next">Next ➡</a>
      <?php endif; ?>
    </div>

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
        <?php for ($day = 1; $day <= $daysInMonth; $day++):
          $dateStr = sprintf("%04d-%02d-%02d", $year, $month, $day);
          $formatted = date("d.m", strtotime($dateStr));
          $weekday = date("w", strtotime($dateStr));
          $isFuture = new DateTime($dateStr) > $today;

          $rowClass = '';
          if ($weekday == 0) $rowClass = 'table-danger';
          elseif ($weekday == 6) $rowClass = 'table-warning';

          $status = "Absent";
          $in = $out = $note = $pause = $unavailable = $total = "-";

          if (isset($clockMap[$dateStr])) {
              $status = "Clocked In";
              $in = date("H:i", strtotime($clockMap[$dateStr]['clock_in']));
              $clockOutRaw = $clockMap[$dateStr]['clock_out'];
              if (!empty($clockOutRaw) && strtotime($clockOutRaw) > strtotime($clockMap[$dateStr]['clock_in'])) {
                  $out = date("H:i", strtotime($clockOutRaw));
                  $rawHours = (strtotime($clockOutRaw) - strtotime($clockMap[$dateStr]['clock_in'])) / 3600;
                  if ($rawHours >= 6) {
                      $pause = "0.5h";
                      $total = round($rawHours - 0.5, 2);
                  } else {
                      $pause = "0.0h";
                      $total = round($rawHours, 2);
                  }
              } elseif ($dateStr < $todayDateOnly) {
                  $estimated = new DateTime($clockMap[$dateStr]['clock_in']);
                  $estimated->add(new DateInterval("PT8H"));
                  $out = $estimated->format("H:i") . " (auto)";
                  $pause = "0.5h";
                  $total = 7.5;
              }
              $unavailable = "-";
          } elseif (isset($leaveDays[$dateStr])) {
              $status = "Vacation ({$leaveDays[$dateStr]})";
          } elseif (in_array($formatted, $holidays)) {
              $status = "Holiday";
          } elseif ($isFuture) {
              $status = "Upcoming";
          }
        ?>
        <tr class="<?= $rowClass ?>">
          <td><?= date("d.m.Y", strtotime($dateStr)) ?></td>
          <td><?= $status ?></td>
          <td><?= $in ?></td>
          <td><?= $out ?></td>
          <td><?= $pause ?></td>
          <td><?= $unavailable ?></td>
          <td><?= $total ?></td>
        </tr>
        <?php endfor; ?>
      </tbody>
    </table>
    <!-- Weekly & Yearly View Placeholder -->
<div id="weeklyView" class="d-none">
  <h4 class="mt-4 text-center">Weekly View (Hours Per Week)</h4>
  <table class="table table-bordered">
    <thead class="table-light">
      <tr>
        <th>Week #</th>
        <th>Start</th>
        <th>End</th>
        <th>Total Hours</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $weekHours = [];
      foreach ($clockData as $row) {
          $date = new DateTime($row['day']);
          $week = (int)$date->format("W");
          $yearKey = $date->format("Y");
          $key = "$yearKey-W$week";
          if (!isset($weekHours[$key])) $weekHours[$key] = ["start" => $date, "end" => $date, "total" => 0];
          else $weekHours[$key]["end"] = $date;

          if ($row["clock_out"]) {
              $start = strtotime($row["clock_in"]);
              $end = strtotime($row["clock_out"]);
              $hours = ($end - $start) / 3600;
              if ($hours >= 6) $hours -= 0.5;
              $weekHours[$key]["total"] += round($hours, 2);
          }
      }

      foreach ($weekHours as $week => $data): ?>
        <tr>
          <td><?= htmlspecialchars($week) ?></td>
          <td><?= $data["start"]->format("d.m.Y") ?></td>
          <td><?= $data["end"]->format("d.m.Y") ?></td>
          <td><?= $data["total"] ?>h</td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<div id="yearlyView" class="d-none">
  <h4 class="mt-4 text-center">Yearly View (Monthly Summary)</h4>
  <table class="table table-bordered">
    <thead class="table-light">
      <tr>
        <th>Month</th>
        <th>Total Hours</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $stmt = $conn->prepare("SELECT strftime('%m', clock_in) AS month, SUM((julianday(COALESCE(clock_out, clock_in)) - julianday(clock_in)) * 24) AS total_hours FROM timesheet WHERE employee_id = ? AND strftime('%Y', clock_in) = ? GROUP BY month");
      $stmt->execute([$employee_id, $year]);
      $monthlyData = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $monthMap = array_column($monthlyData, "total_hours", "month");

      for ($m = 1; $m <= 12; $m++):
        $key = str_pad($m, 2, "0", STR_PAD_LEFT);
        $hours = isset($monthMap[$key]) ? round($monthMap[$key], 2) : 0;
        if ($hours >= 6 * 20) $hours -= (0.5 * 20); // fast 20 dager pause
      ?>
        <tr>
          <td><?= date("F", strtotime("$year-$key-01")) ?></td>
          <td><?= $hours ?>h</td>
        </tr>
      <?php endfor; ?>
    </tbody>
  </table>
</div>

  </div>
  <?php if ($todayClockIn): ?>
  <script>
    const clockInTime = new Date("<?= $todayClockIn ?>");
    const endTime = new Date(clockInTime.getTime() + 8 * 60 * 60 * 1000);
    function updateCountdown() {
      const now = new Date();
      const diff = endTime - now;
      if (diff <= 0) {
        document.getElementById("timeRemaining").innerText = "Workday complete!";
        return;
      }
      const hours = Math.floor(diff / (1000 * 60 * 60));
      const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
      document.getElementById("timeRemaining").innerText = `Time remaining: ${hours}h ${minutes}m`;
    }
    updateCountdown();
    setInterval(updateCountdown, 60000);
  </script>
  <?php endif; ?>
  <script>
document.getElementById("viewSelect").addEventListener("change", function () {
  const view = this.value;
  const monthTable = document.querySelector("table");
  const weeklyView = document.getElementById("weeklyView");
  const yearlyView = document.getElementById("yearlyView");
  weeklyView.classList.add("d-none");
  monthTable.classList.add("d-none");
 
  yearlyView.classList.add("d-none");

  if (view === "weekly") {
    weeklyView.classList.remove("d-none");
  } else if (view === "monthly") {
    monthTable.classList.remove("d-none");
  } else if (view === "yearly") {
    yearlyView.classList.remove("d-none");
  }
});
</script>


</body>
</html>