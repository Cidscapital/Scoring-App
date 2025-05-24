<?php
header('Content-Type: application/json');
session_start();
include '../includes/db.php';

if (!isset($_SESSION['judge_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$judge_id = $_SESSION['judge_id'];

$metrics = [];
$metrics['total_scores'] = $conn->query("SELECT COUNT(*) FROM scores WHERE judge_id = '$judge_id'")->fetch_row()[0];
$metrics['participants_scored'] = $conn->query("SELECT COUNT(DISTINCT participant_id) FROM scores WHERE judge_id = '$judge_id'")->fetch_row()[0];
$avg_result = $conn->query("SELECT AVG(score) FROM scores WHERE judge_id = '$judge_id'")->fetch_row()[0];
$metrics['average_score'] = $avg_result ? floatval($avg_result) : 0;

$stmt = $conn->prepare("SELECT id, name FROM participants");
$stmt->execute();
$participants = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$stmt = $conn->prepare("
    SELECT s.participant_id, p.name as participant_name, s.score
    FROM scores s
    JOIN participants p ON s.participant_id = p.id
    WHERE s.judge_id = ?
    ORDER BY s.participant_id
");
$stmt->bind_param("s", $judge_id);
$stmt->execute();
$scores = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();

echo json_encode([
    'metrics' => $metrics,
    'participants' => $participants,
    'scores' => $scores
]);
?>