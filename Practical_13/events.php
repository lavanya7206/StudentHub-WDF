<?php
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../db.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function respond($success, $message, $data = null, $status = 200)
{
    http_response_code($status);

    echo json_encode([
        "success" => $success,
        "message" => $message,
        "data" => $data
    ]);

    exit;
}

try {
    $method = $_SERVER["REQUEST_METHOD"];

    // LIST EVENTS
    if ($method === "GET") {
        $result = $conn->query(
            "SELECT event_id, event_name, event_date,
                    venue, description, poster
             FROM events ORDER BY event_id DESC"
        );

        respond(true, "Events fetched successfully",
                $result->fetch_all(MYSQLI_ASSOC));
    }

    // READ JSON INPUT
    $input = json_decode(file_get_contents("php://input"), true);

    if (!is_array($input)) {
        respond(false, "Invalid JSON input", null, 400);
    }

    // ADD EVENT
    if ($method === "POST") {
        $name = trim($input["event_name"] ?? "");
        $date = trim($input["event_date"] ?? "");
        $venue = trim($input["venue"] ?? "");
        $description = trim($input["description"] ?? "");

        if ($name === "" || $date === "") {
            respond(false, "Event name and date are required", null, 400);
        }

        $stmt = $conn->prepare(
            "INSERT INTO events
             (event_name, event_date, venue, description)
             VALUES (?, ?, ?, ?)"
        );

        $stmt->bind_param("ssss", $name, $date, $venue, $description);
        $stmt->execute();

        respond(true, "Event added successfully",
                ["event_id" => $conn->insert_id], 201);
    }

    // UPDATE EVENT
    if ($method === "PUT") {
        $id = filter_var($input["event_id"] ?? null, FILTER_VALIDATE_INT);
        $name = trim($input["event_name"] ?? "");
        $date = trim($input["event_date"] ?? "");
        $venue = trim($input["venue"] ?? "");
        $description = trim($input["description"] ?? "");

        if (!$id || $name === "" || $date === "") {
            respond(false, "Valid ID, name and date are required", null, 400);
        }

        $stmt = $conn->prepare(
            "UPDATE events
             SET event_name = ?, event_date = ?,
                 venue = ?, description = ?
             WHERE event_id = ?"
        );

        $stmt->bind_param(
            "ssssi", $name, $date, $venue, $description, $id
        );

        $stmt->execute();

        respond(true, "Event updated successfully");
    }

    // DELETE EVENT
    if ($method === "DELETE") {
        $id = filter_var($input["event_id"] ?? null, FILTER_VALIDATE_INT);

        if (!$id) {
            respond(false, "Valid event ID is required", null, 400);
        }

        $stmt = $conn->prepare(
            "DELETE FROM events WHERE event_id = ?"
        );

        $stmt->bind_param("i", $id);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            respond(false, "Event not found", null, 404);
        }

        respond(true, "Event deleted successfully");
    }

    header("Allow: GET, POST, PUT, DELETE");
    respond(false, "Method not allowed", null, 405);

} catch (Throwable $e) {
    error_log($e->getMessage());
    respond(false, "A server error occurred", null, 500);
}