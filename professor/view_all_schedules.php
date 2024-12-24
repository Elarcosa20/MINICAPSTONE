<?php
session_start();
require_once '../db/db.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'professor') {
    header("Location: index.html");
    exit();
}

$class_id = isset($_GET['schedule_id']) ? $_GET['schedule_id'] : null;


$stmt = $conn->prepare("SELECT section_id FROM professor_schedule WHERE id = :class_id");
$stmt->bindParam(':class_id', $class_id);
$stmt->execute();
$section = $stmt->fetch(PDO::FETCH_ASSOC);

if ($section) { 
    $section_id = $section['section_id'];

   
    // Assuming $section_id and $class_id are already defined
$stmt2 = $conn->prepare("CALL GetStudentsBySectionAndClass(:section_id, :class_id)");
$stmt2->bindParam(':section_id', $section_id);
$stmt2->bindParam(':class_id', $class_id);
$stmt2->execute();
$students = $stmt2->fetchAll(PDO::FETCH_ASSOC);

if (!$students) {
    $students = [];
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Students</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f1f3f5;
            font-family: 'Arial', sans-serif;
        }
        .navbar {
            background-color: #000051;
        }

        .navbar-nav {
            margin-left: auto;
        }

        .navbar-nav .nav-item .nav-link {
            color: white;
        }

        h2 {
            color: #495057;
            margin-bottom: 40px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        .table th {
            background-color: #000051;
            color: white;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f8f9fa;
        }
        .table-striped tbody tr:hover {
            background-color: #d1e7fd;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            font-weight: bold;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a href="start_class.php" class="btn-back"><i class="fas fa-arrow-left"></i></a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a href="professor_view_profile.php" class="nav-link"><i class="fas fa-user-circle"></i> Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="professor_settings.php"><i class="fas fa-cogs"></i> Settings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container mt-5">
    <h2 class="text-center">Students List for Your Class</h2>
    <div class="card">
        <div class="card-body">
            <?php if (count($students) > 0): ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                                <td><?php echo htmlspecialchars($student['name']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center">No students found for this section.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>