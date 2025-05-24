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
$score = $_POST['score'] ?? '';

if (empty($participant_id) || !is_numeric($score) || $score < 1 || $score > 10) {
    echo json_encode(['success' => false, 'message' => 'Invalid participant or score']);
    exit;
}

$stmt = $conn->prepare("UPDATE scores SET score = ? WHERE judge_id = ? AND participant_id = ?");
$stmt->bind_param("iss", $score, $judge_id, $participant_id);
$success = $stmt->execute();
$stmt->close();
$conn->close();

echo json_encode(['success' => $success]);
?>