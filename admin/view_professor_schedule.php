<?php
session_start();
require_once '../db/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}


try {
    $stmt = $conn->prepare("SELECT 1 FROM times LIMIT 1");
    $stmt->execute();
} catch (PDOException $e) {
   
    $createTableQuery = "
    CREATE TABLE `times` (
        `time_id` INT AUTO_INCREMENT PRIMARY KEY,
        `time_range` VARCHAR(50) NOT NULL
    );
    ";
    $conn->exec($createTableQuery);

 
    $insertDefaultTimes = "
    INSERT INTO `times` (time_range) VALUES
    ('08:00 AM - 10:00 AM'),
    ('10:00 AM - 12:00 PM'),
    ('12:00 PM - 02:00 PM'),
    ('02:00 PM - 04:00 PM');
    ";
    $conn->exec($insertDefaultTimes);
}


try {
    $stmt = $conn->prepare("CALL GetProfessorSchedules()");
    $stmt->execute();
    $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

if (isset($_GET['delete_schedule'])) {
    $schedule_id = $_GET['delete_schedule'];

    // Call the stored procedure to delete the professor's schedule
    $deleteStmt = $conn->prepare("CALL DeleteProfessorSchedule(:schedule_id)");
    $deleteStmt->bindParam(':schedule_id', $schedule_id, PDO::PARAM_INT);
    
    // Execute the statement
    $deleteStmt->execute();
    
    // Close the cursor to free up the connection for the next query
    $deleteStmt->closeCursor();

    header("Location: view_professor_schedule.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professor Schedule</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #001b44;
            color: white;
            font-family: Arial, sans-serif;
        }
        .sidebar {
            background-color: #000033;
            min-height: 100vh;
            width: 220px;
            padding: 20px;
        }
        .sidebar .nav-link {
            color: white;
        }
        .sidebar .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
        }
        .main-content {
            padding: 20px;
            margin-left: 240px;
        }
        .table {
            background-color: #000051;
            color: white;
            border-radius: 5px;
        }
        .table th, .table td {
            padding: 10px;
            text-align: left;
        }
        .btn-danger {
            background-color: #e60000;
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            cursor: pointer;
        }
        .btn-danger:hover {
            background-color: #cc0000;
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <div class="sidebar">
            <h2>Admin Panel</h2>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="student_schedule.php">Student Schedule</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="add_student.php">Add Student</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="view_student_schedule.php">View Student Schedule</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="professor_schedule.php">Professor Schedule</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="add_professor.php">Add Professor</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="view_professor_schedule.php">View Professor Schedule</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Log Out</a>
                </li>
            </ul>
        </div>
        
        <div class="main-content">
            <h2>Professor Schedule Overview</h2>
            <table class="table table-dark">
                <thead>
                    <tr>
                        <th>Professor Name</th>
                        <th>Subject</th>
                        <th>Section</th>
                        <th>Time</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($schedules as $schedule): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($schedule['professor_name']); ?></td>
                            <td><?php echo htmlspecialchars($schedule['subject_name']); ?></td>
                            <td><?php echo htmlspecialchars($schedule['section_name']); ?></td>
                            <td><?php echo htmlspecialchars($schedule['time_range']); ?></td>
                            <td>
                                <a href="?delete_schedule=<?php echo $schedule['schedule_id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>