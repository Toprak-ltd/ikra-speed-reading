<?php
require __DIR__ . '/../app/db.php';
session_start();

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if (empty($title) || empty($content)) {
        $message = "Lütfen başlık ve içeriği doldurun.";
    } else {
        $sql = "INSERT INTO texts (user_id, title, content) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$_SESSION['user_id'], $title, $content])) {
            header("Location: dashboard.php");
            exit;
        } else {
            $message = "Hata oluştu.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metin Ekle / Yükle - IKRA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&family=Merriweather:wght@300;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    </script>
    <style>
        .editor-title { font-family: 'Inter', sans-serif; background: transparent; outline: none; }
        .editor-content { font-family: 'Merriweather', serif; background: transparent; outline: none; resize: none; line-height: 1.8; }
        .drag-active { border-color: #4f46e5 !important; background-color: #eef2ff !important; }
        /* Yükleme Animasyonu */
        .loader { border: 3px solid #f3f3f3; border-top: 3px solid #4f46e5; border-radius: 50%; width: 20px; height: 20px; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body class="bg-white h-screen flex flex-col overflow-hidden">

    <nav class="h-16 border-b border-slate-100 flex items-center justify-between px-6 lg:px-24 bg-white/90 backdrop-blur z-50">
        <div class="flex items-center">
            <a href="dashboard.php" class="text-slate-400 hover:text-slate-800 transition-colors mr-4">
                <i class="fas fa-arrow-left"></i>
            </a>
            <span class="text-sm font-medium text-slate-500">Editör</span>
        </div>
        <div class="flex items-center space-x-4">
            <button onclick="toggleSidebar()" class="lg:hidden text-slate-500 hover:text-indigo-600 transition-colors p-2 mr-2">
                <i class="fas fa-file-import text-xl"></i>
            </button>
            <span class="text-xs text-slate-400 hidden sm:block" id="word-count">0 kelime</span>
            <button onclick="document.getElementById('save-btn').click()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 sm:px-6 py-2 rounded-full text-xs sm:text-sm font-medium shadow-lg shadow-indigo-200 transition-all">
                Kaydet
            </button>
        </div>
    </nav>

    <div class="flex flex-1 overflow-hidden relative">
        
        <!-- Mobile Sidebar Overlay -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/50 z-40 hidden opacity-0 transition-opacity duration-300 lg:hidden glass"></div>

        <aside id="sidebar-menu" class="fixed inset-y-0 left-0 z-50 w-80 bg-slate-50 border-r border-slate-200 flex flex-col p-6 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 lg:relative lg:flex shadow-2xl lg:shadow-none">
            <div class="flex justify-between items-center lg:hidden mb-4">
                <h3 class="font-bold text-slate-800">Dosya Yükle</h3>
                <button onclick="toggleSidebar()" class="text-slate-500 hover:text-red-500">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <h3 class="font-bold text-slate-800 mb-4 flex items-center">
                <i class="fas fa-file-import mr-2 text-indigo-500"></i> Dosya İçe Aktar
            </h3>
            
            <div id="drop-zone" class="border-2 border-dashed border-slate-300 rounded-xl p-6 text-center transition-all cursor-pointer hover:border-indigo-400 hover:bg-slate-100 mb-4 group relative">
                <input type="file" id="file-input" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept=".txt,.pdf">
                <div class="pointer-events-none">
                    <i class="fas fa-cloud-upload-alt text-3xl text-slate-400 group-hover:text-indigo-500 mb-3 transition-colors"></i>
                    <p class="text-sm text-slate-600 font-medium">PDF veya TXT Yükle</p>
                    <p class="text-xs text-slate-400 mt-1">Sürükle veya tıkla</p>
                </div>
            </div>

            <div class="space-y-3 mb-6">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" id="clean-headers" checked class="accent-indigo-600 rounded">
                    <span class="text-sm text-slate-600">Sayfa numaralarını temizle</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" id="clean-refs" checked class="accent-indigo-600 rounded">
                    <span class="text-sm text-slate-600">Referansları temizle [1]</span>
                </label>
            </div>

            <div id="status-area" class="hidden p-3 bg-white rounded-lg border border-slate-200 text-sm flex items-center">
                <div class="loader mr-3" id="loader"></div>
                <span id="status-text" class="text-slate-600">İşleniyor...</span>
            </div>
        </aside>

        <main class="flex-1 overflow-y-auto relative">
            <div class="max-w-3xl mx-auto px-8 py-12">
                
                <?php if($message): ?>
                    <div class="mb-6 p-4 bg-red-50 text-red-600 rounded-lg text-sm flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i> <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <form action="" method="POST" id="editor-form" class="space-y-6">
                    <input type="text" name="title" id="title-input" required
                        class="editor-title w-full text-4xl font-bold text-slate-900 placeholder:text-slate-300" 
                        placeholder="Metin Başlığı...">

                    <textarea name="content" id="content-area" required
                        class="editor-content w-full min-h-[60vh] text-base sm:text-lg text-slate-700 pb-32" 
                        placeholder="Metni buraya yapıştırın veya sol taraftan dosya yükleyin..."></textarea>

                    <button type="submit" id="save-btn" class="hidden">Kaydet</button>
                </form>
            </div>
        </main>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar-menu');
        const sidebarOverlay = document.getElementById('sidebar-overlay');

        function toggleSidebar() {
            const isClosed = sidebar.classList.contains('-translate-x-full');
            if (isClosed) {
                sidebar.classList.remove('-translate-x-full');
                sidebarOverlay.classList.remove('hidden');
                setTimeout(() => sidebarOverlay.classList.remove('opacity-0'), 10);
            } else {
                sidebar.classList.add('-translate-x-full');
                sidebarOverlay.classList.add('opacity-0');
                setTimeout(() => sidebarOverlay.classList.add('hidden'), 300);
            }
        }

        // ... Existing scripts ...
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('file-input');
        const contentArea = document.getElementById('content-area');
        const titleInput = document.getElementById('title-input');
        const statusArea = document.getElementById('status-area');
        const statusText = document.getElementById('status-text');
        const loader = document.getElementById('loader');
        const wordCountDisplay = document.getElementById('word-count');

        // Sürükle Bırak Efektleri
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropZone.classList.add('drag-active');
            });
        });
        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropZone.classList.remove('drag-active');
            });
        });

        // Dosya Seçimi
        dropZone.addEventListener('drop', (e) => handleFiles(e.dataTransfer.files));
        fileInput.addEventListener('change', (e) => handleFiles(e.target.files));

        function handleFiles(files) {
            if (files.length === 0) return;
            const file = files[0];
            
            // UI Güncelle
            statusArea.classList.remove('hidden');
            statusText.innerText = "Dosya analiz ediliyor...";
            loader.style.display = 'block';

            // Başlığı dosya adı yap (eğer boşsa)
            if(!titleInput.value) {
                titleInput.value = file.name.replace(/\.[^/.]+$/, "");
            }

            if (file.type === 'application/pdf') {
                parsePDF(file);
            } else if (file.type === 'text/plain') {
                parseTXT(file);
            } else {
                showError("Sadece PDF ve TXT dosyaları desteklenir.");
            }
        }

        // TXT Okuma
        function parseTXT(file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                let text = e.target.result;
                text = cleanText(text);
                insertText(text);
                finishProcess();
            };
            reader.readAsText(file);
        }

        // PDF Okuma (PDF.js ile)
        async function parsePDF(file) {
            try {
                const arrayBuffer = await file.arrayBuffer();
                const pdf = await pdfjsLib.getDocument({ data: arrayBuffer }).promise;
                let fullText = "";

                statusText.innerText = `PDF Okunuyor (0/${pdf.numPages})...`;

                for (let i = 1; i <= pdf.numPages; i++) {
                    const page = await pdf.getPage(i);
                    const textContent = await page.getTextContent();
                    
                    // Sayfa metinlerini birleştir
                    const pageText = textContent.items.map(item => item.str).join(' ');
                    
                    fullText += pageText + "\n\n";
                    statusText.innerText = `PDF Okunuyor (${i}/${pdf.numPages})...`;
                }

                fullText = cleanText(fullText);
                insertText(fullText);
                finishProcess();

            } catch (error) {
                console.error(error);
                showError("PDF okunamadı. Dosya şifreli veya bozuk olabilir.");
            }
        }

        // AKILLI TEMİZLİK FONKSİYONU
        function cleanText(text) {
            const cleanHeaders = document.getElementById('clean-headers').checked;
            const cleanRefs = document.getElementById('clean-refs').checked;

            if (cleanHeaders) {
                // Sayfa numaralarını temizle (Örn: "Page 1", "Sayfa 12", sadece "12")
                // Satır başındaki veya sonundaki tekil sayıları temizler
                text = text.replace(/^\s*\d+\s*$/gm, ""); 
                text = text.replace(/Sayfa\s+\d+/gi, "");
            }

            if (cleanRefs) {
                // [1], [12] gibi referansları temizle
                text = text.replace(/\[\d+\]/g, "");
            }

            // Fazla boşlukları temizle
            text = text.replace(/\s+/g, " ").replace(/\n\s*\n/g, "\n\n").trim();
            
            return text;
        }

        function insertText(text) {
            contentArea.value = text;
            // Tetikle input eventini (Kelime sayacı ve yükseklik için)
            contentArea.dispatchEvent(new Event('input'));
        }

        function finishProcess() {
            statusText.innerText = "İşlem Tamamlandı!";
            statusText.classList.add('text-green-600');
            loader.style.display = 'none';
            setTimeout(() => statusArea.classList.add('hidden'), 3000);
        }

        function showError(msg) {
            statusText.innerText = msg;
            statusText.classList.add('text-red-600');
            loader.style.display = 'none';
        }

        // Kelime Sayacı
        contentArea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
            const count = this.value.trim() ? this.value.trim().split(/\s+/).length : 0;
            wordCountDisplay.innerText = count + " kelime";
        });
    </script>
</body>
</html>
