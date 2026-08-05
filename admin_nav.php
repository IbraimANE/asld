<?php require_once 'lang_setup.php'; ?>
<div class="bg-indigo-900 text-white w-64 min-h-screen p-6 hidden md:block">
    <h2 class="text-2xl font-bold mb-10 text-center">AssocFlow Admin</h2>
    <nav class="space-y-4">
        <a href="admin.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-indigo-800">
            👥 <?php echo $lang['members_management']; ?>
        </a>
        <a href="admin_articles.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-indigo-800">
            📄 <?php echo $lang['articles_moderation']; ?>
        </a>
        <a href="index.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-indigo-800 border-t border-indigo-700 pt-4">
            🏠 <?php echo $lang['view_site']; ?>
        </a>
        <a href="logout.php" class="block py-2.5 px-4 rounded transition duration-200 text-red-400 hover:bg-red-900 mt-10">
            🚪 <?php echo $lang['logout']; ?>
        </a>
    </nav>
</div>