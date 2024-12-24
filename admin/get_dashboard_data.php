<?php
require_once '../db/db.php';

$data = [
    'totalStudents' => 0,
    'totalProfessors' => 0,
    'totalCourses' => 0
];

try {
    $stmt = $conn->query("SELECT COUNT(*) FROM students");
    $data['totalStudents'] = $stmt->fetchColumn();

    $stmt = $conn->query("SELECT COUNT(*) FROM professors");
    $data['totalProfessors'] = $stmt->fetchColumn();

    $stmt = $conn->query("SELECT COUNT(*) FROM subjects");
    $data['totalCourses'] = $stmt->fetchColumn();

    echo json_encode($data);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>

