<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/inc/bootstrap.php';

$count = (int)ru_fetch_scalar('SELECT COUNT(*) FROM ' . ru_t('users'));
if ($count > 0) {
    http_response_code(403);
    echo '<!doctype html><html lang="tr"><head><meta charset="utf-8"><title>Admin Kurtarma Kapalı</title></head><body>';
    echo '<h1>Admin kurtarma kapalı</h1><p>Sistemde zaten yönetici hesabı var. Giriş sayfasını kullanın.</p>';
    echo '<p><a href="/admin/login.php">Admin girişine dön</a></p></body></html>';
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!ru_csrf_check()) {
        $error = 'Güvenlik doğrulaması başarısız.';
    } else {
        $email = strtolower(trim((string)($_POST['email'] ?? '')));
        $name = trim((string)($_POST['full_name'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        $password2 = (string)($_POST['password2'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Geçerli bir e-posta girin.';
        } elseif ($name === '') {
            $error = 'Ad soyad zorunlu.';
        } elseif (strlen($password) < 10) {
            $error = 'Şifre en az 10 karakter olmalı.';
        } elseif ($password !== $password2) {
            $error = 'Şifreler eşleşmiyor.';
        } else {
            ru_exec(
                'INSERT INTO ' . ru_t('users') . ' (email, password_hash, full_name, role, is_active, created_at, updated_at)
                 VALUES (?, ?, ?, "superadmin", 1, NOW(), NOW())',
                [$email, password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]), $name]
            );
            ru_flash('success', 'Superadmin hesabı oluşturuldu. Güvenlik için recover.php dosyasını sunucudan silin.');
            ru_redirect('/admin/login.php');
        }
    }
}
?><!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Kurtarma - RAYU</title>
<link rel="stylesheet" href="/admin/assets/admin.css">
</head>
<body class="login-body">
  <form class="login-card" method="post">
    <img src="/assets/img/logo.svg" alt="RAYU">
    <h1>İlk Admin Oluştur</h1>
    <?php if ($error): ?><div class="admin-alert admin-alert--error"><?= h($error) ?></div><?php endif; ?>
    <?= ru_csrf_field() ?>
    <p style="margin-top:0;color:#667">Bu ekran yalnızca veritabanında hiç yönetici yokken çalışır. Hesabı oluşturduktan sonra <code>admin/recover.php</code> dosyasını silin.</p>
    <label>Ad Soyad<input name="full_name" required autofocus></label>
    <label>E-posta<input type="email" name="email" value="info@rayutarim.com" required></label>
    <label>Şifre<input type="password" name="password" required minlength="10"></label>
    <label>Şifre Tekrar<input type="password" name="password2" required minlength="10"></label>
    <button class="btn btn--primary" type="submit" style="width:100%;margin-top:14px">Superadmin Oluştur</button>
  </form>
</body>
</html>
