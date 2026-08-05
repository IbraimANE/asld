<?php
require_once 'lang_setup.php'; // استدعاء ملف إعدادات اللغة الديناميكي وجلسة المستخدم
require_once 'config.php';

try {
    $query = "SELECT articles.*, users.nom_complet 
              FROM articles 
              JOIN users ON articles.auteur_id = users.id 
              WHERE articles.status = 'publie' 
              ORDER BY articles.id DESC";
    $stmt = $pdo->query($query);
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $total_articles = count($articles);
    $stmt_users = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'تم القبول'");
    $total_members = $stmt_users->fetchColumn();
} catch (Exception $e) { $articles = []; }
?>
<!DOCTYPE html>
<html lang="<?php echo $lang['lang_code']; ?>" dir="<?php echo $lang['direction']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['site_title']; ?></title>
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
        .hero-bg {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            position: relative;
        }
        .hero-bg::before {
            content: '';
            position: absolute;
            top: -50%; left: -50%; width: 200%; height: 200%;
            background: radial-gradient(circle, rgba(234,179,8,0.1) 0%, transparent 50%);
            animation: pulse-glow 10s infinite alternate;
            pointer-events: none;
        }
        @keyframes pulse-glow {
            0% { transform: scale(1); }
            100% { transform: scale(1.1); }
        }
        .card-modern {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card-modern:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 60px -15px rgba(30, 58, 138, 0.25);
            border-color: rgba(30, 58, 138, 0.1);
        }
        
        /* إضافة تأثير العلامة المائية للشعار الجديد في الخلفية دون التأثير على العناصر */
        .home-watermark {
            position: relative;
        }
        .home-watermark::before {
            content: "";
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 700px;
            height: 700px;
            background-image: url('image/Gemini_Generated_Image_yz35cpyz35cpyz35.png');
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            opacity: 0.04;
            z-index: -1;
            pointer-events: none;
        }
    </style>
</head>
<body class="text-gray-800 home-watermark">

    <nav class="glass-nav sticky top-0 z-50 h-24 flex items-center transition-all duration-300">
        <div class="container mx-auto px-6 flex justify-between items-center">
            <div class="flex items-center gap-6">
                <a href="index.php" class="flex items-center transition-transform hover:scale-105">
                    <img src="image/logo_assoc.jpg" alt="Logo" class="h-16 w-auto object-contain rounded-2xl drop-shadow-xl">
                </a>
                <div class="hidden md:flex items-center gap-4">
                    <a href="index.php" class="text-blue-900 font-black px-4 py-2 hover:text-blue-600 transition-colors"><?php echo $lang['nav_home']; ?></a>
                    <a href="membres.php" class="text-gray-600 font-bold px-4 py-2 hover:text-blue-600 transition-colors"><?php echo $lang['nav_members']; ?></a>
                    <a href="contact.php" class="text-gray-600 font-bold px-4 py-2 hover:text-blue-600 transition-colors"><?php echo $lang['nav_contact']; ?></a>
                </div>
            </div>
            <div class="flex items-center gap-4">
                
                <div class="flex gap-2 bg-white/80 border border-gray-200 px-3 py-1.5 rounded-xl font-bold text-sm shadow-sm">
                    <a href="?lang=ar" class="<?php echo $_SESSION['lang'] == 'ar' ? 'text-blue-900 font-black underline' : 'text-gray-400 hover:text-blue-900'; ?>">العربية</a>
                    <span class="text-gray-300">|</span>
                    <a href="?lang=en" class="<?php echo $_SESSION['lang'] == 'en' ? 'text-blue-900 font-black underline' : 'text-gray-400 hover:text-blue-900'; ?>">English</a>
                </div>

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

    <header class="hero-bg text-white pt-24 pb-48 px-6 text-center overflow-hidden">
        <div class="container mx-auto relative z-10 flex flex-col items-center">
            
            <div style="display: flex; justify-content: center; align-items: center; width: 100%; margin-bottom: 2rem;">
                <img src="image/logo_assoc.jpg" alt="Logo" style="max-width: 450px; width: 100%; height: auto; object-fit: contain;">
            </div>
            
            <h1 class="text-5xl md:text-7xl font-black mb-6 leading-tight drop-shadow-lg">
                <?php echo $lang['hero_title_1']; ?><br><span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-400"><?php echo $lang['hero_title_2']; ?></span>
            </h1>
            
            <p class="text-xl text-blue-100 mb-10 max-w-2xl mx-auto leading-relaxed font-medium">
                <?php echo $lang['hero_description']; ?>
            </p>
            
            <a href="#activities" class="bg-white text-blue-950 px-10 py-4 rounded-2xl font-black text-lg hover:bg-yellow-400 hover:text-blue-900 transition-all shadow-2xl hover:-translate-y-1">
                <?php echo $lang['explore_btn']; ?>
            </a>

        </div>

        <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none z-0">
            <svg class="relative block w-full h-[150px]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" opacity=".25" fill="#f0f4f8"></path>
                <path d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z" opacity=".5" fill="#f0f4f8"></path>
                <path d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z" fill="#f0f4f8"></path>
            </svg>
        </div>
    </header>

    <section id="about-section" class="py-24 container mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <div data-aos="fade-up" class="bg-white p-10 rounded-[2.5rem] shadow-xl shadow-blue-900/5 border border-gray-100 flex flex-col justify-between">
                <div>
                    <h2 class="text-3xl font-black text-blue-900 mb-6 pb-2 border-b-2 border-yellow-400 inline-block">
                        <a href="about_details.php" class="hover:text-blue-700 transition-colors"><?php echo $lang['about_title']; ?></a>
                    </h2>
                    <p class="text-gray-600 leading-relaxed mb-8 text-lg">
                        <?php echo $lang['about_desc']; ?>
                    </p>
                </div>
                <a href="about_details.php" class="w-full bg-[#122a4e] text-white py-4 rounded-2xl font-black text-center block hover:bg-blue-900 transition-all shadow-lg"><?php echo $lang['read_more']; ?></a>
            </div>

            <div class="bg-white p-10 rounded-[2.5rem] shadow-xl shadow-blue-900/5 border border-gray-100 flex flex-col justify-between" data-aos="fade-up" data-aos-delay="100">
                <div>
                    <h2 class="text-3xl font-black text-blue-900 mb-6 pb-2 border-b-2 border-yellow-400 inline-block">
                        <a href="president_details.php" class="hover:text-blue-700 transition-colors"><?php echo $lang['president_title']; ?></a>
                    </h2>
                    <p class="text-gray-600 leading-relaxed mb-8 text-lg">
                        <?php echo $lang['president_desc']; ?>
                    </p>
                </div>
                <a href="president_details.php" class="w-full bg-[#122a4e] text-white py-4 rounded-2xl font-black text-center block hover:bg-blue-900 transition-all shadow-lg"><?php echo $lang['read_more']; ?></a>
            </div>
        </div>
    </section>

    <main id="activities" class="py-16 container mx-auto px-6">
        <div class="mb-20 text-center" data-aos="fade-up">
            <h2 class="text-5xl font-black text-blue-900 mb-6 drop-shadow-sm"><?php echo $lang['activities_title']; ?></h2>
            <div class="h-2 w-32 bg-gradient-to-r from-blue-600 to-yellow-400 mx-auto rounded-full"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
            <?php foreach($articles as $art): ?>
            <article data-aos="fade-up" class="card-modern rounded-[2rem] overflow-hidden flex flex-col">
                <div class="relative h-72 overflow-hidden">
                    <div class="absolute inset-0 bg-blue-900/20 z-10 mix-blend-multiply"></div>
                    <img src="uploads_activites/<?php echo $art['image_article']; ?>" class="w-full h-full object-cover transition-transform duration-1000 hover:scale-125" alt="Article illustration">
                    <div class="absolute top-6 right-6 z-20">
                        <span class="bg-white/90 backdrop-blur-md text-blue-900 px-5 py-2 rounded-xl text-sm font-black shadow-lg">
                            <?php echo htmlspecialchars($art['category'] ?? 'عام'); ?>
                        </span>
                    </div>
                </div>
                <div class="p-8 flex-grow flex flex-col">
                    <p class="text-xs font-bold text-gray-400 mb-4 tracking-widest uppercase">
                        <?php echo $lang['author_prefix'] . ' ' . htmlspecialchars($art['nom_complet']); ?>
                    </p>
                    <h3 class="text-3xl font-black text-blue-950 mb-6 leading-tight"><?php echo htmlspecialchars($art['titre']); ?></h3>
                    <p class="text-gray-600 leading-relaxed mb-8 line-clamp-3">
                        <?php echo htmlspecialchars(mb_substr(strip_tags($art['contenu']), 0, 150)); ?>
                    </p>
                    <a href="article_details.php?id=<?php echo $art['id']; ?>" class="mt-auto block w-full bg-gray-50 text-blue-900 py-4 rounded-xl font-bold text-center hover:bg-blue-900 hover:text-white transition-colors border border-gray-100"><?php echo $lang['view_details']; ?></a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
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