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
$orders = $adminOrder->getOrders();

// Message handling
if (isset($_GET['success']) || isset($_GET['error'])) {
    echo "<div id='flash-message' class='fixed inset-0 flex items-center justify-center z-50'>";
    if (isset($_GET['success'])) {
        echo "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative' role='alert'>
                  <span class='block sm:inline'>" . htmlspecialchars($_GET['success']) . "</span>
              </div>";
    } elseif (isset($_GET['error'])) {
        echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative' role='alert'>
                  <span class='block sm:inline'>" . htmlspecialchars($_GET['error']) . "</span>
              </div>";
    }
    echo "</div>";
}

// Handle order status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order_status'])) {
    $orderId = $_POST['order_id'];
    $newStatus = $_POST['new_status'];

    // Update the order status
    $updateMessage = $adminOrder->updateOrderStatus($orderId, $newStatus);

    // Redirect back to the same page with a success or error message
    header("Location: order_manager.php?success=" . urlencode($updateMessage));
    exit();
}

// Handle sorting
if (isset($_GET['sort_by'])) {
    $sortBy = $_GET['sort_by'];
    usort($orders, function($a, $b) use ($sortBy) {
        switch ($sortBy) {
            case 'date':
                return strtotime($b['order_date']) - strtotime($a['order_date']);
            case 'status':
                return strcmp($a['status'], $b['status']);
            case 'name':
                return strcmp($a['customer_name'], $b['customer_name']);
            default:
                return 0;
        }
    });
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>BakeEase Bakery - Manage Orders</title>
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
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-100 text-gray-800 font-bembo min-h-screen flex flex-col">
    <header class="bg-purple-400 text-white shadow-md py-3">
        <div class="container mx-auto px-3 flex justify-between items-center">
            <h1 class="text-2xl font-bold">Manage Orders</h1>
            <a href="staff_dashboard.php" class="text-white hover:text-accent">Back to Dashboard</a>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <!-- Transaction Mode Selection -->
        <div class="mb-6 bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">Manage Orders</h2>
            <div class="flex justify-center gap-4">
                <button id="show-all-btn" class="px-4 py-2 bg-purple-400 text-white rounded hover:bg-green-600 transition-colors">
                    All Orders
                </button>
                <button id="pickup-btn" class="px-4 py-2 bg-purple-400 text-white rounded hover:bg-gray-300 transition-colors">
                    Pickup Orders
                </button>
                <button id="delivery-btn" class="px-4 py-2 bg-purple-400 text-white rounded hover:bg-gray-300 transition-colors">
                    Delivery Orders
                </button>
            </div>
        </div>

        <section class="manage-orders bg-white p-8 rounded-lg shadow-md">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold" id="orders-heading">All Orders</h2>
                <div class="flex items-center">
                    <span class="mr-2">Sort by:</span>
                    <form method="get" action="order_manager.php" class="flex items-center">
                        <select name="sort_by" class="border rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-primary">
                            <option value="date">Date</option>
                            <option value="status">Status</option>
                            <option value="name">Customer Name</option>
                        </select>
                        <button type="submit" class="bg-purple-400 text-white font-bold py-1 px-2 rounded ml-2 hover:bg-green-600 transition-colors">
                            Sort
                        </button>
                    </form>
                </div>
            </div>

            <!-- Card View Container (Default View) -->
            <div id="card-view">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $order): ?>
                            <div class="order-card bg-white rounded-lg shadow-md overflow-hidden border border-gray-200"
                                 data-order-type="<?= $order['order_type'] ?>" data-order-id="<?= $order['id'] ?>">
                                <div class="bg-purple-400 text-white px-4 py-2 flex justify-between items-center">
                                    <h3 class="font-bold">Order #<?= $order['id'] ?></h3>
                                    <span class="text-sm"><?= ucfirst($order['status']) ?></span>
                                </div>
                                <div class="p-4">
                                    <div class="mb-2"><strong>Customer:</strong> <?= $order['customer_name'] ?></div>
                                    <div class="mb-2"><strong>Order Date:</strong> <?= date('F j, Y g:i A', strtotime($order['order_date'])) ?></div>
                                    <div class="mb-2"><strong>Type:</strong> <?= ucfirst($order['order_type']) ?></div>
                                    <div class="mb-2"><strong>Total:</strong> ₱<?= $order['total_price'] ?></div>
                                    <div class="mb-4"><strong>Payment:</strong> <?= $order['payment_method'] ?></div>

                                    <details class="mb-4">
                                        <summary class="cursor-pointer font-medium text-primary">View Items</summary>
                                        <div class="mt-2 pl-4 border-l-2 border-gray-200">
                                            <?= str_replace(', ', '<br>', $order['product_names']) ?>
                                        </div>
                                    </details>

                                    <div class="flex justify-between items-center mt-4">
                                        <form method='post' action='order_manager.php' class="inline-block flex-1 mr-2">
                                            <input type='hidden' name='order_id' value='<?= $order['id'] ?>'>
                                            <div class="flex">
                                                <select name='new_status' class="border rounded-l px-2 py-1 focus:outline-none focus:ring-2 focus:ring-primary flex-1">
                                                    <option value='pending' <?= ($order['status'] == 'pending' ? 'selected' : '') ?>>Pending</option>
                                                    <option value='processing' <?= ($order['status'] == 'processing' ? 'selected' : '') ?>>Processing</option>
                                                    <option value='Ready for pickup' <?= ($order['status'] == 'Ready for pickup' ? 'selected' : '') ?>>Ready for Pickup</option>
                                                    <option value='Ready for delivery' <?= ($order['status'] == 'Ready for delivery' ? 'selected' : '') ?>>Ready for Delivery</option>
                                                    <option value='Out for Delivery' <?= ($order['status'] == 'Out for Delivery' ? 'selected' : '') ?>>Out for Delivery</option>
                                                    <option value='Delivered' <?= ($order['status'] == 'Delivered' ? 'selected' : '') ?>>Delivered</option>
                                                    <option value='Cancelled' <?= ($order['status'] == 'Cancelled' ? 'selected' : '') ?>>Cancelled</option>
                                                </select>
                                                <button type='submit' name='update_order_status'
                                                        class="bg-purple-400 text-white font-bold py-1 px-2 rounded-r hover:bg-green-600 transition-colors">
                                                    Update
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-span-full text-center py-8">
                            No orders found.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Hidden iframe for delete requests -->
        <iframe name="delete-iframe" style="display: none;"></iframe>

        <!-- Hidden Iframe for updates -->
        <iframe name="update-iframe" style="display: none;"></iframe>
    </main>

    <!-- Footer Section -->
    <footer class="bg-purple-400 text-white mt-12 py-2">
        <div class="container mx-auto px-2">
        <p class="text-center">© 2025 BakeEase Bakery. All rights reserved.</p>        </div>
    </footer>

    <!-- JavaScript for interactivity -->
    <script>
        // Flash Message Handling
        const flashMessage = document.getElementById('flash-message');
        if (flashMessage) {
            setTimeout(() => {
                flashMessage.remove();
            }, 5000); // Automatically removes after 5 seconds
            flashMessage.addEventListener('click', () => {
                flashMessage.remove();
            });
        }

        // Transaction Mode buttons
        const showAllBtn = document.getElementById('show-all-btn');
        const pickupBtn = document.getElementById('pickup-btn');
        const deliveryBtn = document.getElementById('delivery-btn');

        function filterOrders(orderType) {
            const orderCards = document.querySelectorAll('.order-card');
            orderCards.forEach(card => {
                const type = card.getAttribute('data-order-type');
                if (orderType === 'all' || type === orderType) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function setActiveBtn(activeBtn, inactiveBtn1, inactiveBtn2) {
            activeBtn.classList.remove('bg-purple-400', 'text-gray-800');
            activeBtn.classList.add('bg-purple-400', 'text-white');

            inactiveBtn1.classList.remove('bg-purple-400', 'text-white');
            inactiveBtn1.classList.add('bg-purple-400', 'text-gray-800');

            inactiveBtn2.classList.remove('bg-purple-400', 'text-white');
            inactiveBtn2.classList.add('bg-purple-400', 'text-gray-800');
        }

        showAllBtn.addEventListener('click', () => {
            filterOrders('all');
            setActiveBtn(showAllBtn, pickupBtn, deliveryBtn);
        });

        pickupBtn.addEventListener('click', () => {
            filterOrders('pickup');
            setActiveBtn(pickupBtn, showAllBtn, deliveryBtn);
        });

        deliveryBtn.addEventListener('click', () => {
            filterOrders('delivery');
            setActiveBtn(deliveryBtn, showAllBtn, pickupBtn);
        });
    </script>
</body>
</html>
