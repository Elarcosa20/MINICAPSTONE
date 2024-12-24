<?php
session_start();
require_once '../db/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.html");
    exit();
}

$admin_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #000051;
            color: white;
        }
        .sidebar {
            background-color: #000033;
            min-height: 100vh;
        }
        .content {
            padding: 20px;
        }
        .nav-link {
            color: white;
        }
        .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="admin_dashboard.php">
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="student_schedule.php">
                                Student Schedule
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="add_student.php">
                                Add Student
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="view_student_schedule.php">
                                View Student Schedule
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="professor_schedule.php">
                                Professor Schedule
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="add_professor.php">
                                Add Professor
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="view_professor_schedule.php">
                                View Professor Schedule
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../logout.php">
                                Log Out
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Welcome Admin</h1>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Students</h5>
                                <p class="card-text" id="totalStudents">Loading...</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Professors</h5>
                                <p class="card-text" id="totalProfessors">Loading...</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Courses</h5>
                                <p class="card-text" id="totalCourses">Loading...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
  
        fetch('get_dashboard_data.php')
            .then(response => response.json())
            .then(data => {
                document.getElementById('totalStudents').textContent = data.totalStudents;
                document.getElementById('totalProfessors').textContent = data.totalProfessors;
                document.getElementById('totalCourses').textContent = data.totalCourses;
            });
    </script>
</body>
</html>

