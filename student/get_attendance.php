<?php
session_start();
require_once '../db/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'professor') {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$class_id = isset($_GET['class_id']) ? $_GET['class_id'] : null;

try {
    
    $stmt = $conn->prepare("
        SELECT 
            s.student_id, 
            s.name, 
            a.time_in, 
            a.device_used
        FROM attendance a
        JOIN students s ON a.student_id = s.student_id
        JOIN class_sessions cs ON a.class_id = cs.id
        WHERE cs.class_id = :class_id
        ORDER BY a.time_in DESC
    ");
    $stmt->bindParam(':class_id', $class_id);
    $stmt->execute();
    
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($records);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
}
?>

