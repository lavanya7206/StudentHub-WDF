# 🎓 StudentHub — Student Management Portal

StudentHub is a full-stack web application developed for the **Web Development Frameworks (ITUE203)** course. It provides a platform for managing student information, college events, registrations, and administrative activities.

The project covers Practical 1–15, implementing HTML, CSS, JavaScript, PHP, and MySQL concepts.

## 🚀 Features

- **Responsive UI:** Semantic HTML5, CSS Grid, Flexbox, and mobile-friendly layouts.
- **JavaScript Interactivity:** Dark mode, FAQ accordion, modals, sliders, and form validation.
- **Fetch API:** Dynamic JSON data loading, search, filtering, sorting, and pagination.
- **PHP Form Processing:** Server-side validation and CSV/JSON file storage.
- **MySQL Database:** Database connectivity, normalized tables, and prepared statements.
- **Authentication:** Secure registration, password hashing, sessions, and role-based access control.
- **Student Management:** Add, view, update, delete, search, and export student records.
- **Event Management:** Manage events and upload posters with file validation.
- **REST API:** Perform event CRUD operations using Fetch API without page reloads.
- **Admin Dashboard:** Dynamic statistics, role-based navigation, and audit logs.

## 🛠️ Technologies Used

| Technology | Purpose |
|---|---|
| HTML5 | Page structure and accessibility |
| CSS3 | Responsive design and layouts |
| JavaScript | DOM manipulation and interactivity |
| PHP | Server-side processing and authentication |
| MySQL | Database management |
| Fetch API & JSON | Asynchronous data exchange |
| XAMPP | Local Apache and MySQL server |
| Git & GitHub | Version control and project hosting |

## 📁 Project Structure

```text
Student-Hub/
├── index.html
├── style.css
├── README.md
├── database/
│   └── studenthub.sql
├── Assets/
│   ├── CSS/
│   ├── JS/
│   ├── Data/
│   └── Images/
├── Pages/
│   ├── login.html
│   ├── register.html
│   ├── events.html
│   ├── manage_students.php
│   ├── manage_events.php
│   ├── events_ajax.php
│   └── admin.php
├── PHP/
│   ├── db.php
│   ├── session.php
│   ├── login_action.php
│   ├── register_action.php
│   ├── student_crud.php
│   ├── event_crud.php
│   └── api_events.php
├── data/
└── uploads/
```

## 📚 Practical Coverage

| Practicals | Topics Covered |
|---|---|
| 1–3 | Project planning, semantic HTML, responsive CSS |
| 4–6 | JavaScript DOM, form validation, Fetch API |
| 7–9 | PHP file handling, MySQL, secure registration |
| 10–12 | Authentication, student CRUD, event management |
| 13–14 | REST API, admin dashboard, audit logs |
| 15 | Integration, testing, documentation, deployment |

## ⚙️ Installation & Setup

1. Install and open **XAMPP**.
2. Start the Apache and MySQL modules.
3. Place the `Student-Hub` folder inside `C:\xampp\htdocs\`.
4. Open `http://localhost/phpmyadmin/`.
5. Create a database named `studenthub`.
6. Import the `database/studenthub.sql` file.
7. Open the portal using the following URLs:

   - **Home:** `http://localhost/Student-Hub/index.html`
   - **Login:** `http://localhost/Student-Hub/Pages/login.html`
   - **Admin Dashboard:** `http://localhost/Student-Hub/Pages/admin.php`

Make sure the database credentials in `PHP/db.php` match your local MySQL configuration.

## 🔐 Demo Login Credentials

Use these accounts only if they have been created in your local database.

| Role | Email | Password |
|---|---|---|
| Admin | `admin@studenthub.com` | `Admin@123` |
| Student | `student@studenthub.com` | `Student@123` |

**Note:** If login fails, verify that the accounts exist and their passwords are stored as valid password hashes.

## 🎯 Learning Outcomes

- Developed responsive and interactive web pages.
- Implemented client-side and server-side validation.
- Connected PHP applications with a MySQL database.
- Applied authentication, sessions, and role-based access control.
- Built CRUD operations and asynchronous API functionality.
- Practiced database management, testing, and deployment using XAMPP.

## 👨‍💻 Author

- **Name:** Lavanya Virendra Parekh
- **Program:** B.Tech Computer Engineering
- **Institution:** CHARUSAT University (DEPSTAR)
- **Course:** Web Development Frameworks (ITUE203)
- **Semester:** 3rd Semester
- **Academic Year:** 2026–27

---
