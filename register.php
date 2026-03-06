<?php
session_start();

// Redirect to dashboard if already logged in
if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit;
}

$message = "";

// Handle POST registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($email) || empty($password) || empty($confirm)) {
        $message = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email address.";
    } elseif ($password !== $confirm) {
        $message = "Passwords do not match.";
    } else {

        $usersFile = 'users.json';
        $users = file_exists($usersFile) ? json_decode(file_get_contents($usersFile), true) : [];

        // Check if email already exists
        $exists = false;
        if (is_array($users)) {
            foreach ($users as $u) {
                if (strtolower($u['email']) === strtolower($email)) {
                    $exists = true;
                    break;
                }
            }
        }

        if ($exists) {
            $message = "Email is already registered.";
        } else {
            // Add new user
            $newUser = [
                'id' => time(),
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role' => 'member',
                'profile_pic' => "https://ui-avatars.com/api/?name=" . urlencode($name)
            ];

            $users[] = $newUser;
            file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));

            // Log in user immediately
            $_SESSION['user'] = $newUser;
            header("Location: dashboard.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign Up • EventReg Ghana</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-950 via-indigo-950 to-purple-950 p-6">

<div class="w-full max-w-md">
    <div class="bg-white/5 backdrop-blur-2xl border border-white/10 rounded-3xl p-10 shadow-2xl">

        <!-- Logo -->
        <div class="flex justify-center mb-8">
            <div class="w-16 h-16 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-3xl flex items-center justify-center text-white text-3xl font-bold shadow-lg">
                E
            </div>
        </div>

        <h1 class="text-4xl font-bold text-white text-center mb-2">Create Account</h1>
        <p class="text-zinc-400 text-center mb-8">Join EventReg Ghana today</p>

        <?php if (!empty($message)): ?>
            <div class="bg-red-500/20 border border-red-400 text-red-300 p-4 rounded-2xl mb-6 text-center">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div>
                <label class="block text-sm text-zinc-400 mb-2">Full Name</label>
                <input type="text" name="name" required
                    class="w-full bg-white/10 border border-white/20 rounded-2xl px-5 py-4 text-white placeholder-zinc-500 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-500/40 outline-none transition">
            </div>

            <div>
                <label class="block text-sm text-zinc-400 mb-2">Email</label>
                <input type="email" name="email" required
                    class="w-full bg-white/10 border border-white/20 rounded-2xl px-5 py-4 text-white placeholder-zinc-500 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-500/40 outline-none transition">
            </div>

            <div>
                <label class="block text-sm text-zinc-400 mb-2">Password</label>
                <input type="password" name="password" required
                    class="w-full bg-white/10 border border-white/20 rounded-2xl px-5 py-4 text-white placeholder-zinc-500 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-500/40 outline-none transition">
            </div>

            <div>
                <label class="block text-sm text-zinc-400 mb-2">Confirm Password</label>
                <input type="password" name="confirm_password" required
                    class="w-full bg-white/10 border border-white/20 rounded-2xl px-5 py-4 text-white placeholder-zinc-500 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-500/40 outline-none transition">
            </div>

            <button type="submit"
                class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 py-4 rounded-2xl font-bold text-lg text-white hover:opacity-90 transition shadow-lg">
                Sign Up
            </button>
        </form>

        <p class="text-center text-zinc-400 mt-8">
            Already have an account?
            <form method="POST" action="login.php" class="inline">
                <button type="submit" class="text-indigo-400 hover:underline">Login</button>
            </form>
        </p>

    </div>
</div>
</body>
</html>