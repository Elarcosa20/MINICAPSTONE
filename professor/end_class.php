<?php
session_start();
require_once '../db/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'professor') {
    header("Location: index.html");
    exit();
}

$class_id = isset($_GET['class_id']) ? $_GET['class_id'] : null;

// Assuming $class_id is already defined
$stmt = $conn->prepare("CALL GetClassSessionDetails(:class_id)");
$stmt->bindParam(':class_id', $class_id);
$stmt->execute();
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if records are returned
if (!empty($records)) {
    $presentCount = $records[0]['present_count']; // Get the present count from the first record
    $classInfo = [
        'subject_name' => $records[0]['subject_name'],
        'section_name' => $records[0]['section_name'],
        'time_range' => $records[0]['time_range'],
        'start_time' => $records[0]['start_time']
    ];
} else {
    $presentCount = 0;
    $classInfo = null;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>End Class</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #e3f2fd;
            font-family: 'Arial', sans-serif;
        }
        .container {
            margin-top: 60px;
            padding: 20px;
            border-radius: 8px;
            background-color: #ffffff;
        }
        h4 {
            color: #000051;
            font-size: 28px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }
        .class-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .class-info p {
            margin-bottom: 10px;
            color: #000051;
        }
        .table {
            background-color: #f9f9f9;
            color: #000051;
            border-radius: 8px;
            border: none;
        }
        .table th, .table td {
            padding: 15px;
            text-align: center;
            font-size: 16px;
            border-bottom: 1px solid #ddd;
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
        .no-records {
            text-align: center;
            padding: 20px;
            color: #666;
        }
    </style>
</head>
<body>
    
<div class="container">
    <h4>Class Attendance</h4>
    <p><strong>Total Present Students:</strong> <?php echo htmlspecialchars($presentCount); ?></p>
    
    <?php if ($classInfo): ?>
    <div class="class-info">
        <p><strong>Subject:</strong> <?php echo htmlspecialchars($classInfo['subject_name']); ?></p>
        <p><strong>Section:</strong> <?php echo htmlspecialchars($classInfo['section_name']); ?></p>
        <p><strong>Time:</strong> <?php echo htmlspecialchars($classInfo['time_range']); ?></p>
        <p><strong>Started:</strong> <?php echo htmlspecialchars(date('F j, Y g:i A', strtotime($classInfo['start_time']))); ?></p>
    </div>
    <?php endif; ?>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Time In</th>
                <th>Device Used</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($records) && isset($records[0]['student_id'])): ?>
                <?php foreach ($records as $record): ?>
                    <?php if ($record['student_id']): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($record['student_id']); ?></td>
                        <td><?php echo htmlspecialchars($record['student_name']); ?></td>
                        <td><?php echo htmlspecialchars(date('g:i A', strtotime($record['time_in']))); ?></td>
                        <td><?php echo htmlspecialchars($record['device_used']); ?></td>
                    </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="no-records">No attendance records yet</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <form id="endClassForm" method="POST" action="end_class_action.php?action=end&class_id=<?php echo htmlspecialchars($class_id); ?>" class="text-center">
        <button type="submit" class="btn btn-danger">End Class</button>
    </form>
</div>

<script>
    document.getElementById('endClassForm').addEventListener('submit', function(event) {
        event.preventDefault();

        if (confirm('Are you sure you want to end this class?')) {
            fetch(this.action, {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Class ended successfully.");
                    window.location.href = 'start_class.php';
                } else {
                    alert("Failed to end the class: " + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("An error occurred while ending the class.");
            });
        }
    });

    function updateAttendanceList() {
        const classId = <?php echo json_encode($class_id); ?>;
        
        fetch(`get_attendance.php?class_id=${classId}`)
            .then(response => response.json())
            .then(data => {
                const tbody = document.querySelector('tbody');
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" class="no-records">No attendance records yet</td></tr>';
                    return;
                }
                
                tbody.innerHTML = '';
                data.forEach(record => {
                    if (record.student_id) {
                        const row = document.createElement('tr');
                        const timeIn = new Date(record.time_in).toLocaleTimeString('en-US', {
                            hour: 'numeric',
                            minute: '2-digit',
                            hour12: true
                        });
                        row.innerHTML = `
                            <td>${record.student_id}</td>
                            <td>${record.name}</td>
                            <td>${timeIn}</td>
                            <td>${record.device_used}</td>
                        `;
                        tbody.appendChild(row);
                    }
                });
            })
            .catch(error => console.error('Error:', error));
    }

    setInterval(updateAttendanceList, 10000);
    updateAttendanceList();
</script>

</body>
</html>