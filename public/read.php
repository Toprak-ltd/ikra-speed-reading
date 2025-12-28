<?php
require __DIR__ . '/../app/db.php';
session_start();

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
if (!isset($_GET['id'])) { header("Location: dashboard.php"); exit; }

$text_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Metni Çek
$stmt = $pdo->prepare("SELECT * FROM texts WHERE id = ? AND user_id = ?");
$stmt->execute([$text_id, $user_id]);
$text = $stmt->fetch();

if (!$text) { die("Metin bulunamadı."); }

// Kaldığı Yeri Çek (Kritik Kısım)
$progStmt = $pdo->prepare("SELECT last_position, wpm_setting FROM reading_progress WHERE user_id = ? AND text_id = ?");
$progStmt->execute([$user_id, $text_id]);
$progress = $progStmt->fetch();

// Varsayılan değerler (Eğer kayıt yoksa 0'dan başla)
$startPosition = $progress ? $progress->last_position : 0;
$savedWpm = $progress ? $progress->wpm_setting : 250;
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IKRA Zen Okuyucu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .highlight-char { color: #f87171; font-weight: 600; }
        .font-sans { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
        .font-serif { font-family: 'Merriweather', ui-serif, Georgia, serif; }
        .font-mono { font-family: 'Fira Code', ui-monospace, monospace; }
        .no-select { user-select: none; -webkit-user-select: none; }
        .ui-element { transition: opacity 0.4s ease-in-out, transform 0.4s ease; }
        .ui-hidden { opacity: 0; pointer-events: none; }
        input[type=range] { -webkit-appearance: none; background: transparent; }
        input[type=range]::-webkit-slider-thumb {
            -webkit-appearance: none; height: 16px; width: 16px; border-radius: 50%;
            background: #6366f1; cursor: pointer; margin-top: -6px; box-shadow: 0 0 10px rgba(99,102,241,0.5);
        }
        input[type=range]::-webkit-slider-runnable-track {
            width: 100%; height: 4px; cursor: pointer; background: rgba(255,255,255,0.2); border-radius: 2px;
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&family=Merriweather:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="h-screen flex flex-col overflow-hidden bg-gray-900 text-gray-100 no-select font-sans" id="body-area">

    <header id="ui-header" class="ui-element absolute top-0 left-0 w-full h-20 flex items-center justify-between px-8 bg-gradient-to-b from-black/60 to-transparent z-30">
        <div class="flex items-center space-x-4">
            <a href="dashboard.php" class="text-white/60 hover:text-white transition-colors p-2 rounded-full hover:bg-white/10">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-sm font-medium text-white/90 truncate w-64"><?php echo htmlspecialchars($text->title); ?></h1>
                <div class="text-xs text-white/50" id="progress-indicator">0% Tamamlandı</div>
            </div>
            <div id="save-status" class="text-xs text-green-400 opacity-0 transition-opacity">
                <i class="fas fa-save"></i>
            </div>
        </div>
        
        <button onclick="toggleSettings()" class="text-white/60 hover:text-white p-3 rounded-full hover:bg-white/10 transition-all">
            <i class="fas fa-sliders-h text-xl"></i>
        </button>
    </header>

    <div id="settings-panel" class="absolute top-0 right-0 h-full w-80 bg-black/80 backdrop-blur-xl border-l border-white/10 transform translate-x-full transition-transform duration-300 z-40 p-8 shadow-2xl">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-xl font-bold text-white">Görünüm</h2>
            <button onclick="toggleSettings()" class="text-white/50 hover:text-white"><i class="fas fa-times text-xl"></i></button>
        </div>
        
        <div class="mb-8">
            <label class="text-xs text-indigo-400 uppercase font-bold tracking-wider mb-3 block">Yazı Tipi</label>
            <div class="grid grid-cols-3 gap-2">
                <button onclick="setFont('sans')" class="py-2 rounded bg-white/5 hover:bg-white/10 text-sm border border-white/5">Sans</button>
                <button onclick="setFont('serif')" class="py-2 rounded bg-white/5 hover:bg-white/10 text-sm font-serif border border-white/5">Serif</button>
                <button onclick="setFont('mono')" class="py-2 rounded bg-white/5 hover:bg-white/10 text-sm font-mono border border-white/5">Mono</button>
            </div>
        </div>

        <div class="mb-8">
            <label class="text-xs text-indigo-400 uppercase font-bold tracking-wider mb-3 block">Boyut</label>
            <div class="flex items-center space-x-4">
                <span class="text-xs text-white/50">Aa</span>
                <input type="range" min="2" max="9" value="6" class="flex-1 accent-indigo-500" oninput="setSize(this.value)">
                <span class="text-xl text-white/50">Aa</span>
            </div>
        </div>

        <div class="mb-8">
            <label class="text-xs text-indigo-400 uppercase font-bold tracking-wider mb-3 block">Tema</label>
            <div class="flex space-x-4">
                <button onclick="setTheme('dark')" class="w-10 h-10 rounded-full bg-gray-900 border-2 border-indigo-500 shadow-lg ring-2 ring-offset-2 ring-offset-black ring-indigo-500"></button>
                <button onclick="setTheme('light')" class="w-10 h-10 rounded-full bg-gray-100 border border-gray-300"></button>
                <button onclick="setTheme('sepia')" class="w-10 h-10 rounded-full bg-[#f5e6d3] border border-amber-200"></button>
            </div>
        </div>

        <div class="space-y-4">
            <label class="flex items-center justify-between cursor-pointer group">
                <span class="text-sm text-white/80">Kırmızı Odak Harfi</span>
                <input type="checkbox" id="check-focus" checked class="accent-indigo-500 w-5 h-5 rounded" onchange="updateDisplay()">
            </label>
            <label class="flex items-center justify-between cursor-pointer group">
                <span class="text-sm text-white/80">Rehber Çizgiler</span>
                <input type="checkbox" id="check-guides" checked class="accent-indigo-500 w-5 h-5 rounded" onchange="toggleGuides()">
            </label>
        </div>
    </div>

    <main class="flex-grow flex items-center justify-center relative w-full h-full cursor-pointer" id="reader-area" ondblclick="rewind()">
        <div id="guide-h" class="absolute w-full h-px bg-white/10 top-1/2 transform -translate-y-1/2 pointer-events-none transition-opacity duration-300"></div>
        <div id="guide-v" class="absolute h-20 w-px bg-white/10 left-1/2 transform -translate-x-1/2 pointer-events-none transition-opacity duration-300"></div>

        <div id="word-display" class="text-7xl font-bold text-center px-4 leading-none text-white/90 z-10 transition-all duration-100">
            Hazır?
        </div>

        <div id="rewind-feedback" class="absolute top-1/2 left-1/4 transform -translate-y-1/2 -translate-x-1/2 text-white/20 text-8xl opacity-0 transition-opacity duration-200 pointer-events-none">
            <i class="fas fa-backward"></i>
        </div>
    </main>

    <footer id="ui-footer" class="ui-element absolute bottom-0 left-0 w-full h-24 bg-gradient-to-t from-black/80 to-transparent flex flex-col justify-end pb-6 px-8 z-30">
        <div class="max-w-4xl mx-auto w-full flex items-center justify-between gap-8">
            
            <div class="flex-1 flex flex-col justify-end group">
                <div class="flex justify-between text-xs font-bold text-indigo-400 mb-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <span>Yavaş</span>
                    <span id="wpm-display" class="text-white"><?php echo $savedWpm; ?> WPM</span>
                    <span>Hızlı</span>
                </div>
                <input type="range" id="wpm-slider" min="100" max="1000" step="10" value="<?php echo $savedWpm; ?>" class="w-full">
            </div>

            <button id="btn-play" onclick="togglePlay()" class="w-16 h-16 bg-white text-black rounded-full flex items-center justify-center text-2xl shadow-lg hover:scale-110 hover:bg-indigo-50 transition-all duration-200 flex-shrink-0">
                <i class="fas fa-play ml-1"></i>
            </button>

            <button onclick="rewind()" class="text-white/50 hover:text-white p-4 rounded-full hover:bg-white/10 transition-all" title="Geri Sar">
                <i class="fas fa-undo-alt text-xl"></i>
            </button>
        </div>
        
        <div class="absolute bottom-2 left-1/2 transform -translate-x-1/2 text-[10px] text-white/30 font-mono">
            [BOŞLUK] Başlat/Durdur &bull; [SOL OK] Geri Sar
        </div>
    </footer>

    <div id="raw-content" class="hidden"><?php echo htmlspecialchars($text->content); ?></div>

    <script>
        // --- TEMEL DEĞİŞKENLER ---
        const rawContent = document.getElementById('raw-content').innerText;
        let words = rawContent.match(/\S+/g) || ["Metin", "Yok"];
        
        // BAŞLANGIÇ KONUMUNU BURADA PHP İLE ALIYORUZ
        // PHP'den gelen değeri güvenli bir şekilde alıp sınırları kontrol ediyoruz
        let currentIndex = <?php echo (int)$startPosition; ?>;
        
        // Hata kontrolü: Eğer kayıtlı indeks metin uzunluğunu geçtiyse sıfırla
        if(currentIndex >= words.length || currentIndex < 0) {
            currentIndex = 0;
        }

        let wpm = <?php echo (int)$savedWpm; ?>;
        let isPlaying = false;
        let timer = null;
        let uiTimeout = null;

        // UI Elementleri
        const display = document.getElementById('word-display');
        const playBtn = document.getElementById('btn-play');
        const slider = document.getElementById('wpm-slider');
        const wpmDisplay = document.getElementById('wpm-display');
        const progressIndicator = document.getElementById('progress-indicator');
        const uiHeader = document.getElementById('ui-header');
        const uiFooter = document.getElementById('ui-footer');
        const bodyArea = document.getElementById('body-area');
        const rewindFeedback = document.getElementById('rewind-feedback');
        const saveStatus = document.getElementById('save-status');

        // Başlangıç Durumu
        updateDisplay();
        scheduleHideUI();

        // --- OTOMATİK KAYIT SİSTEMİ (SORUN ÇÖZÜCÜ) ---
        // 1. Her 3 saniyede bir otomatik kaydet (Okuyorsa)
        setInterval(() => {
            if (isPlaying) {
                saveProgress(false);
            }
        }, 3000);

        // 2. Sayfadan çıkarken kaydet (Gelişmiş Yöntem)
        window.addEventListener('beforeunload', () => {
            // keepalive: true ile sayfa kapansa bile istek devam eder
            saveProgress(false, true);
        });

        // --- HAYALET UI MANTIĞI ---
        function showUI() {
            uiHeader.classList.remove('ui-hidden');
            uiFooter.classList.remove('ui-hidden');
            bodyArea.style.cursor = 'default';
        }

        function hideUI() {
            const settingsOpen = document.getElementById('settings-panel').classList.contains('translate-x-0');
            if (isPlaying && !settingsOpen) {
                uiHeader.classList.add('ui-hidden');
                uiFooter.classList.add('ui-hidden');
                bodyArea.style.cursor = 'none';
            }
        }

        function scheduleHideUI() {
            showUI();
            clearTimeout(uiTimeout);
            uiTimeout = setTimeout(hideUI, 2500);
        }

        document.addEventListener('mousemove', scheduleHideUI);
        document.addEventListener('click', scheduleHideUI);

        // --- OKUMA MOTORU ---
        function togglePlay() {
            if (isPlaying) stop(); else start();
        }

        function start() {
            isPlaying = true;
            playBtn.innerHTML = '<i class="fas fa-pause"></i>';
            scheduleHideUI();
            processNextWord();
        }

        function stop() {
            isPlaying = false;
            clearTimeout(timer);
            playBtn.innerHTML = '<i class="fas fa-play ml-1"></i>';
            showUI();
            saveProgress(false); // Durdurunca kaydet
        }

        function processNextWord() {
            let delay = (60 / wpm) * 1000;

            timer = setTimeout(() => {
                currentIndex++;
                if (currentIndex >= words.length) {
                    stop();
                    currentIndex = 0;
                    display.innerText = "Bitti ✨";
                    saveProgress(true); // Bitince kaydet
                } else {
                    updateDisplay();
                    if (isPlaying) processNextWord();
                }
            }, delay);
        }

        function rewind() {
            let step = 10;
            currentIndex = Math.max(0, currentIndex - step);
            
            rewindFeedback.style.opacity = '1';
            rewindFeedback.style.transform = 'translate(-50%, -50%) scale(1.2)';
            setTimeout(() => {
                rewindFeedback.style.opacity = '0';
                rewindFeedback.style.transform = 'translate(-50%, -50%) scale(1)';
            }, 400);

            updateDisplay();
            if(!isPlaying) saveProgress(false); // Geri sarınca kaydet
        }

        // --- GÖRÜNTÜLEME ---
        function updateDisplay() {
            let text = words[currentIndex];
            const checkFocus = document.getElementById('check-focus');

            if (checkFocus.checked && text.length > 1) {
                const mid = Math.floor(text.length / 2);
                display.innerHTML = `${text.slice(0, mid)}<span class="highlight-char">${text.charAt(mid)}</span>${text.slice(mid + 1)}`;
            } else {
                display.innerText = text;
            }

            const percent = Math.round(((currentIndex + 1) / words.length) * 100);
            progressIndicator.innerText = `${percent}% Tamamlandı (${currentIndex}/${words.length})`;
        }

        // --- AYARLAR VE EVENTLER ---
        slider.addEventListener('input', (e) => {
            wpm = parseInt(e.target.value);
            wpmDisplay.innerText = wpm + ' WPM';
            if(isPlaying) {
                clearTimeout(timer);
                processNextWord();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.code === 'Space') { e.preventDefault(); togglePlay(); }
            else if (e.code === 'ArrowLeft') { rewind(); }
        });

        // --- TEMA VE STİL FONKSİYONLARI ---
        function setTheme(theme) {
            const body = document.getElementById('body-area');
            const word = document.getElementById('word-display');
            const guides = [document.getElementById('guide-h'), document.getElementById('guide-v')];
            
            body.className = "h-screen flex flex-col overflow-hidden no-select font-sans transition-colors duration-500";
            
            if (theme === 'dark') {
                body.classList.add('bg-gray-900', 'text-gray-100');
                word.classList.remove('text-gray-900', 'text-[#3e3427]');
                guides.forEach(g => g.style.backgroundColor = 'rgba(255,255,255,0.1)');
            } else if (theme === 'light') {
                body.classList.add('bg-gray-50', 'text-gray-900');
                word.classList.add('text-gray-900');
                guides.forEach(g => g.style.backgroundColor = 'rgba(0,0,0,0.1)');
            } else if (theme === 'sepia') {
                body.classList.add('bg-[#f5e6d3]', 'text-[#3e3427]');
                word.classList.add('text-[#3e3427]');
                guides.forEach(g => g.style.backgroundColor = 'rgba(62, 52, 39, 0.1)');
            }
        }

        function setFont(family) {
            bodyArea.classList.remove('font-sans', 'font-serif', 'font-mono');
            bodyArea.classList.add('font-' + family);
        }

        function setSize(val) {
            const sizes = ['text-3xl', 'text-4xl', 'text-5xl', 'text-6xl', 'text-7xl', 'text-8xl', 'text-9xl'];
            display.className = display.className.replace(/text-\d+xl/g, '');
            display.classList.add(sizes[val-2] || 'text-7xl');
        }
        
        function toggleSettings() {
            const panel = document.getElementById('settings-panel');
            panel.classList.toggle('translate-x-full');
        }
        
        function toggleGuides() {
            const h = document.getElementById('guide-h');
            const v = document.getElementById('guide-v');
            const isChecked = document.getElementById('check-guides').checked;
            h.style.display = isChecked ? 'block' : 'none';
            v.style.display = isChecked ? 'block' : 'none';
        }

        // --- GELİŞMİŞ KAYIT FONKSİYONU ---
        function saveProgress(completed, isUnload = false) {
            const data = {
                text_id: <?php echo $text_id; ?>,
                progress: currentIndex,
                wpm: wpm,
                completed: completed ? 1 : 0
            };

            // Görsel Geri Bildirim
            if(!isUnload) {
                saveStatus.style.opacity = '1';
                setTimeout(() => saveStatus.style.opacity = '0', 1000);
            }

            // Fetch API kullan (Keepalive ile arka planda devam eder)
            fetch('save_progress.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data),
                keepalive: true // SİHİRLİ KOMUT: Sayfa kapansa bile isteği tamamla
            }).catch(err => console.error("Kayıt hatası:", err));
        }
    </script>
</body>
</html>