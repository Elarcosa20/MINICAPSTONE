<?php
session_start();
require_once '../db/db.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'] ?? null;
    $professor_id = $_POST['professor_id'] ?? null;
    $subject_code = $_POST['subject_code'] ?? null;
    $time_id = $_POST['time_id'] ?? null;
    $section_id = $_POST['section_id'] ?? null;

    if (!$student_id || !$professor_id || !$subject_code || !$time_id || !$section_id) {
        $_SESSION['error'] = "All fields are required!";
        header("Location: student_schedule.php");
        exit();
    }


    try {
        $insertSchedule = $conn->prepare("
            INSERT INTO student_schedule (student_id, professor_id, subject_code, time_id, section_id)
            VALUES (:student_id, :professor_id, :subject_code, :time_id, :section_id)
        ");
        $insertSchedule->bindParam(':student_id', $student_id);
        $insertSchedule->bindParam(':professor_id', $professor_id);
        $insertSchedule->bindParam(':subject_code', $subject_code);
        $insertSchedule->bindParam(':time_id', $time_id);
        $insertSchedule->bindParam(':section_id', $section_id);

        if ($insertSchedule->execute()) {
            $_SESSION['success'] = "Schedule added successfully!";
            header("Location: student_schedule.php");
            exit();
        } else {
            $_SESSION['error'] = "Failed to add schedule.";
            header("Location: student_schedule.php");
            exit();
        }
    } catch (PDOException $e) {
        error_log("Error: " . $e->getMessage());
        $_SESSION['error'] = "An error occurred while trying to add the schedule.";
        header("Location: student_schedule.php");
        exit();
    }
}
?>
