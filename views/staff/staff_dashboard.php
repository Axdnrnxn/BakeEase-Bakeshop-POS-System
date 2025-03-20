<?php
include '../../classes/AdminUser.php';
include '../../classes/Product.php';
include '../../classes/Order.php';
include_once '../../classes/AdminProduct.php'; // Include AdminProduct class
include '../../classes/AdminOrder.php'; // Include AdminOrder class

session_start();

// --- STRICT STAFF LOGIN CHECK ---
if (!isset($_SESSION['user_id']) || !isset($_SESSION['isAdmin']) || ($_SESSION['isAdmin'] != 0 && $_SESSION['isAdmin'] != 1)) {
    header("Location: ../login.php?error=" . urlencode("You are not authorized to access the staff dashboard.")); // Redirect with error message
    exit();
}
// --- END STAFF LOGIN CHECK ---

$adminUser     = new AdminUser (); 
$product = new Product(); 
$order = new AdminOrder(); // Use AdminOrder for order management
$adminProduct = new AdminProduct(); // Instantiate AdminProduct

// Fetch data for the dashboard
$totalOrders = count($order->getOrders()); 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BakeEase Bakery - Staff Dashboard</title>
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
    <style>
        html, body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column; /* Make body a flex container */
        }

        main {
            flex: 1; /* Makes the main content take up the remaining space */
        }

        footer {
            flex-shrink: 0; /* Prevents the footer from shrinking */
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-100 text-gray-800 font-bembo">

    <!-- Header Section -->
    <header class="bg-purple-400 text-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-3 py-3 flex justify-between items-center">
            <div class="logo flex items-center">
                <img src="https://img.icons8.com/doodle/48/000000/bread.png" alt="BakeEase Logo" class="w-10 h-10 mr-2">
                <h1 class="text-2xl font-bold">Staff Dashboard</h1>
            </div>
            <nav>
                <ul class="flex space-x-6">
                    <li><a href="../../actions/logout_admin.php" class="hover:text-accent transition-colors">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Dashboard Section -->
    <main class="container mx-auto px-4 py-8">
        <h2 class="text-3xl font-bold text-center mb-8">Staff Dashboard</h2>

        <!-- Dashboard Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12"> 
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <h3 class="text-xl font-semibold mb-4">Total Orders</h3>
                <p class="text-2xl font-bold"><?= $totalOrders ?></p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <h3 class="text-xl font-semibold mb-4">Manage Orders</h3>
                <a href="order_manager.php" class="bg-purple-400 text-white font-bold py-2 px-4 rounded hover:bg-green-600 transition-colors">View Orders</a>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <h3 class="text-xl font-semibold mb-4">Delivery Orders</h3>
                <a href="delivery_orders.php" class="bg-purple-400 text-white font-bold py-2 px-4 rounded hover:bg-green-600 transition-colors">View Delivery Orders</a>
            </div>
        </div>
    </main>

    <!-- Footer Section -->
    <footer class="bg-purple-400 text-white mt-12 py-2">
        <div class="container mx-auto px-2">
        <p class="text-center">© 2025 BakeEase Bakery. All rights reserved.</p>        </div>
    </footer>

</body>
</html>
