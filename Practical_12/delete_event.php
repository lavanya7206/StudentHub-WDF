<?php
require_once 'admin_guard.php';
require_once 'db.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}
$token = $_POST['csrf_token'] ?? '';

if (
    !is_string($token) ||
    !isset($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $token)
) {
    http_response_code(403);
    exit('Invalid request. Please return to the events page and try again.');
}
$eventId = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);

if (!$eventId || $eventId < 1) {
    exit('Invalid event ID.');
}
$stmt = $conn->prepare(
    "SELECT poster FROM events WHERE event_id = ?"
);
$stmt->bind_param("i", $eventId);
$stmt->execute();

$result = $stmt->get_result();
$event = $result->fetch_assoc();
$stmt->close();

if (!$event) {
    header('Location: admin_events.php');
    exit;
}
$stmt = $conn->prepare(
    "DELETE FROM events WHERE event_id = ?"
);
$stmt->bind_param("i", $eventId);

if (!$stmt->execute()) {
    $stmt->close();
    exit('Could not delete the event. Please try again.');
}

$stmt->close();
$poster = $event['poster'] ?? '';

if (
    is_string($poster) &&
    preg_match(
        '/\Auploads\/events\/[a-zA-Z0-9_-]+\.(jpg|jpeg|png|webp)\z/i',
        $poster
    )
) {
    $posterPath = __DIR__ . DIRECTORY_SEPARATOR
        . str_replace('/', DIRECTORY_SEPARATOR, $poster);

    if (is_file($posterPath)) {
        unlink($posterPath);
    }
}

header('Location: admin_events.php?deleted=1');
exit;
?>
