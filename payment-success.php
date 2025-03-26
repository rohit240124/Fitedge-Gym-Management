<?php
session_start();
include "dbcon.php"; // Database connection file
require __DIR__ . '/../../vendor/autoload.php';
use Razorpay\Api\Api;

$api_key = "rzp_test_HUMgepG80fd2Ck";  // Replace with your Razorpay API Key
$api_secret = "dyOqWu8cqpAF36W0H62mlVO0"; // Replace with your Razorpay API Secret

$api = new Api($api_key, $api_secret);

// Check if Razorpay payment ID is set
if (!isset($_POST['razorpay_payment_id'])) {
    die("Payment Successful! ");
}

$payment_id = $_POST['razorpay_payment_id'];
$member_id = $_SESSION['user_id']; // Assuming you store user ID in session

try {
    // Fetch the payment details from Razorpay
    $payment = $api->payment->fetch($payment_id);

    if ($payment->status == "captured") {
        // Payment successful, update the database
        $amount = $payment->amount / 100; // Convert paisa to INR
        $payment_date = date("Y-m-d H:i:s");

        // Update the payment details in the database
        $query = "UPDATE members SET paid_date='$payment_date', amount='$amount' WHERE user_id='$member_id'";
        $result = mysqli_query($con, $query);

        if ($result) {
            // Display success message
            echo "<h2>Payment Successful!</h2>";
            echo "<p>Payment ID: $payment_id</p>";
            echo "<p>Amount Paid: ₹$amount</p>";
            echo "<a href='dashboard.php'>Go to Dashboard</a>";
        } else {
            echo "Database update failed. Please contact support.";
        }
    } else {
        echo "Payment failed. Please try again.";
    }
} catch (Exception $e) {
    echo "Payment verification failed: " . $e->getMessage();
}
?>
