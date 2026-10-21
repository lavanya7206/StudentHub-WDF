<?php
require_once "db.php";
$message = "";
$messageType = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_id"])) {
    $id = filter_var($_POST["delete_id"], FILTER_VALIDATE_INT);
    if ($id && $id > 0) {
        $stmt = $conn->prepare("DELETE FROM students WHERE student_id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $message = "Student deleted successfully.";
                $messageType = "success";
            } else {
                $message = "Student not found.";
                $messageType = "error";
            }
        } else {
            $message = "Failed to delete student.";
            $messageType = "error";
        }
        $stmt->close();
    } else {
        $message = "Invalid student ID.";
        $messageType = "error";
    }
}
$search = trim($_GET["search"] ?? "");
$course = trim($_GET["course"] ?? "");
$sql = "SELECT student_id, name, email, course FROM students WHERE 1=1";
$params = [];
$types = "";
if ($search !== "") {
    $sql .= " AND (name LIKE ? OR email LIKE ?)";
    $term = "%" . $search . "%";
    $params[] = $term;
    $params[] = $term;
    $types .= "ss";
}
if ($course !== "") {
    $sql .= " AND course = ?";
    $params[] = $course;
    $types .= "s";
}
$sql .= " ORDER BY student_id DESC";
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$courses = $conn->query(
    "SELECT DISTINCT course FROM students
     WHERE course IS NOT NULL AND course <> ''
     ORDER BY course"
);
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management | StudentHub</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 25px;
            background: #f4f6f9;
            color: #222;
        }
        .container {
            max-width: 1100px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
        }
        h1 { margin-top: 0; }
        .toolbar {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin: 20px 0;
        }
        input, select, button, .button {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }
        button, .button {
            background: #2563eb;
            color: white;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .delete { background: #dc2626; }
        .edit { background: #15803d; }
        .message {
            padding: 12px;
            margin: 15px 0;
            border-radius: 5px;
        }
        .success { background: #dcfce7; color: #166534; }
        .error { background: #fee2e2; color: #991b1b; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border-bottom: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th { background: #eef2ff; }
        .actions {
            display: flex;
            gap: 6px;
            align-items: center;
        }
        .actions form { margin: 0; }
        @media (max-width: 700px) {
            body { padding: 10px; }
            .container { padding: 12px; overflow-x: auto; }
            table { min-width: 650px; }
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Student Management</h1>
    <p>Manage student records in StudentHub.</p>
    <?php if ($message !== ""): ?>
        <div class="message <?= e($messageType) ?>">
            <?= e($message) ?>
        </div>
    <?php endif; ?>
    <a class="button" href="add_student.php">+ Add Student</a>
    <form method="GET" class="toolbar">
        <input
            type="text"
            name="search"
            placeholder="Search name or email"
            value="<?= e($search) ?>"
        >
        <select name="course">
            <option value="">All Courses</option>
            <?php while ($c = $courses->fetch_assoc()): ?>
                <option value="<?= e($c["course"]) ?>"
                    <?= $course === $c["course"] ? "selected" : "" ?>
                >
                    <?= e($c["course"]) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <button type="submit">Search / Filter</button>
        <a class="button" href="students.php">Reset</a>
    </form>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Course</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($student = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= e($student["student_id"]) ?></td>
                    <td><?= e($student["name"]) ?></td>
                    <td><?= e($student["email"]) ?></td>
                    <td><?= e($student["course"]) ?></td>
                    <td>
                        <div class="actions">
                            <a class="button edit" href="edit_student.php?id=<?= (int)$student["student_id"] ?>">Edit</a>
                            <form method="POST" onsubmit="return confirm('Delete this student?');">
                                <input type="hidden" name="delete_id" value="<?= (int)$student["student_id"] ?>">
                                <button class="delete" type="submit">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">No student records found.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
<?php
$stmt->close();
$courses->free();
$conn->close();
?>