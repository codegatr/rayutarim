<?php
/**
 * RAYU TARIM MAKİNELERİ — 404 Sayfa Bulunamadı
 */

declare(strict_types=1);

// Bootstrap'i güvenli yükle (kurulu değilse direkt render et)
if (is_file(__DIR__ . '/inc/bootstrap.php') && is_file(__DIR__ . '/inc/config.php')) {
    require_once __DIR__ . '/inc/bootstrap.php';
    $siteName = (string)(ru_setting('site_name', 'RAYU Tarım Makineleri'));
} else {
    $siteName = 'RAYU Tarım Makineleri';
}

http_response_code(404);
?>
<!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>404 — Sayfa Bulunamadı — <?= htmlspecialchars($siteName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></title>
<meta name="robots" content="noindex,nofollow">
<style>
  :root {
    --bg:#0e2920;--card:#1a3d2a;--primary:#2d5a3d;--accent:#d4a017;
    --text:#f5f7f4;--muted:#8a9b8e;
  }
  *{box-sizing:border-box}
  html,body{margin:0;padding:0;height:100%}
  body{
    font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;
    background:radial-gradient(ellipse at center, var(--card), var(--bg));
    color:var(--text);min-height:100vh;display:grid;place-items:center;padding:2rem 1rem;text-align:center;
  }
  .num{
    font-size:clamp(8rem, 22vw, 14rem);font-weight:900;line-height:1;
    background:linear-gradient(135deg, var(--accent), #e8c463);
    -webkit-background-clip:text;background-clip:text;color:transparent;
    letter-spacing:0;margin:0;
  }
  h1{margin:0 0 .75rem;font-size:1.6rem;font-weight:600}
  p{color:var(--muted);max-width:480px;margin:0 auto 2rem}
  a{
    display:inline-block;background:var(--accent);color:var(--bg);
    padding:.85rem 1.8rem;border-radius:8px;font-weight:600;
    text-decoration:none;transition:transform .2s;
  }
  a:hover{transform:translateY(-2px)}
</style>
</head>
<body>
  <div>
    <p class="num">404</p>
    <h1>Aradığınız sayfa bulunamadı</h1>
    <p>Adres yanlış yazılmış olabilir veya sayfa taşınmış ya da kaldırılmış olabilir.</p>
    <a href="/">Anasayfaya dön &rarr;</a>
  </div>
</body>
</html>
