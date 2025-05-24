<?php
header('Content-Type: application/json');
session_start();
include '../includes/db.php';

$judge_id = $_POST['judge_id'] ?? '';

if (empty($judge_id)) {
    echo json_encode(['success' => false, 'message' => 'Judge ID is required']);
    exit;
}

$stmt = $conn->prepare("SELECT id, name FROM judges WHERE id = ?");
$stmt->bind_param("s", $judge_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $judge = $result->fetch_assoc();
    $_SESSION['judge_id'] = $judge['id'];
    $_SESSION['judge_name'] = $judge['name'];
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid Judge ID']);
}

$stmt->close();
$conn->close();
?>