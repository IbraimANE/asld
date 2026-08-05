<?php
session_start();
// Vérifie si l'utilisateur est connecté ET s'il est responsable ou admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'responsable' && $_SESSION['role'] !== 'admin')) {
    header('Location: index.php'); // Redirige vers l'accueil s'il n'a pas les droits
    exit();
}

require_once 'config.php';

// Vérifier si l'action est demandée
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if ($_GET['action'] == 'valider') {
        $pdo->prepare("UPDATE articles SET status = 'publie' WHERE id = ?")->execute([$id]);
    } elseif ($_GET['action'] == 'supprimer') {
        $pdo->prepare("DELETE FROM articles WHERE id = ?")->execute([$id]);
    }
    header("Location: admin_articles.php");
    exit();
}

// Récupérer uniquement les articles en attente - تم التأكد من جلب حقل category
$articles_attente = $pdo->query("SELECT articles.*, users.nom_complet FROM articles JOIN users ON articles.auteur_id = users.id WHERE articles.status = 'en_attente' ORDER BY articles.id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مراجعة المقالات</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Tajawal', sans-serif; }</style>
</head>
<body class="bg-gray-100 p-6 text-right">
    <div class="max-w-4xl mx-auto">
        <h2 class="text-3xl font-black mb-8 text-blue-900 border-r-4 border-blue-600 pr-4">المقالات في انتظار التفعيل</h2>
        
        <?php if (empty($articles_attente)): ?>
            <div class="bg-white p-8 rounded-2xl shadow-sm border-2 border-dashed border-gray-200 text-center">
                <span class="text-4xl block mb-2">✅</span>
                <p class="text-gray-500 font-bold">لا توجد مقالات جديدة للمراجعة حالياً.</p>
            </div>
        <?php else: ?>
            <div class="grid gap-6">
                <?php foreach($articles_attente as $art): ?>
                <div class="bg-white p-5 rounded-2xl shadow-sm flex flex-col md:flex-row justify-between items-center border border-gray-100 hover:shadow-md transition">
                    <div class="flex items-center gap-4 w-full">
                        <?php if($art['image_article']): ?>
                            <img src="uploads_activites/<?php echo $art['image_article']; ?>" class="w-20 h-20 object-cover rounded-xl shadow-sm">
                        <?php else: ?>
                            <div class="w-20 h-20 bg-gray-100 rounded-xl flex items-center justify-center text-gray-400">🖼️</div>
                        <?php endif; ?>
                        
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="font-bold text-xl text-gray-800"><?php echo htmlspecialchars($art['titre']); ?></h3>
                                <span class="bg-blue-100 text-blue-700 text-[10px] px-2 py-0.5 rounded-full font-bold">
                                    # <?php echo htmlspecialchars($art['category'] ?? 'عام'); ?>
                                </span>
                            </div>
                            <p class="text-sm text-gray-500 flex items-center gap-1">
                                <span>👤 الكاتب:</span>
                                <span class="font-bold text-blue-600"><?php echo htmlspecialchars($art['nom_complet']); ?></span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex gap-3 mt-4 md:mt-0 w-full md:w-auto">
                        <a href="?action=valider&id=<?php echo $art['id']; ?>" class="flex-1 md:flex-none text-center bg-green-600 text-white px-6 py-2 rounded-xl font-bold hover:bg-green-700 shadow-sm transition">تفعيل</a>
                        <a href="?action=supprimer&id=<?php echo $art['id']; ?>" class="flex-1 md:flex-none text-center bg-red-50 text-red-600 border border-red-100 px-6 py-2 rounded-xl font-bold hover:bg-red-600 hover:text-white transition" onclick="return confirm('هل أنت متأكد من حذف هذا المقال نهائياً؟')">حذف</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div class="mt-10 flex items-center justify-between bg-white p-4 rounded-2xl shadow-sm">
            <a href="index.php" class="text-blue-600 font-bold hover:underline flex items-center gap-1">
                <span>🏠</span> العودة للرئيسية
            </a>
            <span class="text-gray-400 text-xs italic font-light">إدارة جمعية مبادرات بلا حدود</span>
        </div>
    </div>
</body>
</html>