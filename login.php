
<?php
// login.php (DEFINITIVE COOKIE VERSION)
// NO session_start() needed.
require 'db.php';

// If a user with a valid cookie visits this page, send them to the dashboard
if (isset($_COOKIE['user_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';
$success = '';
$containerClass = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = get_db();
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (isset($_POST['signup'])) {
        $containerClass = 'right-panel-active';
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->fetch_assoc()) {
            $error = 'Email already exists. Please login.';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters.';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $email, $hashedPassword);
            $stmt->execute();
            header('Location: login.php?signedup=true');
            exit();
        }
    }
    else {
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        if ($user && password_verify($password, $user['password'])) {
            // ✅ THIS IS THE KEY: Set a cookie.
            setcookie('user_id', $user['id'], time() + (86400 * 30), "/"); // Cookie lasts for 30 days

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
    <title>Welcome to Mindful Moments</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">

    <!--  3. Echo the PHP variable directly into the class attribute -->
    <div class="auth-container <?php echo $containerClass; ?>" id="auth-container">
        
        <!-- SIGN UP FORM -->
        <div class="form-container sign-up-container">
            <form action="login.php" method="POST">
                <h1>Create Account</h1>
                <?php if ($error && isset($_POST['signup'])): ?><p class="error"><?php echo $error; ?></p><?php endif; ?>
                <input type="email" name="email" placeholder="Email" required />
                <input type="password" name="password" placeholder="Password (min 6 characters)" required />
                <button type="submit" name="signup">Sign Up</button>
            </form>
        </div>

        <!-- SIGN IN (LOGIN) FORM -->
        <div class="form-container sign-in-container">
            <form action="login.php" method="POST">
                <h1>Sign In</h1>
                <?php if ($error && !isset($_POST['signup'])): ?><p class="error"><?php echo $error; ?></p><?php endif; ?>
                <?php if (isset($_GET['signedup'])): ?><p class="success">Signup successful! Please log in.</p><?php endif; ?>
                <input type="email" name="email" placeholder="Email" required />
                <input type="password" name="password" placeholder="Password" required />
                <button type="submit">Sign In</button>
            </form>
        </div>

        <!-- OVERLAY PANELS -->
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>Welcome Back!</h1>
                    <p>To stay connected with us, please login with your personal info</p>
                    <button class="ghost" id="signIn">Sign In</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1>Hello, Friend!</h1>
                    <p>Enter your personal details and start your journey with us</p>
                    <button class="ghost" id="signUp">Sign Up</button>
                </div>
            </div>
        </div>
    </div>

    <!-- THIS JAVASCRIPT HANDLES THE TOGGLE -->
    <script>
        const signUpButton = document.getElementById('signUp');
        const signInButton = document.getElementById('signIn');
        const container = document.getElementById('auth-container');

        signUpButton.addEventListener('click', () => {
            container.classList.add("right-panel-active");
        });

        signInButton.addEventListener('click', () => {
            container.classList.remove("right-panel-active");
        });
    </script>

</body>
</html>