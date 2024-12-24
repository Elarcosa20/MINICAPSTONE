<?php
require_once '../db/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $professor_id = $_POST['professor_id'];
    $subject_code = $_POST['subject_code'];
    $section_id = $_POST['section_id'];
    $time_id = $_POST['time_id'];

    // Insert into the professor_schedule table
    $stmt = $conn->prepare("INSERT INTO professor_schedule (professor_id, subject_code, section_id, time_id) 
                            VALUES (:professor_id, :subject_code, :section_id, :time_id)");
    $stmt->bindParam(':professor_id', $professor_id);
    $stmt->bindParam(':subject_code', $subject_code);
    $stmt->bindParam(':section_id', $section_id);
    $stmt->bindParam(':time_id', $time_id);

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php?success=1");
    } else {
        header("Location: admin_dashboard.php?error=1");
    }
}
?>
