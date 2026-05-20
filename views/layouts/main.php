<?php
/**
 * Ana Layout — views/layouts/main.php
 *
 * $page_content     — page goverdesi (ru_render'da olusturulur)
 * $page_title       — sayfa basligi (null ise sadece site adi)
 * $page_meta        — meta description
 * $page_keywords    — meta keywords
 * $page_og_image    — OpenGraph gorsel
 * $page_class       — body class
 */

declare(strict_types=1);

$siteName = h((string)ru_setting('site_name', 'RAYU Tarım Makineleri'));
$tagline  = h((string)ru_setting('site_tagline', ''));
$logo     = (string)ru_setting('site_logo', '');
$favicon  = (string)ru_setting('site_favicon', '');
$verification = (string)ru_setting('google_site_verification', '');

$titleSuffix = $siteName . ($tagline ? ' — ' . $tagline : '');
$fullTitle   = isset($page_title) && $page_title !== null && $page_title !== ''
    ? h($page_title) . ' — ' . h($siteName)
    : $titleSuffix;

$ogImage = !empty($page_og_image) ? ru_upload_url($page_og_image) : '/assets/img/og-default.jpg';
$canonicalPath = strtok((string)($_SERVER['REQUEST_URI'] ?? '/'), '?') ?: '/';
$canonical = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'rayutarim.com') . $canonicalPath;
?><!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= $fullTitle ?></title>
<?php if (!empty($page_meta)): ?>
<meta name="description" content="<?= h($page_meta) ?>">
<?php else: ?>
<meta name="description" content="<?= $tagline ?>">
<?php endif; ?>
<?php if (!empty($page_keywords)): ?>
<meta name="keywords" content="<?= h($page_keywords) ?>">
<?php endif; ?>
<link rel="canonical" href="<?= h($canonical) ?>">
<meta name="robots" content="index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1">
<?php if ($verification): ?>
<meta name="google-site-verification" content="<?= h($verification) ?>">
<?php endif; ?>

<!-- OpenGraph / Twitter -->
<meta property="og:type" content="website">
<meta property="og:title" content="<?= $fullTitle ?>">
<meta property="og:description" content="<?= !empty($page_meta) ? h($page_meta) : $tagline ?>">
<meta property="og:image" content="<?= h($ogImage) ?>">
<meta property="og:url" content="<?= h($canonical) ?>">
<meta name="twitter:card" content="summary_large_image">

<!-- Favicon -->
<?php if ($favicon): ?>
<link rel="icon" href="<?= h(ru_upload_url($favicon)) ?>">
<link rel="shortcut icon" href="<?= h(ru_upload_url($favicon)) ?>">
<?php else: ?>
<link rel="icon" type="image/svg+xml" href="/assets/img/favicon.svg">
<link rel="icon" type="image/png" sizes="32x32" href="/assets/img/favicon-32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/assets/img/favicon-16.png">
<link rel="apple-touch-icon" href="/assets/img/apple-touch-icon.png">
<link rel="shortcut icon" href="/assets/img/favicon.ico">
<?php endif; ?>
<meta name="theme-color" content="#1F4D33">

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap">

<!-- Stiller -->
<link rel="stylesheet" href="<?= ru_asset('assets/css/style.css') ?>">

<!-- JSON-LD -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "<?= addslashes((string)ru_setting('site_name', 'RAYU Tarım Makineleri')) ?>",
  "url": "<?= addslashes((string)ru_setting('site_url', 'https://rayutarim.com')) ?>",
  "logo": "<?= $logo ? addslashes(ru_upload_url($logo)) : 'https://rayutarim.com/assets/img/logo.png' ?>",
  "telephone": "<?= addslashes((string)ru_setting('site_phone', '')) ?>",
  "email": "<?= addslashes((string)ru_setting('site_email', '')) ?>",
  "address": {
    "@type": "PostalAddress",
    "addressLocality": "Konya",
    "addressCountry": "TR"
  }
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "name": "<?= addslashes((string)ru_setting('site_name', 'RAYU Tarım Makineleri')) ?>",
  "url": "<?= addslashes((string)ru_setting('site_url', 'https://rayutarim.com')) ?>",
  "inLanguage": "tr-TR",
  "publisher": {
    "@type": "Organization",
    "name": "<?= addslashes((string)ru_setting('site_name', 'RAYU Tarım Makineleri')) ?>"
  }
}
</script>
</head>
<body class="<?= h($page_class ?? '') ?>">

<?php ru_partial('header'); ?>

<main class="site-main">
<?= $page_content ?? '' ?>
</main>

<?php ru_partial('footer'); ?>

<script src="<?= ru_asset('assets/js/site.js') ?>" defer></script>
</body>
</html>
