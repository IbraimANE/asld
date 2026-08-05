<?php
session_start();
require_once 'config.php';

// حماية الصفحة
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['action'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];

    // فحص الأكشن: نقبل 'valider' أو 'accepter' لضمان عمل الزر
    if ($action === 'valider' || $action === 'accepter') {
        
        /* هام جداً: إذا كانت قاعدة بياناتك تستخدم الكلمات العربية في الـ ENUM
           يجب أن نضع 'تم القبول'. إذا كانت تستخدم 'valide' نضع 'valide'.
           بناءً على الكود السابق، سنضع 'تم القبول' لتظهر في الإحصائيات.
        */
        $status = 'تم القبول'; 

    } elseif ($action === 'rejeter' || $action === 'refuser') {
        $status = 'مرفوض';
    } else {
        header("Location: admin.php");
        exit();
    }

    try {
        $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
        
        header("Location: admin.php");
        exit();
    } catch (PDOException $e) {
        die("خطأ تقني: " . $e->getMessage());
    }
} else {
    header("Location: admin.php");
    exit();
}