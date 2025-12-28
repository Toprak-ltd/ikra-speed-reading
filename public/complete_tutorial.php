<?php
require __DIR__ . '/../app/db.php';
session_start();

if (!isset($_SESSION['user_id'])) exit;

// Kullanıcının "Gördü" durumunu 1 yap
$stmt = $pdo->prepare("UPDATE users SET has_seen_tutorial = 1 WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);

echo "OK";
?>