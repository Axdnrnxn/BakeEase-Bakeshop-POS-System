<?php
session_start();

$error_message = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include '../Classes/User.php'; // Include User class

    $user = new User();
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Attempt to log in
    $loginResult = $user->login($email, $password);

    if (is_string($loginResult)) {
        header("Location: staff_login.php?error=" . urlencode($loginResult));
        exit();
    } else {
        header("Location: staff_dashboard.php"); // Redirect to staff dashboard
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BakeEase Bakery - Staff Login</title>
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
<body class="bg-gray-100 text-gray-800 font-sans min-h-screen flex flex-col">

    <header class="bg-purple-400 text-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-3 py-3 flex justify-between items-center">
            <h1 class="text-2xl font-bold">BakeEase Bakery Staff</h1>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8 flex-grow"> 
        <section class="bg-white p-8 rounded-lg shadow-md max-w-lg mx-auto border-[3px] border-gray-400">
            <h2 class="text-3xl font-bold text-center mb-6">Staff Login</h2>

            <?php if ($error_message): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                    <p><?= $error_message ?></p>
                </div>
            <?php endif; ?>

            <form method="post" action="" class="space-y-4">
                <div>
                    <input type="email" name="email" required placeholder="Email">
                </div>
                <div>
                    <input type="password" name="password" required placeholder="Password">
                </div>
                <button type="submit" name="staff_login" class="w-full bg-purple-400 text-white font-bold py-2 rounded hover:bg-purple-400 transition-colors">
                    Log In
                </button>
            </form>
        </section>
    </main>

    <footer class="bg-purple-400 text-white mt-12 py-2">
        <div class="container mx-auto px-2">
        <p class="text-center">© 2025 BakeEase Bakery. All rights reserved.</p>        </div>
    </footer>
</body>
</html>
