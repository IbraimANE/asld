<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>انضم إلينا | مبادرات بلا حدود</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        body { font-family: 'Tajawal', sans-serif; background: #f8fafc; }
        .hero-pattern { background-color: #122a4e; background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.05) 1px, transparent 0); background-size: 40px 40px; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6 py-20 hero-pattern">
    <div class="max-w-2xl w-full bg-white/95 backdrop-blur-xl p-10 md:p-14 rounded-[3rem] shadow-2xl border border-white/20" data-aos="zoom-in">
        <div class="text-center mb-12">
            <img src="image\logo_assoc.jpg" class="h-16 mx-auto mb-6 rounded-2xl shadow-sm" alt="Logo">
            <h2 class="text-4xl font-black text-[#122a4e] mb-3">تسجيل عضو جديد</h2>
            <p class="text-gray-500 font-medium">كن جزءاً من مسيرة التنمية والابتكار</p>
        </div>
        
        <form action="traitement.php" method="POST" enctype="multipart/form-data" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="text" name="nom" class="w-full bg-gray-50 border border-gray-100 p-4 rounded-2xl outline-none focus:ring-4 focus:ring-blue-500/10 transition" placeholder="الإسم الكامل" required>
                <input type="tel" name="telephone" class="w-full bg-gray-50 border border-gray-100 p-4 rounded-2xl outline-none focus:ring-4 focus:ring-blue-500/10 transition" placeholder="06XXXXXXXX" required>
            </div>
            <input type="email" name="email" class="w-full bg-gray-50 border border-gray-100 p-4 rounded-2xl outline-none focus:ring-4 focus:ring-blue-500/10 transition" placeholder="name@mail.com" required>
            <input type="password" name="password" class="w-full bg-gray-50 border border-gray-100 p-4 rounded-2xl outline-none focus:ring-4 focus:ring-blue-500/10 transition" placeholder="كلمة المرور" required>

            <div class="bg-yellow-50 border-r-4 border-yellow-400 p-4 rounded-2xl my-8">
                <p class="text-yellow-800 text-sm font-bold">📢 تذكير: واجب الانخراط السنوي محدد في 100 درهم.</p>
            </div>

            <div class="space-y-4 bg-blue-50/50 p-6 rounded-[2rem] border border-blue-100">
                <h3 class="font-black text-blue-900 text-sm mb-4 uppercase tracking-widest">رفع الوثائق المطلوبة</h3>
                <div class="space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-white p-3 px-5 rounded-2xl border border-blue-50">
                        <label class="text-xs font-bold text-gray-500">الصورة الشخصية</label>
                        <input type="file" name="photo" class="text-xs text-blue-600 file:hidden" required>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-white p-3 px-5 rounded-2xl border border-blue-50">
                        <label class="text-xs font-bold text-gray-500">البطاقة (وجه 1)</label>
                        <input type="file" name="cin_recto" class="text-xs text-blue-600 file:hidden" required>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-white p-3 px-5 rounded-2xl border border-blue-50">
                        <label class="text-xs font-bold text-gray-500">البطاقة (وجه 2)</label>
                        <input type="file" name="cin_recto" class="text-xs text-blue-600 file:hidden" required>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-[#122a4e] to-[#2C69B3] text-white py-5 rounded-[2rem] font-black text-xl shadow-2xl shadow-blue-900/30 hover:scale-[1.02] transition-all">إرسال طلب الانخراط</button>
            <div class="mt-8 text-center">
                <a href="index.php" class="text-blue-600 font-bold hover:underline">← العودة للرئيسية</a>
            </div>
        </form>
    </div>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>AOS.init({ duration: 800, once: true });</script>
</body>
</html>