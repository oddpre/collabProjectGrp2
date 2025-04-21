<?php

ini_set("display_errors", 1);
error_reporting(E_ALL);


session_start();

require_once "includes/config.php";

$emp_id = $_POST["emp_id"] ?? null;

if (!$emp_id) {

    header("Location: index.html");
    exit;
}

date_default_timezone_set("Europe/Oslo");
$now = new DateTime();
$currentDate = $now->format("Y-m-d");
$currentTime = $now->format("H:i:s");

$stmt = $conn->prepare("SELECT clock_in FROM timesheet WHERE employee_id = ? AND DATE(clock_in) = ? AND clock_out IS NULL");
$stmt->execute([$emp_id, $currentDate]);
$entry = $stmt->fetch(PDO::FETCH_ASSOC);

if ($entry) {

    $clockIn = new DateTime($entry["clock_in"]);
    $defaultOut = $clockIn->add(new DateInterval("PT8H"))->format("Y-m-d H:i:s");

    $stmt = $conn->prepare("UPDATE timesheet SET clock_out = ? WHERE employee_id = ? AND DATE(clock_in) = ? AND clock_out IS NULL");
    $stmt->execute([$defaultOut, $emp_id, $currentDate]);
} else {

    $stmt = $conn->prepare("INSERT INTO timesheet (employee_id, clock_in) VALUES (?, ?)");
    $stmt->execute([$emp_id, $now->format("Y-m-d H:i:s")]);
}

header("Location: index.html");
exit;
?>
