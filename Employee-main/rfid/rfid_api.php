<?php
// English: Handle RFID clock-in/out
// Russian: Обработка входа/выхода по RFID

header("Content-Type: application/json");

// Validate input
if (!isset($_POST["rfid_id"])) {
    echo json_encode(["success" => false, "error" => "Missing rfid_id"]);
    exit;
}

$rfid_id = trim($_POST["rfid_id"]); // Remove any whitespace or newline

try {
    // Connect to SQLite
    $db = new PDO("sqlite:../db/users.db");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Find employee_id from rfid_id (ensure text match)
    $stmt = $db->prepare("SELECT employee_id FROM users WHERE rfid_id = :rfid_id");
    $stmt->bindValue(":rfid_id", $rfid_id, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        echo json_encode(["success" => false, "error" => "RFID not registered to any user"]);
        exit;
    }

    $employee_id = $result["employee_id"];

    // Now fetch full user info from employee_id
    $stmt = $db->prepare("SELECT * FROM users WHERE employee_id = ?");
    $stmt->execute([$employee_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(["success" => false, "error" => "User not found"]);
        exit;
    }

    $name = $user["name"];
    $timestamp = date("Y-m-d H:i:s");

    // Determine if this is clock-in or clock-out
    $today = date("Y-m-d");
    $stmt = $db->prepare("SELECT * FROM time_log WHERE employee_id = ? AND date(clock_in) = ?");
    $stmt->execute([$employee_id, $today]);
    $log = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$log) {
        // No log today → clock in
        $stmt = $db->prepare("INSERT INTO time_log (employee_id, clock_in) VALUES (?, ?)");
        $stmt->execute([$employee_id, $timestamp]);
        echo json_encode([
            "success" => true,
            "name" => $name,
            "status" => "Clocked In",
            "time" => $timestamp
        ]);
    } else {
        // Clock out if not already done
        if (!$log["clock_out"]) {
            $stmt = $db->prepare("UPDATE time_log SET clock_out = ? WHERE id = ?");
            $stmt->execute([$timestamp, $log["id"]]);
            echo json_encode([
                "success" => true,
                "name" => $name,
                "status" => "Clocked Out",
                "time" => $timestamp
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "error" => "Already clocked out today"
            ]);
        }
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => "DB Error: " . $e->getMessage()]);
}
?>