<?php
// login.php
session_start();
require 'db.php';

// If user is already logged in, send them to the dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = get_db();
    $users = $db->users;
    $email = $_POST['email'];
    $password = $_POST['password'];

    // --- SIGN UP LOGIC ---
    if (isset($_POST['signup'])) {
        $existingUser = $users->findOne(['email' => $email]);
        if ($existingUser) {
            $error = 'Email already exists. Please login.';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $users->insertOne(['email' => $email, 'password' => $hashedPassword]);
            // Redirect to login after successful signup
            header('Location: login.php?signedup=true');
            exit();
        }
    }
    // --- LOGIN LOGIC ---
    else {
        $user = $users->findOne(['email' => $email]);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = (string)$user['_id'];
            header('Location: index.php');
            exit();
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login / Signup</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
    <div class="card auth-card">
        <h2>Welcome to Mindful Moments</h2>
        <?php if ($error): ?><p class="error"><?php echo $error; ?></p><?php endif; ?>
        <?php if (isset($_GET['signedup'])): ?><p class="success">Signup successful! Please log in.</p><?php endif; ?>

        <form method="POST">
            <h3>Login</h3>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <hr>
        <form method="POST">
            <h3>Or, Sign Up</h3>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="signup">Sign Up</button>
        </form>
    </div>
</body>
</html>