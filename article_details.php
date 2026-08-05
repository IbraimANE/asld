<?php
require_once 'lang_setup.php';
require_once 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT articles.*, users.nom_complet FROM articles 
                        LEFT JOIN users ON articles.auteur_id = users.id 
                        WHERE articles.id = ? AND articles.status = 'publie'");
$stmt->execute([$id]);
$article = $stmt->fetch();

if (!$article) { 
    die($lang['article_not_found']); 
}

// ترجمة ديناميكية لاسم القسم من قاعدة البيانات إذا كانت اللغة إنجليزية
$display_category = $article['category'] ?? 'عام';
if ($_SESSION['lang'] === 'en') {
    if ($display_category === 'اجتماعي') $display_category = 'Social';
    elseif ($display_category === 'تنموي') $display_category = 'Developmental';
    elseif ($display_category === 'تطوعي') $display_category = 'Volunteer';
    elseif ($display_category === 'عام') $display_category = 'General';
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang['lang_code']; ?>" dir="<?php echo $lang['direction']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['titre']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        body { font-family: 'Tajawal', sans-serif; background-color: #f8fafc; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f8fafc; }
        ::-webkit-scrollbar-thumb { background: #122a4e; border-radius: 10px; }
        .glass-nav { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(16px); border-bottom: 1px solid rgba(0,0,0,0.05); }
        .sticky-ad { position: sticky; top: 100px; }
    </style>
</head>
<body class="leading-normal tracking-normal">

    <nav class="glass-nav sticky top-0 z-50 h-20 flex items-center shadow-sm">
        <div class="container mx-auto px-6 flex justify-between items-center">
            <a href="index.php" class="flex items-center">
                <img src="image\logo_assoc.jpg" alt="Logo" class="h-14 w-auto object-contain rounded-lg">
            </a>
            <a href="index.php" class="bg-blue-900 text-white px-6 py-2 rounded-2xl font-bold shadow-lg hover:bg-blue-800 transition text-sm"><?php echo $lang['back_home']; ?></a>
        </div>
    </nav>

    <div class="container mx-auto py-12 px-4">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
            <div class="hidden lg:block lg:col-span-1">
                <div class="sticky-ad bg-white p-2 rounded-2xl shadow-sm border border-gray-100 text-center" data-aos="<?php echo $_SESSION['lang'] === 'en' ? 'fade-right' : 'fade-left'; ?>">
                    <p class="text-[10px] text-gray-300 mb-2 uppercase"><?php echo $lang['ad_sponsored']; ?></p>
                    <img src="ads/youtech.png" class="w-full rounded-xl" alt="Ad">
                </div>
            </div>

            <div class="lg:col-span-3 bg-white shadow-2xl shadow-gray-200/50 rounded-[2.5rem] p-8 md:p-12 border border-gray-50" data-aos="fade-up">
                <div class="flex justify-center mb-6">
                    <span class="bg-blue-50 text-blue-700 text-xs font-black px-5 py-2 rounded-full border border-blue-100">
                        📂 <?php echo $_SESSION['lang'] === 'en' ? 'Category: ' : 'قسم: '; ?><?php echo htmlspecialchars($display_category); ?>
                    </span>
                </div>
                <h1 class="text-3xl md:text-5xl font-black text-[#122a4e] mb-8 text-center leading-tight">
                    <?php echo htmlspecialchars($article['titre']); ?>
                </h1>
                <div class="flex flex-wrap items-center justify-center text-gray-400 text-sm mb-10 border-y py-4 border-gray-50 gap-4">
                    <span>👤 <?php echo $lang['author_prefix']; ?> <strong class="text-gray-700"><?php echo htmlspecialchars($article['nom_complet'] ?? 'غير معروف'); ?></strong></span>
                    <span>📅 <?php echo $lang['date_prefix']; ?> <strong class="text-gray-700"><?php echo date('d/m/Y', strtotime($article['date_publication'] ?? 'now')); ?></strong></span>
                </div>
                <?php if($article['image_article']): ?>
                    <img src="uploads_activites/<?php echo $article['image_article']; ?>" class="w-full max-w-2xl mx-auto rounded-3xl shadow-xl mb-12 border-8 border-gray-50">
                <?php endif; ?>
                <div class="text-lg md:text-xl leading-loose text-gray-600 text-justify space-y-6">
                    <?php echo nl2br(htmlspecialchars($article['contenu'])); ?>
                </div>
            </div>

            <div class="hidden lg:block lg:col-span-1">
                <div class="sticky-ad bg-white p-2 rounded-2xl shadow-sm border border-gray-100 text-center" data-aos="<?php echo $_SESSION['lang'] === 'en' ? 'fade-left' : 'fade-right'; ?>">
                    <p class="text-[10px] text-gray-300 mb-2 uppercase"><?php echo $lang['ad_sponsored']; ?></p>
                    <img src="ads/youtech.png" class="w-full rounded-xl" alt="Ad">
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>AOS.init({ duration: 800, once: true });</script>
</body>
</html>