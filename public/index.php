<?php
require __DIR__ . '/../app/db.php';
session_start();

// Kullanıcı zaten giriş yapmış mı?
$isLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="tr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IKRA - Yeni Nesil Hızlı Okuma Platformu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .hero-bg {
            background: radial-gradient(circle at top right, #e0e7ff, transparent 40%),
                        radial-gradient(circle at bottom left, #f3e8ff, transparent 40%);
        }
    </style>
</head>
<body class="text-slate-800 bg-white">

    <nav class="fixed w-full z-50 bg-white/80 backdrop-blur-md border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white text-xl shadow-lg shadow-indigo-200">
                    <i class="fas fa-book-reader"></i>
                </div>
                <span class="font-bold text-2xl tracking-tight text-slate-900">IKRA</span>
            </div>

            <div class="flex items-center gap-4">
                <?php if ($isLoggedIn): ?>
                    <a href="dashboard.php" class="hidden sm:inline-flex items-center px-6 py-2.5 bg-slate-900 text-white font-medium rounded-full hover:bg-slate-800 transition-all hover:shadow-lg transform hover:-translate-y-0.5">
                        Panele Dön <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                <?php else: ?>
                    <div class="hidden sm:flex items-center gap-4">
                        <a href="login.php" class="text-slate-600 font-medium hover:text-indigo-600 transition-colors">Giriş Yap</a>
                        <a href="register.php" class="inline-flex items-center px-6 py-2.5 bg-indigo-600 text-white font-medium rounded-full hover:bg-indigo-700 transition-all hover:shadow-lg shadow-indigo-200 transform hover:-translate-y-0.5">
                            Kayıt Ol
                        </a>
                    </div>
                <?php endif; ?>
                
                <!-- Mobile Menu Button -->
                <button id="mobile-menu-btn" class="sm:hidden text-slate-600 hover:text-indigo-600 transition-colors p-2">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu Overlay -->
        <div id="mobile-menu" class="fixed inset-0 bg-white z-50 transform translate-x-full transition-transform duration-300 sm:hidden flex flex-col">
            <div class="flex items-center justify-between p-6 border-b border-slate-100">
                <span class="font-bold text-2xl text-slate-900">Menü</span>
                <button id="close-menu-btn" class="text-slate-500 hover:text-red-500 transition-colors p-2">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <div class="flex flex-col p-6 space-y-4">
                <?php if ($isLoggedIn): ?>
                    <a href="dashboard.php" class="flex items-center justify-center px-6 py-4 bg-slate-900 text-white font-medium rounded-xl hover:bg-slate-800 transition-all shadow-lg">
                        Panele Dön <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                <?php else: ?>
                    <a href="login.php" class="text-center py-4 text-slate-600 font-medium hover:text-indigo-600 hover:bg-slate-50 rounded-xl transition-colors">Giriş Yap</a>
                    <a href="register.php" class="flex items-center justify-center px-6 py-4 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200">
                        Kayıt Ol
                    </a>
                <?php endif; ?>
                <a href="#features" class="text-center py-4 text-slate-600 font-medium hover:text-indigo-600 hover:bg-slate-50 rounded-xl transition-colors" onclick="document.getElementById('mobile-menu').classList.add('translate-x-full')">
                    Özellikler
                </a>
            </div>
        </div>
        <script>
            const menuBtn = document.getElementById('mobile-menu-btn');
            const closeBtn = document.getElementById('close-menu-btn');
            const mobileMenu = document.getElementById('mobile-menu');

            if(menuBtn) {
                menuBtn.addEventListener('click', () => {
                    mobileMenu.classList.remove('translate-x-full');
                    document.body.classList.add('overflow-hidden');
                });
            }

            if(closeBtn) {
                closeBtn.addEventListener('click', () => {
                    mobileMenu.classList.add('translate-x-full');
                    document.body.classList.remove('overflow-hidden');
                });
            }
        </script>
    </nav>

    <section class="pt-32 pb-20 lg:pt-48 lg:pb-32 hero-bg overflow-hidden relative">
        <div class="max-w-7xl mx-auto px-6 text-center relative z-10">
            <div class="inline-flex items-center px-4 py-2 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-700 text-sm font-semibold mb-8 animate-bounce">
                <span class="w-2 h-2 bg-indigo-600 rounded-full mr-2"></span> v3.0 Yayında: Yapay Zeka Destekli Analiz
            </div>
            
            <h1 class="text-5xl lg:text-7xl font-extrabold text-slate-900 leading-tight mb-8">
                Okuma Hızını <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600">Sınırların Ötesine</span> Taşı.
            </h1>
            
            <p class="text-xl text-slate-600 mb-10 max-w-2xl mx-auto leading-relaxed">
                Göz kaslarını eğit, PDF dosyalarını saniyeler içinde analiz et ve okuma hızını 3 kata kadar artır. Modern arayüz, bilimsel teknikler.
            </p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <?php if ($isLoggedIn): ?>
                    <a href="dashboard.php" class="w-full sm:w-auto px-8 py-4 bg-indigo-600 text-white text-lg font-bold rounded-2xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-200 flex items-center justify-center">
                        <i class="fas fa-play mr-2"></i> Egzersize Başla
                    </a>
                <?php else: ?>
                    <a href="register.php" class="w-full sm:w-auto px-8 py-4 bg-indigo-600 text-white text-lg font-bold rounded-2xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-200 flex items-center justify-center">
                        <i class="fas fa-rocket mr-2"></i> Ücretsiz Başla
                    </a>
                    <a href="#features" class="w-full sm:w-auto px-8 py-4 bg-white text-slate-700 border border-slate-200 text-lg font-bold rounded-2xl hover:bg-slate-50 transition-all flex items-center justify-center">
                        Keşfet
                    </a>
                <?php endif; ?>
            </div>

            <div class="mt-20 relative max-w-5xl mx-auto">
                <div class="absolute inset-0 bg-indigo-600 blur-3xl opacity-20 rounded-full"></div>
                <div class="relative bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
                    <div class="h-8 bg-slate-50 border-b border-slate-100 flex items-center px-4 space-x-2">
                        <div class="w-3 h-3 rounded-full bg-red-400"></div>
                        <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                        <div class="w-3 h-3 rounded-full bg-green-400"></div>
                    </div>
                    <div class="p-8 grid grid-cols-3 gap-6 opacity-80">
                        <div class="col-span-1 bg-slate-100 h-64 rounded-xl"></div>
                        <div class="col-span-2 space-y-4">
                            <div class="h-32 bg-indigo-50 rounded-xl border border-indigo-100"></div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="h-28 bg-slate-100 rounded-xl"></div>
                                <div class="h-28 bg-slate-100 rounded-xl"></div>
                            </div>
                        </div>
                    </div>
                    <div class="absolute inset-0 flex items-center justify-center bg-white/10 backdrop-blur-[2px]">
                        <span class="bg-white px-6 py-3 rounded-full shadow-lg font-bold text-slate-800 flex items-center">
                            <i class="fas fa-lock mr-2 text-indigo-600"></i> Kullanıcı Dostu Panel
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="features" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-slate-900 mb-4">Neden IKRA?</h2>
                <p class="text-slate-500 max-w-2xl mx-auto">Sıradan bir okuma aracı değil, tam kapsamlı bir bilişsel gelişim platformu.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-12">
                <div class="group p-8 rounded-3xl bg-slate-50 hover:bg-white hover:shadow-xl hover:shadow-indigo-100 transition-all border border-slate-100 duration-300">
                    <div class="w-14 h-14 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">RSVP Teknolojisi</h3>
                    <p class="text-slate-600 leading-relaxed">
                        Kelimeleri tek tek, optimum hızda göstererek göz hareketlerini minimize eder. 250 WPM'den 1000 WPM'e kadar çıkın.
                    </p>
                </div>

                <div class="group p-8 rounded-3xl bg-slate-50 hover:bg-white hover:shadow-xl hover:shadow-indigo-100 transition-all border border-slate-100 duration-300">
                    <div class="w-14 h-14 bg-orange-100 text-orange-600 rounded-2xl flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="fas fa-dumbbell"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Göz Laboratuvarı</h3>
                    <p class="text-slate-600 leading-relaxed">
                        6 farklı egzersiz modu ile göz kaslarınızı güçlendirin. Periferik görüşünüzü genişletin ve odaklanma sürenizi artırın.
                    </p>
                </div>

                <div class="group p-8 rounded-3xl bg-slate-50 hover:bg-white hover:shadow-xl hover:shadow-indigo-100 transition-all border border-slate-100 duration-300">
                    <div class="w-14 h-14 bg-purple-100 text-purple-600 rounded-2xl flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Akıllı Dosya Analizi</h3>
                    <p class="text-slate-600 leading-relaxed">
                        PDF veya TXT dosyalarınızı yükleyin. Sistemimiz gereksiz başlıkları temizler ve sadece saf metni size sunar.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-slate-900 text-slate-400 py-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center">
            <div class="flex items-center gap-3 mb-4 md:mb-0">
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white text-sm">
                    <i class="fas fa-book-reader"></i>
                </div>
                <span class="font-bold text-xl text-white">IKRA</span>
            </div>
            <div class="text-sm">
                &copy; <?php echo date("Y"); ?> IKRA Proje. Tüm hakları saklıdır.
            </div>
            <div class="flex gap-6 mt-4 md:mt-0">
                <a href="#" class="hover:text-white transition-colors"><i class="fab fa-github"></i></a>
                <a href="#" class="hover:text-white transition-colors"><i class="fab fa-twitter"></i></a>
                <a href="#" class="hover:text-white transition-colors"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </footer>

</body>
</html>