<?php
session_start();
require_once '../db/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: index.html");
    exit();
}

$student_id = $_SESSION['user_id'];


$sql = "
    SELECT s.subject_name, p.name AS professor_name, t.time_range
    FROM student_schedule e
    JOIN subjects s ON e.subject_code = s.subject_code
    JOIN professors p ON e.professor_id = p.professor_id
    JOIN times t ON e.time_id = t.time_id
    WHERE e.student_id = :student_id
";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':student_id', $student_id);
$stmt->execute();
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);


header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=study_load.csv');


$output = fopen('php://output', 'w');

// Add CSV headers
fputcsv($output, ['Subject', 'Professor', 'Time']);

foreach ($classes as $class) {
    fputcsv($output, [
        $class['subject_name'],
        $class['professor_name'],
        $class['time_range']
    ]);
}

fclose($output);
exit();
?>
