<?php
require '../vendor/autoload.php'; // Load Stripe SDK
require '../dbcon.php'; // Database connection (Modify as needed)

\Stripe\Stripe::setApiKey('sk_test_51Q2yxVP2qoWCB5t6GkHBwSVbW6nrUJ7oIdAtYZAw3f3XSDEGthx1c6GzK5mdmAjYpvq1xsYVw1hIoJOOT7P9wWIY00Yxsrf1sE'); // Replace with your Secret Key

header('Content-Type: application/json');

$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!isset($data["payment_method_id"])) {
    echo json_encode(["error" => "Payment method ID is missing."]);
    exit();
}

try {
    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => 5000, // Amount in cents ($50.00)
        'currency' => 'usd',
        'payment_method' => $data["payment_method_id"],
        'confirm' => true,
    ]);

    // Store payment in database
    $paymentId = $paymentIntent->id;
    $amount = $paymentIntent->amount / 100;
    $status = $paymentIntent->status;
    $customerEmail = "customer@example.com"; // Modify to fetch real email

    mysqli_query($conn, "INSERT INTO payments (payment_id, amount, status, email) VALUES ('$paymentId', '$amount', '$status', '$customerEmail')");

    echo json_encode(["success" => true, "paymentIntent" => $paymentIntent]);
} catch (\Stripe\Exception\ApiErrorException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
