<?php
require_once 'lang_setup.php';
require_once 'config.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->query("SELECT * FROM users WHERE role != 'admin' ORDER BY date_inscription DESC");
$users = $stmt->fetchAll();

$total_members = count($users);
$pending_count = 0;
$accepted_count = 0;

foreach ($users as $u) {
    if ($u['status'] == 'en_attente' || $u['status'] == 'قيد الانتظار') $pending_count++;
    if ($u['status'] == 'تم القبول' || $u['status'] == 'valide') $accepted_count++;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang['lang_code']; ?>" dir="<?php echo $lang['direction']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $_SESSION['lang'] === 'en' ? 'Dashboard - Association Management' : 'لوحة التحكم - إدارة الجمعية'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Tajawal', sans-serif; background-color: #f4f7f6; }
        .glass-card {
            background: #ffffff;
            border-radius: 1.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            border: 1px solid rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .glass-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(0,0,0,0.06); }
        .table-row-hover:hover { background-color: #f8fafc; }
    </style>
</head>
<body class="text-gray-800">

    <nav class="bg-blue-950 text-white p-5 shadow-2xl sticky top-0 z-50">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-black tracking-wide"><?php echo $_SESSION['lang'] === 'en' ? 'Administrative Center' : 'المركز الإداري'; ?></h1>
            <div class="flex gap-4">
                <a href="index.php" class="bg-white/10 px-5 py-2.5 rounded-xl font-bold hover:bg-white/20 transition-all"><?php echo $lang['nav_home'] ?? 'الرئيسية'; ?></a>
                <a href="logout.php" class="bg-red-500 px-5 py-2.5 rounded-xl font-bold hover:bg-red-600 transition-all shadow-lg shadow-red-500/30"><?php echo $lang['logout'] ?? 'خروج'; ?></a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto py-12 px-6">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <div class="glass-card p-8 <?php echo $_SESSION['lang'] === 'en' ? 'border-l-8' : 'border-r-8'; ?> border-blue-600">
                <p class="text-sm font-bold text-gray-400 mb-2"><?php echo $_SESSION['lang'] === 'en' ? 'Total Registered' : 'إجمالي المسجلين'; ?></p>
                <p class="text-5xl font-black text-blue-950"><?php echo $total_members; ?></p>
            </div>
            <div class="glass-card p-8 <?php echo $_SESSION['lang'] === 'en' ? 'border-l-8' : 'border-r-8'; ?> border-amber-500">
                <p class="text-sm font-bold text-gray-400 mb-2"><?php echo $_SESSION['lang'] === 'en' ? 'Pending Requests' : 'طلبات معلقة'; ?></p>
                <p class="text-5xl font-black text-amber-600"><?php echo $pending_count; ?></p>
            </div>
            <div class="glass-card p-8 <?php echo $_SESSION['lang'] === 'en' ? 'border-l-8' : 'border-r-8'; ?> border-emerald-500">
                <p class="text-sm font-bold text-gray-400 mb-2"><?php echo $_SESSION['lang'] === 'en' ? 'Approved Members' : 'أعضاء معتمدون'; ?></p>
                <p class="text-5xl font-black text-emerald-600"><?php echo $accepted_count; ?></p>
            </div>
        </div>

        <div class="glass-card overflow-hidden">
            <div class="bg-gray-50/50 p-6 border-b border-gray-100">
                <h2 class="text-2xl font-black text-blue-950"><?php echo $_SESSION['lang'] === 'en' ? 'Requests and Members List' : 'قائمة الطلبات والأعضاء'; ?></h2>
            </div>
            <div class="overflow-x-auto p-4">
                <table class="w-full <?php echo $_SESSION['lang'] === 'en' ? 'text-left' : 'text-right'; ?> border-collapse">
                    <thead>
                        <tr class="text-gray-400 text-sm border-b border-gray-100">
                            <th class="pb-4 px-4 font-bold"><?php echo $_SESSION['lang'] === 'en' ? 'Identity' : 'الهوية'; ?></th>
                            <th class="pb-4 px-4 font-bold"><?php echo $_SESSION['lang'] === 'en' ? 'Contact' : 'التواصل'; ?></th>
                            <th class="pb-4 px-4 font-bold"><?php echo $_SESSION['lang'] === 'en' ? 'Status' : 'الوضعية'; ?></th>
                            <th class="pb-4 px-4 font-bold"><?php echo $_SESSION['lang'] === 'en' ? 'Documents' : 'الوثائق'; ?></th>
                            <th class="pb-4 px-4 font-bold text-center"><?php echo $_SESSION['lang'] === 'en' ? 'Action' : 'الإجراء'; ?></th>
                        </tr>
                    </thead>
                    <tbody class="text-sm font-medium">
                        <?php foreach ($users as $user): ?>
                        <tr class="table-row-hover border-b border-gray-50 transition-colors">
                            <td class="py-5 px-4">
                                <div class="flex items-center gap-4">
                                    <?php $photoPath = "uploads_photos/" . $user['photo_profil'];
                                    if (!empty($user['photo_profil']) && file_exists($photoPath)): ?>
                                        <img src="<?php echo $photoPath; ?>" class="w-14 h-14 rounded-xl object-cover shadow-sm">
                                    <?php else: ?>
                                        <div class="w-14 h-14 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400 border border-gray-200"><?php echo $_SESSION['lang'] === 'en' ? 'Photo' : 'صورة'; ?></div>
                                    <?php endif; ?>
                                    <div>
                                        <p class="font-black text-lg text-blue-950"><?php echo htmlspecialchars($user['nom_complet']); ?></p>
                                        <p class="text-gray-400 text-xs mt-1"><?php echo htmlspecialchars($user['email']); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-5 px-4 text-gray-600 font-bold"><?php echo htmlspecialchars($user['telephone'] ?? '-'); ?></td>
                            <td class="py-5 px-4">
                                <?php if($user['status'] == 'en_attente' || $user['status'] == 'قيد الانتظار'): ?>
                                    <span class="bg-amber-100 text-amber-700 px-4 py-1.5 rounded-lg text-xs font-black"><?php echo $_SESSION['lang'] === 'en' ? 'Pending' : 'قيد الانتظار'; ?></span>
                                <?php elseif($user['status'] == 'تم القبول' || $user['status'] == 'valide'): ?>
                                    <span class="bg-emerald-100 text-emerald-700 px-4 py-1.5 rounded-lg text-xs font-black"><?php echo $_SESSION['lang'] === 'en' ? 'Approved' : 'معتمد'; ?></span>
                                <?php else: ?>
                                    <span class="bg-red-100 text-red-700 px-4 py-1.5 rounded-lg text-xs font-black"><?php echo $_SESSION['lang'] === 'en' ? 'Rejected' : 'مرفوض'; ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="py-5 px-4">
                                <div class="flex gap-2">
                                    <a href="uploads_cin/<?php echo $user['cin_recto']; ?>" target="_blank" class="bg-gray-100 text-gray-600 px-3 py-1.5 rounded-lg hover:bg-blue-100 hover:text-blue-700 transition-colors text-xs font-bold"><?php echo $_SESSION['lang'] === 'en' ? 'Front' : 'الوجه 1'; ?></a>
                                    <a href="uploads_cin/<?php echo $user['cin_verso']; ?>" target="_blank" class="bg-gray-100 text-gray-600 px-3 py-1.5 rounded-lg hover:bg-blue-100 hover:text-blue-700 transition-colors text-xs font-bold"><?php echo $_SESSION['lang'] === 'en' ? 'Back' : 'الوجه 2'; ?></a>
                                </div>
                            </td>
                            <td class="py-5 px-4">
                                <div class="flex justify-center gap-2">
                                    <a href="valider_membre.php?id=<?php echo $user['id']; ?>&action=accepter" class="bg-emerald-500 text-white px-5 py-2 rounded-xl font-bold hover:bg-emerald-600 shadow-md shadow-emerald-500/20 transition-all"><?php echo $_SESSION['lang'] === 'en' ? 'Approve' : 'اعتماد'; ?></a>
                                    <a href="valider_membre.php?id=<?php echo $user['id']; ?>&action=refuser" class="bg-red-50 text-red-600 px-5 py-2 rounded-xl font-bold hover:bg-red-100 transition-all"><?php echo $_SESSION['lang'] === 'en' ? 'Reject' : 'رفض'; ?></a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>