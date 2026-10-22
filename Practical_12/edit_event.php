
<?php
require_once 'admin_guard.php';
require_once 'db.php';

$message = '';
$uploadDir = __DIR__ . '/uploads/events/';
$uploadUrl = 'uploads/events/';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit('Invalid event ID.');
}

$stmt = $conn->prepare(
    "SELECT event_name, event_date, venue, description, poster
     FROM events WHERE event_id = ?"
);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();
$stmt->close();

if (!$event) {
    http_response_code(404);
    exit('Event not found.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';

    $name = trim($_POST['event_name'] ?? '');
    $date = trim($_POST['event_date'] ?? '');
    $venue = trim($_POST['venue'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (!is_string($token) ||
        !hash_equals($_SESSION['csrf_token'], $token)) {
        $message = 'Invalid request. Refresh and try again.';
    } elseif ($name === '' || strlen($name) > 100) {
        $message = 'Enter an event name (maximum 100 characters).';
    } elseif ($venue !== '' && strlen($venue) > 100) {
        $message = 'Venue must be at most 100 characters.';
    } elseif (
        $date !== '' &&
        (!DateTime::createFromFormat('!Y-m-d', $date) ||
         DateTime::createFromFormat('!Y-m-d', $date)->format('Y-m-d') !== $date)
    ) {
        $message = 'Enter a valid event date.';
    } else {
        $posterPath = $event['poster'];
        $newPoster = null;
        $uploadError = '';

        if (
            isset($_FILES['poster']) &&
            $_FILES['poster']['error'] !== UPLOAD_ERR_NO_FILE
        ) {
            $file = $_FILES['poster'];

            if ($file['error'] !== UPLOAD_ERR_OK) {
                $uploadError = 'Poster upload failed.';
            } elseif ($file['size'] > 2 * 1024 * 1024) {
                $uploadError = 'Poster must be 2 MB or smaller.';
            } elseif (!is_uploaded_file($file['tmp_name'])) {
                $uploadError = 'Invalid uploaded file.';
            } else {
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime = $finfo->file($file['tmp_name']);

                $allowed = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/webp' => 'webp'
                ];

                $imageInfo = @getimagesize($file['tmp_name']);

                if (
                    !isset($allowed[$mime]) ||
                    $imageInfo === false ||
                    $imageInfo['mime'] !== $mime
                ) {
                    $uploadError =
                        'Only valid JPG, PNG or WebP images are allowed.';
                } elseif (!is_dir($uploadDir) &&
                          !mkdir($uploadDir, 0755, true)) {
                    $uploadError = 'Could not create upload folder.';
                } elseif (!is_writable($uploadDir)) {
                    $uploadError = 'Upload folder is not writable.';
                } else {
                    $filename = bin2hex(random_bytes(16))
                        . '.' . $allowed[$mime];

                    if (move_uploaded_file(
                        $file['tmp_name'],
                        $uploadDir . $filename
                    )) {
                        $newPoster = $uploadUrl . $filename;
                        $posterPath = $newPoster;
                    } else {
                        $uploadError = 'Could not save the poster.';
                    }
                }
            }
        }

        if ($uploadError !== '') {
            $message = $uploadError;
        } else {
            $update = $conn->prepare(
                "UPDATE events
                 SET event_name = ?,
                     event_date = NULLIF(?, ''),
                     venue = NULLIF(?, ''),
                     description = ?,
                     poster = ?
                 WHERE event_id = ?"
            );

            $update->bind_param(
                'sssssi',
                $name,
                $date,
                $venue,
                $description,
                $posterPath,
                $id
            );

            if ($update->execute()) {
                if ($newPoster !== null &&
                    !empty($event['poster'])) {
                    $oldFile = __DIR__ . '/' . $event['poster'];

                    if (is_file($oldFile)) {
                        unlink($oldFile);
                    }
                }

                $update->close();
                header('Location: admin_events.php?updated=1');
                exit;
            } else {
                if ($newPoster !== null) {
                    $newFile = __DIR__ . '/' . $newPoster;

                    if (is_file($newFile)) {
                        unlink($newFile);
                    }
                }

                $message = 'Could not update event.';
                error_log('Event update failed: ' . $update->error);
                $update->close();
            }
        }
    }
    $event['event_name'] = $name;
    $event['event_date'] = $date;
    $event['venue'] = $venue;
    $event['description'] = $description;
    $event['poster'] = $posterPath ?? $event['poster'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Event - StudentHub</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .event-form {
            max-width: 650px;
            margin: 30px auto;
            padding: 24px;
            border: 1px solid #ddd;
            border-radius: 10px;
        }

        .event-form input,
        .event-form textarea {
            display: block;
            box-sizing: border-box;
            width: 100%;
            padding: 11px;
            margin: 10px 0 16px;
        }

        .event-form button {
            padding: 11px 20px;
            background: #1f4e79;
            color: white;
            border: 0;
            border-radius: 5px;
            cursor: pointer;
        }

        .poster-preview {
            max-width: 180px;
            display: block;
            margin: 10px 0;
        }

        .error {
            color: #b00020;
        }
    </style>
</head>
<body>
<main class="event-form">
    <h2>Edit Event</h2>

    <?php if ($message !== ''): ?>
        <p class="error" role="alert">
            <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token"
            value="<?= htmlspecialchars(
                $_SESSION['csrf_token'],
                ENT_QUOTES,
                'UTF-8'
            ) ?>">

        <label for="event_name">Event Name *</label>
        <input id="event_name" name="event_name" maxlength="100"
            value="<?= htmlspecialchars($event['event_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
            required>

        <label for="event_date">Event Date</label>
        <input type="date" id="event_date" name="event_date"
            value="<?= htmlspecialchars($event['event_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <label for="venue">Venue</label>
        <input id="venue" name="venue" maxlength="100"
            value="<?= htmlspecialchars($event['venue'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <label for="description">Description</label>
        <textarea id="description" name="description" rows="4"><?= htmlspecialchars($event['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>

        <?php if (!empty($event['poster'])): ?>
            <p>Current poster:</p>
            <img class="poster-preview"
                src="<?= htmlspecialchars($event['poster'], ENT_QUOTES, 'UTF-8') ?>"
                alt="Current event poster">
        <?php endif; ?>

        <label for="poster">Replace poster (JPG, PNG or WebP; max 2 MB)</label>
        <input type="file" id="poster" name="poster"
            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">

        <button type="submit">Update Event</button>
    </form>

    <p><a href="admin_events.php">Back to Event Management</a></p>
</main>
</body>
</html>
