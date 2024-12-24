<?php
session_start();
require_once '../db/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$student_id = $_SESSION['user_id'];

try {
    
    $stmt = $conn->prepare("
        SELECT 
            ss.id,
            CASE 
                WHEN cs.id IS NOT NULL AND cs.end_time IS NULL THEN 1 
                ELSE 0 
            END as class_active
        FROM student_schedule ss
        LEFT JOIN professor_schedule ps ON ss.professor_id = ps.professor_id 
            AND ss.subject_code = ps.subject_code 
            AND ss.section_id = ps.section_id
        LEFT JOIN class_sessions cs ON ps.id = cs.class_id AND cs.end_time IS NULL
        WHERE ss.student_id = :student_id
    ");
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    
    $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($schedules);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
}
?>

