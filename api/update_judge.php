<?php
header('Content-Type: application/json');
include '../includes/db.php';

$judge_id = $_POST['judge_id'] ?? '';
$judge_name = $_POST['judge_name'] ?? '';

if (empty($judge_id) || empty($judge_name)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

if (strlen($judge_name) > 100) {
    echo json_encode(['success' => false, 'message' => 'Name exceeds maximum length']);
    exit;
}

$stmt = $conn->prepare("UPDATE judges SET name = ? WHERE id = ?");
$stmt->bind_param("ss", $judge_name, $judge_id);
$success = $stmt->execute();
$stmt->close();
$conn->close();

echo json_encode(['success' => $success]);
?>