<?php
header('Content-Type: application/json');
include '../includes/db.php';

$participant_id = $_POST['participant_id'] ?? '';
$participant_name = $_POST['participant_name'] ?? '';

if (empty($participant_id) || empty($participant_name)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

if (strlen($participant_name) > 100) {
    echo json_encode(['success' => false, 'message' => 'Name exceeds maximum length']);
    exit;
}

$stmt = $conn->prepare("UPDATE participants SET name = ? WHERE id = ?");
$stmt->bind_param("ss", $participant_name, $participant_id);
$success = $stmt->execute();
$stmt->close();
$conn->close();

echo json_encode(['success' => $success]);
?>