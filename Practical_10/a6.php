
<?php
session_start();

// Redirect visitors who are not logged in.
if (!isset($_SESSION['user_id'])) {
    header('Location: a5.php');
    exit;
}

// End inactive sessions after 15 minutes.
if (
    !isset($_SESSION['last_activity']) ||
    time() - $_SESSION['last_activity'] >= 900
) {
    $_SESSION = [];
    session_destroy();

    header('Location: a5.php?timeout=1');
    exit;
}

$_SESSION['last_activity'] = time();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - StudentHub</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <div class="header-top">
        <img src="Logo_StudentHub.png" alt="Logo" class="logo">
        <h1>StudentHub</h1>
        <button id="themeBtn">🌙</button>
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
        <a href="logout.php">Logout</a>
    </nav>
</header>
<section class="dashboard">
    <div class="welcome-box">
        <h2>Welcome, Lavanya</h2>
        <p>Here's what's happening today in StudentHub.</p>
    </div>
    <div class="stats">
        <div class="stat-card">
            <h3>3</h3>
            <p>Upcoming Events</p>
        </div>
        <div class="stat-card">
            <h3>2</h3>
            <p>Announcements</p>
        </div>
        <div class="stat-card">
            <h3>95%</h3>
            <p>Attendance</p>
        </div>
    </div>
    <div class="dashboard-container">
        <div class="dashboard-box">
            <h3>Student Information</h3>
            <p><strong>Name:</strong> Lavanya Parekh</p>
            <p><strong>Enrollment:</strong> 25DCE067</p>
            <p><strong>Department:</strong> Computer Engineering</p>
        </div>
        <div class="dashboard-box">
            <h3>Latest Announcements</h3>
            <ul>
                Mid-Semester Timetable Released.
                Mock Interviews from next week.
                Library remains open till 8 PM.
            </ul>
        </div>
        <div class="dashboard-box">
            <h3>Upcoming Events</h3>
            <ul>
                Hackathon 2026
                Sports Week
                Coding Competition
            </ul>
        </div>
        <div class="dashboard-box">
            <h3>Quick Links</h3>
            <a class="btn" href="a7.html">View Events</a>
            <a class="btn" href="a8.html">My Profile</a>
            <a class="btn" href="a10.html">Give Feedback</a>
        </div>
    </div>
</section>
<footer>
    <p>© 2026 StudentHub | All Rights Reserved</p>
</footer>
<script src="script.js"></script>
</body>
</html>