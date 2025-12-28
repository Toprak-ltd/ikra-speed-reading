<?php
require __DIR__ . '/../app/db.php';
session_start();

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
$user_id = $_SESSION['user_id'];

// Güvenlik Kilidi
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
if (!$user) { session_destroy(); header("Location: login.php"); exit; }

// Metinleri Çek
$stmtTexts = $pdo->prepare("SELECT * FROM texts WHERE user_id = ? ORDER BY created_at DESC");
$stmtTexts->execute([$user_id]);
$texts = $stmtTexts->fetchAll();

// İstatistikler
$totalWords = $user->total_words_read;
$streak = $user->current_streak;
?>

<!DOCTYPE html>
<html lang="tr" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IKRA Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.css"/>

    <style>
        body { font-family: 'Inter', sans-serif; }
        .hover-lift { transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); }
        .hover-lift:hover { transform: translateY(-4px); }
    </style>
</head>
<body class="h-full flex overflow-hidden">

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/50 z-40 hidden opacity-0 transition-opacity duration-300 md:hidden glass"></div>

    <aside id="sidebar-menu" class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-slate-200 flex flex-col transform -translate-x-full md:translate-x-0 transition-transform duration-300 md:relative shadow-2xl md:shadow-none">
        <div class="h-20 flex items-center px-8 border-b border-slate-100">
            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-200">
                <i class="fas fa-book-open"></i>
            </div>
            <span class="ml-3 font-bold text-xl text-slate-800 tracking-tight">IKRA</span>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="dashboard.php" class="flex items-center px-4 py-3 bg-indigo-50 text-indigo-700 rounded-xl font-medium transition-colors">
                <i class="fas fa-th-large w-6"></i> Ana Panel
            </a>
            <a href="add_text.php" id="btn-add-text-sidebar" class="flex items-center px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-slate-900 rounded-xl font-medium transition-colors">
                <i class="fas fa-plus-circle w-6"></i> Metin Ekle
            </a>
            <a href="exercises.php" id="btn-exercises" class="flex items-center px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-slate-900 rounded-xl font-medium transition-colors">
                <i class="fas fa-dumbbell w-6"></i> Egzersizler
            </a>
            <a href="profile.php" id="btn-profile" class="flex items-center px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-slate-900 rounded-xl font-medium transition-colors">
                <i class="fas fa-user-circle w-6"></i> Profilim
            </a>
        </nav>

        <div class="p-4 border-t border-slate-100">
            <a href="logout.php" class="flex items-center px-4 py-3 text-red-500 hover:bg-red-50 rounded-xl font-medium transition-colors">
                <i class="fas fa-sign-out-alt w-6"></i> Çıkış Yap
            </a>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-full overflow-hidden bg-slate-50 relative">
        <div class="absolute top-0 left-0 w-full h-96 bg-gradient-to-b from-indigo-50 to-transparent -z-10"></div>

        <div class="flex-1 overflow-y-auto p-8 lg:p-12">
            
            <!-- Mobile Header with Toggle -->
            <div class="md:hidden flex items-center justify-between mb-8">
                <div class="flex items-center">
                    <div class="w-8 h-8 mr-3 bg-indigo-600 rounded-lg flex items-center justify-center text-white text-sm shadow-md">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <span class="font-bold text-lg text-slate-800 tracking-tight">IKRA</span>
                </div>
                <button id="sidebar-toggle" class="text-slate-500 hover:text-indigo-600 transition-colors p-2 bg-white rounded-lg border border-slate-200 shadow-sm">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>

            <div class="flex justify-between items-end mb-10">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">Hoş geldin, <?php echo htmlspecialchars($user->username); ?> 👋</h1>
                    <p class="text-slate-500 mt-2 font-medium">Bugün zihnini geliştirmek için harika bir gün.</p>
                </div>
            </div>

            <div id="stats-area" class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover-lift relative overflow-hidden group">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-indigo-50 rounded-full -mr-10 -mt-10 group-hover:scale-110 transition-transform"></div>
                    <div class="relative z-10">
                        <div class="text-slate-500 text-sm font-semibold uppercase tracking-wider mb-1">Toplam Kelime</div>
                        <div class="text-4xl font-bold text-slate-800"><?php echo number_format($totalWords); ?></div>
                        <div class="mt-4 flex items-center text-sm text-green-600 font-medium">
                            <i class="fas fa-arrow-up mr-1"></i> Gelişiyorsun!
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover-lift relative overflow-hidden group">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-orange-50 rounded-full -mr-10 -mt-10 group-hover:scale-110 transition-transform"></div>
                    <div class="relative z-10">
                        <div class="text-slate-500 text-sm font-semibold uppercase tracking-wider mb-1">Günlük Seri</div>
                        <div class="text-4xl font-bold text-slate-800"><?php echo $streak; ?> <span class="text-xl text-slate-400">Gün</span></div>
                        <div class="mt-4 flex items-center text-sm text-orange-600 font-medium">
                            <i class="fas fa-fire mr-1"></i> Seriyi bozma!
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover-lift relative overflow-hidden group">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-blue-50 rounded-full -mr-10 -mt-10 group-hover:scale-110 transition-transform"></div>
                    <div class="relative z-10">
                        <div class="text-slate-500 text-sm font-semibold uppercase tracking-wider mb-1">Kütüphane</div>
                        <div class="text-4xl font-bold text-slate-800"><?php echo count($texts); ?> <span class="text-xl text-slate-400">Metin</span></div>
                        <div class="mt-4 flex items-center text-sm text-blue-600 font-medium cursor-pointer" onclick="window.location='add_text.php'">
                            <i class="fas fa-plus mr-1"></i> Yeni Ekle
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-slate-800">Okuma Listem</h2>
                <a href="add_text.php" id="btn-add-text-main" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-200">
                    <i class="fas fa-plus mr-2"></i> Yeni Metin
                </a>
            </div>

            <div id="library-area">
                <?php if (count($texts) > 0): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        <?php foreach ($texts as $text): ?>
                            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:shadow-md transition-all group flex flex-col h-full">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                    <span class="text-xs font-medium text-slate-400"><?php echo date("d.m.Y", strtotime($text->created_at)); ?></span>
                                </div>
                                
                                <h3 class="text-lg font-bold text-slate-900 mb-2 line-clamp-1 group-hover:text-indigo-600 transition-colors">
                                    <?php echo htmlspecialchars($text->title); ?>
                                </h3>
                                <p class="text-slate-500 text-sm leading-relaxed mb-6 line-clamp-3 flex-1">
                                    <?php echo htmlspecialchars(mb_substr($text->content, 0, 150)); ?>...
                                </p>
                                
                                <div class="pt-4 border-t border-slate-50 flex items-center justify-between">
                                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Hazır</span>
                                    <a href="read.php?id=<?php echo $text->id; ?>" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 flex items-center">
                                        Başla <i class="fas fa-arrow-right ml-2"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="rounded-3xl border-2 border-dashed border-slate-200 bg-slate-50/50 p-12 text-center">
                        <div class="mx-auto w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-sm mb-4">
                            <i class="fas fa-layer-group text-slate-400 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900">Kütüphanen henüz boş</h3>
                        <p class="text-slate-500 mt-1 max-w-sm mx-auto">Okuma hızını geliştirmek için ilk metnini ekleyerek yolculuğa başla.</p>
                        <a href="add_text.php" class="inline-block mt-6 px-6 py-3 bg-white border border-slate-200 text-slate-700 font-medium rounded-xl hover:bg-slate-50 transition-colors">
                            İlk Metni Oluştur
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            
        </div>
        
    </main>
    

    <script src="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.js.iife.js"></script>

    <script>
        // Kullanıcı daha önce turu gördü mü? (PHP'den gelen veri)
        const hasSeenTutorial = <?php echo $user->has_seen_tutorial; ?>;

        if (hasSeenTutorial === 0) {
            
            const driver = window.driver.js.driver;

            const driverObj = driver({
                showProgress: true,
                animate: true,
                doneBtnText: 'Bitir ve Başla',
                nextBtnText: 'Sonraki',
                prevBtnText: 'Önceki',
                steps: [
                    { 
                        element: '#sidebar-menu', 
                        popover: { 
                            title: 'Hoş Geldin! 👋', 
                            description: 'IKRA Hızlı Okuma Platformuna hoş geldin. Burası senin komuta merkezin. Kısa bir tur atalım mı?', 
                            side: "right", 
                            align: 'start' 
                        } 
                    },
                    { 
                        element: '#stats-area', 
                        popover: { 
                            title: 'İstatistiklerini Takip Et', 
                            description: 'Burada okuduğun toplam kelime sayısını, günlük serini (streak) ve kazandığın başarıları görebilirsin.', 
                            side: "bottom", 
                            align: 'start' 
                        } 
                    },
                    { 
                        element: '#btn-add-text-sidebar', 
                        popover: { 
                            title: 'İlk Adım: Metin Ekle', 
                            description: 'Kendi metnini yapıştırabilir veya bir PDF dosyası yükleyebilirsin.', 
                            side: "right", 
                            align: 'start' 
                        } 
                    },
                    { 
                        element: '#btn-exercises', 
                        popover: { 
                            title: 'Göz Egzersizleri', 
                            description: 'Okumadan önce göz kaslarını ısıtmak için bu laboratuvarı kullan.', 
                            side: "right", 
                            align: 'start' 
                        } 
                    },
                    { 
                        element: '#library-area', 
                        popover: { 
                            title: 'Kütüphanen', 
                            description: 'Eklediğin tüm metinler burada listelenir. İstediğin zaman kaldığın yerden devam edebilirsin.', 
                            side: "top", 
                            align: 'center' 
                        } 
                    }
                ],
                onDestroyed: () => {
                    // Tur bitince veya kapatılınca veritabanına kaydet
                    fetch('complete_tutorial.php');
                }
            });

            // Turu Başlat
            driverObj.drive();
        }
    </script>
    
    <script>
        const sidebar = document.getElementById('sidebar-menu');
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebarOverlay = document.getElementById('sidebar-overlay');

        function toggleSidebar() {
            const isClosed = sidebar.classList.contains('-translate-x-full');
            if (isClosed) {
                sidebar.classList.remove('-translate-x-full');
                sidebarOverlay.classList.remove('hidden');
                setTimeout(() => sidebarOverlay.classList.remove('opacity-0'), 10);
                document.body.classList.add('overflow-hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                sidebarOverlay.classList.add('opacity-0');
                setTimeout(() => sidebarOverlay.classList.add('hidden'), 300);
                document.body.classList.remove('overflow-hidden');
            }
        }

        if(sidebarToggle) {
            sidebarToggle.addEventListener('click', toggleSidebar);
            sidebarOverlay.addEventListener('click', toggleSidebar);
        }
    </script>
</body>
</html>
