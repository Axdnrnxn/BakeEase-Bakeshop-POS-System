<?php
include '../../classes/AdminUser.php'; 

session_start(); 

// --- STRICT ADMIN LOGIN CHECK ---
if (!isset($_SESSION['user_id']) || $_SESSION['isAdmin'] != 0) {
    header("Location: ../login.php?error=" . urlencode("You are not authorized to access the admin dashboard.")); 
    exit();
}
// --- END ADMIN LOGIN CHECK ---

if (!isset($_GET['id'])) {
    header("Location: manage_users.php");
    exit();
}

$userId = $_GET['id']; 
$adminUser  = new AdminUser ();
$userToEdit = $adminUser ->getUserDetails($userId);

// Display error message 
if (isset($_GET['error'])) {
    echo "<p class='text-red-500 font-bold mb-4'>" . htmlspecialchars($_GET['error']) . "</p>"; 
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>BakeEase Bakery - Edit User</title>
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
    <header class="bg-green-500 text-white shadow-md py-4">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold">Edit User</h1>
            <a href="manage_users.php" class="text-white hover:text-accent">Back to Manage Users</a>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <section class="bg-white p-8 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold mb-6">User  Details</h2> 
            <?php if ($userToEdit): ?>
            <form method="post" action="../../actions/admin-user-actions.php" class="space-y-4"> 
                <input type="hidden" name="user_id" value="<?php echo $userToEdit['id']; ?>">

                <div>
                    <label for="name" class="block text-gray-700 font-bold">Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($userToEdit['name']); ?>" required
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div>
                    <label for="email" class="block text-gray-700 font-bold">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userToEdit['email']); ?>" required
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div>
                    <label for="password" class="block text-gray-700 font-bold">New Password (leave blank to keep current):</label>
                    <input type="password" id="password" name="password" 
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div>
                    <label for="role" class="block text-gray-700 font-bold">Role:</label>
                    <select name="role" id="role" class="border rounded px-3 py-2 w-full">
                        <option value="0" <?= $userToEdit['isAdmin'] == 0 ? 'selected' : ''; ?>>Admin</option>
                        <option value="1" <?= $userToEdit['isAdmin'] == 1 ? 'selected' : ''; ?>>Staff</option>
                        <option value="2" <?= $userToEdit['isAdmin'] == 2 ? 'selected' : ''; ?>>Customer</option>
                    </select>
                </div>

                <button type="submit" name="update_user" 
                        class="bg-green-500 text-white font-bold py-2 px-4 rounded hover:bg-green-600 transition-colors">
                    Update User
                </button>
            </form>
            <?php else: ?> 
                <p class="text-red-500 font-bold">User  not found.</p> 
            <?php endif; ?>
        </section>
    </main>
    <!-- Footer Section -->
    <footer class="bg-green-500 text-white mt-12 py-2">
        <div class="container mx-auto px-2">
        <p class="text-center">© 2025 BakeEase Bakery. All rights reserved.</p>        </div>
    </footer> 
</body>
</html>
