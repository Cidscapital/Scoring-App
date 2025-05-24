<?php
header('Content-Type: application/json');
session_start();
include '../includes/db.php';

if (!isset($_SESSION['judge_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$judge_id = $_SESSION['judge_id'];

$stmt = $conn->prepare("
    SELECT p.id, p.name as participant_name, COALESCE(AVG(s.score), 0) as average_score
    FROM participants p
    LEFT JOIN scores s ON p.id = s.participant_id AND s.judge_id = ?
    GROUP BY p.id, p.name
    ORDER BY average_score DESC
");
$stmt->bind_param("s", $judge_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();

echo json_encode($result);
?>