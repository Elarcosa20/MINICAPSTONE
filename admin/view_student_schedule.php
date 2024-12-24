<?php
session_start();
require_once '../db/db.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}


try {
    $stmt = $conn->prepare("CALL GetStudentSchedules()");
    $stmt->execute();
    $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching student schedules: " . $e->getMessage());
}

$groupedSchedules = [];
foreach ($schedules as $schedule) {
    $groupedSchedules[$schedule['subject_name']][$schedule['time_range']][] = $schedule;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Student Schedule</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #001b44;
            color: white;
            display: flex;
        }
        .sidebar {
            background-color: #000033;
            min-height: 100vh;
            width: 220px;
            padding: 20px;
            position: fixed;
        }
        .sidebar .nav-link {
            color: white;
            margin-bottom: 10px;
        }
        .sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        .main-content {
            margin-left: 240px;
            padding: 20px;
            width: 100%;
        }
        .table-container {
            background-color: #000033;
            padding: 20px;
            border-radius: 10px;
        }
        .btn-danger {
            background-color: #e60000;
            border: none;
        }
        .btn-danger:hover {
            background-color: #cc0000;
        }
    </style>
</head>
<body>
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
        <div class="table-container">
            <h1>Student Schedules</h1>

            <?php foreach ($groupedSchedules as $subject => $times): ?>
                <div class="subject-group">
                    <h3><?php echo htmlspecialchars($subject); ?></h3>
                    <?php foreach ($times as $time => $students): ?>
                        <h4><?php echo htmlspecialchars($time); ?></h4>
                        <table class="table table-dark table-striped">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Section</th>
                                    <th>Professor</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $schedule): ?>
                                    <tr id="schedule-<?php echo $schedule['id']; ?>">
                                        <td><?php echo htmlspecialchars($schedule['student_name']); ?><s/td>
                                        <td><?php echo htmlspecialchars($schedule['section_name']); ?></td>
                                        <td><?php echo htmlspecialchars($schedule['professor_name']); ?></td>
                                        <td>
                                            <button class="btn btn-danger btn-sm" onclick="deleteSchedule(<?php echo $schedule['id']; ?>)">Delete</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        function deleteSchedule(scheduleId) {
            if (!confirm("Are you sure you want to delete this schedule?")) return;

            fetch('delete_student_schedule.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: scheduleId }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Schedule deleted successfully.");
                    document.getElementById('schedule-' + scheduleId).remove();
                } else {
                    alert("Failed to delete schedule: " + (data.error || "Unknown error"));
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("An error occurred while deleting the schedule.");
            });
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
