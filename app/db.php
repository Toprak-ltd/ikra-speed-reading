<?php
// Veritabanı dosyası "database" klasöründe "ikra.sqlite" adıyla oluşacak
$dbPath = __DIR__ . '/../database/ikra.sqlite';

try {
    $pdo = new PDO("sqlite:" . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
} catch (PDOException $e) {
    die("Veritabanı Hatası: " . $e->getMessage());
}
?>