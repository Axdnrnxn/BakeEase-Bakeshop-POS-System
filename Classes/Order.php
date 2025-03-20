<?php
require_once "Database.php";
require_once "User.php"; 

class Order extends Database {

    public function createOrder($userId, $customerEmail, $customerName, $cartItems, $finalTotal, $paymentMethod, $address, $orderType, $pickupTime = null) {
        $userId = isset($userId) ? $this->conn->real_escape_string($userId) : null; 
        $finalTotal = 0; // Initialize the total
    
        if (!isset($_SESSION['user_id']) && $userId === null) { 
            $this->conn->query("INSERT INTO guest_orders (customer_name, customer_email) 
            VALUES ('$customerName', '$customerEmail')");
            $userId = $this->conn->insert_id;
        }
        elseif (isset($_SESSION['user_id']) && $userId !== null) {
            $userId = $this->conn->real_escape_string($userId);
        } else {
            return "Error: invalid request";
        }
    
        $customerEmail = $this->conn->real_escape_string($customerEmail);
        $paymentMethod = $this->conn->real_escape_string($paymentMethod);
        $orderType = $this->conn->real_escape_string($orderType);
        $address = ($orderType === 'delivery' && !empty($address)) ? $this->conn->real_escape_string($address) : 'N/A'; 
        $deliveryAddress = ($orderType === 'delivery' && !empty($address)) ? $this->conn->real_escape_string($address) : null;
        $pickupTime = ($orderType === 'pickup') ? $this->conn->real_escape_string($pickupTime) : 'N/A';
    
        foreach ($cartItems as $item) {
            $productId = $this->conn->real_escape_string($item['product_id']);
            $quantity = $this->conn->real_escape_string($item['quantity']);
    
            $sql = "SELECT price, quantity FROM products WHERE id = '$productId'";
            $result = $this->conn->query($sql);
            $product = $result->fetch_assoc();
    
            if (!$product || $product['quantity'] < $quantity) {
                return "Error: Not enough of product ID $productId in stock.";
            }
    
            // Calculate the final total
            $finalTotal += $product['price'] * $quantity;
        }
    
        // Now insert the order with the calculated final total, including customer_name
        $sql = "INSERT INTO orders (user_id, customer_email, customer_name, final_total, payment_method, address, order_type, pickup_time, delivery_address) 
                VALUES ('$userId', '$customerEmail', '$customerName', '$finalTotal', '$paymentMethod', '$address', '$orderType', '$pickupTime', '$deliveryAddress')";
    
        if ($this->conn->query($sql) === TRUE) {
            $orderId = $this->conn->insert_id;
    
            foreach ($cartItems as $item) {
                $productId = $this->conn->real_escape_string($item['product_id']);
                $quantity = $this->conn->real_escape_string($item['quantity']);
    
                $sql = "INSERT INTO order_items (order_id, product_id, quantity) VALUES ('$orderId', '$productId', '$quantity')";
                if (!$this->conn->query($sql)) {
                    error_log("Error inserting order item: " . $this->conn->error);
                    return false; 
                }
    
                $sql = "UPDATE products SET quantity = quantity - '$quantity' WHERE id = '$productId'";
                $this->conn->query($sql);
            }
    
            unset($_SESSION['cart']);
            return $orderId; 
    
        } else {
            error_log("Database Error: " . $this->conn->error);
            return false; 
        }
    }

    public function executeQuery($sql) {
        return $this->conn->query($sql);
    }

    public function getOrders() {
        $sql = "SELECT o.*, u.name AS customer_name, p.name AS product_name
                FROM orders o
                INNER JOIN users u ON o.user_id = u.id
                INNER JOIN order_items oi ON o.id = oi.order_id
                INNER JOIN products p ON oi.product_id = p.id
                ORDER BY o.id DESC";
        $result = $this->conn->query($sql);
        return ($result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getOrdersForUser ($userId) {
        $userId = $this->conn->real_escape_string($userId);
  
        $sql = "SELECT o.id, o.final_total, o.payment_method, 
                     CASE 
                         WHEN o.order_type = 'delivery' THEN o.delivery_address
                         ELSE o.address 
                     END AS address, 
                     o.status, o.order_date,
                     GROUP_CONCAT(p.name SEPARATOR ', ') AS product_names,
                     SUM(oi.quantity) AS total_quantity
              FROM orders o
              JOIN order_items oi ON o.id = oi.order_id
              JOIN products p ON oi.product_id = p.id
              WHERE o.user_id = '$userId'
              GROUP BY o.id
              ORDER BY o.order_date DESC"; 
  
        $result = $this->conn->query($sql);
        return ($result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getOrder($orderId) {
        $orderId = $this->conn->real_escape_string($orderId);

        $sql = "SELECT o.*, 
                   GROUP_CONCAT(p.name SEPARATOR ', ') AS product_names, 
                   SUM(oi.quantity) AS total_quantity 
            FROM orders o 
            INNER JOIN order_items oi ON o.id = oi.order_id
            INNER JOIN products p ON oi.product_id = p.id
            WHERE o.id = '$orderId' 
            GROUP BY o.id";

        $result = $this->conn->query($sql);
        return ($result->num_rows == 1) ? $result->fetch_assoc() : null;
    }

    public function updateOrderStatus($orderId, $newStatus) {
        $orderId = $this->conn->real_escape_string($orderId);
        $newStatus = $this->conn->real_escape_string($newStatus);

        // Allowed statuses (Pickup & Delivery)
        $validStatuses = [
            'pending', 'processing', 'ready_for_pickup','ready_for_delivery' , 'out_for_delivery', 'delivered', 'cancelled'
        ];

        if (!in_array($newStatus, $validStatuses)) {
            return "Invalid status.";
        }

        $sql = "UPDATE orders SET status = '$newStatus' WHERE id = '$orderId'";
        
        if ($this->conn->query($sql)) {
            return "Order status updated successfully.";
        } else {
            return "Error updating order status: " . $this->conn->error;
        }
    }

    public function deleteOrder($orderId) {
        $orderId = $this->conn->real_escape_string($orderId);
        $sql = "DELETE FROM orders WHERE id = '$orderId'";
        return $this->conn->query($sql);
    }

    public function generateReceipt($orderId) {
        $orderId = $this->conn->real_escape_string($orderId);
        
        $orderDetails = $this->getOrder($orderId);
        
        $receiptContent = "Order ID: {$orderDetails['id']}\n";
        $receiptContent .= "Customer Name: {$orderDetails['customer_name']}\n";
        $receiptContent .= "Products:\n";
        $receiptContent .= $orderDetails['product_names'] . "\n"; 

        return $receiptContent;
    }
}
?>
