<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone']; 
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // دالة لرفع الملفات بشكل آمن مع التحقق من النوع
    function moveFile($file, $folder) {
        if ($file['error'] !== UPLOAD_ERR_OK) return null; // التأكد من عدم وجود خطأ في الرفع
        if (!is_dir($folder)) mkdir($folder, 0777, true);
        
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'pdf']; // الأنواع المسموح بها
        
        if (!in_array($ext, $allowed)) return null;

        $filename = uniqid() . '.' . $ext;
        move_uploaded_file($file['tmp_name'], $folder . $filename);
        return $filename;
    }

    // رفع الصور
    $photo = moveFile($_FILES['photo'], 'uploads_photos/');
    $recto = moveFile($_FILES['cin_recto'], 'uploads_cin/');
    $verso = moveFile($_FILES['cin_verso'], 'uploads_cin/');

    try {
        // جملة الاستعلام المحدثة
        $sql = "INSERT INTO users (nom_complet, email, telephone, password, photo_profil, cin_recto, cin_verso, role, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'عضو', 'قيد الانتظار')";
        $stmt = $pdo->prepare($sql);
        
        // تنفيذ الاستعلام بنفس ترتيب الحقول
        $stmt->execute([$nom, $email, $telephone, $password, $photo, $recto, $verso]);
        
        ?>
        <!DOCTYPE html>
        <html lang="ar" dir="rtl">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <script src="https://cdn.tailwindcss.com"></script>
            <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
            <style>body { font-family: 'Tajawal', sans-serif; }</style>
            <title>تم التسجيل بنجاح</title>
        </head>
        <body class="bg-gray-100 flex items-center justify-center h-screen px-4">
            <div class="bg-white p-8 rounded-3xl shadow-2xl text-center max-w-sm w-full border-t-8 border-[#4AB76B] transform scale-100 transition-transform">
                <div class="text-6xl mb-4 animate-bounce">✅</div>
                <h1 class="text-2xl font-black text-[#122A4E] mb-4">تم تسجيلك بنجاح!</h1>
                <p class="text-gray-600 mb-2 font-medium">تم استلام طلب انخراطك (100 درهم).</p>
                <p class="text-gray-400 text-sm mb-6">سيتم مراجعة ملفك من طرف الإدارة وتفعيله قريباً.</p>
                
                <div class="w-full bg-gray-100 rounded-full h-2 mb-4 overflow-hidden">
                    <div class="bg-[#4AB76B] h-2 rounded-full transition-all duration-[3000ms] w-0" id="progress"></div>
                </div>
                <p class="text-xs text-blue-500 font-bold">جاري توجيهك للرئيسية...</p>

                <script>
                    setTimeout(() => {
                        document.getElementById('progress').style.width = '100%';
                    }, 100);
                </script>
            </div>
        </body>
        </html>
        <?php
        header("refresh:3;url=index.php");
        exit(); 

    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo "<div style='text-align:center; padding:100px 20px; font-family:Tajawal; direction:rtl; background:#f9fafb; height:100vh;'>
                    <div style='background:white; display:inline-block; padding:40px; border-radius:20px; shadow:0 10px 15px rgba(0,0,0,0.1); border-top:8px solid #E04F5E;'>
                        <h2 style='color:#E04F5E; font-weight:900;'>❌ عذراً، البريد الإلكتروني مسجل مسبقاً!</h2>
                        <p style='color:#666;'>يبدو أنك تملك حساباً بالفعل، يرجى تسجيل الدخول.</p>
                        <br>
                        <a href='inscription.php' style='background:#122A4E; color:white; padding:10px 25px; border-radius:10px; text-decoration:none; font-weight:bold;'>العودة للتسجيل</a>
                    </div>
                  </div>";
        } else {
            echo "❌ Erreur : " . $e->getMessage();
        }
    }
}
?>