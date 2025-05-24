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

$check_stmt = $conn->prepare("SELECT id FROM participants WHERE id = ?");
$check_stmt->bind_param("s", $participant_id);
$check_stmt->execute();
if ($check_stmt->get_result()->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Participant does not exist']);
    $check_stmt->close();
    exit;
}
$check_stmt->close();

$check_score_stmt = $conn->prepare("SELECT score FROM scores WHERE judge_id = ? AND participant_id = ?");
$check_score_stmt->bind_param("ss", $judge_id, $participant_id);
$check_score_stmt->execute();
if ($check_score_stmt->get_result()->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Score already submitted for this participant']);
    $check_score_stmt->close();
    exit;
}
$check_score_stmt->close();

$stmt = $conn->prepare("INSERT INTO scores (judge_id, participant_id, score) VALUES (?, ?, ?)");
$stmt->bind_param("ssi", $judge_id, $participant_id, $score);
$success = $stmt->execute();
$stmt->close();
$conn->close();

echo json_encode(['success' => $success]);
?>