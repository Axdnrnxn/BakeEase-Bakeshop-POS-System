<?php
require_once(__DIR__ . "/../Classes/database.php"); // Correct path to database class

// Start output buffering to use JavaScript inside PHP
ob_start();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = new Database();

    // Escape user input to prevent SQL injection
    $name = $db->escapeString($_POST['name'] ?? '');
    $email = $db->escapeString($_POST['email'] ?? '');
    $message = $db->escapeString($_POST['message'] ?? '');

    // Validate input fields
    if (empty($name) || empty($email) || empty($message)) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Please fill in all fields!',
                    confirmButtonColor: '#d33'
                }).then(() => {
                    window.location.href = '../views/user/contact.php';
                });
            });
        </script>";
        exit();
    }

    // Insert contact message into database
    $sql = "INSERT INTO contact_messages (name, email, message) VALUES ('$name', '$email', '$message')";
    
    if ($db->conn->query($sql)) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Message sent successfully!',
                    confirmButtonColor: '#4CAF50'
                }).then(() => {
                    window.location.href = '../views/user/contact.php';
                });
            });
        </script>";
    } else {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'There was a problem sending your message. Please try again.',
                    confirmButtonColor: '#d33'
                }).then(() => {
                    window.location.href = '../views/user/contact.php';
                });
            });
        </script>";
    }
}

// Output JavaScript
echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
ob_end_flush();
?>
