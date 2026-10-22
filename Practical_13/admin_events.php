<?php
require_once 'admin_guard.php';
require_once 'db.php';
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$result = $conn->query(
    "SELECT event_id, event_name, description,
            event_date, venue, poster
     FROM events
     ORDER BY event_date DESC"
);

if (!$result) {
    exit("Could not load events. Please try again.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Events - StudentHub</title>
    <link rel="stylesheet" href="style.css">

    <style>
        .event-admin {
            max-width: 1100px;
            margin: 30px auto;
            padding: 20px;
        }

        .event-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .event-table th,
        .event-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        .event-table th {
            background: #1f4e79;
            color: white;
        }

        .poster-thumb {
            width: 90px;
            height: 65px;
            object-fit: cover;
        }

        .event-actions a {
            display: inline-block;
            margin: 3px;
        }

        .event-actions button {
            margin: 3px;
            padding: 6px 10px;
            cursor: pointer;
            background: #b42318;
            color: white;
            border: none;
            border-radius: 4px;
        }

        .add-event {
            display: inline-block;
            padding: 10px 16px;
            background: #1f4e79;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .table-wrap {
            overflow-x: auto;
        }
    </style>
</head>
<body>

<header>
    <div class="header-top">
        <img src="Logo_StudentHub.png"
             alt="StudentHub Logo" class="logo">
        <h1>StudentHub Admin</h1>
    </div>
</header>

<main class="event-admin">
    <h2>Event Management</h2>
    <p>Add, view, update and delete StudentHub events.</p>

    <?php if (isset($_GET['added'])): ?>
        <p>Event added successfully.</p>
    <?php endif; ?>

    <?php if (isset($_GET['updated'])): ?>
        <p>Event updated successfully.</p>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
        <p>Event deleted successfully.</p>
    <?php endif; ?>

    <a class="add-event" href="add_event.php">
        + Add New Event
    </a>

    <div class="table-wrap">
        <table class="event-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Event Name</th>
                    <th>Date</th>
                    <th>Venue</th>
                    <th>Description</th>
                    <th>Poster</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
            <?php while ($event = $result->fetch_assoc()): ?>
                <tr>
                    <td>
                        <?= (int) $event['event_id'] ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            $event['event_name'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            $event['event_date'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            $event['venue'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            $event['description'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>

                    <td>
                        <?php if (!empty($event['poster'])): ?>
                            <img
                                class="poster-thumb"
                                src="<?= htmlspecialchars(
                                    $event['poster'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                alt="Event poster"
                            >
                        <?php else: ?>
                            No poster
                        <?php endif; ?>
                    </td>

                    <td class="event-actions">
                        <a href="edit_event.php?id=<?= (int) $event['event_id'] ?>">
                            Edit
                        </a>

                        <form
                            method="POST"
                            action="delete_event.php"
                            onsubmit="return confirm('Are you sure you want to delete this event?');"
                        >
                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?= htmlspecialchars(
                                    $_SESSION['csrf_token'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                            >

                            <input
                                type="hidden"
                                name="event_id"
                                value="<?= (int) $event['event_id'] ?>"
                            >

                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <p>
        <a href="admin_a6.php">Back to Admin Dashboard</a>
    </p>
</main>

</body>
</html>
