<?php
require '../config.php'; // Ensure database connection
require 'MpesaAPI.php'; // Include MPESA Daraja API class
include_once "../functions-tena.php";

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$loanId = $input['loan_id'];
$phoneNumber = $input['phone_number'];

// Retrieve the payment amount for the loan from the database
$amount = getNextPaymentAmount($loanId);

// Convert the amount to an integer
$amount = intval($amount);

// Log the converted amount for debugging
error_log("Converted Amount: " . $amount);

if (!$amount || !$phoneNumber) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data provided']);
    exit;
}

// Initialize MPESA API and send STK Push
$mpesa = new MpesaAPI();
$response = $mpesa->stkPush($phoneNumber, $amount, $loanId);

// Log the raw response from the STK Push
error_log("STK Push Response: " . json_encode($response));

// Check for response and return the appropriate response to the frontend
if (isset($response['ResponseCode']) && $response['ResponseCode'] === '0') {
    echo json_encode(['status' => 'success', 'message' => 'STK Push initiated']);
} else {
    $errorMessage = isset($response['ResponseDescription']) ? $response['ResponseDescription'] : 'Failed to initiate STK Push';
    echo json_encode(['status' => 'error', 'message' => $errorMessage]);
}
?>
