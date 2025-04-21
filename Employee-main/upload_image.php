<?php
// Enable error reporting for debugging purposes
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start the session to manage user authentication
session_start();

// Redirect to login page if the user is not logged in
if (!isset($_SESSION["employee_id"])) {
    header("Location: login.php");
    exit;
}

// Include the database configuration file
require_once "includes/config.php";

// Get the logged-in user's employee ID
$employee_id = $_SESSION["employee_id"];

// Define the upload directory and file paths
$uploadDir = "uploads/"; // Directory where images will be stored
$uploadFile = $uploadDir . $employee_id . ".jpg"; // Full path for the uploaded file
$dbPath = "./uploads/" . $employee_id . ".jpg"; // Path to store in the database

// Check if a file is uploaded and there are no errors
if (isset($_FILES["profile_image"]) && $_FILES["profile_image"]["error"] == 0) {
    // Get the MIME type of the uploaded file
    $fileType = mime_content_type($_FILES["profile_image"]["tmp_name"]);

    // Validate the file type (only allow JPG and PNG)
    if (in_array($fileType, ["image/jpeg", "image/png"])) {
        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $uploadFile)) {
            // Update the user's profile image path in the database
            $stmt = $conn->prepare("UPDATE users SET profile_image = ? WHERE employee_id = ?");
            $stmt->execute([$dbPath, $employee_id]);

            // Redirect to the user's profile page after successful upload
            header("Location: my_page.php");
            exit;
        } else {
            // Display an error message if the file could not be moved
            echo "❌ Failed to move uploaded file.";
        }
    } else {
        // Display an error message if the file type is invalid
        echo "❌ Invalid image type. Only JPG or PNG allowed.";
    }
} else {
    // Display an error message if no file is uploaded or an error occurred
    echo "❌ No file uploaded or error occurred.";
}
?>
