<?php
require __DIR__ . '/../app/db.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    echo json_encode(['status' => 'error']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) exit;

$user_id = $_SESSION['user_id'];
$text_id = $input['text_id'];
$position = $input['progress'];
$wpm = $input['wpm'];
$completed = isset($input['completed']) ? $input['completed'] : 0;

try {
    $pdo->beginTransaction();

    // 1. İlerlemeyi Kaydet
    $stmt = $pdo->prepare("SELECT last_position FROM reading_progress WHERE user_id = ? AND text_id = ?");
    $stmt->execute([$user_id, $text_id]);
    $record = $stmt->fetch();
    $old_pos = $record ? $record->last_position : 0;
    
    $diff = $position - $old_pos;
    if ($diff < 0) $diff = 0;

    if ($record) {
        $sql = "UPDATE reading_progress SET last_position = ?, wpm_setting = ?, completed = ?, last_read_at = datetime('now') WHERE user_id = ? AND text_id = ?";
        $pdo->prepare($sql)->execute([$position, $wpm, $completed, $user_id, $text_id]);
    } else {
        $sql = "INSERT INTO reading_progress (user_id, text_id, last_position, wpm_setting, completed) VALUES (?, ?, ?, ?, ?)";
        $pdo->prepare($sql)->execute([$user_id, $text_id, $position, $wpm, $completed]);
    }

    // 2. Kullanıcı İstatistiklerini Güncelle (STREAK ve TOPLAM KELİME)
    // Streak Mantığı: Eğer son aktivite tarihi bugünden farklıysa kontrol et
    $userStmt = $pdo->prepare("SELECT total_words_read, current_streak, last_activity_date FROM users WHERE id = ?");
    $userStmt->execute([$user_id]);
    $user = $userStmt->fetch();

    $today = date('Y-m-d');
    $lastActivity = $user->last_activity_date ? date('Y-m-d', strtotime($user->last_activity_date)) : null;
    $newStreak = $user->current_streak;

    if ($lastActivity !== $today) {
        // Eğer son aktivite dünküyse streak artır, yoksa (daha eskiyse) 1 yap
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        if ($lastActivity === $yesterday) {
            $newStreak++;
        } else {
            $newStreak = 1;
        }
    }

    // Kullanıcıyı güncelle
    $newTotalWords = $user->total_words_read + $diff;
    $updUser = $pdo->prepare("UPDATE users SET total_words_read = ?, current_streak = ?, last_activity_date = ? WHERE id = ?");
    $updUser->execute([$newTotalWords, $newStreak, $today, $user_id]);

    // 3. ROZET KONTROLÜ (Gamification Engine)
    $newBadges = [];
    
    // Henüz kazanılmamış rozetleri getir
    $sqlBadges = "SELECT * FROM badges WHERE id NOT IN (SELECT badge_id FROM user_badges WHERE user_id = ?)";
    $stmtBadges = $pdo->prepare($sqlBadges);
    $stmtBadges->execute([$user_id]);
    $potentialBadges = $stmtBadges->fetchAll();

    foreach ($potentialBadges as $badge) {
        $earned = false;

        // Kelime Hedefi Kontrolü
        if ($badge->required_words > 0 && $newTotalWords >= $badge->required_words) {
            $earned = true;
        }
        // Seri (Streak) Hedefi Kontrolü
        if ($badge->required_streak > 0 && $newStreak >= $badge->required_streak) {
            $earned = true;
        }

        if ($earned) {
            $pdo->prepare("INSERT INTO user_badges (user_id, badge_id) VALUES (?, ?)")->execute([$user_id, $badge->id]);
            $newBadges[] = $badge->name;
        }
    }

    $pdo->commit();
    echo json_encode(['status' => 'success', 'new_badges' => $newBadges]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['status' => 'error']);
}
?>