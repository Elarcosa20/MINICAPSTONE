<?php
session_start();
require_once '../db/db.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit();
}


$data = json_decode(file_get_contents('php://input'), true);
$scheduleId = $data['id'] ?? null;

if (!$scheduleId) {
    echo json_encode(['success' => false, 'error' => 'Invalid ID']);
    exit();
}

try {
   
    $stmt = $conn->prepare("DELETE FROM student_schedule WHERE id = :id");
    $stmt->bindParam(':id', $scheduleId, PDO::PARAM_INT);
    $stmt->execute();


    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No record found with this ID']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
