<?php
header('Content-Type: application/json');
include '../includes/db.php';

$participant_id = $_POST['participant_id'] ?? '';

if (empty($participant_id)) {
    echo json_encode(['success' => false, 'message' => 'Participant ID is required']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM participants WHERE id = ?");
$stmt->bind_param("s", $participant_id);
$success = $stmt->execute();
$stmt->close();
$conn->close();

echo json_encode(['success' => $success]);
?>