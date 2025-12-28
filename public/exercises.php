<?php
require __DIR__ . '/../app/db.php';
session_start();

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

require __DIR__ . '/../views/layout/header.php';
?>

<div class="min-h-screen bg-slate-50 flex flex-col font-sans">
    
    <header class="bg-white shadow-sm border-b border-slate-200 sticky top-0 z-30">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            <h1 class="text-xl font-bold text-slate-900 flex items-center">
                <i class="fas fa-dumbbell mr-3 text-indigo-600"></i> Göz Egzersiz Laboratuvarı
            </h1>
            <a href="dashboard.php" class="text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors">
                <i class="fas fa-arrow-left mr-1"></i> Panele Dön
            </a>
        </div>
    </header>

    <main class="flex-grow container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <div id="selection-screen" class="max-w-6xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-extrabold text-slate-900">Göz Kaslarını Antrene Et</h2>
                <p class="mt-3 text-lg text-slate-600 max-w-2xl mx-auto">Hızlı okuma sadece beyin işi değildir; güçlü göz kasları ve geniş bir görme alanı gerektirir. Bugün ne çalışmak istersin?</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                
                <div onclick="startExercise('horizontal')" class="group bg-white rounded-2xl shadow-sm border border-slate-200 p-8 cursor-pointer hover:shadow-xl hover:border-indigo-300 transition-all transform hover:-translate-y-1 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-50 rounded-full -mr-8 -mt-8 group-hover:scale-110 transition-transform"></div>
                    <i class="fas fa-arrows-alt-h text-4xl text-indigo-600 mb-6 relative z-10"></i>
                    <h3 class="text-xl font-bold text-slate-900">Yatay Tarama</h3>
                    <p class="mt-2 text-sm text-slate-500">Satır takibini hızlandırır, geri dönüşleri azaltır.</p>
                </div>

                <div onclick="startExercise('vertical')" class="group bg-white rounded-2xl shadow-sm border border-slate-200 p-8 cursor-pointer hover:shadow-xl hover:border-indigo-300 transition-all transform hover:-translate-y-1 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-green-50 rounded-full -mr-8 -mt-8 group-hover:scale-110 transition-transform"></div>
                    <i class="fas fa-arrows-alt-v text-4xl text-green-600 mb-6 relative z-10"></i>
                    <h3 class="text-xl font-bold text-slate-900">Dikey Tarama</h3>
                    <p class="mt-2 text-sm text-slate-500">Blok okuma ve sütun tarama yeteneğini geliştirir.</p>
                </div>

                <div onclick="startExercise('box')" class="group bg-white rounded-2xl shadow-sm border border-slate-200 p-8 cursor-pointer hover:shadow-xl hover:border-indigo-300 transition-all transform hover:-translate-y-1 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-orange-50 rounded-full -mr-8 -mt-8 group-hover:scale-110 transition-transform"></div>
                    <i class="far fa-square text-4xl text-orange-500 mb-6 relative z-10"></i>
                    <h3 class="text-xl font-bold text-slate-900">Kutu Genişletme</h3>
                    <p class="mt-2 text-sm text-slate-500">Periferik (çevresel) görüş alanını sınırlarına kadar zorlar.</p>
                </div>

                <div onclick="startExercise('zoom')" class="group bg-white rounded-2xl shadow-sm border border-slate-200 p-8 cursor-pointer hover:shadow-xl hover:border-indigo-300 transition-all transform hover:-translate-y-1 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-pink-50 rounded-full -mr-8 -mt-8 group-hover:scale-110 transition-transform"></div>
                    <i class="fas fa-bullseye text-4xl text-pink-500 mb-6 relative z-10"></i>
                    <h3 class="text-xl font-bold text-slate-900">Derinlik & Odak</h3>
                    <p class="mt-2 text-sm text-slate-500">Göz merceğinin esnekliğini artırır, yorgunluğu alır.</p>
                </div>

                <div onclick="startExercise('circle')" class="group bg-white rounded-2xl shadow-sm border border-slate-200 p-8 cursor-pointer hover:shadow-xl hover:border-indigo-300 transition-all transform hover:-translate-y-1 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-purple-50 rounded-full -mr-8 -mt-8 group-hover:scale-110 transition-transform"></div>
                    <i class="fas fa-circle-notch text-4xl text-purple-500 mb-6 relative z-10"></i>
                    <h3 class="text-xl font-bold text-slate-900">Dairesel Takip</h3>
                    <p class="mt-2 text-sm text-slate-500">Göz kaslarının tam koordinasyonla çalışmasını sağlar.</p>
                </div>

                <div onclick="startExercise('random')" class="group bg-white rounded-2xl shadow-sm border border-slate-200 p-8 cursor-pointer hover:shadow-xl hover:border-indigo-300 transition-all transform hover:-translate-y-1 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 rounded-full -mr-8 -mt-8 group-hover:scale-110 transition-transform"></div>
                    <i class="fas fa-bolt text-4xl text-blue-500 mb-6 relative z-10"></i>
                    <h3 class="text-xl font-bold text-slate-900">Hızlı Refleks</h3>
                    <p class="mt-2 text-sm text-slate-500">Kelimeler arası ani sıçrama (saccade) hızını artırır.</p>
                </div>

            </div>
        </div>

        <div id="exercise-screen" class="hidden max-w-5xl mx-auto h-full flex-col">
            
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 id="exercise-title" class="text-2xl font-bold text-slate-800">Egzersiz</h3>
                    <p class="text-slate-500 text-sm">Gözlerinle topu takip et, başını sabit tut.</p>
                </div>
                <div class="text-2xl font-mono bg-white px-6 py-2 rounded-xl shadow-sm border border-slate-200 text-indigo-600 font-bold">
                    <span id="timer">0</span> <span class="text-xs text-slate-400 font-normal">sn</span>
                </div>
            </div>

            <div class="relative bg-white rounded-3xl shadow-inner border border-slate-200 w-full h-[450px] overflow-hidden mb-8">
                <div class="absolute inset-0 grid grid-cols-2 grid-rows-2 pointer-events-none">
                    <div class="border-r border-b border-slate-100"></div>
                    <div class="border-b border-slate-100"></div>
                    <div class="border-r border-slate-100"></div>
                    <div></div>
                </div>

                <div id="ball" class="absolute w-8 h-8 bg-indigo-600 rounded-full shadow-lg shadow-indigo-500/50 z-10 flex items-center justify-center">
                    <div class="w-2 h-2 bg-white rounded-full opacity-50"></div>
                </div>
            </div>

            <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-6 flex items-start shadow-sm mb-6">
                <div class="flex-shrink-0 mr-4">
                    <div class="w-10 h-10 bg-white text-indigo-600 rounded-full flex items-center justify-center shadow-sm">
                        <i class="fas fa-info"></i>
                    </div>
                </div>
                <div>
                    <h4 class="text-lg font-bold text-indigo-900 mb-1">Bu Egzersiz Ne İşe Yarar?</h4>
                    <p id="exercise-desc" class="text-indigo-700 leading-relaxed text-sm">
                        </p>
                </div>
            </div>

            <div class="text-center">
                <button onclick="stopExercise()" class="inline-flex items-center px-8 py-4 border border-transparent text-lg font-medium rounded-full text-white bg-red-600 hover:bg-red-700 shadow-lg hover:shadow-red-500/30 transition-all transform hover:scale-105">
                    <i class="fas fa-stop mr-2"></i> Egzersizi Bitir
                </button>
            </div>
        </div>

    </main>
</div>

<style>
    /* 1. YATAY */
    @keyframes horizontalMove {
        0%, 100% { left: 5%; top: 50%; transform: translate(0, -50%); }
        50% { left: 95%; top: 50%; transform: translate(-100%, -50%); }
    }
    .anim-horizontal { animation: horizontalMove 3s infinite ease-in-out; }

    /* 2. DİKEY */
    @keyframes verticalMove {
        0%, 100% { top: 5%; left: 50%; transform: translate(-50%, 0); }
        50% { top: 95%; left: 50%; transform: translate(-50%, -100%); }
    }
    .anim-vertical { animation: verticalMove 3s infinite ease-in-out; }

    /* 3. KUTU */
    @keyframes boxMove {
        0% { left: 5%; top: 5%; transform: translate(0,0); }
        25% { left: 95%; top: 5%; transform: translate(-100%, 0); }
        50% { left: 95%; top: 95%; transform: translate(-100%, -100%); }
        75% { left: 5%; top: 95%; transform: translate(0, -100%); }
        100% { left: 5%; top: 5%; transform: translate(0,0); }
    }
    .anim-box { animation: boxMove 6s infinite linear; }

    /* 4. ZOOM (ODAK) */
    @keyframes zoomMove {
        0%, 100% { left: 50%; top: 50%; transform: translate(-50%, -50%) scale(0.5); opacity: 0.5; }
        50% { left: 50%; top: 50%; transform: translate(-50%, -50%) scale(4); opacity: 1; }
    }
    .anim-zoom { animation: zoomMove 4s infinite ease-in-out; }

    /* 5. DAİRESEL */
    @keyframes circleMove {
        0% { left: 50%; top: 50%; transform: rotate(0deg) translate(150px) rotate(0deg); }
        100% { left: 50%; top: 50%; transform: rotate(360deg) translate(150px) rotate(-360deg); }
    }
    .anim-circle { animation: circleMove 4s infinite linear; }

    /* REFLEKS (JS ile yapılacak) */
</style>

<script>
    let timerInterval;
    let seconds = 0;
    let currentType = '';
    let randomInterval; // Refleks modu için

    // BİLGİ BANKASI
    const exerciseInfo = {
        'horizontal': "Bu egzersiz **Sakkadik (Sıçrayıcı) Göz Hareketlerini** geliştirir. Okurken gözümüz satır üzerinde akıcı bir şekilde kaymaz, kelimeden kelimeye sıçrar. Bu kasları güçlendirmek, satır takibini hızlandırır ve okurken satır atlama veya geri dönme (regresyon) sorunlarını azaltır.",
        'vertical': "Dikey tarama, **Blok Okuma** tekniğinin temelidir. Gözün dikey eksendeki hareket kabiliyetini artırarak, sayfayı satır satır değil, bloklar halinde veya yukarıdan aşağıya sütunlar halinde taramanızı sağlar. Özellikle gazete ve dar sütunlu metinlerde hızı ciddi oranda artırır.",
        'box': "**Periferik (Çevresel) Görme** alanınızı genişletir. Gözünüzü ekranın en uç noktalarına gitmeye zorlayarak, odak noktanızın dışındaki kelimeleri de algılamanızı sağlar. Bu sayede bir bakışta tek bir kelimeyi değil, 3-4 kelimeyi aynı anda görebilirsiniz.",
        'zoom': "Göz merceğinin **Akomodasyon (Uyum)** yeteneğini çalıştırır. Tıpkı bir kameranın odaklanması gibi, gözümüz de uzak ve yakın nesneler arasında geçiş yapar. Bu egzersiz göz yorgunluğunu (astenopi) azaltır ve uzun okumalarda odağın bozulmasını engeller.",
        'circle': "**Pürüzsüz Takip (Smooth Pursuit)** hareketini geliştirir. Göz kaslarının birbiriyle koordineli çalışmasını sağlar. Kesik kesik değil, yağ gibi akan bir okuma deneyimi için göz kaslarının esnekliğini artırır.",
        'random': "**Fiksasyon (Sabitleme)** hızını artırır. Gözün aniden beliren bir hedefe ne kadar hızlı kilitlenebildiğini test eder. Hızlı okurken beynin kelimeyi görüp tanıması için gereken süreyi kısaltır."
    };

    function startExercise(type) {
        currentType = type;
        
        // Ekran Değiştir
        document.getElementById('selection-screen').classList.add('hidden');
        document.getElementById('exercise-screen').classList.remove('hidden');
        document.getElementById('exercise-screen').classList.add('flex');

        // Başlık ve Açıklamayı Doldur (Markdown basit çeviri)
        const titles = {
            'horizontal': 'Yatay Tarama', 'vertical': 'Dikey Tarama', 'box': 'Kutu Genişletme',
            'zoom': 'Odaklanma (Zoom)', 'circle': 'Dairesel Takip', 'random': 'Refleks Çalışması'
        };
        document.getElementById('exercise-title').innerText = titles[type];
        
        // Açıklamayı yaz ve **kalın** yerleri bold yap
        let desc = exerciseInfo[type];
        desc = desc.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        document.getElementById('exercise-desc').innerHTML = desc;

        // Topu Ayarla
        const ball = document.getElementById('ball');
        ball.className = 'absolute w-8 h-8 bg-indigo-600 rounded-full shadow-lg shadow-indigo-500/50 z-10 flex items-center justify-center'; // Reset classes
        ball.style = ''; // Reset inline styles

        // Refleks modu temizliği
        if (randomInterval) clearInterval(randomInterval);

        // Animasyon Ata
        if (type === 'random') {
            // Refleks modu özeldir, CSS değil JS ile çalışır
            moveRandomly();
            randomInterval = setInterval(moveRandomly, 800); // Her 0.8 saniyede bir yer değiştir
        } else {
            ball.classList.add('anim-' + type);
        }

        // Sayacı Başlat
        seconds = 0;
        document.getElementById('timer').innerText = seconds;
        timerInterval = setInterval(() => {
            seconds++;
            document.getElementById('timer').innerText = seconds;
        }, 1000);
    }

    function moveRandomly() {
        const ball = document.getElementById('ball');
        const container = ball.parentElement;
        const maxX = container.clientWidth - 40;
        const maxY = container.clientHeight - 40;

        const randomX = Math.floor(Math.random() * maxX);
        const randomY = Math.floor(Math.random() * maxY);

        ball.style.left = randomX + 'px';
        ball.style.top = randomY + 'px';
        ball.style.transition = 'all 0.1s ease-out'; // Hızlı geçiş
    }

    function stopExercise() {
        clearInterval(timerInterval);
        if (randomInterval) clearInterval(randomInterval);
        
        // Veritabanına Kaydet
        fetch('save_exercise.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ type: currentType, duration: seconds })
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                // Şık bir uyarı yerine sayfayı yenilemek yeterli, ama biraz bekleyelim
                location.reload(); 
            } else {
                alert('Hata: ' + data.message);
                location.reload();
            }
        });
    }
</script>

