<?php
/**
 * RAY-U TARIM — Public Giris Noktasi
 *
 * Faz 1: Karsilama / "Yapim Asamasinda" sayfasi
 * Faz 2'de buraya gercek router ve tema gelecek.
 */

declare(strict_types=1);

require_once __DIR__ . '/inc/bootstrap.php';

$siteName    = (string)(ru_setting('site_name', 'Ray-U Tarim'));
$siteTagline = (string)(ru_setting('site_tagline', 'Tarim Makineleri ve Ziraai Ilaclar'));
$siteEmail   = (string)(ru_setting('site_email', 'info@rayutarim.com'));
$sitePhone   = (string)(ru_setting('site_phone', ''));
$siteAddress = (string)(ru_setting('site_address', 'Konya, Turkiye'));
$founded     = (string)(ru_setting('founded_year', '2010'));

$maintenance = ru_setting('maintenance_mode', '0') === '1';
?>
<!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= h($siteName) ?> — <?= h($siteTagline) ?></title>
<meta name="description" content="<?= h($siteTagline) ?>">
<meta name="robots" content="noindex,nofollow">
<link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
<style>
  :root {
    --bg-1: #0e2920;
    --bg-2: #1a3d2a;
    --bg-3: #f5f7f4;
    --primary: #2d5a3d;
    --primary-light: #4a8a5d;
    --accent: #d4a017;
    --accent-light: #e8c463;
    --text: #1a2622;
    --text-light: #f5f7f4;
    --muted: #8a9b8e;
    --border: rgba(212,160,23,.25);
  }
  *,*::before,*::after{box-sizing:border-box}
  html,body{margin:0;padding:0}
  body{
    font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif;
    color:var(--text);
    background:var(--bg-3);
    line-height:1.65;
    min-height:100vh;
    overflow-x:hidden;
  }

  /* HERO */
  .hero{
    position:relative;
    min-height:100vh;
    color:var(--text-light);
    background:
      radial-gradient(ellipse at top right, rgba(212,160,23,.18), transparent 50%),
      radial-gradient(ellipse at bottom left, rgba(74,138,93,.25), transparent 55%),
      linear-gradient(135deg, var(--bg-1) 0%, var(--bg-2) 100%);
    display:flex;
    flex-direction:column;
  }
  .hero::before{
    content:"";
    position:absolute;inset:0;
    background-image:
      linear-gradient(rgba(212,160,23,.04) 1px, transparent 1px),
      linear-gradient(90deg, rgba(212,160,23,.04) 1px, transparent 1px);
    background-size:48px 48px;
    pointer-events:none;
    mask-image:radial-gradient(ellipse at center, black 30%, transparent 80%);
  }

  /* NAV */
  .nav{
    position:relative;
    z-index:2;
    padding:1.5rem 2rem;
    display:flex;
    justify-content:space-between;
    align-items:center;
    border-bottom:1px solid rgba(255,255,255,.06);
  }
  .logo{
    display:flex;align-items:center;gap:.75rem;
    font-size:1.25rem;font-weight:800;letter-spacing:-.02em;
    color:var(--text-light);text-decoration:none;
  }
  .logo-mark{
    width:38px;height:38px;
    background:linear-gradient(135deg, var(--accent), var(--accent-light));
    border-radius:9px;
    display:grid;place-items:center;
    color:var(--bg-1);font-weight:900;font-size:1.05rem;
    box-shadow:0 4px 14px rgba(212,160,23,.35);
  }
  .logo small{
    display:block;font-size:.7rem;font-weight:400;
    color:var(--muted);letter-spacing:.08em;text-transform:uppercase;
  }
  .nav-meta{
    color:var(--muted);font-size:.85rem;
    font-family:"SF Mono","JetBrains Mono",Menlo,monospace;
  }

  /* CONTENT */
  .hero-body{
    position:relative;z-index:2;
    flex:1;
    display:grid;place-items:center;
    padding:3rem 1.5rem;
  }
  .hero-inner{
    max-width:880px;text-align:center;
  }
  .badge{
    display:inline-flex;align-items:center;gap:.5rem;
    background:rgba(212,160,23,.12);
    border:1px solid var(--border);
    color:var(--accent-light);
    padding:.4rem .9rem;border-radius:999px;
    font-size:.78rem;letter-spacing:.12em;text-transform:uppercase;
    margin-bottom:2rem;
  }
  .badge .dot{
    width:8px;height:8px;border-radius:50%;background:var(--accent);
    box-shadow:0 0 0 0 var(--accent);
    animation:pulse 2s infinite;
  }
  @keyframes pulse{0%,100%{box-shadow:0 0 0 0 rgba(212,160,23,.6)}50%{box-shadow:0 0 0 10px rgba(212,160,23,0)}}

  h1{
    font-size:clamp(2.4rem, 5.5vw, 4.2rem);
    line-height:1.05;
    margin:0 0 1.25rem;
    font-weight:800;
    letter-spacing:-.03em;
  }
  h1 .ac{
    background:linear-gradient(135deg, var(--accent), var(--accent-light));
    -webkit-background-clip:text;background-clip:text;color:transparent;
  }
  .lead{
    font-size:clamp(1rem, 1.6vw, 1.2rem);
    color:rgba(245,247,244,.75);
    max-width:640px;margin:0 auto 2.5rem;
  }

  /* PHASES */
  .phases{
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(190px, 1fr));
    gap:.75rem;
    max-width:880px;margin:0 auto 2.5rem;
  }
  .phase{
    background:rgba(255,255,255,.04);
    border:1px solid rgba(255,255,255,.06);
    border-radius:10px;
    padding:.85rem .9rem;
    text-align:left;
    transition:background .2s, border-color .2s, transform .2s;
  }
  .phase:hover{background:rgba(255,255,255,.06);transform:translateY(-2px)}
  .phase.current{
    background:rgba(212,160,23,.08);
    border-color:var(--border);
  }
  .phase .pv{
    font-family:"SF Mono",Menlo,monospace;
    font-size:.72rem;color:var(--accent-light);letter-spacing:.08em;
  }
  .phase .pt{
    margin-top:.25rem;font-weight:600;font-size:.92rem;color:var(--text-light);
  }
  .phase .ps{
    margin-top:.15rem;font-size:.78rem;color:var(--muted);
  }
  .phase.done .pv::before{content:"\2713 ";color:var(--primary-light)}

  /* CTA */
  .cta-row{
    display:flex;gap:.85rem;flex-wrap:wrap;justify-content:center;
    margin-top:.5rem;
  }
  .cta{
    display:inline-flex;align-items:center;gap:.6rem;
    padding:.85rem 1.6rem;border-radius:8px;
    font-weight:600;text-decoration:none;font-size:.95rem;
    transition:transform .15s, box-shadow .15s;
  }
  .cta-primary{
    background:linear-gradient(135deg, var(--accent), var(--accent-light));
    color:var(--bg-1);
    box-shadow:0 8px 24px rgba(212,160,23,.3);
  }
  .cta-primary:hover{transform:translateY(-2px);box-shadow:0 12px 32px rgba(212,160,23,.4)}
  .cta-ghost{
    background:transparent;
    color:var(--text-light);
    border:1px solid rgba(255,255,255,.18);
  }
  .cta-ghost:hover{background:rgba(255,255,255,.05)}

  /* FOOTER */
  .footer{
    position:relative;z-index:2;
    padding:1.5rem 2rem;
    border-top:1px solid rgba(255,255,255,.06);
    display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;
    color:var(--muted);font-size:.85rem;
  }
  .footer a{color:var(--muted);text-decoration:none}
  .footer a:hover{color:var(--accent-light)}
  .footer-info{display:flex;gap:1.5rem;flex-wrap:wrap}

  @media(max-width:640px){
    .nav{padding:1rem;flex-direction:column;gap:.75rem;align-items:flex-start}
    .footer{padding:1.5rem 1rem;flex-direction:column;align-items:flex-start}
  }
</style>
</head>
<body>

<section class="hero">

  <nav class="nav">
    <a href="/" class="logo">
      <div class="logo-mark">RU</div>
      <div>
        <?= h($siteName) ?>
        <small>EST. <?= h($founded) ?> &middot; Konya</small>
      </div>
    </a>
    <div class="nav-meta">v<?= h(ru_version()) ?> &middot; Faz 1/7</div>
  </nav>

  <main class="hero-body">
    <div class="hero-inner">

      <div class="badge">
        <span class="dot"></span>
        Yapim asamasinda &middot; Cekirdek hazir
      </div>

      <h1>
        Topragin Gucunu
        <span class="ac">Teknolojiyle</span><br>
        Bulusturuyoruz
      </h1>

      <p class="lead">
        <?= h($siteTagline) ?>. Kurumsal platformumuz asama asama yayina aliniyor.
        Cekirdek altyapi (PHP 8.3, PDO, idempotent migration, GitHub guncelleme hazirligi) tamamlandi.
      </p>

      <div class="phases">
        <?php
        $manifest = ru_manifest();
        $phases = $manifest['phases'] ?? [];
        $currentVersion = ru_version();
        foreach ($phases as $p):
            $v = $p['version'] ?? '';
            $isDone = !empty($p['released']);
            $isCurrent = $v === $currentVersion;
            $cls = 'phase';
            if ($isDone) $cls .= ' done';
            if ($isCurrent) $cls .= ' current';
        ?>
          <div class="<?= h($cls) ?>">
            <div class="pv">v<?= h($v) ?></div>
            <div class="pt"><?= h($p['title'] ?? '') ?></div>
            <div class="ps"><?= h($p['summary'] ?? '') ?></div>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="cta-row">
        <a class="cta cta-primary" href="mailto:<?= h($siteEmail) ?>">
          Iletisime Gec
        </a>
        <a class="cta cta-ghost" href="https://github.com/<?= h(ru_repo()) ?>" target="_blank" rel="noopener">
          GitHub Repo &nearr;
        </a>
      </div>

    </div>
  </main>

  <footer class="footer">
    <div>&copy; <?= date('Y') ?> <?= h($siteName) ?> &middot; CODEGA tarafindan gelistirildi</div>
    <div class="footer-info">
      <?php if ($sitePhone): ?><span><?= h($sitePhone) ?></span><?php endif; ?>
      <a href="mailto:<?= h($siteEmail) ?>"><?= h($siteEmail) ?></a>
      <span><?= h($siteAddress) ?></span>
    </div>
  </footer>

</section>

</body>
</html>
