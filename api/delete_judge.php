<?php
header('Content-Type: application/json');
include '../includes/db.php';

$judge_id = $_POST['judge_id'] ?? '';

if (empty($judge_id)) {
    echo json_encode(['success' => false, 'message' => 'Judge ID is required']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM judges WHERE id = ?");
$stmt->bind_param("s", $judge_id);
$success = $stmt->execute();
$stmt->close();
$conn->close();

echo json_encode(['success' => $success]);
?>