<?php
header('Content-Type: application/json');
include '../includes/db.php';

$stmt = $conn->prepare("
    SELECT p.id, p.name, COALESCE(SUM(s.score), 0) as total_score
    FROM participants p
    LEFT JOIN scores s ON p.id = s.participant_id
    GROUP BY p.id, p.name
    ORDER BY total_score DESC
");
$stmt->execute();
$result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();

echo json_encode($result);
?>