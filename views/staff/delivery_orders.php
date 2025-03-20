<?php
include '../../classes/AdminOrder.php';

session_start();

// --- STRICT STAFF LOGIN CHECK ---
if (!isset($_SESSION['user_id']) || !isset($_SESSION['isAdmin']) || ($_SESSION['isAdmin'] != 0 && $_SESSION['isAdmin'] != 1)) {
    header("Location: ../login.php?error=" . urlencode("You are not authorized to access the staff dashboard.")); // Redirect with error message
    exit();
}
// --- END STAFF LOGIN CHECK ---

$adminOrder = new AdminOrder();
$deliveryOrders = $adminOrder->getDeliveryOrders(); // Fetch delivery orders

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BakeEase Bakery - Delivery Orders</title>
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
        }

        body {
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1; /* Makes the main content take up the remaining space */
        }

        footer {
            flex-shrink: 0; /* Prevents the footer from shrinking */
        }

        .fixed.hidden {
            display: none;
        }

        .fixed {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .hidden {
            display: none;
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-100 text-gray-800 font-bembo">

    <!-- Header Section -->
    <header class="bg-purple-400 text-white shadow-md py-3">
        <div class="container mx-auto px-3 flex justify-between items-center">
            <div class="logo flex items-center">
                <img src="https://img.icons8.com/doodle/48/000000/bread.png" alt="BakeEase Logo" class="w-10 h-10 mr-2">
                <h1 class="text-2xl font-bold">Delivery Orders</h1>
            </div>
            <nav>
                <ul class="flex space-x-6">
                    <li><a href="staff_dashboard.php" class="hover:text-accent transition-colors">Back to Dashboard</a></li>
                    <li><a href="../../actions/logout_admin.php" class="hover:text-accent transition-colors">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Main Section -->
    <main class="container mx-auto px-4 py-8">
        <h2 class="text-3xl font-bold text-center mb-8">Delivery Orders</h2>

        <section class="bg-white p-8 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold mb-6">Orders to Deliver</h2>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 table-auto">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer Name</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Price</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (!empty($deliveryOrders)): ?> 
                            <?php foreach ($deliveryOrders as $order): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($order['id']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($order['customer_name']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($order['address']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">₱<?= number_format($order['total_price'], 2) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($order['status']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center">No delivery orders found.</td> 
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- Footer Section -->
    <footer class="bg-purple-400 text-white mt-12 py-2">
        <div class="container mx-auto px-2">
        <p class="text-center">© 2025 BakeEase Bakery. All rights reserved.</p>        </div>
    </footer>

</body>
</html>
