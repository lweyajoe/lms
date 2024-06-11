<?php
// Include database connection file
require_once("config.php");

// Define an associative array to hold the response data
$response = array();

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST["name"];
    $email = $_POST["email"];
    $mobile = $_POST["mobile"];
    $subject = $_POST["subject"];
    $message = $_POST["message"];

    // Prepare and bind SQL statement
    $stmt = $conn->prepare("INSERT INTO call_back_contacts (name, email, mobile, subject, message) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $mobile, $subject, $message);

    // Execute SQL statement
    if ($stmt->execute() === TRUE) {
        // Set success message in the response array
        $response['success'] = true;
    } else {
        // Set error message in the response array
        $response['success'] = false;
        $response['error'] = "Error: " . $stmt->error;
    }

    // Close statement
    $stmt->close();

    // Close database connection
    $conn->close();

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit(); // Terminate script execution after sending response
}
?>
