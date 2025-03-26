<?php
require '../includes/stripe/init.php';


\Stripe\Stripe::setApiKey('sk_test_51Q2yxVP2qoWCB5t6GkHBwSVbW6nrUJ7oIdAtYZAw3f3XSDEGthx1c6GzK5mdmAjYpvq1xsYVw1hIoJOOT7P9wWIY00Yxsrf1sE'); // Replace with your actual secret key

try {
    $balance = \Stripe\Balance::retrieve();
    echo "Stripe is working! Available balance: " . json_encode($balance);
} catch (\Exception $e) {
    echo "Stripe error: " . $e->getMessage();
}
?>
