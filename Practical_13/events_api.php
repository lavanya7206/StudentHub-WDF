<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub - Event API</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            background: #f4f6f9;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
        }

        input, textarea, button {
            padding: 10px;
            margin: 6px 0;
            box-sizing: border-box;
        }

        input, textarea {
            width: 100%;
        }

        button {
            cursor: pointer;
            border: none;
            border-radius: 4px;
            color: white;
            background: #2563eb;
        }

        .delete {
            background: #dc2626;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #e8eefc;
        }

        #message {
            margin-top: 12px;
        }
    </style>
</head>

<body>
<div class="container">
    <h2>Manage Events - StudentHub</h2>

    <form id="eventForm">
        <input type="hidden" id="eventId">

        <label>Event Title</label>
        <input type="text" id="title" required>

        <label>Description</label>
        <textarea id="description"></textarea>

        <label>Event Date</label>
        <input type="date" id="event_date" required>

        <button type="submit" id="saveButton">Add Event</button>
        <button type="button" onclick="resetForm()">Clear</button>
    </form>

    <p id="message" role="status"></p>

    <h3>Event List</h3>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Description</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="eventList"></tbody>
    </table>
</div>
<script>
const API = "../api/events.php";

async function requestAPI(method, data = null) {
    const options = {
        method: method,
        headers: {}
    };

    if (data !== null) {
        options.headers["Content-Type"] = "application/json";
        options.body = JSON.stringify(data);
    }

    const response = await fetch(API, options);
    const result = await response.json();

    if (!response.ok || !result.success) {
        throw new Error(result.message || "Request failed");
    }

    return result;
}

function showMessage(message, error = false) {
    const element = document.getElementById("message");
    element.textContent = message;
    element.style.color = error ? "red" : "green";
}
async function loadEvents() {
    try {
        const result = await requestAPI("GET");
        const tbody = document.getElementById("eventList");

        tbody.replaceChildren();

        result.data.forEach(event => {
            const row = document.createElement("tr");

            [
                event.event_id,
                event.event_name,
                event.description || "",
                event.event_date
            ].forEach(value => {
                const cell = document.createElement("td");
                cell.textContent = value ?? "";
                row.appendChild(cell);
            });

            const actions = document.createElement("td");

            const edit = document.createElement("button");
            edit.textContent = "Edit";
            edit.onclick = () => editEvent(event);

            const del = document.createElement("button");
            del.textContent = "Delete";
            del.className = "delete";
            del.onclick = () => deleteEvent(event.event_id);

            actions.append(edit, del);
            row.appendChild(actions);
            tbody.appendChild(row);
        });

    } catch (error) {
        showMessage(error.message, true);
    }
}
document.getElementById("eventForm").addEventListener("submit",
    async function(e) {
        e.preventDefault();

        const id = document.getElementById("eventId").value;

        const data = {
            event_name: document.getElementById("title").value.trim(),
            description: document.getElementById("description").value.trim(),
            event_date: document.getElementById("event_date").value
        };

        if (id) {
            data.event_id = Number(id);
        }

        try {
            const result = await requestAPI(id ? "PUT" : "POST", data);

            showMessage(result.message);
            resetForm();
            await loadEvents();

        } catch (error) {
            showMessage(error.message, true);
        }
    }
);
function editEvent(event) {
    document.getElementById("eventId").value = event.event_id;
    document.getElementById("title").value = event.event_name;
    document.getElementById("description").value =
        event.description || "";
    document.getElementById("event_date").value = event.event_date;

    document.getElementById("saveButton").textContent = "Update Event";

    window.scrollTo({ top: 0, behavior: "smooth" });
}
async function deleteEvent(id) {
    if (!confirm("Are you sure you want to delete this event?")) {
        return;
    }

    try {
        const result = await requestAPI("DELETE", {
            event_id: Number(id)
        });

        showMessage(result.message);
        await loadEvents();

    } catch (error) {
        showMessage(error.message, true);
    }
}
function resetForm() {
    document.getElementById("eventForm").reset();
    document.getElementById("eventId").value = "";
    document.getElementById("saveButton").textContent = "Add Event";
}
loadEvents();
</script>
</body>
</html>
