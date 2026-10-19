<?php
$message = "";
$messageType = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $enrollment = trim($_POST["enrollment"] ?? "");
    $department = trim($_POST["department"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirmPassword"] ?? "";
    if (
        $name == "" || $email == "" ||
        $enrollment == "" || $department == "" ||
        $password == "" || $confirmPassword == ""
    ) {
        $message = "Please fill in all fields.";
        $messageType = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $messageType = "error";
    } elseif ($password !== $confirmPassword) {
        $message = "Passwords do not match.";
        $messageType = "error";
    } else {
        $name = strip_tags($name);
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $enrollment = strip_tags($enrollment);
        $department = strip_tags($department);
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $file = fopen("registrations.csv", "a");
        if ($file) {
            if (flock($file, LOCK_EX)) {
                $saved = fputcsv($file, [
                    $name,
                    $email,
                    $enrollment,
                    $department,
                    $hashedPassword
                ]);
                fflush($file);
                flock($file, LOCK_UN);
                if ($saved !== false) {
                    $message = "Registration successful!";
                    $messageType = "success";
                } else {
                    $message = "Error saving registration.";
                    $messageType = "error";
                }
            } else {
                $message = "Unable to save registration.";
                $messageType = "error";
            }
            fclose($file);
        } else {
            $message = "Unable to open the registration file.";
            $messageType = "error";
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
        <a href="a5.html">LOGIN</a>
        <a href="a6.html">DASHBOARD</a>
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

    <input type="text" name="name"
           placeholder="Full Name" required>

    <input type="email" name="email"
           placeholder="Email Address" required>

    <input type="text" name="enrollment"
           placeholder="Enrollment Number" required>

    <input type="text" name="department"
           placeholder="Department" required>

    <input type="password" name="password"
           placeholder="Password" required>

    <input type="password" name="confirmPassword"
           placeholder="Confirm Password" required>

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

</body>
</html>
