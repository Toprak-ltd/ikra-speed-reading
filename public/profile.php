<?php
require __DIR__ . '/../app/db.php';
session_start();

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
$user_id = $_SESSION['user_id'];

// Avatar Güncelleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['avatar'])) {
    $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
    $stmt->execute([$_POST['avatar'], $user_id]);
    header("Location: profile.php"); exit;
}

// Verileri Çek
$user = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$user->execute([$user_id]);
$user = $user->fetch();

// Rozetleri Çek
$badgesStmt = $pdo->prepare("
    SELECT b.*, ub.earned_at 
    FROM badges b 
    LEFT JOIN user_badges ub ON b.id = ub.badge_id AND ub.user_id = ?
    ORDER BY b.required_words, b.required_streak
");
$badgesStmt->execute([$user_id]);
$badges = $badgesStmt->fetchAll();

// Seviye Hesapla
$level = floor($user->total_words_read / 5000) + 1;
$progressPercent = ($user->total_words_read % 5000) / 5000 * 100;

require __DIR__ . '/../views/layout/header.php';
?>

<div class="min-h-screen bg-slate-50 pb-12 font-sans">
    <header class="bg-white shadow-sm border-b border-slate-200">
        <div class="max-w-6xl mx-auto py-4 px-6 flex justify-between items-center">
            <h1 class="text-xl font-bold text-slate-800"><i class="fas fa-user-astronaut mr-2 text-indigo-600"></i> Profilim</h1>
            <a href="dashboard.php" class="text-sm font-medium text-slate-500 hover:text-slate-900"><i class="fas fa-arrow-left mr-1"></i> Panele Dön</a>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-6 mt-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            <div class="md:col-span-1 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 text-center relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-24 bg-gradient-to-r from-indigo-500 to-purple-600"></div>
                    
                    <div class="relative inline-block mt-8">
                        <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=<?php echo $user->avatar; ?>" class="h-32 w-32 rounded-full bg-white p-1 border-4 border-white shadow-lg">
                        <div class="absolute bottom-1 right-1 bg-yellow-400 text-yellow-900 text-xs font-bold px-2 py-1 rounded-full border border-white shadow-sm">
                            Lv. <?php echo $level; ?>
                        </div>
                    </div>
                    
                    <h2 class="text-2xl font-bold text-slate-900 mt-4"><?php echo htmlspecialchars($user->username); ?></h2>
                    <p class="text-slate-500 text-sm"><?php echo htmlspecialchars($user->email); ?></p>
                    
                    <div class="mt-6 pt-6 border-t border-slate-100">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-3">Avatar Seç</h4>
                        <form method="POST" class="grid grid-cols-4 gap-2">
                            <?php foreach(['Felix', 'Aneka', 'Zoe', 'Jack', 'Bear', 'Molly', 'Cale', 'Buster'] as $av): ?>
                                <button type="submit" name="avatar" value="<?php echo $av; ?>" class="hover:scale-110 transition-transform rounded-full border-2 <?php echo $user->avatar == $av ? 'border-indigo-500 ring-2 ring-indigo-200' : 'border-transparent'; ?>">
                                    <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=<?php echo $av; ?>" class="w-full h-full rounded-full bg-slate-50">
                                </button>
                            <?php endforeach; ?>
                        </form>
                    </div>
                </div>
            </div>

            <div class="md:col-span-2 space-y-6">
                
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <div class="flex justify-between items-end mb-2">
                        <div>
                            <span class="text-xs font-bold text-indigo-500 uppercase tracking-wide">Seviye <?php echo $level; ?></span>
                            <div class="text-sm text-slate-500">Sonraki seviyeye: <span class="font-bold text-slate-800"><?php echo number_format(5000 - ($user->total_words_read % 5000)); ?></span> kelime</div>
                        </div>
                        <span class="text-xl font-bold text-slate-800">%<?php echo round($progressPercent); ?></span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-4 overflow-hidden shadow-inner">
                        <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-full rounded-full transition-all duration-1000 relative" style="width: <?php echo $progressPercent; ?>%">
                            <div class="absolute top-0 left-0 w-full h-full bg-white/20 animate-pulse"></div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl mr-4">
                            <i class="fas fa-book-reader"></i>
                        </div>
                        <div>
                            <div class="text-xs text-slate-400 font-bold uppercase">Toplam Okuma</div>
                            <div class="text-2xl font-bold text-slate-800"><?php echo number_format($user->total_words_read); ?></div>
                        </div>
                    </div>
                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center">
                        <div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-xl mr-4">
                            <i class="fas fa-fire"></i>
                        </div>
                        <div>
                            <div class="text-xs text-slate-400 font-bold uppercase">Günlük Seri</div>
                            <div class="text-2xl font-bold text-slate-800"><?php echo $user->current_streak; ?> Gün</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <h3 class="font-bold text-slate-800 mb-4 flex items-center">
                        <i class="fas fa-trophy text-yellow-500 mr-2"></i> Başarımlar & Rozetler
                    </h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <?php foreach($badges as $badge): ?>
                            <?php $isEarned = !empty($badge->earned_at); ?>
                            <div class="flex items-center p-4 rounded-xl border <?php echo $isEarned ? 'border-indigo-100 bg-indigo-50/50' : 'border-slate-100 bg-slate-50 opacity-60 grayscale'; ?>">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center text-xl shadow-sm mr-4 <?php echo $isEarned ? 'bg-white text-indigo-600' : 'bg-slate-200 text-slate-400'; ?>">
                                    <i class="<?php echo $badge->icon; ?>"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-sm <?php echo $isEarned ? 'text-indigo-900' : 'text-slate-600'; ?>">
                                        <?php echo htmlspecialchars($badge->name); ?>
                                    </h4>
                                    <p class="text-xs text-slate-500 mt-1"><?php echo htmlspecialchars($badge->description); ?></p>
                                    <?php if($isEarned): ?>
                                        <div class="text-[10px] text-indigo-400 font-medium mt-1">
                                            <i class="fas fa-check-circle mr-1"></i> Kazanıldı
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
        </div>
    </main>
</div>
</body>
</html>
