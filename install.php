<?php
/**
 * RAYU TARIM MAKİNELERİ — Kurulum Sihirbazi
 *
 * Adimlar:
 *  1. Sistem gereksinim kontrolu (PHP 8.3+, eklentiler, yazma izinleri)
 *  2. DB baglanti bilgileri + site temel ayarlari
 *  3. Yonetici hesabi olusturma
 *  4. migration.sql calistirma + .installed mark
 *
 * Kurulumdan sonra:
 *  - inc/config.php olusur
 *  - inc/.installed dosyasi olusur (yeniden kurulumu engeller)
 *  - install.php otomatik kilitlenir, kullanici siler
 */

declare(strict_types=1);

// Sabit yolar
define('RU_BASE', __DIR__);
const RU_CONFIG_PATH = __DIR__ . '/inc/config.php';
const RU_MARK_PATH   = __DIR__ . '/inc/.installed';

require_once __DIR__ . '/inc/version.php';

// Zaten kurulu mu?
if (is_file(RU_MARK_PATH)) {
    http_response_code(403);
    echo render_locked();
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$step  = max(1, min(5, (int)($_GET['step'] ?? $_POST['step'] ?? 1)));
$error = '';
$info  = '';

// ──────────────────────────────────────────────────────────────────────
// Adim 1: Gereksinimler
// ──────────────────────────────────────────────────────────────────────
$reqs = [
    'PHP 8.3+'               => version_compare(PHP_VERSION, '8.3.0', '>='),
    'PDO + pdo_mysql'        => extension_loaded('pdo') && extension_loaded('pdo_mysql'),
    'mbstring'               => extension_loaded('mbstring'),
    'json'                   => extension_loaded('json'),
    'GD veya Imagick'        => extension_loaded('gd') || extension_loaded('imagick'),
    'openssl'                => extension_loaded('openssl'),
    'curl'                   => extension_loaded('curl'),
    'fileinfo'               => extension_loaded('fileinfo'),
    'inc/ klasoru yazilabilir'    => is_writable(__DIR__ . '/inc'),
    'uploads/ klasoru yazilabilir' => is_writable(__DIR__ . '/uploads'),
];
$reqsOk = !in_array(false, $reqs, true);

// ──────────────────────────────────────────────────────────────────────
// Adim 2: DB ve site bilgileri kaydet (sessiona)
// ──────────────────────────────────────────────────────────────────────
if ($step === 2 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = [
        'host' => trim((string)($_POST['db_host'] ?? 'localhost')),
        'port' => (int)($_POST['db_port'] ?? 3306),
        'name' => trim((string)($_POST['db_name'] ?? '')),
        'user' => trim((string)($_POST['db_user'] ?? '')),
        'pass' => (string)($_POST['db_pass'] ?? ''),
    ];
    $site = [
        'url'         => rtrim(trim((string)($_POST['site_url'] ?? '')), '/'),
        'name'        => trim((string)($_POST['site_name'] ?? 'RAYU Tarim Makineleri')),
        'admin_email' => trim((string)($_POST['admin_email'] ?? '')),
    ];

    try {
        // Test baglanti
        $dsn = "mysql:host={$db['host']};port={$db['port']};dbname={$db['name']};charset=utf8mb4";
        $pdo = new \PDO($dsn, $db['user'], $db['pass'], [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ]);
        $pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
        $_SESSION['ru_install']['db']   = $db;
        $_SESSION['ru_install']['site'] = $site;
        header('Location: install.php?step=3');
        exit;
    } catch (\Throwable $e) {
        $error = 'DB baglantisi basarisiz: ' . $e->getMessage();
    }
}

// ──────────────────────────────────────────────────────────────────────
// Adim 3: Yonetici bilgileri kaydet (sessiona)
// ──────────────────────────────────────────────────────────────────────
if ($step === 3 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin = [
        'name'  => trim((string)($_POST['admin_name'] ?? '')),
        'email' => trim((string)($_POST['admin_email'] ?? '')),
        'pass'  => (string)($_POST['admin_pass'] ?? ''),
        'pass2' => (string)($_POST['admin_pass2'] ?? ''),
    ];
    if ($admin['name'] === '' || !filter_var($admin['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Ad ve gecerli e-posta zorunlu.';
    } elseif (strlen($admin['pass']) < 8) {
        $error = 'Sifre en az 8 karakter olmali.';
    } elseif ($admin['pass'] !== $admin['pass2']) {
        $error = 'Sifreler eslesmiyor.';
    } else {
        $_SESSION['ru_install']['admin'] = [
            'name'  => $admin['name'],
            'email' => $admin['email'],
            'hash'  => password_hash($admin['pass'], PASSWORD_BCRYPT, ['cost' => 12]),
        ];
        header('Location: install.php?step=4');
        exit;
    }
}

// ──────────────────────────────────────────────────────────────────────
// Adim 4: Uygula (config.php + migration + admin)
// ──────────────────────────────────────────────────────────────────────
if ($step === 4 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $db    = $_SESSION['ru_install']['db']    ?? null;
    $site  = $_SESSION['ru_install']['site']  ?? null;
    $admin = $_SESSION['ru_install']['admin'] ?? null;

    if (!$db || !$site || !$admin) {
        $error = 'Oturum verisi kayboldu, lutfen 1. adimdan baslayin.';
        $step = 1;
    } else {
        try {
            // 1) DB baglan
            $dsn = "mysql:host={$db['host']};port={$db['port']};dbname={$db['name']};charset=utf8mb4";
            $pdo = new \PDO($dsn, $db['user'], $db['pass'], [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]);
            $pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");

            // 2) Migration calistir
            $sql = file_get_contents(__DIR__ . '/migrations/migration.sql');
            if ($sql === false) {
                throw new \RuntimeException('migration.sql okunamadi');
            }
            // Multi-statement
            $pdo->exec($sql);

            // 3) Ilk admin
            $stmt = $pdo->prepare(
                "INSERT INTO ru_users (email, password_hash, full_name, role, is_active)
                 VALUES (?, ?, ?, 'superadmin', 1)
                 ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash),
                                         full_name = VALUES(full_name),
                                         role = 'superadmin',
                                         is_active = 1"
            );
            $stmt->execute([$admin['email'], $admin['hash'], $admin['name']]);

            // 4) Migration kaydi
            $stmt = $pdo->prepare(
                "INSERT IGNORE INTO ru_migrations (version, filename, success, notes)
                 VALUES (?, 'migration.sql', 1, 'install.php kurulumu')"
            );
            $stmt->execute([ru_version()]);

            // 5) Site ayarlarini guncelle
            $settings = [
                'site_name'   => $site['name'],
                'site_email'  => $site['admin_email'],
            ];
            foreach ($settings as $k => $v) {
                $pdo->prepare(
                    "INSERT INTO ru_settings (skey, sval) VALUES (?, ?)
                     ON DUPLICATE KEY UPDATE sval = VALUES(sval)"
                )->execute([$k, $v]);
            }

            // 6) config.php yaz
            $appKey = bin2hex(random_bytes(32));
            $cfg = build_config_php($db, $site, $appKey);
            if (file_put_contents(RU_CONFIG_PATH, $cfg) === false) {
                throw new \RuntimeException('inc/config.php yazilamadi');
            }
            @chmod(RU_CONFIG_PATH, 0640);

            // 7) Kurulum markeri
            file_put_contents(RU_MARK_PATH, date('c') . "\n" . ru_version());

            // Session temizle
            unset($_SESSION['ru_install']);

            header('Location: install.php?step=5');
            exit;
        } catch (\Throwable $e) {
            $error = 'Kurulum hatasi: ' . $e->getMessage();
        }
    }
}

// ──────────────────────────────────────────────────────────────────────
// Render
// ──────────────────────────────────────────────────────────────────────

function build_config_php(array $db, array $site, string $appKey): string
{
    $esc = static fn(string $s): string => str_replace(["\\", "'"], ["\\\\", "\\'"], $s);
    return <<<PHP
<?php
/**
 * RAYU TARIM MAKİNELERİ — Otomatik olusturulan yapilandirma
 * install.php tarafindan {$site['name']} icin uretildi.
 */

declare(strict_types=1);

return [
    'db' => [
        'host'    => '{$esc($db['host'])}',
        'port'    => {$db['port']},
        'name'    => '{$esc($db['name'])}',
        'user'    => '{$esc($db['user'])}',
        'pass'    => '{$esc($db['pass'])}',
        'charset' => 'utf8mb4',
        'collate' => 'utf8mb4_unicode_ci',
        'prefix'  => 'ru_',
    ],
    'site' => [
        'url'         => '{$esc($site['url'])}',
        'name'        => '{$esc($site['name'])}',
        'tagline'     => 'Tarim makineleri ve ziraai ilaclar',
        'admin_email' => '{$esc($site['admin_email'])}',
        'timezone'    => 'Europe/Istanbul',
        'locale'      => 'tr_TR',
        'debug'       => false,
    ],
    'security' => [
        'app_key'             => '{$appKey}',
        'session_name'        => 'RU_SESS',
        'session_lifetime'    => 7200,
        'cookie_secure'       => true,
        'cookie_samesite'     => 'Lax',
        'login_max_attempts'  => 5,
        'login_lockout_seconds' => 900,
    ],
    'uploads' => [
        'max_size_bytes' => 8 * 1024 * 1024,
        'allowed_image'  => ['jpg', 'jpeg', 'png', 'webp', 'gif'],
        'allowed_doc'    => ['pdf'],
        'path'           => 'uploads',
        'webp_quality'   => 85,
    ],
    'update' => [
        'github_token'   => '',
        'backup_dir'     => 'backups',
        'auto_check'     => true,
        'check_interval' => 3600,
    ],
    'smtp' => [
        'enabled'   => false,
        'host'      => '',
        'port'      => 587,
        'user'      => '',
        'pass'      => '',
        'from'      => 'noreply@{$esc(parse_url($site['url'], PHP_URL_HOST) ?: 'rayutarim.com')}',
        'from_name' => '{$esc($site['name'])}',
        'secure'    => 'tls',
    ],
];

PHP;
}

function render_locked(): string
{
    return <<<HTML
<!doctype html><html lang="tr"><head><meta charset="utf-8">
<title>Kurulum kilitli — RAYU Tarim Makineleri</title>
<style>body{font-family:system-ui;margin:0;padding:4rem 1rem;text-align:center;background:#f5f7f4;color:#1a3d1f}
h1{color:#2d5a3d}code{background:#e8efe6;padding:.2em .4em;border-radius:3px}</style>
</head><body>
<h1>Kurulum tamamlanmis</h1>
<p>Bu site zaten kurulu. Yeniden kurmak istiyorsaniz sunucudan asagidaki dosyayi silmelisiniz:</p>
<p><code>inc/.installed</code></p>
<p style="margin-top:2rem"><a href="/">Anasayfaya don</a></p>
</body></html>
HTML;
}
?>
<!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>RAYU Tarim Makineleri — Kurulum (Adim <?= h_step($step) ?>/5)</title>
<style>
  :root {
    --bg: #f5f7f4;
    --card: #ffffff;
    --primary: #2d5a3d;
    --primary-dark: #1a3d1f;
    --accent: #d4a017;
    --text: #1a2622;
    --muted: #6b7770;
    --border: #e0e6dd;
    --success: #2d7a3d;
    --error: #b03a2e;
  }
  *{box-sizing:border-box}
  body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;background:var(--bg);color:var(--text);margin:0;padding:2rem 1rem;line-height:1.6}
  .wrap{max-width:720px;margin:0 auto}
  .brand{text-align:center;margin-bottom:1.5rem}
  .brand h1{margin:0;color:var(--primary-dark);font-size:1.8rem;letter-spacing:-.02em}
  .brand p{margin:.25rem 0 0;color:var(--muted);font-size:.95rem}
  .steps{display:flex;justify-content:space-between;background:var(--card);border:1px solid var(--border);border-radius:10px;padding:1rem;margin-bottom:1.5rem;font-size:.85rem}
  .steps div{flex:1;text-align:center;color:var(--muted);position:relative;padding:.4rem .25rem}
  .steps div.active{color:var(--primary);font-weight:600}
  .steps div.done{color:var(--success)}
  .steps div.done::before{content:"\2713 ";font-weight:700}
  .card{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:2rem;box-shadow:0 2px 8px rgba(0,0,0,.04)}
  h2{margin:0 0 .5rem;color:var(--primary-dark)}
  .muted{color:var(--muted);font-size:.95rem;margin:0 0 1.5rem}
  label{display:block;font-weight:600;margin:1rem 0 .35rem;color:var(--primary-dark);font-size:.92rem}
  input[type=text],input[type=email],input[type=password],input[type=number],input[type=url]{
    width:100%;padding:.7rem .85rem;border:1px solid var(--border);border-radius:6px;font-size:1rem;background:#fafbf9;font-family:inherit;
  }
  input:focus{outline:none;border-color:var(--primary);background:#fff;box-shadow:0 0 0 3px rgba(45,90,61,.1)}
  .row{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
  @media(max-width:540px){.row{grid-template-columns:1fr}}
  .btn{display:inline-block;background:var(--primary);color:#fff;padding:.75rem 1.5rem;border:0;border-radius:6px;font-size:1rem;font-weight:600;cursor:pointer;text-decoration:none;font-family:inherit;margin-top:1.5rem}
  .btn:hover{background:var(--primary-dark)}
  .btn-secondary{background:transparent;color:var(--primary);border:1px solid var(--primary)}
  .alert{padding:.85rem 1rem;border-radius:6px;margin-bottom:1rem;font-size:.92rem}
  .alert-error{background:#fdeded;color:var(--error);border:1px solid #f5c2bd}
  .alert-success{background:#e8f4e8;color:var(--success);border:1px solid #b8d8b8}
  .alert-info{background:#eef3fa;color:#1a4a7a;border:1px solid #c5d8ee}
  table.reqs{width:100%;border-collapse:collapse;margin-top:.5rem}
  table.reqs td{padding:.55rem .75rem;border-bottom:1px solid var(--border)}
  table.reqs td:last-child{text-align:right;font-weight:600}
  .ok{color:var(--success)}
  .fail{color:var(--error)}
  code{background:#eef3eb;padding:.15em .4em;border-radius:3px;font-size:.92em}
  .footer-note{text-align:center;color:var(--muted);font-size:.82rem;margin-top:1.5rem}
</style>
</head>
<body>
<div class="wrap">
  <div class="brand">
    <h1>RAYU TARIM MAKİNELERİ</h1>
    <p>Kurulum Sihirbazi — v<?= h(ru_version()) ?></p>
  </div>

  <div class="steps">
    <div class="<?= step_class(1, $step) ?>">1. Gereksinim</div>
    <div class="<?= step_class(2, $step) ?>">2. Veritabani</div>
    <div class="<?= step_class(3, $step) ?>">3. Yonetici</div>
    <div class="<?= step_class(4, $step) ?>">4. Uygula</div>
    <div class="<?= step_class(5, $step) ?>">5. Tamam</div>
  </div>

  <div class="card">
  <?php if ($error): ?>
    <div class="alert alert-error"><?= h($error) ?></div>
  <?php endif; ?>

  <?php if ($step === 1): ?>
    <h2>1. Sistem Gereksinimleri</h2>
    <p class="muted">Devam etmek icin asagidaki tum kontroller yesil olmali.</p>
    <table class="reqs">
      <?php foreach ($reqs as $name => $ok): ?>
        <tr>
          <td><?= h($name) ?></td>
          <td class="<?= $ok ? 'ok' : 'fail' ?>"><?= $ok ? 'TAMAM' : 'EKSIK' ?></td>
        </tr>
      <?php endforeach; ?>
    </table>
    <?php if ($reqsOk): ?>
      <a class="btn" href="install.php?step=2">Devam Et &rarr;</a>
    <?php else: ?>
      <div class="alert alert-error" style="margin-top:1.5rem">
        Eksikleri tamamlayip bu sayfayi yenileyin. PHP eklentileri icin hosting saglayicinizla iletisime gecin.
      </div>
    <?php endif; ?>

  <?php elseif ($step === 2): ?>
    <h2>2. Veritabani Ayarlari</h2>
    <p class="muted">MySQL/MariaDB baglanti bilgilerinizi girin. DirectAdmin'de "MySQL Yonetimi" altinda olusturdugunuz veritabani.</p>
    <form method="post">
      <input type="hidden" name="step" value="2">
      <div class="row">
        <div><label>Sunucu</label><input type="text" name="db_host" value="<?= h($_POST['db_host'] ?? 'localhost') ?>" required></div>
        <div><label>Port</label><input type="number" name="db_port" value="<?= h($_POST['db_port'] ?? '3306') ?>" required></div>
      </div>
      <label>Veritabani Adi</label>
      <input type="text" name="db_name" value="<?= h($_POST['db_name'] ?? '') ?>" placeholder="orn: rayutarim_db" required>
      <div class="row">
        <div><label>Kullanici Adi</label><input type="text" name="db_user" value="<?= h($_POST['db_user'] ?? '') ?>" required></div>
        <div><label>Sifre</label><input type="password" name="db_pass" value="" required></div>
      </div>

      <h2 style="margin-top:2rem;font-size:1.2rem">Site Bilgileri</h2>
      <label>Site URL</label>
      <input type="url" name="site_url" value="<?= h($_POST['site_url'] ?? 'https://rayutarim.com') ?>" required>
      <div class="row">
        <div><label>Site Adi</label><input type="text" name="site_name" value="<?= h($_POST['site_name'] ?? 'RAYU Tarim Makineleri') ?>" required></div>
        <div><label>Yonetici E-posta</label><input type="email" name="admin_email" value="<?= h($_POST['admin_email'] ?? 'info@rayutarim.com') ?>" required></div>
      </div>
      <button class="btn" type="submit">Test Et &amp; Devam Et &rarr;</button>
    </form>

  <?php elseif ($step === 3): ?>
    <?php $autoEmail = $_SESSION['ru_install']['site']['admin_email'] ?? ''; ?>
    <h2>3. Yonetici Hesabi</h2>
    <p class="muted">Yonetim paneline gireceginiz ilk superadmin hesabi.</p>
    <form method="post">
      <input type="hidden" name="step" value="3">
      <label>Ad Soyad</label>
      <input type="text" name="admin_name" value="<?= h($_POST['admin_name'] ?? '') ?>" required>
      <label>E-posta</label>
      <input type="email" name="admin_email" value="<?= h($_POST['admin_email'] ?? $autoEmail) ?>" required>
      <div class="row">
        <div><label>Sifre (en az 8)</label><input type="password" name="admin_pass" required minlength="8"></div>
        <div><label>Sifre Tekrar</label><input type="password" name="admin_pass2" required minlength="8"></div>
      </div>
      <button class="btn" type="submit">Devam Et &rarr;</button>
    </form>

  <?php elseif ($step === 4): ?>
    <h2>4. Kurulumu Uygula</h2>
    <p class="muted">Asagidaki ozeti onaylayin. "Kurulumu Tamamla" basildiginda:</p>
    <ul>
      <li><code>inc/config.php</code> olusturulacak</li>
      <li><code>migrations/migration.sql</code> calistirilacak</li>
      <li>Yonetici hesabi <strong><?= h($_SESSION['ru_install']['admin']['email'] ?? '') ?></strong> olusturulacak</li>
      <li><code>inc/.installed</code> markeri yazilacak</li>
    </ul>
    <div class="alert alert-info">
      Kurulum birkac saniye surer. Bittikten sonra <code>install.php</code> dosyasini sunucudan silmeniz onerilir.
    </div>
    <form method="post">
      <input type="hidden" name="step" value="4">
      <button class="btn" type="submit">Kurulumu Tamamla &rarr;</button>
    </form>

  <?php elseif ($step === 5): ?>
    <h2>5. Kurulum Tamam</h2>
    <div class="alert alert-success">
      Tebrikler — RAYU Tarim Makineleri v<?= h(ru_version()) ?> basariyla kuruldu.
    </div>
    <h3 style="color:var(--primary-dark)">Sonraki Adimlar</h3>
    <ol>
      <li><strong><code>install.php</code> dosyasini sunucudan silin</strong> (guvenlik).</li>
      <li>Yonetim paneli henuz aktif degil — <strong>Faz 4</strong> ile gelecek.</li>
      <li>Public tema icin <strong>Faz 2</strong> guncellemesini bekleyin.</li>
    </ol>
    <p>Su anki durum: cekirdek altyapi hazir. <code>/</code> adresinde Faz 1 karsilama sayfasi gosterilir.</p>
    <a class="btn" href="/">Anasayfayi Goruntule</a>

  <?php endif; ?>
  </div>

  <p class="footer-note">CODEGA &middot; <?= date('Y') ?> &middot; v<?= h(ru_version()) ?></p>
</div>
</body>
</html>
<?php
function h(mixed $value): string {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function h_step(int $n): string { return (string)$n; }
function step_class(int $own, int $current): string {
    if ($own < $current) return 'done';
    if ($own === $current) return 'active';
    return '';
}
