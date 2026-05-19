<?php
/**
 * RAY-U TARIM — Surum Bilgisi (Single Source of Truth)
 *
 * Bu dosya manifest.json'dan calistirma zamaninda surum okur.
 * Tum kod buradan get_version() / get_manifest() kullanmalidir.
 * ASLA versiyon stringi hard-code edilmez.
 */

declare(strict_types=1);

if (!defined('RU_BASE')) {
    define('RU_BASE', dirname(__DIR__));
}

/**
 * manifest.json icerigini cached olarak dondurur.
 *
 * @return array<string,mixed>
 */
function ru_manifest(): array
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }

    $path = RU_BASE . '/manifest.json';
    if (!is_file($path)) {
        // Failsafe — manifest yoksa bos array
        return $cache = [];
    }

    $raw = file_get_contents($path);
    if ($raw === false) {
        return $cache = [];
    }

    $data = json_decode($raw, true);
    return $cache = is_array($data) ? $data : [];
}

/**
 * Surum numarasini dondurur (orn: "0.1.0")
 */
function ru_version(): string
{
    $m = ru_manifest();
    return (string)($m['version'] ?? '0.0.0');
}

/**
 * Project slug
 */
function ru_slug(): string
{
    $m = ru_manifest();
    return (string)($m['slug'] ?? 'rayutarim');
}

/**
 * Project repo (GitHub: owner/name)
 */
function ru_repo(): string
{
    $m = ru_manifest();
    return (string)($m['repo'] ?? 'codegatr/rayutarim');
}

/**
 * DB tablo prefixi
 */
function ru_db_prefix(): string
{
    $m = ru_manifest();
    return (string)($m['db']['prefix'] ?? 'ru_');
}

/**
 * Surum metadata olarak hayatlik kontrolu icin export
 */
function ru_version_info(): array
{
    $m = ru_manifest();
    return [
        'name'         => (string)($m['name'] ?? 'Ray-U Tarim'),
        'version'      => ru_version(),
        'release_date' => (string)($m['release_date'] ?? ''),
        'repo'         => ru_repo(),
        'php_min'      => (string)($m['php_min'] ?? '8.3'),
    ];
}
