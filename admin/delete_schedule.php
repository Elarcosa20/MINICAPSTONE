<?php
require_once '../db/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $scheduleId = $_POST['id'] ?? null;

    if ($scheduleId) {
        try {

            $stmt = $conn->prepare("DELETE FROM professor_schedule WHERE id = :id");
            $stmt->bindParam(':id', $scheduleId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to delete schedule.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid schedule ID.']);
    }
}
?>
