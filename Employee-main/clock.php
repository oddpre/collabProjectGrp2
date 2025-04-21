<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Clock In/Out | WORK EASE</title>
  <link rel="icon" href="/img/nkey.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;600;800&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="../img/favicon.png">
  <link rel="stylesheet" href="./css/style.css">
</head>
<body class="bg-light">
<div class="container_clock">
  <div class="mx-auto">
    <div class="card-header text-center">
      <h5 class="mb-0">⏱ Clock In / Out</h5>
    </div>
    <div class="card-body">
      <form id="clockForm">
        <div class="mb-3">
          <label for="emp_id" class="form-label"></label>
          <input type="number" class="form-control" id="emp_id" name="emp_id" required min="1" placeholder="Enter your Employee ID">
        </div>
        <div class="d-flex justify-content-between">
          <button type="submit" class="btn-submit">Submit</button>
          <button type="button" class="btn-secondary" onclick="window.location.href='index.html'">Cancel</button>
        </div>
        <div id="errorMsg" class="mt-3 text-danger d-none">Invalid ID. Please try again.</div>
      </form>

      <form id="actionForm" action="clock_action.php" method="POST" class="mt-4 d-none">
        <input type="hidden" name="emp_id" id="hiddenEmpId">
        <p id="actionText"></p>
        <div class="d-flex justify-content-between">
          <button type="submit" class="btn_confirm">Confirm</button>
          <button type="button" id="btn_cansel" class="btn btn-secondary">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>

window.onload = () => {
  document.getElementById("errorMsg").classList.add("d-none");
};

function getFormattedNow() {
  const now = new Date();
  const dd = String(now.getDate()).padStart(2, '0');
  const mm = String(now.getMonth() + 1).padStart(2, '0');
  const yyyy = now.getFullYear();
  const hh = String(now.getHours()).padStart(2, '0');
  const min = String(now.getMinutes()).padStart(2, '0');
  return `${dd}.${mm}.${yyyy} ${hh}:${min}`;
}

async function fetchUserName(empId) {
  try {
    const response = await fetch(`get_name.php?id=${empId}`);
    const data = await response.json();
    return data.success ? data.name : null;
  } catch {
    return null;
  }
}

document.getElementById("clockForm").addEventListener("submit", async function(e) {
  e.preventDefault();
  const empId = document.getElementById("emp_id").value;
  const name = await fetchUserName(empId);
  const nowFormatted = getFormattedNow();

  if (!name) {
    document.getElementById("errorMsg").classList.remove("d-none");
    document.getElementById("emp_id").value = "";
    document.getElementById("emp_id").focus();
    return;
  }

  const isClockedIn = Math.random() < 0.5; // Placeholder logic

  document.getElementById("clockForm").classList.add("d-none");
  document.getElementById("actionForm").classList.remove("d-none");
  document.getElementById("hiddenEmpId").value = empId;
  document.getElementById("actionText").innerText = isClockedIn ?
    `${name} is currently clocked in as of ${nowFormatted}. Do you want to clock out?` :
    `${name} is not clocked in. Clock in now at ${nowFormatted}?`;
});
document.getElementById("btn_cansel").onclick = function() {
  window.location.href = "index.html";
};
</script>
</body>
</html>