<?php
require_once 'admin_guard.php';
require_once 'db.php';
$usersResult = $conn->query("SELECT COUNT(*) AS total FROM users");
$totalUsers = $usersResult->fetch_assoc()['total'];

$studentsResult = $conn->query("SELECT COUNT(*) AS total FROM students");
$totalStudents = $studentsResult->fetch_assoc()['total'];

$eventsResult = $conn->query("SELECT COUNT(*) AS total FROM events");
$totalEvents = $eventsResult->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - StudentHub</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .dashboard { padding: 30px; }
        .stats {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin: 25px 0;
        }
        .stat-card {
            background: #e8eefc;
            padding: 25px;
            border-radius: 10px;
            min-width: 150px;
            text-align: center;
        }
        .stat-card h3 { font-size: 30px; margin: 5px; }
        .dashboard-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<header>
    <div class="header-top">
        <img src="Logo_StudentHub.png" alt="StudentHub Logo"
             class="logo">
        <h1>StudentHub - Admin</h1>
    </div>
    <nav>
        <a href="admin_a6.php">Dashboard</a>
        <a href="admin_events.php">Manage Events</a>
        <a href="students.php">Manage Students</a>
        <a href="a6.php">Student Dashboard</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<section class="dashboard">
    <div class="welcome-box">
        <h2>Welcome, Admin!</h2>
        <p>StudentHub overview</p>
    </div>

    <div class="stats">
        <div class="stat-card">
            <h3><?= (int)$totalUsers ?></h3>
            <p>Total Users</p>
        </div>
        <div class="stat-card">
            <h3><?= (int)$totalStudents ?></h3>
            <p>Total Students</p>
        </div>
        <div class="stat-card">
            <h3><?= (int)$totalEvents ?></h3>
            <p>Total Events</p>
        </div>
    </div>

    
<div class="dashboard-box">
    <h3>Latest Registrations</h3>

    <?php
    $sql = "SELECT
                r.registration_id,
                r.registration_date,
                s.name AS student_name,
                e.event_name
            FROM registrations r
            LEFT JOIN students s ON r.student_id = s.student_id
            LEFT JOIN events e ON r.event_id = e.event_id
            ORDER BY r.registration_date DESC,
                     r.registration_id DESC
            LIMIT 5";

    $registrations = $conn->query($sql);
    if (!$registrations) {
    die("Registration query error: " . $conn->error);
}
    ?>

    <?php if ($registrations && $registrations->num_rows > 0): ?>
        <div style="overflow-x:auto">
            <table>
                <thead>
                    <tr>
                        <th>Registration ID</th>
                        <th>Student</th>
                        <th>Event</th>
                        <th>Registration Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $registrations->fetch_assoc()): ?>
                        <tr>
                            <td><?= (int)$row['registration_id'] ?></td>
                            <td><?= htmlspecialchars($row['student_name'] ?? 'Unknown', ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['event_name'] ?? 'Unknown', ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['registration_date'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>No registrations found yet.</p>
    <?php endif; ?>
</div>

</section>

<footer>
    <p>© 2026 StudentHub | All Rights Reserved</p>
</footer>
<script src="script.js"></script>
</body>
</html>
