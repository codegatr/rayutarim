<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/inc/bootstrap.php';

if (ru_admin_user()) {
    ru_redirect('/admin/index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!ru_csrf_check()) {
        $error = 'Güvenlik doğrulaması başarısız.';
    } elseif (ru_admin_login((string)($_POST['email'] ?? ''), (string)($_POST['password'] ?? ''))) {
        ru_redirect('/admin/index.php');
    } else {
        $error = 'E-posta veya şifre hatalı.';
    }
}
?><!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Giriş - RAYU</title>
<link rel="stylesheet" href="/admin/assets/admin.css">
</head>
<body class="login-body">
  <form class="login-card" method="post">
    <img src="/assets/img/logo.svg" alt="RAYU">
    <h1>Yönetim Paneli</h1>
    <?php if ($error): ?><div class="admin-alert admin-alert--error"><?= h($error) ?></div><?php endif; ?>
    <?= ru_csrf_field() ?>
    <label>E-posta<input type="email" name="email" required autofocus></label>
    <label>Şifre<input type="password" name="password" required></label>
    <button class="btn btn--primary" type="submit" style="width:100%;margin-top:14px">Giriş Yap</button>
  </form>
</body>
</html>
