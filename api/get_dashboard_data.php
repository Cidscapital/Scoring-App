<?php
header('Content-Type: application/json');
include '../includes/db.php';

$counts = [];
$counts['total_judges'] = $conn->query("SELECT COUNT(*) FROM judges")->fetch_row()[0];
$counts['total_participants'] = $conn->query("SELECT COUNT(*) FROM participants")->fetch_row()[0];
$counts['total_scores'] = $conn->query("SELECT COUNT(*) FROM scores")->fetch_row()[0];

$stmt = $conn->prepare("SELECT id, name FROM judges");
$stmt->execute();
$judges = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$stmt = $conn->prepare("SELECT id, name FROM participants");
$stmt->execute();
$participants = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();

echo json_encode([
    'counts' => $counts,
    'judges' => $judges,
    'participants' => $participants
]);
?>