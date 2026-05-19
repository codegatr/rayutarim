<?php
/**
 * RAYU TARIM MAKINELERI - Admin helpers
 */
declare(strict_types=1);

function ru_admin_user(): ?array
{
    ru_session_start();
    $id = (int)($_SESSION['user_id'] ?? 0);
    if ($id <= 0) {
        return null;
    }
    return ru_fetch('SELECT * FROM ' . ru_t('users') . ' WHERE id = ? AND is_active = 1 LIMIT 1', [$id]);
}

function ru_admin_require(): array
{
    $user = ru_admin_user();
    if (!$user) {
        ru_redirect('/admin/login.php');
    }
    return $user;
}

function ru_admin_can(string $capability, ?array $user = null): bool
{
    $user ??= ru_admin_user();
    if (!$user) {
        return false;
    }
    $role = (string)($user['role'] ?? 'viewer');
    if ($role === 'superadmin') {
        return true;
    }
    if ($capability === 'view') {
        return in_array($role, ['admin', 'editor', 'viewer'], true);
    }
    if ($capability === 'write') {
        return in_array($role, ['admin', 'editor'], true);
    }
    if ($capability === 'admin') {
        return $role === 'admin';
    }
    return false;
}

function ru_admin_login(string $email, string $password): bool
{
    $email = strtolower(trim($email));
    $ip = ru_client_ip();
    $user = ru_fetch('SELECT * FROM ' . ru_t('users') . ' WHERE email = ? LIMIT 1', [$email]);
    $ok = $user && (int)$user['is_active'] === 1 && password_verify($password, (string)$user['password_hash']);

    ru_exec('INSERT INTO ' . ru_t('login_attempts') . ' (email, ip, success, created_at) VALUES (?, ?, ?, NOW())', [
        $email,
        $ip,
        $ok ? 1 : 0,
    ]);

    if (!$ok) {
        return false;
    }

    ru_session_start();
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int)$user['id'];
    ru_exec('UPDATE ' . ru_t('users') . ' SET last_login_at = NOW(), last_login_ip = ?, failed_attempts = 0, locked_until = NULL WHERE id = ?', [
        $ip,
        (int)$user['id'],
    ]);
    ru_audit('login', 'user', (int)$user['id']);
    return true;
}

function ru_admin_logout(): void
{
    ru_session_start();
    unset($_SESSION['user_id']);
    session_regenerate_id(true);
}

function ru_admin_flash_html(): string
{
    $html = '';
    foreach (ru_flash_get() as $flash) {
        $type = preg_replace('/[^a-z]/', '', (string)($flash['type'] ?? 'info')) ?: 'info';
        $html .= '<div class="admin-alert admin-alert--' . h($type) . '">' . h($flash['message'] ?? '') . '</div>';
    }
    return $html;
}

function ru_admin_layout(string $title, string $content, array $user = []): never
{
    $user = $user ?: (ru_admin_user() ?? []);
    $nav = [
        'dashboard' => ['Dashboard', '/admin/index.php'],
        'products' => ['Ürünler', '/admin/products.php'],
        'categories' => ['Kategoriler', '/admin/categories.php'],
        'inquiries' => ['Talepler', '/admin/inquiries.php'],
        'pages' => ['Sayfalar', '/admin/pages.php'],
        'sliders' => ['Slider', '/admin/sliders.php'],
        'menus' => ['Menü', '/admin/menus.php'],
        'settings' => ['Ayarlar', '/admin/settings.php'],
        'users' => ['Kullanıcılar', '/admin/users.php'],
        'update' => ['Smart Update', '/admin/update-ui.php'],
    ];
    $current = basename($_SERVER['SCRIPT_NAME'] ?? 'index.php');
    echo '<!doctype html><html lang="tr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
    echo '<title>' . h($title) . ' - RAYU Admin</title><link rel="stylesheet" href="/admin/assets/admin.css"></head>';
    echo '<body class="admin-body"><aside class="admin-sidebar"><a class="admin-brand" href="/admin/index.php"><img src="/assets/img/logo.svg" alt="RAYU"></a><nav>';
    foreach ($nav as $item) {
        [$label, $url] = $item;
        $active = basename(parse_url($url, PHP_URL_PATH) ?: '') === $current ? ' is-active' : '';
        echo '<a class="' . $active . '" href="' . h($url) . '">' . h($label) . '</a>';
    }
    echo '</nav></aside><div class="admin-shell"><header class="admin-top"><div><strong>' . h($title) . '</strong><span>RAYU yönetim paneli</span></div>';
    echo '<div class="admin-user">' . h($user['full_name'] ?? '') . '<a href="/admin/logout.php">Çıkış</a></div></header><main class="admin-main">';
    echo ru_admin_flash_html();
    echo $content;
    echo '</main></div></body></html>';
    exit;
}

function ru_admin_input(string $name, mixed $default = ''): string
{
    return trim((string)($_POST[$name] ?? $default));
}
