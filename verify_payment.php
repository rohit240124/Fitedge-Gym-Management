<?php
session_start();
require __DIR__ . '/../vendor/autoload.php'; // Adjust path if needed
use Razorpay\Api\Api;

session_start();

$api = new Api('rzp_test_HUMgepG80fd2Ck', 'dyOqWu8cqpAF36W0H62mlVO0');

if (isset($_POST['razorpay_payment_id'])) {
    $payment_id = $_POST['razorpay_payment_id'];

    try {
        // Fetch payment details
        $payment = $api->payment->fetch($payment_id);
        $amount = $payment->amount; // Store the amount in a separate variable

        if ($payment->status == 'captured') {
            // Database connection
            $conn = new mysqli("localhost", "root", "", "gymnsb");

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Insert payment details into the database
            $stmt = $conn->prepare("INSERT INTO payments (member_id, payment_id, amount, status) VALUES (?, ?, ?, 'Success')");
            $stmt->bind_param("isd", $_SESSION['user_id'], $payment_id, $amount);
            $stmt->execute();

            echo "Payment Successful! Membership Activated.";
        } else {
            echo "Payment Failed.";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid Payment Request!";
}
