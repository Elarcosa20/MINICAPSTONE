<?php
session_start();
require_once '../db/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'professor') {
    header("Location: index.html");
    exit();
}

$professor_id = $_SESSION['user_id'];

// Prepare to call the stored procedure
$procStmt = $conn->prepare("CALL GetProfessorSchedule(:professor_id)");
$procStmt->bindParam(':professor_id', $professor_id);
$procStmt->execute();
$schedules = $procStmt->fetchAll(PDO::FETCH_ASSOC);

if (!$schedules) {
    $schedules = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Start Class</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .navbar {
            background-color: #000051;
        }

        .navbar-nav {
            margin-left: auto;
        }

        .navbar-nav .nav-item .nav-link {
            color: white;
        }

        .settings-form {
            background-color: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }
        .btn-primary {
            background-color: #000080;
        }
        .btn-primary:hover {
            background-color: #000070;
        }
        body {
            background-color: #e3f2fd;
            font-family: 'Arial', sans-serif;
        }

        .container {
            margin-top: 60px;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
        }

        h4 {
            color: #000051;
            font-size: 28px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }

        .table {
            background-color: #f9f9f9;
            color: #000051;
            border-radius: 8px;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .table th, .table td {
            padding: 15px;
            text-align: center;
            font-size: 16px;
            border-bottom: 1px solid #ddd;
        }

        .table th {
            background-color: #000080;
            color: white;
            font-weight: bold;
        }

        .table-striped tbody tr:nth-child(odd) {
            background-color: #f1f1f1;
        }

        .btn-primary {
            background-color: #000080;
            border-color: #0288d1;
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
        }

        .btn-primary:hover {
            background-color: #0277bd;
        }

        .btn-success {
            background-color: #ff7043;
            border-color: #ff7043;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
        }

        .btn-success:hover {
            background-color: #f4511e;
        }

        .btn-danger {
            background-color: #d32f2f;
            border-color: #d32f2f;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
        }

        .btn-danger:hover {
            background-color: #c62828;
        }

        .student-btn {
            margin-top: 15px;
        }

        .student-list {
            margin-top: 30px;
        }

        .class-info {
            margin-bottom: 40px;
        }

        
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .action-buttons a {
            width: 150px;
            text-align: center;
        }

        .table-responsive {
            margin-top: 40px;
        }
        .student-list {
    display: none;
    margin-top: 20px;
}

.attendance-stats {
    margin-top: 10px;
    display: flex;
    gap: 20px;
    justify-content: center;
}

.stat-box {
    padding: 10px 20px;
    border-radius: 5px;
    color: white;
    text-align: center;
}

.present-box {
    background-color: #28a745;
}

.absent-box {
    background-color: #dc3545;
}
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a href="professor_dashboard.php" class="btn-back"><i class="fas fa-arrow-left"></i></a>
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
    <h4>Your Class Schedule</h4>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Section</th>
                    <th>Time</th>
                    <th>Action</th>
                    <th>Students</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($schedules)): ?>
                <?php foreach ($schedules as $schedule): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($schedule['subject_name']); ?></td>
                        <td><?php echo htmlspecialchars($schedule['section_name']); ?></td>
                        <td><?php echo htmlspecialchars($schedule['time_range']); ?></td>
                        <td>
                            <button id="startClassBtn<?php echo $schedule['id']; ?>" 
                                    class="btn btn-primary" 
                                    onclick="openStartClassModal(<?php echo $schedule['id']; ?>)">
                                Start Class
                            </button>
                        </td>
                        <td>
                            <a href="view_all_schedules.php?schedule_id=<?php echo $schedule['id']; ?>" class="btn btn-info">
                                View Students
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">No schedules available.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="startClassModal" tabindex="-1" aria-labelledby="startClassModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="startClassModalLabel">Start Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to start this class?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmStartClassBtn">Start Class</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="endClassModal" tabindex="-1" aria-labelledby="endClassModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="endClassModalLabel">End Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to end this class?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-danger" onclick="endClass(<?php echo $class_id; ?>)">End Class</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentScheduleId;

function openStartClassModal(scheduleId) {
    currentScheduleId = scheduleId;
    const startClassModal = new bootstrap.Modal(document.getElementById('startClassModal'));
    startClassModal.show();
}

document.getElementById('confirmStartClassBtn').addEventListener('click', function() {
    fetch('start_class_action.php?action=start&class_id=' + currentScheduleId, {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
           
            const startButton = document.getElementById('startClassBtn' + currentScheduleId);
            startButton.disabled = true;
            startButton.textContent = 'Class Started';
            
           
            const modal = bootstrap.Modal.getInstance(document.getElementById('startClassModal'));
            modal.hide();
            
           
            window.location.href = 'end_class.php?class_id=' + currentScheduleId;
        } else {
            alert("Failed to start the class: " + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("An error occurred while starting the class.");
    });
});

function openEndClassModal() {
    const endClassModal = new bootstrap.Modal(document.getElementById('endClassModal'));
    endClassModal.show();
}

document.getElementById('confirmEndClassBtn').addEventListener('click', function() {

    window.location.href = 'end_class.php?class_id=' + currentScheduleId; 
});

function endClass(classId) {
    fetch('end_class_action.php?action=end&class_id=' + classId, {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Class ended successfully.");
          
            document.querySelector('#endClassModal .btn-close').disabled = true;
        } else {
            alert("Failed to end the class: " + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("An error occurred while ending the class.");
    });
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

