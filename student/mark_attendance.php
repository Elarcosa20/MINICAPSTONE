<?php
session_start();
require_once '../db/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $student_id = $data['student_id'];
    $session_id = $data['class_id'];
    $status = $data['status'];
    $device_used = $data['device_used'];

    try {
        
        $verify_stmt = $conn->prepare("
            SELECT id 
            FROM class_sessions 
            WHERE id = :session_id 
            AND end_time IS NULL
        ");
        $verify_stmt->bindParam(':session_id', $session_id);
        $verify_stmt->execute();
        
        if (!$verify_stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Invalid or inactive class session.']);
            exit();
        }

   
        $check_stmt = $conn->prepare("
            SELECT id 
            FROM attendance 
            WHERE student_id = :student_id 
            AND class_id = :session_id
        ");
        $check_stmt->bindParam(':student_id', $student_id);
        $check_stmt->bindParam(':session_id', $session_id);
        $check_stmt->execute();

        if ($check_stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Attendance already marked.']);
            exit();
        }

        $stmt = $conn->prepare("
            INSERT INTO attendance (
                student_id, 
                class_id, 
                status, 
                time_in, 
                device_used
            ) VALUES (
                :student_id, 
                :session_id, 
                :status, 
                NOW(), 
                :device_used
            )
        ");

        $stmt->bindParam(':student_id', $student_id);
        $stmt->bindParam(':session_id', $session_id);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':device_used', $device_used);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to mark attendance.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>

