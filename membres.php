<?php
session_start();
require_once 'config.php';

// جلب الأعضاء من قاعدة البيانات (جدول users)
try {
    $stmt = $pdo->query("SELECT nom_complet, role FROM users ORDER BY id ASC");
    $membres = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $membres = [];
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>أعضاء الجمعية - مبادرات بلا حدود</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Tajawal', sans-serif; }</style>
</head>
<body class="bg-gray-50">

    <nav class="bg-[#122A4E] text-white p-4 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center">
                <img src="image\logo_assoc.jpg" class="h-10 w-10 ml-2" alt="Logo">
                <a href="index.php" class="text-xl font-bold text-[#E04F5E]">مبادرات بلا حدود</a>
            </div>
            <a href="index.php" class="bg-[#4AB76B] px-4 py-2 rounded-lg font-bold">العودة للرئيسية</a>
        </div>
    </nav>

    <main class="container mx-auto py-16 px-4">
        <h1 class="text-3xl font-bold text-center text-[#122A4E] mb-12 italic border-b-4 border-[#4AB76B] w-fit mx-auto pb-2">
            الهيكل التنظيمي للجمعية
        </h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach($membres as $membre): ?>
                <div class="bg-white p-6 rounded-2xl shadow-lg border-r-8 border-[#E04F5E] hover:scale-105 transition-transform">
                    <div class="flex items-center">
                        <div class="bg-[#f0f4f8] p-4 rounded-full ml-4">
                            <span class="text-3xl">👤</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-[#122A4E]"><?php echo htmlspecialchars($membre['nom_complet']); ?></h3>
                            <p class="text-[#4AB76B] font-bold mt-1"><?php echo htmlspecialchars($membre['role'] ?? 'عضو'); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <footer class="bg-[#122A4E] text-white py-6 text-center mt-20">
        <p>جمعية مبادرات بلا حدود &copy; 2026</p>
    </footer>

</body>
</html>