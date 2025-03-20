<?php
include "../classes/AdminUser.php"; 

session_start(); 
// --- STRICT ADMIN LOGIN CHECK ---
if (!isset($_SESSION['user_id']) || $_SESSION['isAdmin'] != 0) {
    header("Location: ../login.php?error=" . urlencode("You are not authorized to access the admin dashboard.")); 
    exit();
}
// --- END ADMIN LOGIN CHECK ---

$adminUser  = new AdminUser ();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_user'])) { // Check for POST submission 
    $userIdToDelete = $_POST['delete_user'];  

    $deleteResult = $adminUser ->deleteUser ($userIdToDelete);

    if ($deleteResult === true) {
        // Redirect parent page with success message
        echo "<script>parent.window.location.href = '../views/admin/manage_users.php?success=" . urlencode("User  deleted successfully.") . "';</script>";
        exit; 
    } else {
        // Redirect parent page with error message
        $errorMessage = is_string($deleteResult) ? $deleteResult : "Error deleting user.";
        echo "<script>parent.window.location.href = '../views/admin/manage_users.php?error=" . urlencode($errorMessage) . "';</script>";
        exit; 
    }
} 
elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_user'])) { 
    $userId = $_POST['user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = !empty($_POST['password']) ? $_POST['password'] : null;
    $role = $_POST['role'];

    if ($adminUser ->updateUser ($userId, $name, $email, $password, $role)) {
        header("Location: ../views/admin/manage_users.php?success=User  updated successfully.");
        exit;
    } else {
        $error_message = "Error updating user.";
        header("Location: ../views/admin/edit_user.php?id=$userId&error=" . urlencode($error_message));
        exit;
    }
}
?>
