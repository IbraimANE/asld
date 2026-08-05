<?php
require_once 'lang_setup.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang['lang_code']; ?>" dir="<?php echo $lang['direction']; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo $_SESSION['lang'] === 'en' ? 'Contact Us | Boundless Initiatives' : 'اتصل بنا | مبادرات بلا حدود'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        body { font-family: 'Tajawal', sans-serif; background-color: #f8fafc; }
        .hero-mini { background: linear-gradient(135deg, #122a4e 0%, #1a365d 100%); }
    </style>
</head>
<body>
    <header class="hero-mini text-white py-20 px-6 text-center shadow-lg">
        <div class="container mx-auto" data-aos="fade-down">
            <h1 class="text-4xl md:text-5xl font-black mb-4 text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-400"><?php echo $lang['contact_title']; ?></h1>
            <p class="text-blue-100/80 max-w-xl mx-auto"><?php echo $lang['contact_subtitle']; ?></p>
        </div>
    </header>

    <div class="container mx-auto py-16 px-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
            <div class="bg-white p-10 rounded-[2.5rem] shadow-2xl shadow-blue-900/5 border border-gray-50" data-aos="fade-left">
                <form action="#" method="POST" class="space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="text" class="w-full bg-gray-50 border border-gray-100 p-4 rounded-2xl outline-none focus:ring-4 focus:ring-blue-500/10 transition" placeholder="<?php echo $_SESSION['lang'] === 'en' ? 'Full Name' : 'الاسم الكامل'; ?>">
                        <input type="email" class="w-full bg-gray-50 border border-gray-100 p-4 rounded-2xl outline-none focus:ring-4 focus:ring-blue-500/10 transition" placeholder="<?php echo $_SESSION['lang'] === 'en' ? 'Email Address' : 'البريد الإلكتروني'; ?>">
                    </div>
                    <input type="text" class="w-full bg-gray-50 border border-gray-100 p-4 rounded-2xl outline-none focus:ring-4 focus:ring-blue-500/10 transition" placeholder="<?php echo $_SESSION['lang'] === 'en' ? 'Subject' : 'موضوع الرسالة'; ?>">
                    <textarea class="w-full bg-gray-50 border border-gray-100 p-4 rounded-2xl h-40 outline-none focus:ring-4 focus:ring-blue-500/10 transition" placeholder="<?php echo $_SESSION['lang'] === 'en' ? 'Your message here...' : 'رسالتك هنا...'; ?>"></textarea>
                    <button class="w-full bg-[#122a4e] text-white py-4 rounded-2xl font-black text-lg hover:bg-blue-900 transition-all shadow-xl shadow-blue-900/20"><?php echo $_SESSION['lang'] === 'en' ? 'Send Message' : 'إرسال الرسالة'; ?></button>
                </form>
            </div>

            <div class="space-y-8" data-aos="fade-right">
                <div class="bg-gradient-to-br from-[#122a4e] to-[#2C69B3] text-white p-10 rounded-[2.5rem] shadow-xl">
                    <h3 class="text-2xl font-black mb-8 border-b border-white/10 pb-4"><?php echo $_SESSION['lang'] === 'en' ? 'Contact Information' : 'معلومات التواصل'; ?></h3>
                    <div class="space-y-6">
                        <p class="flex items-center gap-4 text-blue-50 font-medium">
                            <span class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center text-xl">📍</span> <?php echo $lang['contact_hq']; ?>
                        </p>
                        <p class="flex items-center gap-4 text-blue-50 font-medium">
                            <span class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center text-xl">📞</span> <?php echo $lang['contact_phone']; ?> +212 600 00 00 00
                        </p>
                        <p class="flex items-center gap-4 text-blue-50 font-medium">
                            <span class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center text-xl">✉️</span> <?php echo $lang['contact_email']; ?> contact@assoc.ma
                        </p>
                    </div>
                </div>
                <div class="h-64 rounded-[2.5rem] overflow-hidden shadow-2xl border-4 border-white">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3323.84635!2d-7.60!3d33.57!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMzPCsDM0JzEyLjAiTiA3wrAzNSc2MC4wIlc!5e0!3m2!1sfr!2sma!4v1620000000000!5m2!1sfr!2sma" width="100%" height="100%" style="border:0;" loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>AOS.init({ duration: 800, once: true });</script>
</body>
</html>