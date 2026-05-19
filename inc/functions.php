<?php
/**
 * RAYU TARIM MAKİNELERİ — Genel Yardimci Fonksiyonlar
 */

declare(strict_types=1);

// ──────────────────────────────────────────────────────────────────────
// Cikti Kacisi
// ──────────────────────────────────────────────────────────────────────

function h(mixed $value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function ru_url(string $path = '/'): string
{
    $base = rtrim((string)(ru_config('site.url') ?? ''), '/');
    return $base . '/' . ltrim($path, '/');
}

function ru_redirect(string $path, int $code = 302): never
{
    if (str_starts_with($path, 'http')) {
        header("Location: $path", true, $code);
    } else {
        header('Location: ' . ru_url($path), true, $code);
    }
    exit;
}

// ──────────────────────────────────────────────────────────────────────
// Slug
// ──────────────────────────────────────────────────────────────────────

/**
 * Turkce karakter dahil basit slug uretici. ASCII cikti.
 */
function ru_slugify(string $text): string
{
    $map = [
        'ç'=>'c','Ç'=>'c','ğ'=>'g','Ğ'=>'g','ı'=>'i','I'=>'i','İ'=>'i','i'=>'i',
        'ö'=>'o','Ö'=>'o','ş'=>'s','Ş'=>'s','ü'=>'u','Ü'=>'u',
        'â'=>'a','Â'=>'a','î'=>'i','Î'=>'i','û'=>'u','Û'=>'u',
    ];
    $text = strtr($text, $map);
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
    $text = trim($text, '-');
    return $text === '' ? 'item' : $text;
}

// ──────────────────────────────────────────────────────────────────────
// CSRF
// ──────────────────────────────────────────────────────────────────────

function ru_session_start(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    $name = (string)(ru_config('security.session_name') ?? 'RU_SESS');
    session_name($name);
    session_set_cookie_params([
        'lifetime' => (int)(ru_config('security.session_lifetime') ?? 7200),
        'path'     => '/',
        'secure'   => (bool)(ru_config('security.cookie_secure') ?? true),
        'httponly' => true,
        'samesite' => (string)(ru_config('security.cookie_samesite') ?? 'Lax'),
    ]);
    session_start();
}

function ru_csrf_token(): string
{
    ru_session_start();
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function ru_csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . h(ru_csrf_token()) . '">';
}

function ru_csrf_check(): bool
{
    ru_session_start();
    $sent = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    $stored = $_SESSION['csrf'] ?? '';
    return is_string($sent) && is_string($stored) && $sent !== ''
        && hash_equals($stored, $sent);
}

// ──────────────────────────────────────────────────────────────────────
// JSON
// ──────────────────────────────────────────────────────────────────────

function ru_json(mixed $data, int $code = 200): never
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// ──────────────────────────────────────────────────────────────────────
// Flash mesajlari
// ──────────────────────────────────────────────────────────────────────

function ru_flash(string $type, string $message): void
{
    ru_session_start();
    $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
}

function ru_flash_get(): array
{
    ru_session_start();
    $f = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);
    return $f;
}

// ──────────────────────────────────────────────────────────────────────
// Diger
// ──────────────────────────────────────────────────────────────────────

function ru_is_https(): bool
{
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') return true;
    if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') return true;
    return false;
}

function ru_client_ip(): string
{
    return (string)($_SERVER['HTTP_CF_CONNECTING_IP']
        ?? $_SERVER['HTTP_X_REAL_IP']
        ?? $_SERVER['HTTP_X_FORWARDED_FOR']
        ?? $_SERVER['REMOTE_ADDR']
        ?? '');
}

/**
 * Yetkisiz erisim icin 403
 */
function ru_abort(int $code, string $message = ''): never
{
    http_response_code($code);
    $title = match ($code) {
        400 => 'Hatalı İstek',
        401 => 'Yetkisiz',
        403 => 'Erişim Engellendi',
        404 => 'Bulunamadı',
        429 => 'Çok Fazla İstek',
        default => 'Hata',
    };
    echo "<!doctype html><html lang='tr'><head><meta charset='utf-8'><title>$code - $title</title>";
    echo "<style>body{font-family:system-ui;margin:0;padding:4rem 1rem;text-align:center;background:#f5f7f4;color:#1a3d1f}";
    echo "h1{font-size:6rem;margin:0;color:#2d5a3d}h2{margin:.5rem 0 2rem;font-weight:400}";
    echo "a{color:#2d5a3d;text-decoration:none;border-bottom:1px solid currentColor}</style></head><body>";
    echo "<h1>$code</h1><h2>" . h($title) . "</h2>";
    if ($message !== '') echo '<p>' . h($message) . '</p>';
    echo "<p><a href='/'>Anasayfaya dön</a></p></body></html>";
    exit;
}

// ──────────────────────────────────────────────────────────────────────
// Tarih/Para Bicimleme
// ──────────────────────────────────────────────────────────────────────

function ru_date(?string $datetime, string $fmt = 'd.m.Y'): string
{
    if (!$datetime) return '';
    try {
        return (new \DateTime($datetime))->format($fmt);
    } catch (\Throwable) {
        return '';
    }
}

function ru_money(float|int|string|null $amount, string $currency = 'TRY'): string
{
    if ($amount === null || $amount === '') return '';
    $n = number_format((float)$amount, 2, ',', '.');
    return match ($currency) {
        'TRY' => $n . ' ₺',
        'USD' => '$ ' . $n,
        'EUR' => '€ ' . $n,
        default => $n . ' ' . $currency,
    };
}
