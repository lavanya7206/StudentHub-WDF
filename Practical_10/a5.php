
<?php
// Configure secure session cookies before starting the session.
$isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => $isHttps,
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();

// Session timeout: 15 minutes of inactivity.
$timeout = 900;

if (isset($_SESSION['user_id'])) {
    if (
        !isset($_SESSION['last_activity']) ||
        time() - $_SESSION['last_activity'] >= $timeout
    ) {
        $_SESSION = [];
        session_destroy();

        header('Location: a5.php?timeout=1');
        exit;
    }

    $_SESSION['last_activity'] = time();

    if (($_SESSION['role'] ?? 'student') === 'admin') {
        header('Location: admin_a6.php');
    } else {
        header('Location: a6.php');
    }
    exit;
}

require_once 'db.php';

$error = '';

if (isset($_GET['timeout'])) {
    $error = 'Your session expired. Please log in again.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) ||
        $password === '') {
        $error = 'Please enter a valid email and password.';
    } else {
        $stmt = $conn->prepare(
            "SELECT id, name, email, password, role
             FROM users
             WHERE email = ?
             LIMIT 1"
        );

        if (!$stmt) {
            error_log('Login query preparation failed.');
            $error = 'Unable to log in right now. Please try again.';
        } else {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();

            if (
                $user &&
                password_verify($password, $user['password'])
            ) {
                // Prevent session fixation.
                session_regenerate_id(true);

                // Store trusted database values in the session.
                $_SESSION['user_id'] = (int) $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];

                // Treat unknown roles as ordinary students.
                $_SESSION['role'] =
                    $user['role'] === 'admin' ? 'admin' : 'student';

                $_SESSION['last_activity'] = time();

                if ($_SESSION['role'] === 'admin') {
                    header('Location: admin_a6.php');
                } else {
                    header('Location: a6.php');
                }
                exit;
            } else {
                $error = 'Invalid email or password.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - StudentHub</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="header-top">
        <img src="Logo_StudentHub.png" alt="StudentHub Logo"
             class="logo">
        <h1>StudentHub</h1>
        <button type="button" id="themeBtn">🌙</button>
    </div>

    <nav>
        <a href="a.html">HOME</a>
        <a href="a1.html">ABOUT</a>
        <a href="a3.html">CONTACT</a>
        <a href="a4.php">REGISTER</a>
        <a href="a5.php">LOGIN</a>
        <a href="a6.php">DASHBOARD</a>
        <a href="a7.html">EVENTS</a>
        <a href="a8.html">PROFILE</a>
        <a href="a9.html">FAQ</a>
        <a href="a10.html">FEEDBACK</a>
    </nav>
</header>

<section class="login">
    <h2>Student Login</h2>
    <p>Login to access your StudentHub account.</p>

    <?php if ($error !== ''): ?>
        <p class="error" role="alert">
            <?= htmlspecialchars(
                $error,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="a5.php">
        <input
            type="email"
            name="email"
            placeholder="Email Address"
            autocomplete="username"
            maxlength="254"
            required
        >

        <input
            type="password"
            name="password"
            placeholder="Password"
            autocomplete="current-password"
            required
        >

        <button type="submit">Login</button>
    </form>

    <p>Don't have an account?
        <a href="a4.php">Register Here</a>
    </p>
</section>

<footer>
    <p>© 2026 StudentHub | All Rights Reserved</p>
</footer>

<script src="script.js"></script>
</body>
</html>
