<?php
// Start output buffering to prevent header errors
ob_start();

// Include User class
include '../Classes/User.php';

// Ensure session_start() is only called if no session is active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Handle login form submission
if (isset($_POST['login'])) {
    $user = new User();
    $email = $_POST['email'];
    $password = $_POST['password'];

    $loginResult = $user->login($email, $password);

    if (is_string($loginResult)) {
        header("Location: login.php?error=" . urlencode($loginResult));
        exit; // Ensure script execution stops
    }
}

// Capture error or success messages
$error_message = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : null;
$success_message = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BakeEase Bakery - Login</title>
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
        }
        body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        main {
            flex-grow: 1;
        }
        input {
            border: none;
            border-bottom: 2px solid black;
            width: 100%;
            padding: 8px 0;
            font-size: 16px;
            background: transparent;
            outline: none;
        }
        input::placeholder {
            color: black;
            font-weight: bold;
        }
        p {
            font-family: 'bembo';
        }
    </style>
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

    <!-- Login Section -->
    <main class="container mx-auto px-4 py-8">
        <section class="bg-white p-8 rounded-lg shadow-md max-w-lg mx-auto border-[3px] border-gray-400">
            <h2 class="text-3xl font-bold text-center mb-6 flex justify-center items-center">
                <span class="relative top-1 font-bembo">Bak</span>
                <img width="48" height="48" src="https://img.icons8.com/parakeet/48/cake.png" alt="cake" class="w-10 h-10 inline-block ml-1">
                <span class="relative top-1 font-bembo">Ease</span>
            </h2>
            <h2 class="text-3xl font-bold text-left mb-6 font-bembo">Log-in</h2>

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

            <form method="post" action="" class="space-y-4">
                <div>
                    <label for="email" class="block text-lg font-semibold"></label>
                    <input type="email" id="email" name="email" required placeholder="Email">
                </div>
                <div>
                    <label for="password" class="block text-lg font-semibold"></label>
                    <input type="password" id="password" name="password" required placeholder="Password">
                </div>
                <button type="submit" name="login" class="w-full bg-primary text-white font-bold py-2 rounded hover:bg-green-600 transition-colors focus:outline-none focus:ring-2 focus:ring-primary">
                    Log In
                </button>
            </form>

            <p class="mt-4 text-center font-bembo">Don't have an account? <a href="user/register.php" class="text-primary hover:underline font-bembo">Register here</a>.</p>
        </section>
    </main>

    <!-- Footer Section -->
    <footer class="bg-primary text-white mt-12 py-2">
        <div class="container mx-auto px-2">
        <p class="text-center">© 2025 BakeEase Bakery. All rights reserved.</p>        </div>
    </footer>

</body>
</html>
