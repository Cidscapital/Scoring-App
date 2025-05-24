<?php
header('Content-Type: application/json');
include '../includes/db.php';

$participant_id = $_POST['participant_id'] ?? '';
$participant_name = $_POST['participant_name'] ?? '';

if (empty($participant_id) || empty($participant_name)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

if (strlen($participant_id) > 50 || strlen($participant_name) > 100) {
    echo json_encode(['success' => false, 'message' => 'Input exceeds maximum length']);
    exit;
}

$check_stmt = $conn->prepare("SELECT id FROM participants WHERE id = ?");
$check_stmt->bind_param("s", $participant_id);
$check_stmt->execute();
if ($check_stmt->get_result()->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Participant ID already exists']);
    $check_stmt->close();
    exit;
}
$check_stmt->close();

$stmt = $conn->prepare("INSERT INTO participants (id, name) VALUES (?, ?)");
$stmt->bind_param("ss", $participant_id, $participant_name);
$success = $stmt->execute();
$stmt->close();
$conn->close();

echo json_encode(['success' => $success]);
?>