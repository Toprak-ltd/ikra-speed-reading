<?php
require __DIR__ . '/../app/db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$user_id = $_SESSION['user_id'];
$type = $input['type'];
$duration = $input['duration'];

// 10 saniyeden azsa kaydetmeyelim (yanlışlıkla açmıştır)
if ($duration < 5) {
    echo json_encode(['status' => 'success', 'message' => 'Too short']);
    exit;
}

try {
    $sql = "INSERT INTO exercise_logs (user_id, exercise_type, duration_seconds) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id, $type, $duration]);

    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>