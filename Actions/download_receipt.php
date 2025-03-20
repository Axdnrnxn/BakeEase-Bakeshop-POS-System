<?php
require_once "../classes/Order.php";

session_start();

if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
    header("Location: ../views/user/profile.php?error=" . urlencode("Invalid request."));
    exit();
}

$userId = $_SESSION['user_id'];
$orderId = $_GET['order_id'];

$order = new Order();

// Fetch order details and check if the user owns the order
$orderDetails = $order->getOrder($orderId);
if (!$orderDetails || $orderDetails['user_id'] != $userId) {
    header("Location: ../views/user/profile.php?error=" . urlencode("You do not have permission to download this receipt."));
    exit();
}

// Generate the receipt content
$receiptContent = $order->generateReceipt($orderId);

// Set headers for download
header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename="receipt_' . $orderId . '.txt"'); 

// Output the receipt content
echo $receiptContent;
exit();
?>
