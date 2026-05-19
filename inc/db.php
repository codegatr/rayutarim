<?php
/**
 * RAY-U TARIM — Veritabani Katmani
 *
 * - PDO baglantisi (PHP 8.3+, prepared statements)
 * - Settings k/v cache (ru_settings)
 * - Idempotent sema yardimcilari (_safeAlter)
 * - Audit log helper
 */

declare(strict_types=1);

if (!defined('RU_BASE')) {
    define('RU_BASE', dirname(__DIR__));
}

require_once __DIR__ . '/version.php';

/**
 * Yapilandirmayi yukle ve cachele
 */
function ru_config(?string $key = null): mixed
{
    static $cfg = null;
    if ($cfg === null) {
        $path = __DIR__ . '/config.php';
        if (!is_file($path)) {
            // Kurulum yapilmamis — install.php'ye yonlendir
            if (!str_contains($_SERVER['REQUEST_URI'] ?? '', 'install.php')) {
                header('Location: /install.php');
                exit;
            }
            return $key ? null : [];
        }
        $cfg = require $path;
        if (!is_array($cfg)) {
            throw new \RuntimeException('config.php dogru bir array donmuyor');
        }
    }

    if ($key === null) {
        return $cfg;
    }

    // Nokta notasyonu destegi: 'db.host'
    $segments = explode('.', $key);
    $value = $cfg;
    foreach ($segments as $seg) {
        if (!is_array($value) || !array_key_exists($seg, $value)) {
            return null;
        }
        $value = $value[$seg];
    }
    return $value;
}

/**
 * Global PDO instance
 */
function ru_db(): \PDO
{
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $db = ru_config('db');
    if (!is_array($db)) {
        throw new \RuntimeException('DB yapilandirmasi yok');
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        $db['host'] ?? 'localhost',
        (int)($db['port'] ?? 3306),
        $db['name'] ?? '',
        $db['charset'] ?? 'utf8mb4'
    );

    $pdo = new \PDO($dsn, $db['user'] ?? '', $db['pass'] ?? '', [
        \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_EMULATE_PREPARES   => false,
        \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . ($db['charset'] ?? 'utf8mb4')
                                       . " COLLATE " . ($db['collate'] ?? 'utf8mb4_unicode_ci'),
    ]);

    return $pdo;
}

/**
 * Tablo adi (prefix ile)
 */
function ru_t(string $name): string
{
    $prefix = (string)(ru_config('db.prefix') ?? 'ru_');
    return $prefix . $name;
}

// ──────────────────────────────────────────────────────────────────────
// Sorgu Yardimcilari
// ──────────────────────────────────────────────────────────────────────

/**
 * Tek satir dondurur
 */
function ru_fetch(string $sql, array $params = []): ?array
{
    $stmt = ru_db()->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch();
    return $row === false ? null : $row;
}

/**
 * Tum satirlari dondurur
 */
function ru_fetch_all(string $sql, array $params = []): array
{
    $stmt = ru_db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Tek skalar deger dondurur
 */
function ru_fetch_scalar(string $sql, array $params = []): mixed
{
    $stmt = ru_db()->prepare($sql);
    $stmt->execute($params);
    $val = $stmt->fetchColumn();
    return $val === false ? null : $val;
}

/**
 * Sorgu calistirir, INSERT id donderir (varsa) / etkilenen satir sayisi
 */
function ru_exec(string $sql, array $params = []): int
{
    $stmt = ru_db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->rowCount();
}

function ru_last_id(): int
{
    return (int)ru_db()->lastInsertId();
}

// ──────────────────────────────────────────────────────────────────────
// Settings k/v
// ──────────────────────────────────────────────────────────────────────

/**
 * ru_settings tablosundan deger okur (cache'li)
 */
function ru_setting(string $key, mixed $default = null): mixed
{
    static $cache = null;
    if ($cache === null) {
        try {
            $rows = ru_fetch_all('SELECT skey, sval FROM ' . ru_t('settings'));
            $cache = [];
            foreach ($rows as $r) {
                $cache[$r['skey']] = $r['sval'];
            }
        } catch (\Throwable $e) {
            $cache = [];
        }
    }
    return $cache[$key] ?? $default;
}

function ru_setting_set(string $key, mixed $value): void
{
    $sql = 'INSERT INTO ' . ru_t('settings') . ' (skey, sval, updated_at) VALUES (?, ?, NOW())
            ON DUPLICATE KEY UPDATE sval = VALUES(sval), updated_at = NOW()';
    ru_exec($sql, [$key, (string)$value]);
}

// ──────────────────────────────────────────────────────────────────────
// Idempotent Sema (INFORMATION_SCHEMA korumali)
// ──────────────────────────────────────────────────────────────────────

/**
 * Tablo varsa atlar, yoksa olusturur
 */
function ru_safe_create_table(string $tableNameWithoutPrefix, string $createSql): void
{
    $tbl = ru_t($tableNameWithoutPrefix);
    $exists = ru_fetch_scalar(
        'SELECT COUNT(*) FROM information_schema.tables
         WHERE table_schema = DATABASE() AND table_name = ?',
        [$tbl]
    );
    if ((int)$exists === 0) {
        ru_db()->exec($createSql);
    }
}

/**
 * Kolon varsa atlar, yoksa ALTER TABLE ile ekler
 */
function ru_safe_alter_add(string $tableNameWithoutPrefix, string $column, string $colDef): void
{
    $tbl = ru_t($tableNameWithoutPrefix);
    $exists = ru_fetch_scalar(
        'SELECT COUNT(*) FROM information_schema.columns
         WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?',
        [$tbl, $column]
    );
    if ((int)$exists === 0) {
        ru_db()->exec("ALTER TABLE `$tbl` ADD COLUMN $colDef");
    }
}

/**
 * Index varsa atlar, yoksa ekler
 */
function ru_safe_add_index(string $tableNameWithoutPrefix, string $indexName, string $indexDef): void
{
    $tbl = ru_t($tableNameWithoutPrefix);
    $exists = ru_fetch_scalar(
        'SELECT COUNT(*) FROM information_schema.statistics
         WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ?',
        [$tbl, $indexName]
    );
    if ((int)$exists === 0) {
        ru_db()->exec("ALTER TABLE `$tbl` ADD $indexDef");
    }
}

// ──────────────────────────────────────────────────────────────────────
// Audit Log
// ──────────────────────────────────────────────────────────────────────

function ru_audit(string $action, string $entity = '', ?int $entityId = null, ?array $meta = null): void
{
    try {
        $userId = $_SESSION['user_id'] ?? null;
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);
        $sql = 'INSERT INTO ' . ru_t('audit_log')
             . ' (user_id, action, entity, entity_id, ip, user_agent, meta_json, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())';
        ru_exec($sql, [
            $userId,
            $action,
            $entity,
            $entityId,
            $ip,
            $ua,
            $meta !== null ? json_encode($meta, JSON_UNESCAPED_UNICODE) : null,
        ]);
    } catch (\Throwable $e) {
        // Audit log basarisizliklari sessiz gec
    }
}
