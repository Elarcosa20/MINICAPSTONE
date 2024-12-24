<?php
session_start();
require_once '../db/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'start') {
    $class_id = $_GET['class_id'];

    try {
        
        $check_stmt = $conn->prepare("
            SELECT id FROM class_sessions 
            WHERE class_id = :class_id AND end_time IS NULL
        ");
        $check_stmt->bindParam(':class_id', $class_id);
        $check_stmt->execute();
        
        if ($check_stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Class is already active']);
            exit();
        }

       
        $stmt = $conn->prepare("
            INSERT INTO class_sessions (class_id, start_time, end_time) 
            VALUES (:class_id, NOW(), NULL)
        ");
        $stmt->bindParam(':class_id', $class_id);
        
        if ($stmt->execute()) {
            $_SESSION['class_started'] = true;
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to start class.']);
        }
    } catch (PDOException $e) {
        error_log("Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>

