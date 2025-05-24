<?php
header('Content-Type: application/json');
include '../includes/db.php';

$judge_id = $_POST['judge_id'] ?? '';
$judge_name = $_POST['judge_name'] ?? '';

if (empty($judge_id) || empty($judge_name)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

if (strlen($judge_id) > 50 || strlen($judge_name) > 100) {
    echo json_encode(['success' => false, 'message' => 'Input exceeds maximum length']);
    exit;
}

$check_stmt = $conn->prepare("SELECT id FROM judges WHERE id = ?");
$check_stmt->bind_param("s", $judge_id);
$check_stmt->execute();
if ($check_stmt->get_result()->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Judge ID already exists']);
    $check_stmt->close();
    exit;
}
$check_stmt->close();

$stmt = $conn->prepare("INSERT INTO judges (id, name) VALUES (?, ?)");
$stmt->bind_param("ss", $judge_id, $judge_name);
$success = $stmt->execute();
$stmt->close();
$conn->close();

echo json_encode(['success' => $success]);
?>