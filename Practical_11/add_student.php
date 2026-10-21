<?php
require_once "db.php";
$message = "";
$messageType = "";
$name = "";
$email = "";
$course = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $course = trim($_POST["course"] ?? "");
    if ($name === "" || $email === "" || $password === "") {
        $message = "Please fill in all required fields.";
        $messageType = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $messageType = "error";
    } elseif (strlen($password) < 8) {
        $message = "Password must contain at least 8 characters.";
        $messageType = "error";
    } else {
        $check = $conn->prepare(
            "SELECT student_id FROM students WHERE email = ?"
        );
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) {
            $message = "A student with this email already exists.";
            $messageType = "error";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare(
                "INSERT INTO students (name, email, password, course)
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param(
                "ssss",
                $name,
                $email,
                $hashedPassword,
                $course
            );
            if ($stmt->execute()) {
                $message = "Student added successfully.";
                $messageType = "success";
                $name = $email = $course = "";
            } else {
                $message = "Failed to add student. Please try again.";
                $messageType = "error";
            }
            $stmt->close();
        }
        $check->close();
    }
}
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student | StudentHub</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            padding: 25px;
        }
        .container {
            max-width: 500px;
            margin: auto;
            padding: 25px;
            background: white;
            border-radius: 10px;
        }
        input {
            width: 100%;
            box-sizing: border-box;
            padding: 11px;
            margin: 8px 0 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button, .button {
            display: inline-block;
            padding: 11px 16px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
        }
        .message {
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .success { background: #dcfce7; color: #166534; }
        .error { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
<div class="container">
    <h2>Add New Student</h2>
    <?php if ($message !== ""): ?>
        <div class="message <?= e($messageType) ?>">
            <?= e($message) ?>
        </div>
    <?php endif; ?>
    <form method="POST">
        <label>Student Name *</label>
        <input type="text" name="name" maxlength="100"
               value="<?= e($name) ?>" required>
        <label>Email *</label>
        <input type="email" name="email" maxlength="100"
               value="<?= e($email) ?>" required>
        <label>Password *</label>
        <input type="password" name="password"
               minlength="8" required>
        <label>Course</label>
        <input type="text" name="course" maxlength="100"
               value="<?= e($course) ?>">
        <button type="submit">Add Student</button>
        <a class="button" href="students.php">Back to Students</a>
    </form>
</div>
</body>
</html>
<?php $conn->close(); ?>