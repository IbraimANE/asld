<?php
session_start();
require_once 'config.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        if ($user['role'] !== 'admin' && $user['status'] !== 'تم القبول') {
            $error = "حسابك لا يزال قيد الانتظار أو تم رفضه.";
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role']; 
            if ($user['role'] === 'admin') { header("Location: admin.php"); } 
            else { header("Location: espace_membre.php"); }
            exit();
        }
    } else { $error = "البريد أو كلمة المرور غير صحيحة"; }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>دخول الأعضاء | مبادرات بلا حدود</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Tajawal', sans-serif; background: linear-gradient(135deg, #122a4e 0%, #1a365d 100%); }</style>
</head>
<body class="h-screen flex items-center justify-center p-4">
    <div class="bg-white/95 backdrop-blur-xl p-10 rounded-[2.5rem] shadow-2xl w-full max-w-md border border-white/20">
        <div class="text-center mb-8">
            <img src="image\logo_assoc.jpg" class="h-16 mx-auto mb-4 rounded-xl shadow-sm" alt="Logo">
            <h2 class="text-3xl font-black text-[#122a4e]">مرحباً بك مجدداً</h2>
            <p class="text-gray-400 text-sm mt-2">سجل دخولك لمتابعة مبادراتك</p>
        </div>
        <?php if ($error): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-2xl mb-6 text-sm font-bold border border-red-100"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="login.php" method="POST" class="space-y-5">
            <div>
                <label class="block text-gray-700 font-bold mb-2 mr-2">البريد الإلكتروني</label>
                <input type="email" name="email" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-blue-500/10 outline-none transition-all" placeholder="name@mail.com">
            </div>
            <div>
                <label class="block text-gray-700 font-bold mb-2 mr-2">كلمة المرور</label>
                <input type="password" name="password" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-blue-500/10 outline-none transition-all" placeholder="••••••••">
            </div>
            <button type="submit" class="w-full bg-[#122a4e] text-white py-4 rounded-2xl font-black text-lg shadow-xl shadow-blue-900/20 hover:bg-blue-900 transition-all">دخول</button>
        </form>
        <p class="mt-8 text-center text-sm text-gray-500 font-medium">ليس لديك حساب؟ <a href="inscription.php" class="text-blue-600 font-bold hover:underline">انضم إلينا الآن</a></p>
    </div>
</body>
</html>