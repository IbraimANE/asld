<?php
require_once 'lang_setup.php';
require_once 'config.php';

if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

$stmt = $pdo->prepare("SELECT status, nom_complet FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user || ($user['status'] !== 'تم القبول' && $user['status'] !== 'valide')) {
    if ($_SESSION['lang'] === 'en') {
        die("<div style='text-align:center; padding:100px; font-family:Arial; direction:ltr;'>
                <h2 style='color:#122a4e;'>Sorry, your account has not been activated yet.</h2>
                <p style='color:#666;'>Please wait until your membership request is accepted by the administration.</p>
                <a href='logout.php' style='color:#2C69B3; font-weight:bold;'>Logout</a>
             </div>");
    } else {
        die("<div style='text-align:center; padding:100px; font-family:Arial; direction:rtl;'>
                <h2 style='color:#122a4e;'>عذراً، حسابك لم يتم تفعيله بعد.</h2>
                <p style='color:#666;'>يرجى الانتظار حتى يتم قبول طلب انضمامك من طرف الإدارة.</p>
                <a href='logout.php' style='color:#2C69B3; font-weight:bold;'>تسجيل الخروج</a>
             </div>");
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titre = $_POST['titre'];
    $category = $_POST['category'];
    $contenu = $_POST['contenu'];
    $image = "";
    if (!empty($_FILES['image']['name'])) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image = uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], 'uploads_activites/' . $image);
    }
    $ins = $pdo->prepare("INSERT INTO articles (titre, category, contenu, image_article, auteur_id, status) VALUES (?, ?, ?, ?, ?, 'en_attente')");
    $ins->execute([$titre, $category, $contenu, $image, $_SESSION['user_id']]);
    
    $success = $_SESSION['lang'] === 'en' 
        ? "Your initiative has been submitted successfully! It is now awaiting administrative review." 
        : "تم إرسال مبادرتك بنجاح! وهي الآن في انتظار مراجعة الإدارة.";
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang['lang_code']; ?>" dir="<?php echo $lang['direction']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $_SESSION['lang'] === 'en' ? 'Publish Initiative | Members Area' : 'نشر مبادرة | فضاء الأعضاء'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        body { font-family: 'Tajawal', sans-serif; background-color: #f8fafc; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-thumb { background: #122a4e; border-radius: 10px; }
        .glass-nav { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(0,0,0,0.05); }
    </style>
</head>
<body>
    <nav class="glass-nav sticky top-0 z-50 h-20 flex items-center shadow-sm">
        <div class="container mx-auto px-6 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <img src="image\logo_assoc.jpg" class="h-12 w-auto rounded-lg shadow-sm" alt="Logo">
                <span class="font-bold text-[#122a4e] hidden md:block">
                    <?php echo $_SESSION['lang'] === 'en' ? 'Welcome, ' : 'مرحباً، '; ?><?php echo htmlspecialchars($user['nom_complet']); ?>
                </span>
            </div>
            <div class="flex gap-3">
                <a href="index.php" class="text-gray-500 font-bold px-4 py-2 hover:bg-gray-100 rounded-xl transition text-sm"><?php echo $lang['nav_home']; ?></a>
                <a href="logout.php" class="bg-red-50 text-red-600 px-4 py-2 rounded-xl font-bold hover:bg-red-100 transition text-sm"><?php echo $lang['logout']; ?></a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto py-16 px-4">
        <div class="max-w-2xl mx-auto bg-white p-8 md:p-12 rounded-[2.5rem] shadow-2xl shadow-blue-900/5 border border-gray-50" data-aos="fade-up">
            <h2 class="text-3xl font-black mb-8 text-[#122a4e] text-center">
                <?php echo $_SESSION['lang'] === 'en' ? 'Publish a New Activity or Initiative' : 'نشر نشاط أو مبادرة جديدة'; ?>
            </h2>
            <?php if(isset($success)): ?>
                <div class="bg-green-50 border border-green-100 text-green-700 p-4 mb-8 rounded-2xl font-bold text-center"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                <div>
                    <label class="block mb-2 font-bold text-gray-700 <?php echo $_SESSION['lang'] === 'en' ? 'ml-2' : 'mr-2'; ?>">
                        <?php echo $_SESSION['lang'] === 'en' ? 'Initiative Title' : 'عنوان المبادرة'; ?>
                    </label>
                    <input type="text" name="titre" class="w-full bg-gray-50 border border-gray-100 p-4 rounded-2xl focus:ring-4 focus:ring-blue-500/10 outline-none transition" placeholder="<?php echo $_SESSION['lang'] === 'en' ? 'e.g., Beach Cleaning Campaign' : 'مثال: حملة تنظيف الشاطئ'; ?>" required>
                </div>
                <div>
                    <label class="block mb-2 font-bold text-gray-700 <?php echo $_SESSION['lang'] === 'en' ? 'ml-2' : 'mr-2'; ?>">
                        <?php echo $_SESSION['lang'] === 'en' ? 'Initiative Type' : 'نوع المبادرة'; ?>
                    </label>
                    <select name="category" class="w-full bg-gray-50 border border-gray-100 p-4 rounded-2xl focus:ring-4 focus:ring-blue-500/10 outline-none transition" required>
                        <option value="" disabled selected><?php echo $_SESSION['lang'] === 'en' ? 'Select Type...' : 'اختر النوع...'; ?></option>
                        <option value="رياضي">⚽ <?php echo $_SESSION['lang'] === 'en' ? 'Sports' : 'رياضي'; ?></option>
                        <option value="ثقافي">🎨 <?php echo $_SESSION['lang'] === 'en' ? 'Cultural' : 'ثقافي'; ?></option>
                        <option value="اجتماعي">🤝 <?php echo $_SESSION['lang'] === 'en' ? 'Social' : 'اجتماعي'; ?></option>
                        <option value="تنموي">🚀 <?php echo $_SESSION['lang'] === 'en' ? 'Developmental' : 'تنموي'; ?></option>
                        <option value="تطوعي">🌱 <?php echo $_SESSION['lang'] === 'en' ? 'Volunteer' : 'تطوعي'; ?></option>
                    </select>
                </div>
                <div>
                    <label class="block mb-2 font-bold text-gray-700 <?php echo $_SESSION['lang'] === 'en' ? 'ml-2' : 'mr-2'; ?>">
                        <?php echo $_SESSION['lang'] === 'en' ? 'Details' : 'التفاصيل'; ?>
                    </label>
                    <textarea name="contenu" class="w-full bg-gray-50 border border-gray-100 p-4 rounded-2xl h-48 focus:ring-4 focus:ring-blue-500/10 outline-none transition" placeholder="<?php echo $_SESSION['lang'] === 'en' ? 'Write a detailed description...' : 'اكتب وصفاً مفصلاً...'; ?>" required></textarea>
                </div>
                <div>
                    <label class="block mb-2 font-bold text-gray-700 <?php echo $_SESSION['lang'] === 'en' ? 'ml-2' : 'mr-2'; ?>">
                        <?php echo $_SESSION['lang'] === 'en' ? 'Activity Image' : 'صورة النشاط'; ?>
                    </label>
                    <div class="bg-gray-50 p-6 border-2 border-dashed border-gray-200 rounded-2xl text-center">
                        <input type="file" name="image" class="text-sm text-gray-500 file:bg-[#122a4e] file:text-white file:border-0 file:rounded-xl file:px-4 file:py-2 hover:file:bg-blue-900 cursor-pointer">
                    </div>
                </div>
                <button type="submit" class="w-full bg-gradient-to-r from-[#122a4e] to-[#2C69B3] text-white py-5 rounded-2xl font-black text-lg shadow-xl shadow-blue-900/20 hover:scale-[1.02] transition-all">
                    <?php echo $_SESSION['lang'] === 'en' ? 'Publish Initiative Now 🚀' : 'نشر المبادرة الآن 🚀'; ?>
                </button>
            </form>
        </div>
    </div>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>AOS.init({ duration: 800, once: true });</script>
</body>
</html>