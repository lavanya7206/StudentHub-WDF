<?php
session_start();
require_once "db.php";
$message = "";
$messageType = "";
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $enrollment = trim($_POST["enrollment"] ?? "");
    $department = trim($_POST["department"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirmPassword"] ?? "";
    $token = $_POST["csrf_token"] ?? "";
    if (!is_string($token) ||
        !hash_equals($_SESSION["csrf_token"], $token)) {
        $message = "Invalid request. Please refresh the page.";
        $messageType = "error";
    } elseif (
        $name === "" || $email === "" ||
        $enrollment === "" || $department === "" ||
        $password === "" || $confirmPassword === ""
    ) {
        $message = "Please fill in all fields.";
        $messageType = "error";
    } elseif (
        strlen($name) > 100 ||
        strlen($email) > 255 ||
        strlen($enrollment) > 50 ||
        strlen($department) > 100
    ) {
        $message = "One or more fields exceed the allowed length.";
        $messageType = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $messageType = "error";

    } elseif (
        strlen($password) < 8 || strlen($password) > 72
    ) {
        $message = "Password must be 8–72 bytes long.";
        $messageType = "error";

    } elseif ($password !== $confirmPassword) {
        $message = "Passwords do not match.";
        $messageType = "error";
    } else {
        $check = $conn->prepare(
            "SELECT email, enrollment FROM users
             WHERE email = ? OR enrollment = ?"
        );
        $check->bind_param("ss", $email, $enrollment);
        $check->execute();
        $check->bind_result($existingEmail, $existingEnrollment);
        if ($check->fetch()) {
            if (strcasecmp($existingEmail, $email) === 0) {
                $message = "Email is already registered.";
            } else {
                $message = "Enrollment number already exists.";
            }
            $messageType = "error";
            $check->close();
        } else {
            $check->close();
            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );
            $stmt = $conn->prepare(
                "INSERT INTO users
                (name, email, enrollment, department, password)
                VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->bind_param(
                "sssss",
                $name,
                $email,
                $enrollment,
                $department,
                $hashedPassword
            );
            if ($stmt->execute()) {
                $message = "Registration successful!";
                $messageType = "success";
                $_SESSION["csrf_token"] =
                    bin2hex(random_bytes(32));
            } elseif ($stmt->errno === 1062) {
                $message = "Email or enrollment number already exists.";
                $messageType = "error";
            } else {
                error_log("Registration failed: " . $stmt->error);
                $message = "Registration failed. Please try again.";
                $messageType = "error";
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Register - StudentHub</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <div class="header-top">
        <img src="Logo_StudentHub.png" alt="StudentHub Logo" class="logo">
        <h1>StudentHub</h1>
        <button id="themeBtn" type="button" aria-label="Toggle dark mode">🌙</button>
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
<section class="register">
    <h2>Student Registration</h2>
    <p>Create your StudentHub account.</p>
    <p id="successMsg" style="color:#28a745;font-weight:bold;" role="status"></p>
<form method="POST" action="">
<input type="hidden" name="csrf_token"
       value="<?php echo htmlspecialchars(
           $_SESSION['csrf_token'],
           ENT_QUOTES, 'UTF-8'
       ); ?>">
    <input type="text" name="name"
           placeholder="Full Name" required>
    <input type="email" name="email"
           placeholder="Email Address" required>
    <input type="text" name="enrollment"
           placeholder="Enrollment Number" required>
    <input type="text" name="department"
           placeholder="Department" required>
<input type="password" id="password" name="password"
       placeholder="Password" minlength="8"
       maxlength="72" autocomplete="new-password" required>
<input type="password" id="confirmPassword"
       name="confirmPassword"
       placeholder="Confirm Password"
       minlength="8" maxlength="72"
       autocomplete="new-password" required>
    <button type="submit">Register</button>
</form>
<?php if ($message != ""): ?>
    <p class="<?php echo $messageType; ?>">
        <?php echo htmlspecialchars($message); ?>
    </p>
<?php endif; ?>
    <p>
        Already have an account?
        <a href="a5.html">Login Here</a>
    </p>
</section>
<footer>
    <p>© 2026 StudentHub | All Rights Reserved</p>
</footer>
<script src="script.js"></script>
<script>
document.querySelector("form").addEventListener("submit", function(e) {
    const password = document.getElementById("password").value;
    const confirmPassword =
        document.getElementById("confirmPassword").value;
    if (password !== confirmPassword) {
        e.preventDefault();
        alert("Passwords do not match!");
    }
});
</script>
</body>
</html>
