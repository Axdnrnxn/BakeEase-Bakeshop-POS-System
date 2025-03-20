<?php
require_once '../../classes/User.php';
include '../../classes/Order.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); 
    exit();
}

$user = new User();
$order = new Order();
$userId = $_SESSION['user_id'];
$userDetails = $user->getUserDetails($userId);
$orders = $order->getOrdersForUser($userId);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = !empty($_POST['password']) ? $_POST['password'] : null;

    if ($user->updateProfile($userId, $name, $email, $password)) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Profile updated successfully!',
                    confirmButtonColor: '#4CAF50'
                }).then(() => {
                    window.location.href = 'profile.php'; // Change this to your redirect page
                });
            });
        </script>";
    } else {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'There was a problem updating your profile. Please try again.',
                    confirmButtonColor: '#d33'
                });
            });
        </script>";
    }
    
}

// Handle success and error messages
if (isset($_GET['success'])) {
    $successMessage = htmlspecialchars($_GET['success']);
} elseif (isset($_GET['error'])) {
    $errorMessage = htmlspecialchars($_GET['error']);
}

$current_page = basename($_SERVER['PHP_SELF']); // Get the current file name


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BakeEase Bakery - Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4CAF50',
                        secondary: '#8BC34A',
                        accent: '#FFC107',
                    },
                    fontFamily: {
                        'bembo': ['Libre Baskerville', 'serif'],
                    },
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-green-50 text-gray-800 font-bembo">

    <header class="bg-primary text-white shadow-md sticky top-0 z-50">
    <div class="container mx-auto px-3 py-3 flex justify-between items-center">
    <div class="logo flex items-center">
                <img src="https://img.icons8.com/color/48/000000/birthday-cake.png" alt="BakeEase Logo" class="w-10 h-10 mr-2">
                <h1 class="text-2xl font-bold">BakeEase Bakery</h1>
            </div>
            <nav>
                <ul class="flex space-x-6">
                    <li>
                        <a href="index.php" 
                            class="hover:text-accent transition-colors <?= ($current_page == 'index.php') ? 'text-red-500 font-bold' : 'text-white'; ?>">
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="products.php" 
                            class="hover:text-accent transition-colors <?= ($current_page == 'products.php') ? 'text-red-500 font-bold' : 'text-white'; ?>">
                            Products
                        </a>
                    </li>
                    <li>
                        <a href="contact.php" 
                            class="hover:text-accent transition-colors <?= ($current_page == 'contact.php') ? 'text-red-500 font-bold' : 'text-white'; ?>">
                            Contact
                        </a>
                    </li>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li>
                            <a href="profile.php" 
                                class="hover:text-accent transition-colors <?= ($current_page == 'profile.php') ? 'text-red-500 font-bold' : 'text-white'; ?>">
                                Profile
                            </a>
                        </li>
                        <li>
                            <a href="../../actions/actions.logout.php" class="text-white hover:text-accent transition-colors">
                                Logout
                            </a>
                        </li>
                    <?php else: ?>
                        <li>
                            <a href="../login.php" 
                                class="hover:text-accent transition-colors <?= ($current_page == 'login.php') ? 'text-red-500 font-bold' : 'text-white'; ?>">
                                Login
                            </a>
                        </li>
                        <li>
                            <a href="register.php" 
                                class="hover:text-accent transition-colors <?= ($current_page == 'register.php') ? 'text-red-500 font-bold' : 'text-white'; ?>">
                                Register
                            </a>
                        </li>
                    <?php endif; ?>

                    <li>
                        <a href="cart.php" class="hover:text-accent transition-colors flex items-center">
                            <img src="https://img.icons8.com/?size=100&id=QVQY51sDgy1I&format=png&color=ffffff" 
                                alt="Cart" class="w-6 h-6">
                            <span class="ml-1 cart-count">
                                (<?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?>)
                            </span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <section class="profile bg-white p-8 rounded-lg shadow-md">
            <h2 class="text-3xl font-bold text-center mb-6">User Profile</h2>

            <?php if (isset($successMessage)): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p><?= $successMessage; ?></p>
                </div>
            <?php elseif (isset($errorMessage)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p><?= $errorMessage; ?></p>
                </div>
            <?php endif; ?>


            <?php if ($userDetails): ?>
                <div class="mb-8">
                    <h3 class="text-xl font-semibold mb-4">Profile Details</h3>
                    <p><strong>Name:</strong> <?= $userDetails['name'] ?></p>
                    <p><strong>Email:</strong> <?= $userDetails['email'] ?></p>
                </div>

                <div class="mb-8">
                    <h3 class="text-xl font-semibold mb-4">Update Your Profile</h3>
                    <form method="post" action="" class="space-y-4">
                        <div>
                            <label for="name" class="block text-lg font-semibold">Name:</label>
                            <input type="text" id="name" name="name" value="<?= $userDetails['name'] ?>" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                        <div>
                            <label for="email" class="block text-lg font-semibold">Email:</label>
                            <input type="email" id="email" name="email" value="<?= $userDetails['email'] ?>" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                        <div>
                            <label for="password" class="block text-lg font-semibold">New Password (optional):</label>
                            <input type="password" id="password" name="password" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                        <button type="submit" name="update_profile" class="w-full bg-primary text-white font-bold py-2 rounded hover:bg-green-600 transition-colors focus:outline-none focus:ring-2 focus:ring-primary">Update Profile</button>
                    </form>
                </div>

                <div class="profile-section mb-8">
                    <h3 class="text-xl font-semibold mb-4">Order History</h3>
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-md">
                        <thead class="bg-gray-50 text-gray-700 font-bold">
                            <tr>
                                <th class="py-3 px-6 text-left">Order ID</th>
                                <th class="py-3 px-6 text-left">Product Names</th>
                                <th class="py-3 px-6 text-left">Total Quantity</th>
                                <th class="py-3 px-6 text-left">Total Price</th>
                                <th class="py-3 px-6 text-left">Payment Method</th>
                                <th class="py-3 px-6 text-left">Address</th>
                                <th class="py-3 px-6 text-left">Status</th>
                                <th class="py-3 px-6 text-left">Order Date</th>
                                <th class="py-3 px-6 text-left">Actions</th>  
                            </tr>
                        </thead>
                        <tbody class="text-gray-700">
                            <?php if (!empty($orders)): ?>
                                <?php foreach ($orders as $order): ?>
                                    <tr id="order-row-<?= $order['id'] ?>">
                                        <td class="py-3 px-6"><?= $order['id'] ?></td>
                                        <td class="py-3 px-6"><?= $order['product_names'] ?></td>
                                        <td class="py-3 px-6"><?= $order['total_quantity'] ?></td>
                                        <td class="py-3 px-6">₱<?= $order['final_total'] ?></td> 
                                        <td class="py-3 px-6"><?= $order['payment_method'] ?></td>
                                        <td class="py-3 px-6"><?= $order['address'] ?></td> 
                                        <td class="py-3 px-6"><?= $order['status'] ?></td>
                                        <td class="py-3 px-6"><?= $order['order_date'] ?></td>
                                        <td class="py-3 px-6"> 
                                            <?php if ($order['status'] == 'pending'): ?> 
                                                <a href="../../actions/cancel_order.php?order_id=<?= $order['id'] ?>" class="text-red-500 hover:underline">Cancel</a>
                                            <?php elseif ($order['status'] == 'cancelled' || $order['status'] == 'delivered'): ?>
                                                <a href="../../actions/admin-order-actions.php?delete_user_order=<?= $order['id'] ?>" 
                                                   class="text-red-500 hover:underline"
                                                   onclick="return confirm('Are you sure you want to delete this order from your history?')">Delete</a>
                                            <?php endif; ?>

                                            <?php if ($order['status'] == 'delivered'): ?>
                                                <a href="../../actions/download_receipt.php?order_id=<?= $order['id'] ?>" 
                                                   class="text-blue-500 hover:underline ml-2">Download Receipt</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="9" class="py-3 px-6 text-center text-gray-600">No orders found.</td></tr> 
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            <?php else: ?>
                <p class="text-center text-red-600">User details not found.</p>
            <?php endif; ?>

        </section>
    </main>
    <script>
        // You can add any additional JavaScript code here if needed.
    </script>
</body>

</html>
