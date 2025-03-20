<?php
require_once "Database.php";

class AdminOrder extends Database {
    public function getOrders() {
        $sql = "SELECT o.*, 
                       u.name AS customer_name, 
                       GROUP_CONCAT(p.name SEPARATOR ', ') AS product_names, 
                       SUM(oi.quantity) AS total_quantity,
                       COALESCE(SUM(oi.quantity * p.price), 0) AS total_price,
                       CASE 
                           WHEN o.order_type = 'delivery' THEN SUBSTRING_INDEX(o.delivery_address, '\n', 1)
                           ELSE CONCAT('Pickup - ', o.pickup_time)
                       END AS delivery_info
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id  
                LEFT JOIN order_items oi ON o.id = oi.order_id  
                LEFT JOIN products p ON oi.product_id = p.id  
                GROUP BY o.id 
                ORDER BY o.id DESC";
        $result = $this->conn->query($sql);
        return ($result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getOrder($orderId) {
        $orderId = $this->conn->real_escape_string($orderId);
        $sql = "SELECT o.*, 
                       u.name AS customer_name, 
                       u.email AS customer_email, 
                       GROUP_CONCAT(p.name SEPARATOR ', ') AS product_names, 
                       SUM(oi.quantity) AS total_quantity,
                       COALESCE(SUM(oi.quantity * p.price), 0) AS total_price
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id  
                LEFT JOIN order_items oi ON o.id = oi.order_id  
                LEFT JOIN products p ON oi.product_id = p.id  
                WHERE o.id = '$orderId'
                GROUP BY o.id";

        $result = $this->conn->query($sql);
        return ($result->num_rows > 0) ? $result->fetch_assoc() : null; // Return the order details or null if not found
    }
    public function updateOrderStatus($orderId, $newStatus) {
        $orderId = $this->conn->real_escape_string($orderId);
        $newStatus = $this->conn->real_escape_string($newStatus);

        // Allowed statuses (must match the database enum values)
        $validStatuses = [
            'pending', 'processing', 'Ready for pickup', 'Ready for delivery', 'Out for Delivery', 'Delivered', 'Cancelled'
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

    public function getDeliveryOrders() {
        $sql = "SELECT o.*, 
                   u.name AS customer_name, 
                   GROUP_CONCAT(p.name SEPARATOR ', ') AS product_names, 
                   SUM(oi.quantity) AS total_quantity,
                   COALESCE(SUM(oi.quantity * p.price), 0) AS total_price
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id  
            LEFT JOIN order_items oi ON o.id = oi.order_id  
            LEFT JOIN products p ON oi.product_id = p.id  
            WHERE o.order_type = 'delivery'
            GROUP BY o.id 
            ORDER BY o.id DESC";
        $result = $this->conn->query($sql);
        return ($result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function deleteOrder($orderId) {
        $orderId = $this->conn->real_escape_string($orderId);
        
        // First, delete related order items
        $deleteItemsSql = "DELETE FROM order_items WHERE order_id = '$orderId'";
        $this->conn->query($deleteItemsSql); // Execute the delete for order items
    
        // Now delete the order
        $sql = "DELETE FROM orders WHERE id = '$orderId'";
        
        if ($this->conn->query($sql)) {
            return true; // Return true on success
        } else {
            return false; // Return false on failure
        }
    }

    public function archiveOrder($orderId) {
        // Fetch the order details from the active orders table
        $order = $this->getOrder($orderId); // Assuming you have a method to get order details
    
        // Insert the order into the archived_orders table
        $stmt = $this->conn->prepare("INSERT INTO archived_orders (id, customer_name, order_date, status, total_price, product_names) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issdss", $order['id'], $order['customer_name'], $order['order_date'], $order['status'], $order['total_price'], $order['product_names']);
        
        if ($stmt->execute()) {
            // Now delete the order from the active orders table
            $this->deleteOrder($orderId);
            return true; // Return true on success
        } else {
            return false; // Return false on failure
        }
    }
}
?>
