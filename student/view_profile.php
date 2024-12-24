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
$procStmt = $conn->prepare("CALL GetStudentDetails(:student_id)");
$procStmt->bindParam(':student_id', $student_id);
$procStmt->execute();
$student = $procStmt->fetch(PDO::FETCH_ASSOC);

// Check if the student was found
if ($student) {
    $birthday = date("F j, Y", strtotime($student['birthday']));
    // You can now use $student['name'], $student['gender'], etc.
} else {
    // Handle the case where the student is not found
    echo "Student not found.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f4f8;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: #000051;
        }
        .navbar-nav {
            margin-left: auto;
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
            height: 90vh;
            text-align: center;
        }
        .profile-container {
            background-color: white;
            border: 3px solid #000051;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            padding: 30px;
            width: 350px;
            text-align: center;
        }
        .profile-header h1 {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 1.8rem;
            color: #000051;
            margin-bottom: 5px;
        }
        .profile-header p {
            font-size: 1rem;
            color: #666;
        }
        .profile-info {
            margin-top: 20px;
        }
        .profile-info p {
            background-color: #f8f9fa;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 12px;
            margin: 10px 0;
            font-size: 1rem;
            color: #333;
            text-align: left;
            display: flex;
            justify-content: space-between;
        }
        .profile-info strong {
            color: #000051;
        }
    
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a href="student_dashboard.php" class="btn-back"><i class="fas fa-arrow-left"></i></a>
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
    <div class="profile-container">
        <div class="profile-header">
            <h1><?php echo htmlspecialchars($student['name']); ?></h1>
            <p>Student Profile</p>
        </div>
        <div class="profile-info">
            <p><strong>ID:</strong> <span><?php echo htmlspecialchars($student['student_id']); ?></span></p>
            <p><strong>Name:</strong> <span><?php echo htmlspecialchars($student['name']); ?></span></p>
            <p><strong>Birthday:</strong> <span><?php echo htmlspecialchars($student['birthday']); ?></span></p>
            <p><strong>Gender:</strong> <span><?php echo htmlspecialchars($student['gender']); ?></span></p>
            <p><strong>Email:</strong> <span><?php echo htmlspecialchars($student['email']); ?></span></p>
            <p><strong>Contact:</strong> <span><?php echo htmlspecialchars($student['contact_number']); ?></span></p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
