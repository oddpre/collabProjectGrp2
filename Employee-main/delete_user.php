<?php


session_start();


if (!isset($_SESSION["employee_id"]) || $_SESSION["role"] != 1) {

    header("Location: login.php");
    exit;
}

require_once "includes/config.php";

if (!isset($_GET['id'])) {
    header("Location: users.php");
    exit;
}

$employee_id = $_GET['id'];

if ($employee_id == $_SESSION['employee_id']) {
    header("Location: users.php?error=self-delete");
    exit;
}

$stmt = $conn->prepare("DELETE FROM users WHERE employee_id = :id");
$stmt->bindParam(':id', $employee_id, PDO::PARAM_INT);
$stmt->execute();

header("Location: users.php?success=deleted");
exit;
