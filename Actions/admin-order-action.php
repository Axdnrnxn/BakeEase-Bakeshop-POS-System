<?php
include "../classes/AdminOrder.php"; 
include "../classes/Order.php";

session_start(); 

$adminOrder = new AdminOrder();
$order = new Order(); 

// --- STRICT ADMIN LOGIN CHECK ---
if (!isset($_SESSION['user_id']) || $_SESSION['isAdmin'] != 0) {
    header("Location: ../login.php?error=" . urlencode("You are not authorized to access the admin dashboard.")); 
    exit();
}
// --- END ADMIN LOGIN CHECK ---

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_order_status'])) {
    $orderId = $_POST['order_id'];
    $newStatus = $_POST['new_status']; 

    // Use the updateOrderStatus() method from the Order class
    if ($order->updateOrderStatus($orderId, $newStatus)) { 

        // --- BEGIN EMAIL NOTIFICATION --- 

        // 1. Get customer email (make sure column name is correct)
        $orderDetails = $adminOrder->getOrder($orderId);
        $customerEmail = $orderDetails['customer_email']; // Get the actual email from the order details

        // 2. Construct email content
        $subject = "Your BakeEase Bakery Order Status Update";
        $message = "Hello,\n\nYour order (ID: $orderId) status has been updated to: $newStatus.\n\n";
        $message .= "Thank you for your order!\n\nBakeEase Bakery";
        
        // 3. Set email headers
        $headers = "From: youractualemail@yourdomain.com" . "\r\n" . // Replace with your ACTUAL email address
                   "Reply-To: youractualemail@yourdomain.com" . "\r\n" . // Replace with your ACTUAL reply-to address
                   "Content-Type: text/plain; charset=UTF-8";  

        // 4. Send the email (and log errors)
        if (mail($customerEmail, $subject, $message, $headers)) {
            error_log("Email sent successfully to: " . $customerEmail); 
        } else {
            error_log("Email Error: " . error_get_last()['message']); 
        }

        // --- END EMAIL NOTIFICATION ---

        // Redirect parent page with success message
        echo "<script>parent.window.location.href = '../views/admin/manage_orders.php?success=" . urlencode("Order status updated successfully.") . "';</script>";
        exit;
    } else { 
        // Redirect parent page with error message
        echo "<script>parent.window.location.href = '../views/admin/manage_orders.php?error=" . urlencode("Error updating order status.") . "';</script>";
        exit;
    }

} elseif ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['archive_order'])) {
    $orderIdToArchive = $_GET['archive_order'];

    if ($adminOrder->archiveOrder($orderIdToArchive)) {
        // Redirect parent page with success message
        echo "<script>parent.window.location.href = '../views/admin/manage_orders.php?success=" . urlencode("Order archived successfully.") . "';</script>";
        exit; 
    } else {
        // Redirect parent page with error message
        echo "<script>parent.window.location.href = '../views/admin/manage_orders.php?error=" . urlencode("Error archiving order.") . "';</script>";
        exit;
    }
} else {
    // Handle any other unauthorized access attempts
    echo "Unauthorized access."; 
    exit();
}
?>
