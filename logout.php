<?php
// بدء الجلسة للتمكن من حذفها
session_start();

// حذف جميع متغيرات الجلسة
$_SESSION = array();

// تدمير الجلسة بالكامل من الخادم
session_destroy();

// التوجيه إلى الصفحة الرئيسية فوراً
header("Location: index.php");
exit();
?>