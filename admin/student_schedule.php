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
    // Call the stored procedure
    $stmt = $conn->prepare("CALL GetAllData()");
    $stmt->execute();

    // Fetch results
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $sections = $stmt->nextRowset(); // Move to the next result set
    $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $professors = $stmt->nextRowset(); // Move to the next result set
    $professors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $subjects = $stmt->nextRowset(); // Move to the next result set
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $times = $stmt->nextRowset(); // Move to the next result set
    $times = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle exception
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Schedule</title>
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
        .form-select {
            background-color: #000051;
            color: white;
            border: none;
            border-radius: 5px;
        }
        .form-select:hover {
            background-color: #000080;
        }
        .form-select:focus {
            border-color: white;
        }
        .submit-btn {
            background-color: #000080;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .submit-btn:hover {
            background-color: #0000cc;
        }
        .cancel-btn {
            background-color: #e60000;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 10px;
            text-decoration: none;
        }
        .cancel-btn:hover {
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
            <h1>Student Schedule</h1>

            <form action="submit_schedule.php" method="POST">
                <div class="mb-3">
                    <label for="studentSelect" class="form-label">Student</label>
                    <select id="studentSelect" name="student_id" class="form-select">
                        <option selected>Choose a student</option>
                        <?php foreach ($students as $student): ?>
                            <option value="<?php echo $student['student_id']; ?>"><?php echo htmlspecialchars($student['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

<div class="mb-3">
    <label for="sectionSelect" class="form-label">Section</label>
    <select id="sectionSelect" name="section_id" class="form-select">
        <option selected>Choose a section</option>
        <?php foreach ($sections as $section): ?>
            <option value="<?php echo $section['section_id']; ?>"><?php echo htmlspecialchars($section['section_name']); ?></option>
        <?php endforeach; ?>
    </select>
</div>

             
                <div class="mb-3">
                    <label for="professorSelect" class="form-label">Professor</label>
                    <select id="professorSelect" name="professor_id" class="form-select">
                        <option selected>Choose a professor</option>
                        <?php foreach ($professors as $professor): ?>
                            <option value="<?php echo $professor['professor_id']; ?>"><?php echo htmlspecialchars($professor['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="subjectSelect" class="form-label">Subject</label>
                    <select id="subjectSelect" name="subject_code" class="form-select">
                        <option selected>Choose a subject</option>
                        <?php foreach ($subjects as $subject): ?>
                            <option value="<?php echo $subject['subject_code']; ?>"><?php echo htmlspecialchars($subject['subject_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="timeSelect" class="form-label">Time</label>
                    <select id="timeSelect" name="time_id" class="form-select">
                        <option selected>Choose a time</option>
                        <?php foreach ($times as $time): ?>
                            <option value="<?php echo $time['time_id']; ?>"><?php echo htmlspecialchars($time['time_range']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="submit-btn">Submit</button>
                <a href="admin_dashboard.php" class="cancel-btn">Cancel</a>
                <div id="successAlert" class="alert alert-success mt-3" style="display: none;">
                    Schedule added successfully!
                </div>
                <script>
                    document.querySelector("form").addEventListener("submit", function (event) {
                        const student = document.getElementById('studentSelect').value;
                        const professor = document.getElementById('professorSelect').value;
                        const subject = document.getElementById('subjectSelect').value;
                        const time = document.getElementById('timeSelect').value;
                        const section = document.getElementById('sectionSelect').value;

                        if (student === "Choose a student" || professor === "Choose a professor" || 
                            subject === "Choose a subject" || time === "Choose a time" || 
                            section === "Choose a section") {
                            alert("Please assign values to all fields before submitting.");
                            event.preventDefault();
                            return;
                        }

                        const xhr = new XMLHttpRequest();
                        xhr.open("POST", "check_professor_schedule.php", true);
                        xhr.setRequestHeader("Content-Type", "application/json");
                        xhr.onload = function () {
                            const response = JSON.parse(xhr.responseText);
                            if (!response.success) {
                                alert("The selected professor does not have a matching schedule for the selected subject/ time/section.");
                                event.preventDefault();
                            } else {
                                document.getElementById('successAlert').style.display = 'block';
                                document.querySelector("form").submit();
                            }
                        };
                        xhr.send(JSON.stringify({
                            professor_id: professor,
                            subject_code: subject,
                            time_id: time,
                            section_id: section
                        }));

                        event.preventDefault();
                    });
                </script>

            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
