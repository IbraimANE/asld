<?php
session_start();
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>كلمة الرئيس والسيرة الذاتية | مبادرات بلا حدود</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        body { font-family: 'Tajawal', sans-serif; scroll-behavior: smooth; background-color: #f0f4f8; overflow-x: hidden; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f0f4f8; }
        ::-webkit-scrollbar-thumb { background: #1e3a8a; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #eab308; }
        .glass-nav { 
            background: rgba(255, 255, 255, 0.7); 
            backdrop-filter: blur(20px); 
            border-bottom: 1px solid rgba(255, 255, 255, 0.3); 
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
        }
    </style>
</head>
<body class="text-gray-800">

    <nav class="glass-nav sticky top-0 z-50 h-24 flex items-center transition-all duration-300">
        <div class="container mx-auto px-6 flex justify-between items-center">
            <div class="flex items-center gap-6">
                <a href="index.php" class="flex items-center transition-transform hover:scale-105">
                    <img src="image\logo_assoc.jpg" alt="Logo" class="h-16 w-auto object-contain rounded-2xl drop-shadow-xl">
                </a>
                <div class="hidden md:flex items-center gap-4">
                    <a href="index.php" class="text-gray-600 font-bold px-4 py-2 hover:text-blue-600 transition-colors">الرئيسية</a>
                    <a href="membres.php" class="text-gray-600 font-bold px-4 py-2 hover:text-blue-600 transition-colors">الأعضاء</a>
                    <a href="contact.php" class="text-gray-600 font-bold px-4 py-2 hover:text-blue-600 transition-colors">اتصل بنا</a>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="espace_membre.php" class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white px-6 py-3 rounded-xl font-black shadow-lg hover:shadow-indigo-500/30 hover:-translate-y-1 transition-all">لوحة التحكم</a>
                    <a href="logout.php" class="bg-red-50 text-red-600 px-6 py-3 rounded-xl font-bold hover:bg-red-100 transition-colors">خروج</a>
                <?php else: ?>
                    <a href="login.php" class="text-blue-900 font-bold px-4 py-2 hover:text-blue-600 transition-colors">دخول</a>
                    <a href="inscription.php" class="bg-gradient-to-r from-yellow-400 to-yellow-500 text-blue-900 px-6 py-3 rounded-xl font-black shadow-lg hover:shadow-yellow-500/30 hover:-translate-y-1 transition-all">انضم إلينا</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main class="py-20 container mx-auto px-6 max-w-4xl">
        <article data-aos="fade-up" class="bg-white p-12 rounded-[2.5rem] shadow-xl shadow-blue-900/5 border border-gray-100">
            
            <div class="flex flex-col md:flex-row items-center md:items-start gap-8 pb-8 border-b border-gray-100">
                <div class="w-48 h-48 rounded-2xl overflow-hidden bg-gray-200 flex-shrink-0 shadow-md border-4 border-white">
                    <!-- لاستبدال الصورة قم بتغيير المسار src إلى مسار صورة رئيس الجمعية الصحيح -->
                    <img src="image/president.jpg" alt="رئيس الجمعية" class="w-full h-full object-cover" onerror="this.src='https://via.placeholder.com/200x200?text=صورة+الرئيس'">
                </div>
                <div>
                    <h1 class="text-4xl font-black text-blue-900 mb-2">كلمة رئيس الجمعية

                    </h1>رئيس الجمعية / الخدير الغرابي</p>
                    <div class="h-1 w-20 bg-gradient-to-r from-blue-600 to-yellow-400 rounded-full"></div>
                </div>
            </div>

            <div class="space-y-8 text-gray-700 text-lg leading-relaxed text-justify mt-8">
                
                <div>
                    <h3 class="text-2xl font-black text-blue-950 mb-3">الكلمة الافتتاحية</h3>
                    <p>
                        إن الاستثمار الحقيقي لأي مجتمع يكمن في سواعد شبابه ونخبته الحية. نلتزم في جمعية مبادرات بلا حدود بتوفير بيئة خصبة تتيح لكل فكرة طموحة أن تتحول إلى أثر ملموس على أرض الواقع، مساهمين معاً في بناء مغرب الغد وتفعيل آليات التضامن والتكافل الاجتماعي والمواطنة الفاعلة.
                    </p>
                </div>

                <div>
                    <h3 class="text-2xl font-black text-blue-950 mb-3">السيرة الذاتية لرئيس الجمعية</h3>
                    <ul class="space-y-3 pr-2">
                        <li class="flex items-start gap-2">
                            <span class="text-yellow-500 font-black mt-1">▪</span>
                            <div><strong class="text-blue-900">المسار الأكاديمي:</strong> [أدخل الشواهد والديبلومات الأكاديمية هنا]</div>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-yellow-500 font-black mt-1">▪</span>
                            <div><strong class="text-blue-900">الخبرة المهنية:</strong> [أدخل الميدان المهني والوظائف التخصصية هنا]</div>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-yellow-500 font-black mt-1">▪</span>
                            <div><strong class="text-blue-900">التجربة الجمعوية والمدنية:</strong> [أدخل مجالات التدبير التنموي والورش التكوينية السابقة هنا]</div>
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-2xl font-black text-blue-950 mb-4">العرض المرئي والميداني</h3>
                    <!-- إطار مخصص لإدراج الفيديو لاحقاً -->
                    <div class="w-full aspect-video rounded-3xl bg-gray-900 flex flex-col justify-center items-center p-6 text-center shadow-inner relative overflow-hidden group border border-gray-800">
                        <!-- عند توفر كود الفيديو (مثلاً كود iframe من يوتيوب) قم بمسح الـ div الداخلي ووضعه هنا -->
                        <div class="z-10">
                            <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-gray-400 font-bold text-sm">مساحة مخصصة لإدراج الفيديو التعريفي لاحقاً</p>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-tr from-blue-950/20 to-transparent pointer-events-none"></div>
                    </div>
                </div>

            </div>

            <div class="mt-12 pt-6 border-t border-gray-100 flex justify-end">
                <a href="index.php" class="bg-blue-900 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-800 transition-colors shadow-md">العودة للرئيسية</a>
            </div>
        </article>
    </main>

    <footer class="bg-white py-12 border-t border-gray-200">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-2xl font-black text-blue-900 mb-4">مبادرات بلا حدود</h2>
            <p class="text-gray-500 font-bold">&copy; <?php echo date("Y"); ?> المنصة الرسمية</p>
        </div>
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>AOS.init({duration: 800, once: true});</script>
</body>
</html>