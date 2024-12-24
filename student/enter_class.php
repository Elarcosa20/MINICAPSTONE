<?php
session_start();
require_once '../db/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: index.html");
    exit();
}

$student_id = $_SESSION['user_id'];

// Fetch the student's enrolled classes with active sessions
$sql = "CALL GetStudentClasses(:student_id)";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':student_id', $student_id);
$stmt->execute();
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$classes) {
    $classes = []; 
}
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Enter Class</title>
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
                padding-left: 10px;
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
                margin-top: 100px;
            }
            .subject-card {
                background-color: #f0f8ff;
                padding: 20px;
                border-radius: 8px;
                margin-bottom: 20px;
                width: 80%;
            }
            .subject-card button {
                background-color:  #000051;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 5px;
                font-size: 16px;
                transition: background-color 0.3s, transform 0.2s;
            }
            .subject-card button:disabled {
                background-color: #cccccc;
            }
            .subject-card button:hover {
                background-color: #000080;
                transform: scale(1.05);
            }

            .btn-present:hover {
                background-color:#000070 ;
                transform: scale(1.05);
            }

            .back-button {
                font-size: 24px;
                color: #fff;
                background: none;
                border: none;
            }
            .back-button:hover {
                color: #000080;
            }
           
    .subject-card button {
        background-color: #000051;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        font-size: 16px;
        transition: background-color 0.3s, transform 0.2s;
    }

   
    .subject-card button:hover {
        background-color: #004080;
        transform: scale(1.05);
    }

    .subject-card button:disabled {
        background-color: #cccccc;
    }

        </style>
    </head>
    <body>

        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container-fluid">
              
                <a href="student_dashboard.php" class="btn-back"><i class="fas fa-arrow-left"></i></a>
              
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
    <h2>Enter Class</h2>
    <p>Click "Present" to mark your attendance when the class starts.</p>
    <form method="POST" action="download_study_load.php" class="mb-4">
    <button type="submit" class="btn" style="background-color: #008000; color: white;">
        <i class="fas fa-download"></i> Download Study Load
    </button>
</form>


    <?php if (!empty($classes)): ?>
        <?php foreach ($classes as $class): ?>
            <div class="subject-card">
                <h4><?php echo htmlspecialchars($class['subject_name']); ?></h4>
                <p>Professor: <?php echo htmlspecialchars($class['professor_name']); ?></p>
                <p>Time: <?php echo htmlspecialchars($class['time_range']); ?></p>
                
                <button 
                    class="btn <?php echo $class['is_active'] ? 'btn-success' : 'btn-secondary'; ?>" 
                    data-schedule-id="<?php echo htmlspecialchars($class['schedule_id']); ?>"
                    data-session-id="<?php echo htmlspecialchars($class['session_id']); ?>"
                    onclick="markPresent(this, '<?php echo htmlspecialchars($class['session_id']); ?>')"
                    <?php echo !$class['is_active'] ? 'disabled' : ''; ?>
                >
                    <?php echo $class['is_active'] ? 'Present' : 'Class Not Started'; ?>
                </button>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No classes found for this student.</p>
    <?php endif; ?>
</div>


<script>
function markPresent(button, sessionId) {
    if (!sessionId) {
        alert("No active class session found.");
        return;
    }

    fetch('mark_attendance.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            student_id: '<?php echo $student_id; ?>',
            class_id: sessionId,
            status: 'present',
            device_used: navigator.userAgent
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            button.disabled = true;
            button.textContent = 'Marked Present';
            button.classList.remove('btn-success');
            button.classList.add('btn-secondary');
        } else {
            alert(data.message || "Failed to mark attendance.");
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("An error occurred while marking attendance.");
    });
}


function checkClassStatus() {
    fetch('check_class_status.php')
        .then(response => response.json())
        .then(data => {
            data.forEach(schedule => {
                const button = document.querySelector(`button[data-schedule-id="${schedule.id}"]`);
                if (button) {
                    if (schedule.class_active === 1) {
                        button.disabled = false;
                        button.classList.remove('btn-secondary');
                        button.classList.add('btn-success');
                        button.textContent = 'Present';
                    } else {
                        button.disabled = true;
                        button.classList.remove('btn-success');
                        button.classList.add('btn-secondary');
                        button.textContent = 'Class Not Started';
                    }
                }
            });
        })
        .catch(error => console.error('Error:', error));
}


setInterval(checkClassStatus, 5000);

checkClassStatus();
</script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
       
        <div class="modal fade" id="attendanceModal" tabindex="-1" aria-labelledby="attendanceModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="attendanceModalLabel">Confirm Attendance</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p id="modalSubject"></p>
                        <p id="modalProfessor"></p>
                        <p>Are you sure you want to mark yourself as present?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="confirmPresentButton">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>

