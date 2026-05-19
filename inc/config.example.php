<?php
/**
 * RAY-U TARIM — Yapilandirma Sablonu
 *
 * KURULUM:
 * 1. Bu dosyayi inc/config.php olarak kopyalayin
 * 2. Asagidaki degerleri kendi sunucunuza gore duzenleyin
 * 3. install.php'yi tarayicidan acin (otomatik tabloyu olusturur)
 * 4. Kurulumdan sonra install.php'yi silin veya inc/.installed dosyasi olusturun
 *
 * NOT: inc/config.php dosyasi .gitignore'da, GitHub'a push EDILMEZ.
 * Smart Update guncellemelerinde de kullaniciya ait config.php KORUNUR.
 */

declare(strict_types=1);

// ─────────────────────────────────────────────────────────────────────
// VERITABANI
// ─────────────────────────────────────────────────────────────────────
return [
    'db' => [
        'host'     => 'localhost',
        'port'     => 3306,
        'name'     => 'rayutarim_db',
        'user'     => 'rayutarim_user',
        'pass'     => 'CHANGE_ME_STRONG_PASSWORD',
        'charset'  => 'utf8mb4',
        'collate'  => 'utf8mb4_unicode_ci',
        'prefix'   => 'ru_',
    ],

    // ─────────────────────────────────────────────────────────────────
    // SITE
    // ─────────────────────────────────────────────────────────────────
    'site' => [
        'url'       => 'https://rayutarim.com',
        'name'      => 'Ray-U Tarim',
        'tagline'   => 'Tarim makineleri ve ziraai ilaclar',
        'admin_email' => 'admin@rayutarim.com',
        'timezone'  => 'Europe/Istanbul',
        'locale'    => 'tr_TR',
        'debug'     => false,                  // production: false
    ],

    // ─────────────────────────────────────────────────────────────────
    // GUVENLIK
    // ─────────────────────────────────────────────────────────────────
    'security' => [
        // openssl rand -base64 32 ile uretin
        'app_key'        => 'CHANGE_ME_TO_RANDOM_64_CHAR_STRING_FOR_HMAC_AND_ENC',
        'session_name'   => 'RU_SESS',
        'session_lifetime' => 7200,            // 2 saat
        'cookie_secure'  => true,              // HTTPS gerekli
        'cookie_samesite'=> 'Lax',
        'login_max_attempts' => 5,
        'login_lockout_seconds' => 900,        // 15 dakika
    ],

    // ─────────────────────────────────────────────────────────────────
    // YUKLEMELER
    // ─────────────────────────────────────────────────────────────────
    'uploads' => [
        'max_size_bytes' => 8 * 1024 * 1024,   // 8 MB
        'allowed_image'  => ['jpg', 'jpeg', 'png', 'webp', 'gif'],
        'allowed_doc'    => ['pdf'],
        'path'           => 'uploads',
        'webp_quality'   => 85,
    ],

    // ─────────────────────────────────────────────────────────────────
    // SMART UPDATE
    // ─────────────────────────────────────────────────────────────────
    'update' => [
        // GitHub PAT (sadece private repo icin gerekli)
        'github_token'   => '',
        'backup_dir'     => 'backups',
        'auto_check'     => true,
        'check_interval' => 3600,              // 1 saat
    ],

    // ─────────────────────────────────────────────────────────────────
    // SMTP (opsiyonel — Faz 5'te kullanilacak)
    // ─────────────────────────────────────────────────────────────────
    'smtp' => [
        'enabled'  => false,
        'host'     => 'mail.rayutarim.com',
        'port'     => 587,
        'user'     => 'noreply@rayutarim.com',
        'pass'     => '',
        'from'     => 'noreply@rayutarim.com',
        'from_name'=> 'Ray-U Tarim',
        'secure'   => 'tls',                   // tls | ssl | ''
    ],
];
