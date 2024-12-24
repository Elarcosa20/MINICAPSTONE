<?php
session_start();
require_once '../db/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: index.html");
    exit();
}

// Assuming you have already started the session and connected to the database
$student_id = $_SESSION['user_id'];

// Prepare to call the stored procedure
$procStmt = $conn->prepare("CALL GetStudentName(:student_id)");
$procStmt->bindParam(':student_id', $student_id);
$procStmt->execute();
$student = $procStmt->fetch(PDO::FETCH_ASSOC);

// Check if the student was found
if ($student) {
    $student_name = $student['name'];
} else {
    $student_name = "Student not found.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- Font Awesome -->
    <style>
        body {
            background-color: white;
            color: black;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: #000051;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        
        }
        .navbar-nav {
            margin-left: auto; /* This pushes navbar items to the right */
        }
        .navbar .navbar-brand img {
            width: 40px;
            height: 40px;
        }
        .navbar-nav .nav-item .nav-link {
            color: white;
        }
        .navbar-nav .nav-item .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            height: 100vh;
            text-align: center;
        }
        .logo {
            width: 100px;
            margin-bottom: 20px;
        }
        .enter-class-btn {
            background-color: #000051;
            color: white;
            padding: 15px 35px;
            font-size: 20px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
        }
        .enter-class-btn:hover {
            background-color: #000070;
        }
        .icons-container {
            margin-top: 20px;
        }
        .icons-container i {
            font-size: 24px;
            color: #007bff;
            margin: 0 15px;
            cursor: pointer;
        }
        .icons-container i:hover {
            color: #0056b3;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a href="view_profile.php" class="nav-link"><i class="fas fa-user-circle"></i> Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="settings.php"><i class="fas fa-cogs"></i> Settings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Logo Section -->
        <img src="/images/logo.png" alt="Logo" class="logo">
        
        <h1>Welcome <br><?php echo htmlspecialchars($student['name']); ?></h1>
        <br>
        
        <a href="enter_class.php" class="enter-class-btn">Enter Class</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
