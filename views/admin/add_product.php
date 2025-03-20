<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include '../../classes/Product.php';

    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    $uploadDir = '../../assets/images/';  // Upload directory
    $imagePath = '';

    if (!empty($_FILES['image']['name'])) {
        // Ensure the directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Get image info
        $imageInfo = getimagesize($_FILES['image']['tmp_name']);
        if ($imageInfo === false) {
            header("Location: add_product.php?error=Invalid image file.");
            exit;
        }

        $sourceWidth = $imageInfo[0];
        $sourceHeight = $imageInfo[1];

        // Target dimensions
        $targetWidth = 600;
        $targetHeight = 400;

        // Create image resource based on file type
        switch ($imageInfo['mime']) {
            case 'image/jpeg':
                $sourceImage = imagecreatefromjpeg($_FILES['image']['tmp_name']);
                break;
            case 'image/png':
                $sourceImage = imagecreatefrompng($_FILES['image']['tmp_name']);
                break;
            case 'image/gif':
                $sourceImage = imagecreatefromgif($_FILES['image']['tmp_name']);
                break;
            default:
                header("Location: add_product.php?error=Unsupported image type.");
                exit;
        }

        // Create destination image
        $destImage = imagecreatetruecolor($targetWidth, $targetHeight);

        // Calculate scaling and cropping
        $sourceAspect = $sourceWidth / $sourceHeight;
        $targetAspect = $targetWidth / $targetHeight;

        if ($sourceAspect > $targetAspect) {
            // Source is wider than target
            $newHeight = $targetHeight;
            $newWidth = $sourceWidth * ($targetHeight / $sourceHeight);
            $srcX = ($newWidth - $targetWidth) / 2;
            $srcY = 0;
        } else {
            // Source is taller than target
            $newWidth = $targetWidth;
            $newHeight = $sourceHeight * ($targetWidth / $sourceWidth);
            $srcX = 0;
            $srcY = ($newHeight - $targetHeight) / 2;
        }

        // Resize and crop
        imagecopyresampled(
            $destImage, $sourceImage,
            0, 0, // Destination start
            $srcX, $srcY, // Source start
            $targetWidth, $targetHeight, // Destination size
            $newWidth, $newHeight // Source size
        );

        // Generate unique filename
        $imageName = uniqid('product_') . '.jpg';
        $targetFile = $uploadDir . $imageName;

        // Save processed image
        imagejpeg($destImage, $targetFile, 85); // 85 is quality (0-100)

        // Free up memory
        imagedestroy($sourceImage);
        imagedestroy($destImage);

        $imagePath = $imageName;
    }

    $product = new Product();
    $result = $product->addProduct($name, $description, $price, $quantity, $imagePath);

    if ($result) {
        header("Location: manage_products.php?success=Product added!");
    } else {
        header("Location: add_product.php?error=Error adding product.");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BakeEase Bakery - Add Product</title>
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-100 text-gray-800 font-bembo">

    <header class="bg-green-500 text-white shadow-md py-4">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold">Add New Product</h1>
            <a href="manage_products.php" class="text-white hover:text-accent">Back to Manage Products</a>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <section class="add-product bg-white p-8 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold mb-6">Product Details</h2>

            <!-- Check if there is an error message to display -->
            <?php if (isset($_GET['error'])): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p class="font-bold">Error:</p>
                    <p><?php echo htmlspecialchars($_GET['error']); ?></p>
                </div>
            <?php endif; ?>

            <form method="post" action="" enctype="multipart/form-data" class="space-y-4">
                <div>
                    <label for="name" class="block text-gray-700 font-bold">Name:</label>
                    <input type="text" id="name" name="name" required 
                           class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary"
                    >
                </div>
                <div>
                    <label for="description" class="block text-gray-700 font-bold">Description:</label>
                    <textarea id="description" name="description" required
                              class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary"
                    ></textarea>
                </div>
                <div>
                    <label for="price" class="block text-gray-700 font-bold">Price:</label>
                    <input type="number" id="price" name="price" step="0.01" required
                           class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary"
                    >
                </div>
                <div>
                    <label for="quantity" class="block text-gray-700 font-bold">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" min="0" required
                           class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary"
                    >
                </div>
                <div>
                    <label for="image" class="block text-gray-700 font-bold">Image:</label>
                    <input type="file" id="image" name="image" accept="image/*" required
                           class="w-full"
                    >
                </div>
                <button type="submit" name="add_product" 
                        class="bg-green-500 text-white font-bold py-2 px-4 rounded hover:bg-green-600 transition-colors"
                >
                    Add Product
                </button>
            </form>

        </section>
    </main>

    <footer class="bg-green-500 text-white mt-12 py-2">
        <div class="container mx-auto px-2">
        <p class="text-center">© 2025 BakeEase Bakery. All rights reserved.</p>        </div>
    </footer>
</body>
</html>
