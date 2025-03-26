<?php
require __DIR__ . '/../vendor/autoload.php';
// Include Razorpay SDK
include "dbcon.php";
use Razorpay\Api\Api;

session_start();
if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
    exit();
}

// Get user details from database
if (!isset($_GET['id'])) {
    die("Invalid request!");
}

$user_id = $_GET['id'];
$qry = "SELECT * FROM members WHERE user_id='$user_id'";
$result = mysqli_query($con, $qry);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    die("User not found!");
}

$amount = $row['amount'] * 100; // Convert to paise
$fullname = $row['fullname'];
$email = $row['email'];
$contact = $row['phone'];

// Razorpay API Key
$api_key = "rzp_test_HUMgepG80fd2Ck"; // Replace with your Razorpay Key ID
$api_secret = "dyOqWu8cqpAF36W0H62mlVO0"; // Replace with your Razorpay Secret

$api = new Api($api_key, $api_secret);

// Create an order
$order = $api->order->create([
    'receipt' => 'order_rcpt_' . $user_id,
    'amount' => $amount,
    'currency' => 'INR',
    'payment_capture' => 1, // Auto capture
]);

$order_id = $order['id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Make Payment</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body onload="payNow()">

<script>
function payNow() {
    var options = {
        "key": "<?php echo $api_key; ?>", 
        "amount": "<?php echo $amount; ?>", 
        "currency": "INR",
        "name": "FitEdge+ Gym",
        "description": "Membership Payment",
        "image": "https://yourwebsite.com/logo.png", 
        "order_id": "<?php echo $order_id; ?>",
        "handler": function (response){
            window.location.href = "payment-success.php?payment_id=" + response.razorpay_payment_id + "&order_id=" + response.razorpay_order_id;
        },
        "prefill": {
            "name": "<?php echo $fullname; ?>",
            "email": "<?php echo $email; ?>",
            "contact": "<?php echo $contact; ?>"
        },
        "theme": {
            "color": "#528FF0"
        }
    };
    var rzp = new Razorpay(options);
    rzp.open();
}
</script>

</body>
</html>
