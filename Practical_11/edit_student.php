<?php
require_once "db.php";
$message = "";
$student = null;
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = filter_input(INPUT_POST, "student_id", FILTER_VALIDATE_INT);
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $course = trim($_POST["course"] ?? "");
    if (!$id || $id < 1) {
        $message = "Invalid student ID.";
    } elseif ($name === "" || $email === "") {
        $message = "Name and email are required.";
    } elseif (
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($name) > 100 ||
        strlen($email) > 100 ||
        strlen($course) > 100
    ) {
        $message = "Please enter valid details within the allowed lengths.";
    } else {
        $check = $conn->prepare(
            "SELECT student_id FROM students
             WHERE email = ? AND student_id <> ?"
        );
        $check->bind_param("si", $email, $id);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) {
            $message = "This email is already used by another student.";
        } else {
            $stmt = $conn->prepare(
                "UPDATE students
                 SET name = ?, email = ?, course = ?
                 WHERE student_id = ?"
            );
            $stmt->bind_param("sssi", $name, $email, $course, $id);
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    header("Location: students.php?updated=1");
                    exit;
                }
                $message = "No changes were made to the student record.";
            } else {
                $message = "Failed to update student.";
            }
            $stmt->close();
        }
        $check->close();
    }
}
if (!$id || $id < 1) {
    exit("Invalid student ID.");
}
$stmt = $conn->prepare(
    "SELECT student_id, name, email, course
     FROM students WHERE student_id = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();
if (!$student) {
    exit("Student record not found.");
}
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Student | StudentHub</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            padding: 25px;
        }
        .container {
            max-width: 500px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
        }
        label { display: block; margin-top: 15px; }
        input {
            width: 100%;
            box-sizing: border-box;
            padding: 11px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button, .button {
            display: inline-block;
            margin-top: 20px;
            padding: 11px 15px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
        }
        .message {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Edit Student</h2>
    <?php if ($message !== ""): ?>
        <p class="message"><?= e($message) ?></p>
    <?php endif; ?>
    <form method="POST">
        <input type="hidden" name="student_id"
               value="<?= (int)$student["student_id"] ?>">
        <label>Student Name *</label>
        <input type="text" name="name" maxlength="100"
               value="<?= e($student["name"]) ?>" required>
        <label>Email *</label>
        <input type="email" name="email" maxlength="100"
               value="<?= e($student["email"]) ?>" required>
        <label>Course</label>
        <input type="text" name="course" maxlength="100"
               value="<?= e($student["course"]) ?>">
        <button type="submit">Save Changes</button>
        <a class="button" href="students.php">Cancel</a>
    </form>
</div>
</body>
</html>
<?php $conn->close(); ?>