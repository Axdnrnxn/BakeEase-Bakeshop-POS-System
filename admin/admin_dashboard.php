<?php
include '../../classes/AdminUser.php';
include '../../classes/Product.php';
include '../../classes/Order.php';
include_once '../../classes/AdminProduct.php'; // Include AdminProduct class

session_start();

// --- STRICT ADMIN LOGIN CHECK ---
if (!isset($_SESSION['user_id']) || $_SESSION['isAdmin'] != 0) {
    header("Location: ../login.php?error=" . urlencode("You are not authorized to access the admin dashboard.")); 
    exit();
}
// --- END ADMIN LOGIN CHECK ---

$adminUser     = new AdminUser  (); 
$product = new Product(); 
$order = new Order();
$adminProduct = new AdminProduct(); // Instantiate AdminProduct

// Fetch data for the dashboard
$totalUsers = count($adminUser  ->displayUsers());  
$totalProducts = count($product->getProducts()); 
$totalOrders = count($order->getOrders()); 

// Low Stock Products
$lowStockThreshold = 10; 
$lowStockProducts = $adminProduct->getLowStockProducts($lowStockThreshold);

// Initialize variables
$timePeriod = 'weekly'; 
$salesData = [];
$totalSalesValue = 0;

// Handle sales report form submission
if (isset($_GET['time_period'])) {
    $timePeriod = htmlspecialchars($_GET['time_period'], ENT_QUOTES, 'UTF-8'); // Sanitize input 

    // Validate time period
    if ($timePeriod !== 'today' && $timePeriod !== 'weekly' && $timePeriod !== 'monthly') {
        $timePeriod = 'weekly'; // Default to weekly if invalid input 
    }

    // Prepare SQL query based on selected time period
    if ($timePeriod == 'today') {
        $sql = "SELECT p.id, p.name, SUM(oi.quantity) AS total_quantity_sold, SUM(oi.quantity * p.price) AS total_sales_value
                FROM order_items oi
                JOIN orders o ON oi.order_id = o.id
                JOIN products p ON oi.product_id = p.id
                WHERE DATE(o.order_date) = CURDATE() AND o.status = 'delivered'
                GROUP BY p.id
                ORDER BY total_sales_value DESC;";
    } elseif ($timePeriod == 'weekly') {
        $sql = "SELECT p.id, p.name, SUM(oi.quantity) AS total_quantity_sold, SUM(oi.quantity * p.price) AS total_sales_value
                FROM order_items oi
                JOIN orders o ON oi.order_id = o.id
                JOIN products p ON oi.product_id = p.id
                WHERE o.order_date >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK) AND o.status = 'delivered'
                GROUP BY p.id
                ORDER BY total_sales_value DESC;";
    } elseif ($timePeriod == 'monthly') {
        $sql = "SELECT p.id, p.name, SUM(oi.quantity) AS total_quantity_sold, SUM(oi.quantity * p.price) AS total_sales_value
                FROM order_items oi
                JOIN orders o ON oi.order_id = o.id
                JOIN products p ON oi.product_id = p.id
                WHERE o.order_date >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH) AND o.status = 'delivered'
                GROUP BY p.id
                ORDER BY total_sales_value DESC;"; 
    }

    $result = $order->executeQuery($sql);
    $salesData = $result->fetch_all(MYSQLI_ASSOC); 

    // Calculate total sales value
    foreach ($salesData as $row) {
        $totalSalesValue += $row['total_sales_value'];
    }
} 

// Handle product filter
$productFilter = '';
if (isset($_GET['product_filter'])) {
    $productFilter = htmlspecialchars($_GET['product_filter'], ENT_QUOTES, 'UTF-8'); // Sanitize input 

    $sql = "SELECT p.id, p.name, SUM(oi.quantity) AS total_quantity_sold, SUM(oi.quantity * p.price) AS total_sales_value
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            JOIN products p ON oi.product_id = p.id
            WHERE o.status = 'delivered' AND (p.name LIKE '%$productFilter%' OR p.id = '$productFilter')
            GROUP BY p.id
            ORDER BY total_sales_value DESC;";

    $result = $order->executeQuery($sql);
    $salesData = $result->fetch_all(MYSQLI_ASSOC); 

    // Calculate total sales value for filtered product
    $totalSalesValue = 0;
    foreach ($salesData as $row) {
        $totalSalesValue += $row['total_sales_value'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BakeEase Bakery - Admin Dashboard</title>
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
</head>
<body class="bg-gray-100 text-gray-800 font-bembo">

    <!-- Header Section -->
    <header class="bg-green-500 text-white shadow-md py-3">
    <div class="container mx-auto px-3 flex justify-between items-center">
            <div class="logo flex items-center">
                <img src="https://img.icons8.com/doodle/48/000000/bread.png" alt="BakeEase Logo" class="w-10 h-10 mr-2">
                <h1 class="text-2xl font-bembo">Admin Dashboard</h1>
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
    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h2 class="text-2xl font-bold text-black text-center mb-2 font-bembo">Hi!, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h2>
        <h3 class="text-lg text-gray-600 text-center mb-4 font-bembo">You are logged in as an Admin</h3>
        <p class="text-center text-gray-700 font-bembo">This is your Admin Dashboard where you can manage users, products, and orders.</p>
    </div>

    <h2 class="text-3xl font-bold text-center mb-8 font-bembo">Admin Dashboard Overview</h2>

        <!-- Dashboard Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12"> 
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <h3 class="text-xl font-semibold mb-4">Users</h3>
                <a href="manage_users.php" class="bg-green-500 text-white font-bold py-2 px-4 rounded hover:bg-green-600 transition-colors">Manage Users</a>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <h3 class="text-xl font-semibold mb-4">Products</h3>
                <a href="manage_products.php" class="bg-green-500 text-white font-bold py-2 px-4 rounded hover:bg-green-600 transition-colors">Manage Products</a>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <h3 class="text-xl font-semibold mb-4">Orders</h3>
                <a href="manage_orders.php" class="bg-green-500 text-white font-bold py-2 px-4 rounded hover:bg-green-600 transition-colors">Order Management</a>
            </div>
        </div>

        <!-- Sales Report Section -->
        <section class="sales-report bg-white p-8 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold mb-6">Sales Report</h2>

            <!-- Filter Options -->
            <form method="get" action="" class="mb-6"> 
                <label for="time_period" class="block text-gray-700 font-bold">Select Time Period:</label>
                <select name="time_period" id="time_period" class="border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="today" <?=($timePeriod == 'today') ? 'selected' : ''; ?>>Today's Sales</option>
                    <option value="weekly" <?= ($timePeriod == 'weekly') ? 'selected' : ''; ?>>Weekly</option>
                    <option value="monthly" <?= ($timePeriod == 'monthly') ? 'selected' : ''; ?>>Monthly</option>
                </select>
                <button type="submit" class="bg-green-500 text-white font-bold py-2 px-4 rounded ml-2 hover:bg-green-600 transition-colors">View Sales Reports</button>
            </form>

            <!-- Product Filter -->
            <form method="get" action="" class="mb-6"> 
                <label for="product_filter" class="block text-gray-700 font-bold">Filter by Product ID or Name:</label>
                <input type="text" name="product_filter" id="product_filter" value="<?= htmlspecialchars($productFilter) ?>" class="border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary">
                <button type="submit" class="bg-green-500 text-white font-bold py-2 px-4 rounded ml-2 hover:bg-green-600 transition-colors">Filter</button>
            </form>

            <!-- Sales Report Receipt -->
            <div class="receipt bg-gray-100 p-4 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold mb-4">Sales Receipt</h3>
                <div class="receipt-content">
                    <!-- Sales Report Table -->
                    <div class="overflow-x-auto" id="sales-report-table">
                        <table class="min-w-full divide-y divide-gray-200 table-auto">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product ID</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Name</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Quantity Sold</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Sales Value</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (!empty($salesData)): ?> 
                                    <?php foreach ($salesData as $row): ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['id']) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['name']) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap"><?= $row['total_quantity_sold'] ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap">P<?= number_format($row['total_sales_value'], 2) ?></td> 
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center">No sales data found for the selected period.</td> 
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Total Sales Value -->
                    <div id="total-sales-value" class="mt-4">
                        <h3 class="text-lg font-semibold">Total Sales Value: ₱<?= number_format($totalSalesValue, 2) ?></h3>
                    </div>
                </div>
            </div>

            <!-- Print Button -->
            <button onclick="printTable('sales-report-table', true)" class="bg-green-500 text-white font-bold py-2 px-4 rounded mt-4 hover:bg-green-600 transition-colors">Print Sales Report</button>
        </section> 

        <section class="low-stock bg-white p-8 rounded-lg shadow-md mt-12">
            <h2 class="text-2xl font-bold mb-6">Low Stock Products</h2>

            <div id="low-stock-table">
                <?php if ($lowStockProducts !== false && !empty($lowStockProducts)): ?>
                    <table class="min-w-full divide-y divide-gray-200 table-auto">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Name</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($lowStockProducts as $product): ?>
                                <tr class="<?php echo ($product['quantity'] <= 5) ? 'bg-red-100' : 'bg-white'; ?>">
                                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($product['name']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= $product['quantity'] ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="edit_product.php?id=<?= $product['id'] ?>" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php elseif ($lowStockProducts === false): ?>
                    <p class="text-red-500">Error fetching low stock products: <?= $adminProduct->getError() ?></p>
                <?php else: ?>
                    <p>No products are currently low in stock.</p>
                <?php endif; ?>
            </div>

            <!-- Print Button -->
            <button onclick="printTable('low-stock-table', false)" class="bg-green-500 text-white font-bold py-2 px-4 rounded mt-4 hover:bg-green-600 transition-colors">Print Low Stock Products</button>
        </section>
    </main>

    <!-- Footer Section -->
    <footer class="bg-green-500 text-white mt-12 py-2">
        <div class="container mx-auto px-2">
        <p class="text-center">© 2025 BakeEase Bakery. All rights reserved.</p>        </div>
    </footer>

    <script>
    function printTable(tableId, includeTotal) {
        var printContents = document.getElementById(tableId).innerHTML;
        var originalContents = document.body.innerHTML;

        if (includeTotal) {
            var totalSalesValue = document.getElementById('total-sales-value').innerHTML; // Get total sales value
            document.body.innerHTML = printContents + totalSalesValue; // Include total sales value
        } else {
            document.body.innerHTML = printContents; // Only include the table
        }

        window.print();
        document.body.innerHTML = originalContents;
    }
    </script>

</body>
</html>
