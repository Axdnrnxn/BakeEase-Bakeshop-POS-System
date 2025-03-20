<?php
include '../../classes/AdminProduct.php';

session_start(); 

// --- STRICT ADMIN LOGIN CHECK ---
if (!isset($_SESSION['user_id']) || $_SESSION['isAdmin'] != 0) {
    header("Location: ../login.php?error=" . urlencode("You are not authorized to access the admin dashboard.")); 
    exit();
}
// --- END ADMIN LOGIN CHECK ---

$adminProduct = new AdminProduct();

if (isset($_GET['id'])) {
    $productId = $_GET['id'];
    $product = $adminProduct->getProduct($productId); 

    if (!$product) {
        echo "Product not found.";
        exit; 
    }
} else {
    header("Location: manage_products.php"); 
    exit();
}

// Handle error messages
if (isset($_GET['error'])) {
    echo "<p class='text-red-500 font-bold mb-4'>" . htmlspecialchars($_GET['error']) . "</p>"; 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BakeEase Bakery - Edit Product</title>
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
    <header class="bg-green-500 text-white shadow-md py-3">
        <div class="container mx-auto px-3 flex justify-between items-center">
            <h1 class="text-2xl font-bold">Edit Product</h1>
            <a href="manage_products.php" class="text-white hover:text-accent">Back to Manage Products</a>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <section class="edit-product bg-white p-8 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold mb-6">Product Details</h2>

            <form method="post" action="../../actions/admin-product-actions.php" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

                <div>
                    <label for="name" class="block text-gray-700 font-bold">Name:</label>
                    <input type="text" id="name" name="name" value="<?= $product['name'] ?>" required
                           class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary"
                    >
                </div>

                <div>
                    <label for="description" class="block text-gray-700 font-bold">Description:</label>
                    <textarea id="description" name="description" required
                              class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary"
                    ><?= $product['description'] ?></textarea>
                </div>

                <div>
                    <label for="price" class="block text-gray-700 font-bold">Price:</label>
                    <input type="number" id="price" name="price" step="0.01" value="<?= $product['price'] ?>" required
                           class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary"
                    >
                </div>

                <div>
                    <label for="quantity" class="block text-gray-700 font-bold">Quantity:</label> 
                    <input type="number" id="quantity" name="quantity" min="0" value="<?= $product['quantity'] ?>" required
                           class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary"
                    >
                </div>

                <div>
                    <label for="image" class="block text-gray-700 font-bold">Image:</label>
                    <input type="file" id="image" name="image" accept="image/*" class="w-full"> 
                </div>

                <button type="submit" name="update_product" 
                        class="bg-green-500 text-white font-bold py-2 px-4 rounded hover:bg-green-600 transition-colors"
                >
                    Update Product
                </button>
            </form>

        </section>
    </main>

    <!-- Footer Section -->
    <footer class="bg-green-500 text-white mt-12 py-2">
        <div class="container mx-auto px-2">
        <p class="text-center">© 2025 BakeEase Bakery. All rights reserved.</p>        </div>
    </footer>
</body>
</html>
