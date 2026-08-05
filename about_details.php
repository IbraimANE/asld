<?php
require_once 'lang_setup.php';
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang['lang_code']; ?>" dir="<?php echo $lang['direction']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['about_page_title']; ?></title>
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
                    <a href="index.php" class="text-gray-600 font-bold px-4 py-2 hover:text-blue-600 transition-colors"><?php echo $lang['nav_home']; ?></a>
                    <a href="membres.php" class="text-gray-600 font-bold px-4 py-2 hover:text-blue-600 transition-colors"><?php echo $lang['nav_members']; ?></a>
                    <a href="contact.php" class="text-gray-600 font-bold px-4 py-2 hover:text-blue-600 transition-colors"><?php echo $lang['nav_contact']; ?></a>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="espace_membre.php" class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white px-6 py-3 rounded-xl font-black shadow-lg hover:shadow-indigo-500/30 hover:-translate-y-1 transition-all"><?php echo $lang['dashboard']; ?></a>
                    <a href="logout.php" class="bg-red-50 text-red-600 px-6 py-3 rounded-xl font-bold hover:bg-red-100 transition-colors"><?php echo $lang['logout']; ?></a>
                <?php else: ?>
                    <a href="login.php" class="text-blue-900 font-bold px-4 py-2 hover:text-blue-600 transition-colors"><?php echo $lang['login']; ?></a>
                    <a href="inscription.php" class="bg-gradient-to-r from-yellow-400 to-yellow-500 text-blue-900 px-6 py-3 rounded-xl font-black shadow-lg hover:shadow-yellow-500/30 hover:-translate-y-1 transition-all"><?php echo $lang['join_us']; ?></a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main class="py-20 container mx-auto px-6 max-w-4xl">
        <article data-aos="fade-up" class="bg-white p-12 rounded-[2.5rem] shadow-xl shadow-blue-900/5 border border-gray-100">
            <h1 class="text-4xl font-black text-blue-900 mb-8 pb-4 border-b-2 border-yellow-400 inline-block"><?php echo $lang['about_page_title']; ?></h1>
            
            <div class="space-y-8 text-gray-700 text-lg leading-relaxed text-justify">
                
                <div>
                    <h3 class="text-2xl font-black text-blue-950 mb-3"><?php echo $_SESSION['lang'] === 'en' ? 'Identity & Establishment' : 'الهوية والتأسيس'; ?></h3>
                    <p>
                        <?php echo $_SESSION['lang'] === 'en' 
                            ? '"Boundless Initiatives" is a Moroccan civil organization founded in 2025 in Deroua, Berrechid Province. The association operates in accordance with the laws regulating associations in the Kingdom of Morocco, and is headquartered at the Deroua Youth Center, with ambitions to expand through national and international partnerships.' 
                            : 'جمعية "مبادرات بلا حدود" منظمة مدنية مغربية تأسست سنة 2025 بمدينة الدروة إقليم برشيد. تعمل الجمعية وفق القانون المنظم للجمعيات بالمملكة المغربية، وتتخذ من دار الشباب دروة مقراً لها، مع طموح للتوسع عبر شراكات وطنية ودولية.'; ?>
                    </p>
                </div>

                <div>
                    <h3 class="text-2xl font-black text-blue-950 mb-3"><?php echo $_SESSION['lang'] === 'en' ? 'Mission & Objectives' : 'الرسالة والأهداف'; ?></h3>
                    <p>
                        <?php echo $_SESSION['lang'] === 'en' 
                            ? 'The association is committed to achieving human and sustainable development by empowering vulnerable groups, particularly youth, women, and children. Our vision focuses on enhancing social and economic inclusion through vocational training, social accompaniment, and environmental action.' 
                            : 'تلتزم الجمعية بتحقيق التنمية البشرية والمستدامة عبر تمكين الفئات الهشة، لاسيما الشباب والنساء والأطفال. تركز رؤيتنا على تعزيز الإدماج الاجتماعي والاقتصادي من خلال التأهيل المهني، المواكبة الاجتماعية، والعمل البيئي.'; ?>
                    </p>
                </div>

                <div>
                    <h3 class="text-2xl font-black text-blue-950 mb-3"><?php echo $_SESSION['lang'] === 'en' ? 'Programs & Field Interventions' : 'البرامج والتدخلات الميدانية'; ?></h3>
                    <p class="mb-4"><?php echo $_SESSION['lang'] === 'en' ? 'The association works through practical tracks including:' : 'تعمل الجمعية عبر مسارات عملية تشمل:'; ?></p>
                    <ul class="space-y-4 <?php echo $_SESSION['lang'] === 'en' ? 'pl-2' : 'pr-2'; ?>">
                        <li class="flex items-start gap-2">
                            <span class="text-yellow-500 font-black mt-1">▪</span>
                            <div>
                                <strong class="text-blue-900"><?php echo $_SESSION['lang'] === 'en' ? 'Education & Training:' : 'التربية والتكوين:'; ?></strong> <?php echo $_SESSION['lang'] === 'en' ? 'Providing educational support classes and combating school dropouts.' : 'تقديم دروس الدعم التربوي ومحاربة الهدر المدرسي.'; ?>
                            </div>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-yellow-500 font-black mt-1">▪</span>
                            <div>
                                <strong class="text-blue-900"><?php echo $_SESSION['lang'] === 'en' ? 'Vocational Qualification:' : 'التأهيل المهني:'; ?></strong> <?php echo $_SESSION['lang'] === 'en' ? 'Organizing training courses for youth and women to enhance their employability.' : 'تنظيم دورات تكوينية لفائدة الشباب والنساء لتعزيز قدراتهم التشغيلية.'; ?>
                            </div>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-yellow-500 font-black mt-1">▪</span>
                            <div>
                                <strong class="text-blue-900"><?php echo $_SESSION['lang'] === 'en' ? 'Social Accompaniment:' : 'المواكبة الاجتماعية:'; ?></strong> <?php echo $_SESSION['lang'] === 'en' ? 'Providing listening, guidance, and field support spaces.' : 'توفير فضاءات للاستماع والتوجيه والدعم الميداني.'; ?>
                            </div>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-yellow-500 font-black mt-1">▪</span>
                            <div>
                                <strong class="text-blue-900"><?php echo $_SESSION['lang'] === 'en' ? 'Sustainability & Sports:' : 'الاستدامة والرياضة:'; ?></strong> <?php echo $_SESSION['lang'] === 'en' ? 'Supporting and organizing environmental awareness activities, cultural and sports initiatives for various groups.' : 'دعم وتنظيم أنشطة بيئية تحسيسية ومبادرات ثقافية ورياضية لمختلف الفئات.'; ?>
                            </div>
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-2xl font-black text-blue-950 mb-3"><?php echo $_SESSION['lang'] === 'en' ? 'Partnerships & Financial Sustainability' : 'الشراكات والاستدامة المالية'; ?></h3>
                    <p>
                        <?php echo $_SESSION['lang'] === 'en' 
                            ? 'The association believes in the importance of opening up to government sectors, local authorities, and the private sector to develop projects with sustainable impact. It relies on member contributions, public support, and self-developed developmental partnerships to ensure the continuity of its programs.' 
                            : 'تؤمن الجمعية بأهمية الانفتاح على القطاعات الحكومية، الجماعات الترابية، والقطاع الخاص لتطوير مشاريع ذات أثر مستدام. تعتمد في تمويلها على مساهمات الأعضاء، الدعم العمومي، والشراكات التنموية الذاتية بما يضمن استمرارية برامجها.'; ?>
                    </p>
                </div>

                <div>
                    <h3 class="text-2xl font-black text-blue-950 mb-3"><?php echo $lang['about_vision_title']; ?></h3>
                    <p>
                        <?php echo $lang['about_vision_desc']; ?>
                    </p>
                </div>

                <div class="<?php echo $_SESSION['lang'] === 'en' ? 'border-l-4 pl-6' : 'border-r-4 pr-6'; ?> bg-blue-50 p-6 rounded-2xl border-blue-900 italic font-medium text-blue-950">
                    <?php echo $lang['about_royal_quote']; ?>
                </div>

            </div>

            <div class="mt-12 pt-6 border-t border-gray-100 flex justify-end">
                <a href="index.php" class="bg-blue-900 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-800 transition-colors shadow-md"><?php echo $lang['back_home']; ?></a>
            </div>
        </article>
    </main>

    <footer class="bg-white py-12 border-t border-gray-200">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-2xl font-black text-blue-900 mb-4"><?php echo $lang['footer_title']; ?></h2>
            <p class="text-gray-500 font-bold">&copy; <?php echo date("Y"); ?> <?php echo $lang['footer_rights']; ?></p>
        </div>
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>AOS.init({duration: 800, once: true});</script>
</body>
</html>