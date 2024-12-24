<?php
session_start();
require_once '../db/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'end') {
    $class_id = $_GET['class_id'];

    try {
        
        $stmt = $conn->prepare("
            UPDATE class_sessions 
            SET end_time = NOW() 
            WHERE class_id = :class_id AND end_time IS NULL
        ");
        $stmt->bindParam(':class_id', $class_id);
        
        if ($stmt->execute()) {
            
            $delete_stmt = $conn->prepare("
                DELETE FROM class_sessions 
                WHERE class_id = :class_id
            ");
            $delete_stmt->bindParam(':class_id', $class_id);
            $delete_stmt->execute();

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to end class.']);
        }
    } catch (PDOException $e) {
        error_log("Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>

