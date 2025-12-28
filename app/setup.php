<?php
require 'db.php';

try {
    // 1. Kullanıcılar
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    avatar TEXT DEFAULT 'avatar1.png',
    total_words_read INTEGER DEFAULT 0,
    current_streak INTEGER DEFAULT 0,
    last_activity_date DATE,
    has_seen_tutorial INTEGER DEFAULT 0, -- YENİ EKLENDİ (0: Görmedi, 1: Gördü)
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

    // 2. Metinler
    $pdo->exec("CREATE TABLE IF NOT EXISTS texts (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        title TEXT NOT NULL,
        content TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(user_id) REFERENCES users(id)
    )");

    // 3. İlerleme
    $pdo->exec("CREATE TABLE IF NOT EXISTS reading_progress (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        text_id INTEGER NOT NULL,
        last_position INTEGER DEFAULT 0,
        wpm_setting INTEGER DEFAULT 200,
        completed INTEGER DEFAULT 0,
        last_read_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(user_id) REFERENCES users(id),
        FOREIGN KEY(text_id) REFERENCES texts(id)
    )");

    // 4. Egzersiz Logları
    $pdo->exec("CREATE TABLE IF NOT EXISTS exercise_logs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        exercise_type TEXT NOT NULL,
        duration_seconds INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(user_id) REFERENCES users(id)
    )");

    // --- YENİ EKLENENLER (OYUNLAŞTIRMA) ---

    // 5. Rozet Tanımları
    $pdo->exec("CREATE TABLE IF NOT EXISTS badges (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        description TEXT NOT NULL,
        icon TEXT NOT NULL,
        required_words INTEGER DEFAULT 0,
        required_streak INTEGER DEFAULT 0
    )");

    // 6. Kazanılan Rozetler
    $pdo->exec("CREATE TABLE IF NOT EXISTS user_badges (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        badge_id INTEGER NOT NULL,
        earned_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(user_id) REFERENCES users(id),
        FOREIGN KEY(badge_id) REFERENCES badges(id)
    )");

    // Varsayılan Rozetleri Ekle (Eğer yoksa)
    $badges = [
        ['Başlangıç Adımı', 'İlk 1.000 kelimeyi okudun.', 'fas fa-shoe-prints', 1000, 0],
        ['Kitap Kurdu', 'Toplam 10.000 kelime okudun.', 'fas fa-book-reader', 10000, 0],
        ['Bilgi Canavarı', 'Toplam 50.000 kelimeye ulaştın!', 'fas fa-dragon', 50000, 0],
        ['İstikrarlı Okur', '3 gün üst üste okuma yaptın.', 'fas fa-fire', 0, 3],
        ['Haftalık Maraton', '7 gün boyunca seriyi bozmadın!', 'fas fa-medal', 0, 7]
    ];

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM badges WHERE name = ?");
    $insert = $pdo->prepare("INSERT INTO badges (name, description, icon, required_words, required_streak) VALUES (?, ?, ?, ?, ?)");

    foreach ($badges as $badge) {
        $stmt->execute([$badge[0]]);
        if ($stmt->fetchColumn() == 0) {
            $insert->execute($badge);
        }
    }

    echo "✅ Veritabanı ve Oyunlaştırma Modülü Hazır!";

} catch (PDOException $e) {
    echo "Hata: " . $e->getMessage();
}
?>