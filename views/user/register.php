<?php
session_start();

$error_message = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : null;
$success_message = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : null;

if (isset($_POST['register'])) {
    include '../../classes/User.php'; 
    $user = new User();

    // Combine first and last name into a single name
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $name = $first_name . ' ' . $last_name;
    
    $email = $_POST['email'];
    $password = $_POST['password'];

    $registrationResult = $user->register($name, $email, $password);

    if (strpos($registrationResult, 'Error') === 0) {
        echo '<script>
            window.location.href = "register.php?error=' . urlencode($registrationResult) . '";
        </script>';
    } else { 
        // Successful registration
        $_SESSION['user_id'] = $user->conn->insert_id; 
        $_SESSION['user_name'] = $name; 
        $_SESSION['new_registration'] = true; 
        $_SESSION['welcome_shown'] = true;
        
        echo '<script>
            window.location.href = "../user/index.php";
        </script>';
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BakeEase Bakery - Register</title>
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
        input {
            border: none; /* Removes the default border */
            border-bottom: 2px solid black; /* Adds a bottom border */
            width: 100%; /* Ensures full width */
            padding: 8px 0; /* Adds spacing */
            font-size: 16px; /* Adjust font size */
            background: transparent; /* Removes background color */
            outline: none; /* Removes outline on focus */
        }

        input::placeholder {
            color: black; /* Makes placeholder text black */
            font-weight: bold; /* Makes placeholder text bold */
        }

        p{
            font-family: 'bembo';
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body class="bg-green-50 text-gray-800 font-bembo">

    <!-- Header Section -->
    <header class="bg-primary text-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-3 py-3 flex justify-between items-center">
            <div class="logo flex items-center">
                <img src="https://img.icons8.com/color/48/000000/birthday-cake.png" alt="BakeEase Logo" class="w-10 h-10 mr-2">
                <h1 class="text-2xl font-bold">BakeEase Bakery</h1>
            </div>
        </div>
    </header>

    <!-- Registration Form Section -->
    <main class="container mx-auto px-4 py-8">
        <section class="bg-white p-8 rounded-lg shadow-md max-w-lg mx-auto border-[3px] border-gray-400">
            <h2 class="text-3xl font-bold text-left mb-6">Create an Account</h2>
            <p class="text-center mb-6">Join our bakery community to enjoy delicious treats and exclusive offers!</p>

            <!-- Display error or success messages -->
            <?php if ($error_message): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                    <p><?= $error_message ?></p>
                </div>
            <?php elseif ($success_message): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
                    <p><?= $success_message ?></p>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="space-y-6">
                <!-- First Name -->
                <div class="relative">
                    <label for="first_name" class="block text-lg font-semibold"></label>
                    <div class="border-b-2 border-black">
                        <input type="text" id="first_name" name="first_name" required 
                            class="w-full border-none focus:ring-0 focus:border-primary bg-transparent px-2 py-2 text-lg" 
                            placeholder="First Name">
                    </div>
                </div>

                <!-- Last Name -->
                <div class="relative">
                    <label for="last_name" class="block text-lg font-semibold"></label>
                    <div class="border-b-2 border-black">
                        <input type="text" id="last_name" name="last_name" required 
                            class="w-full border-none focus:ring-0 focus:border-primary bg-transparent px-2 py-2 text-lg" 
                            placeholder="Last Name">
                    </div>
                </div>

                <!-- Email with Icon -->
                <div class="relative">
                    <label for="email" class="block text-lg font-semibold"></label>
                    <div class="flex items-center border-b-2 border-black">
                        <span class="absolute left-2 text-gray-500">
                            <img src="https://img.icons8.com/ios-glyphs/24/000000/new-post.png" alt="Email Icon">
                        </span>
                        <input type="email" id="email" name="email" required 
                            class="w-full border-none focus:ring-0 focus:border-primary bg-transparent px-10 py-2 text-lg" 
                            placeholder="Email">
                    </div>
                </div>

                <!-- Password with Icon & Show/Hide Toggle -->
                <div class="relative">
                    <label for="password" class="block text-lg font-semibold"></label>
                    <div class="flex items-center border-b-2 border-black">
                        <span class="absolute left-2 text-gray-500">
                            <img src="https://img.icons8.com/ios-glyphs/24/000000/lock.png" alt="Lock Icon">
                        </span>
                        <input type="password" id="password" name="password" required 
                            class="w-full border-none focus:ring-0 focus:border-primary bg-transparent px-10 py-2 text-lg" 
                            placeholder="Password">
                        <span class="absolute right-2 cursor-pointer" onclick="togglePassword()">
                            <img id="eyeIcon" src="https://img.icons8.com/ios-glyphs/24/000000/visible.png" alt="Show Password">
                        </span>
                    </div>
                </div>

                <!-- Register Button -->
                <button type="submit" name="register" 
                    class="w-full bg-primary text-white font-bold py-2 rounded hover:bg-green-600 transition-colors focus:outline-none focus:ring-2 focus:ring-primary">
                    Register
                </button>
            </form>

            <!-- Password Show/Hide Toggle Script -->
            <script>
                function togglePassword() {
                    const passwordInput = document.getElementById('password');
                    const eyeIcon = document.getElementById('eyeIcon');
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        eyeIcon.src = "https://img.icons8.com/ios-glyphs/24/000000/invisible.png"; // Eye-off icon
                    } else {
                        passwordInput.type = 'password';
                        eyeIcon.src = "https://img.icons8.com/ios-glyphs/24/000000/visible.png"; // Eye icon
                    }
                }
            </script>

            <p class="mt-4 text-center font-bembo">Already have an account? <a href="../login.php" class="text-primary hover:underline font-bembo">Log-in here</a>.</p>
        </section>
    </main>

    <!-- Footer Section -->
    <footer class="bg-primary text-white mt-12 py-2">
        <div class="container mx-auto px-2">
        <p class="text-center">© 2025 BakeEase Bakery. All rights reserved.</p>        </div>
    </footer>


</body>
</html>
