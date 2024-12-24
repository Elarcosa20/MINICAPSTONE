<?php
session_start();
require_once '../db/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'professor') {
    header("Location: index.html");
    exit();
}

$professor_id = $_SESSION['user_id'];


// Assuming $professor_id is already defined
$stmt = $conn->prepare("CALL GetProfessorName(:professor_id)");
$stmt->bindParam(':professor_id', $professor_id, PDO::PARAM_INT);
$stmt->execute();
$professor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$professor) {
    echo "Professor not found or error in query execution.";
    exit();
}

// Access the professor's name
$professor_name = $professor['name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professor Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
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
            flex-direction: column;
            height: 100vh;
            text-align: center;
            padding-top: 100px;
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
            background-color: #0056b3;
        }
        .class-card {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .class-card:hover {
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
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

    <div class="container">
        <img src="/images/logo.png" alt="Logo" class="logo">
        
        <h1>Welcome, <?php echo htmlspecialchars($professor['name']); ?></h1>
        
        <h3>Today's Classes</h3>
        <div class="row" id="todayClasses"></div>

        <a href="start_class.php" class="btn btn-success mt-4 enter-class-btn">Start Class</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
       
        fetch('get_today_classes.php')
            .then(response => response.json())
            .then(classes => {
                const classesContainer = document.getElementById('todayClasses');
                classes.forEach(cls => {
                    const classCard = document.createElement('div');
                    classCard.className = 'col-md-4 mb-4';
                    classCard.innerHTML = `
                        <div class="card class-card">
                            <div class="card-body">
                                <h5 class="card-title">${cls.subject_name}</h5>
                                <p class="card-text">Section: ${cls.section_name}</p>
                                <p class="card-text">Time: ${cls.start_time} - ${cls.end_time}</p>
                                <a href="manage_attendance.php?class_id=${cls.id}" class="btn btn-primary">Manage Attendance</a>
                            </div>
                        </div>
                    `;
                    classesContainer.appendChild(classCard);
                });
            })
            .catch(error => {
                console.error('Error fetching classes:', error);
            });
    </script>
</body>
</html>
