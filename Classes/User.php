<?php
require_once "Database.php";

class User extends Database {
    public function register($name, $email, $password) {
        $name = $this->conn->real_escape_string($name);
        $email = $this->conn->real_escape_string($email);
    
        // Check if the email is already registered
        $checkEmailSql = "SELECT * FROM users WHERE email = '$email'";
        $checkEmailResult = $this->conn->query($checkEmailSql);
    
        if ($checkEmailResult->num_rows > 0) {
            return "Error: This email address is already registered.";
        }
    
        // Hash the password
        $password = password_hash($password, PASSWORD_DEFAULT);
        
        // Set the default role to 2 (Customer)
        $isAdmin = 2; // 2 for Customer
    
        // Insert the new user into the database
        $sql = "INSERT INTO users (name, email, password, isAdmin) VALUES ('$name', '$email', '$password', '$isAdmin')";
    
        if ($this->conn->query($sql)) {
            return "Registration successful!";
        } else {
            return "Error: " . $this->conn->error;
        }
    }

    public function login($email, $password) {
        $email = $this->conn->real_escape_string($email);
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = $this->conn->query($sql);
    
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name']; 
                $_SESSION['user_email'] = $user['email']; 
                $_SESSION['isAdmin'] = $user['isAdmin']; // 0 for admin, 1 for staff, 2 for buyers
    
                // Redirect based on role
                if ($user['isAdmin'] == 0) {
                    header("Location: http://localhost/bakery/views/admin/admin_dashboard.php"); // Redirect to admin dashboard
                } elseif ($user['isAdmin'] == 1) {
                    header("Location: http://localhost/bakery/views/staff/staff_dashboard.php"); // Redirect to staff dashboard
                } elseif ($user['isAdmin'] == 2) {
                    header("Location: http://localhost/bakery/views/user/index.php"); // Redirect to buyer dashboard
                }
                exit;
            } else {
                return "Invalid password.";
            }
        } else {
            return "User  not found.";
        }
    }

    public function getUserDetails($userId) {
        $userId = $this->conn->real_escape_string($userId);
        $sql = "SELECT * FROM users WHERE id = '$userId'";
        $result = $this->conn->query($sql);
        return ($result->num_rows == 1) ? $result->fetch_assoc() : null;
    }

    public function updateProfile($userId, $name, $email, $password = null) {
        $userId = $this->conn->real_escape_string($userId);
        $name = $this->conn->real_escape_string($name);
        $email = $this->conn->real_escape_string($email);

        $updateFields = "name = '$name', email = '$email'";

        if (!is_null($password) && !empty($password)) {
            $password = password_hash($password, PASSWORD_DEFAULT);
            $updateFields .= ", password = '$password'";
        }

        $sql = "UPDATE users SET $updateFields WHERE id = '$userId'";
        return $this->conn->query($sql);
    }

    public function executeQuery($sql) {
        return $this->conn->query($sql);
    }
}
?>
