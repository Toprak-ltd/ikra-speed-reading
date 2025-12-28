<?php
require __DIR__ . '/../app/db.php';
session_start();

$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user->password)) {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['username'] = $user->username;
        header("Location: dashboard.php");
        exit;
    } else {
        $message = "E-posta veya şifre hatalı.";
    }
}
require __DIR__ . '/../views/layout/header.php';
?>

<div class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="mx-auto h-12 w-12 bg-brand-600 text-white flex items-center justify-center rounded-xl text-2xl shadow-lg shadow-brand-500/50">
            <i class="fas fa-book-reader"></i>
        </div>
        <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900">IKRA'ya Giriş Yap</h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Veya <a href="register.php" class="font-medium text-brand-600 hover:text-brand-500 transition-colors">yeni bir hesap oluştur</a>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow-xl shadow-gray-200/50 sm:rounded-2xl sm:px-10 border border-gray-100">
            
            <?php if($message): ?>
                <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-md">
                    <div class="flex">
                        <div class="flex-shrink-0"><i class="fas fa-exclamation-circle text-red-500"></i></div>
                        <div class="ml-3"><p class="text-sm text-red-700"><?php echo $message; ?></p></div>
                    </div>
                </div>
            <?php endif; ?>

            <form class="space-y-6" action="" method="POST">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">E-posta Adresi</label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" required class="block w-full appearance-none rounded-lg border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-brand-500 sm:text-sm transition-all">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Şifre</label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" required class="block w-full appearance-none rounded-lg border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-brand-500 sm:text-sm transition-all">
                    </div>
                </div>

                <div>
                    <button type="submit" class="flex w-full justify-center rounded-lg border border-transparent bg-brand-600 py-2.5 px-4 text-sm font-medium text-white shadow-sm hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition-all transform hover:-translate-y-0.5">
                        Giriş Yap <i class="fas fa-arrow-right ml-2 mt-1"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../views/layout/footer.php'; ?>
