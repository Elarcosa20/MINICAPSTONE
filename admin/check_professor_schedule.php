<?php
session_start();
require_once '../db/db.php'; 


$data = json_decode(file_get_contents('php://input'), true);
$professor_id = $data['professor_id'] ?? null;
$subject_code = $data['subject_code'] ?? null;
$time_id = $data['time_id'] ?? null;
$section_id = $data['section_id'] ?? null;


if (!$professor_id || !$subject_code || !$time_id || !$section_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
    exit();
}


$stmt = $conn->prepare("
    SELECT COUNT(*) FROM professor_schedule 
    WHERE professor_id = :professor_id 
    AND subject_code = :subject_code 
    AND time_id = :time_id 
    AND section_id = :section_id
");
$stmt->bindParam(':professor_id', $professor_id);
$stmt->bindParam(':subject_code', $subject_code);
$stmt->bindParam(':time_id', $time_id);
$stmt->bindParam(':section_id', $section_id);
$stmt->execute();
$count = $stmt->fetchColumn();

if ($count == 0) {
    echo json_encode(['success' => false, 'message' => 'The selected professor does not have a matching schedule for the selected subject, time, and section.']);
} else {
    echo json_encode(['success' => true]);
}
?>