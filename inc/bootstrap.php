<?php
/**
 * RAY-U TARIM — Bootstrap
 *
 * Tum giris noktalari (index.php, admin/*.php, ajax handlerlari)
 * sadece bunu require eder:
 *
 *     require_once __DIR__ . '/inc/bootstrap.php';
 */

declare(strict_types=1);

// Hata raporlama — debug moduna gore
define('RU_START_TIME', microtime(true));

if (!defined('RU_BASE')) {
    define('RU_BASE', dirname(__DIR__));
}

// Cekirdek dosyalar
require_once __DIR__ . '/version.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/db.php';

// config.php yoksa kurulumu zorla
$configPath = __DIR__ . '/config.php';
$installerActive = str_contains($_SERVER['SCRIPT_NAME'] ?? '', 'install.php');
if (!is_file($configPath) && !$installerActive) {
    header('Location: /install.php');
    exit;
}

if (is_file($configPath)) {
    // Hata raporlama (config yuklendikten sonra)
    $debug = (bool)(ru_config('site.debug') ?? false);
    if ($debug) {
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
    } else {
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
        ini_set('display_errors', '0');
        ini_set('log_errors', '1');
    }

    // Zaman dilimi
    $tz = (string)(ru_config('site.timezone') ?? 'Europe/Istanbul');
    date_default_timezone_set($tz);

    // Session baslat
    ru_session_start();
}

// Faz 1'de yalnizca cekirdek var. Sonraki fazlarda buraya eklenecek:
// - i18n/locale yukleyici (Faz 7)
// - menu/page cache (Faz 2)
// - urun cache (Faz 3)
