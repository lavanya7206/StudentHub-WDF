let events = [];
let filteredEvents = [];
let currentPage = 1;
let eventsPerPage = 4;
fetch("events.json")
    .then(function(response) {
        console.log("JSON response received");
        return response.json();
    })
    .then(function(data) {
        console.log("Events loaded:", data);
        events = data;
        filteredEvents = data;
        displayEvents();
    })
    .catch(function(error) {
        console.log("ERROR:", error);
        document.getElementById("eventContainer").innerHTML ="<h3>Error loading events.json</h3>";
    });
function displayEvents() {
    let container = document.getElementById("eventContainer");
    container.innerHTML = "";
    let start = (currentPage - 1) * eventsPerPage;
    let end = start + eventsPerPage;
    let pageEvents = filteredEvents.slice(start, end);
    pageEvents.forEach(function(event) {
        let card = document.createElement("div");
        card.className = "event-card";
        card.innerHTML = "<h3>" + event.name + "</h3>" +"<p><strong>Category:</strong> "+ event.category + "</p>" +"<p><strong>Date:</strong> "+ event.date + "</p>" +"<p><strong>Venue:</strong> "+ event.venue +"</p>" +"<p>"+ event.description +"</p>";
        container.appendChild(card);
    });
    updatePagination();
}
document.getElementById("searchBox").addEventListener("input", function() {
    applyChanges();
});
document.getElementById("categoryFilter").addEventListener("change", function() {
    applyChanges();
});
document.getElementById("sortSelect").addEventListener("change", function() {
    applyChanges();
});
function applyChanges() {
    let searchText =document.getElementById("searchBox").value.toLowerCase();
    let category = document.getElementById("categoryFilter").value;
    let sortType =document.getElementById("sortSelect").value;
    filteredEvents = events.filter(function(event) {
        return event.name.toLowerCase().includes(searchText);
    });
    if (category !== "all") {
        filteredEvents = filteredEvents.filter(function(event) {
            return event.category === category;
        });
    }
    if (sortType === "nameAsc") {
        filteredEvents.sort(function(a, b) {
            return a.name.localeCompare(b.name);
        });
    }
    if (sortType === "nameDesc") {
        filteredEvents.sort(function(a, b) {
            return b.name.localeCompare(a.name);
        });
    }
    if (sortType === "dateAsc") {
        filteredEvents.sort(function(a, b) {
            return new Date(a.date) - new Date(b.date);
        });
    }
    if (sortType === "dateDesc") {
        filteredEvents.sort(function(a, b) {
            return new Date(b.date) - new Date(a.date);
        });
    }
    currentPage = 1;
    displayEvents();
}
document.getElementById("prevBtn").addEventListener("click", function() {
    if (currentPage > 1) {
        currentPage--;
        displayEvents();
    }
});
document.getElementById("nextBtn").addEventListener("click", function() {
    let totalPages =Math.ceil(filteredEvents.length / eventsPerPage);
    if (currentPage < totalPages) {
        currentPage++;
        displayEvents();
    }
});
function updatePagination() {
    let totalPages =Math.ceil(filteredEvents.length / eventsPerPage);

    if (totalPages === 0) {
        totalPages = 1;
    }
    document.getElementById("pageNumber").innerText ="Page " + currentPage + " of " + totalPages;
    document.getElementById("prevBtn").disabled =currentPage === 1;
    document.getElementById("nextBtn").disabled =currentPage === totalPages;

});
document.getElementById("themeBtn").addEventListener("click", function() {
    document.body.classList.toggle("dark-mode");
});
