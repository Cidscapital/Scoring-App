<?php
header('Content-Type: application/json');
session_start();
include '../includes/db.php';

if (!isset($_SESSION['judge_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$judge_id = $_SESSION['judge_id'];
$participant_id = $_POST['participant_id'] ?? '';

if (empty($participant_id)) {
    echo json_encode(['success' => false, 'message' => 'Participant ID is required']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM scores WHERE judge_id = ? AND participant_id = ?");
$stmt->bind_param("ss", $judge_id, $participant_id);
$success = $stmt->execute();
$stmt->close();
$conn->close();

echo json_encode(['success' => $success]);
?>