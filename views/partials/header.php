<?php
/**
 * Header — top bar + logo + main navigation
 */
declare(strict_types=1);

$navItems = ru_menu_items('header');
$siteName = (string)ru_setting('site_name', 'RAYU Tarim Makineleri');
$logo     = (string)ru_setting('site_logo', '');
$phone    = (string)ru_setting('site_phone', '');
$email    = (string)ru_setting('site_email', '');
$wpp      = (string)ru_setting('site_whatsapp', '');
$fb       = (string)ru_setting('site_facebook', '');
$ig       = (string)ru_setting('site_instagram', '');
$yt       = (string)ru_setting('site_youtube', '');
$li       = (string)ru_setting('site_linkedin', '');
?>
<header class="site-header" id="siteHeader">

  <!-- Top bar -->
  <div class="top-bar">
    <div class="container top-bar__inner">
      <div class="top-bar__info">
        <?php if ($phone): ?>
          <a class="top-bar__item" href="tel:<?= h(preg_replace('/[^0-9+]/', '', $phone)) ?>">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M6.62 10.79a15.05 15.05 0 006.59 6.59l2.2-2.2a1 1 0 011.05-.24c1.12.37 2.33.57 3.57.57a1 1 0 011 1V20a1 1 0 01-1 1A17 17 0 013 4a1 1 0 011-1h3.5a1 1 0 011 1c0 1.24.2 2.45.57 3.57a1 1 0 01-.24 1.05z"/></svg>
            <?= h($phone) ?>
          </a>
        <?php endif; ?>
        <?php if ($email): ?>
          <a class="top-bar__item" href="mailto:<?= h($email) ?>">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4a2 2 0 00-2 2v12a2 2 0 002 2h16a2 2 0 002-2V6a2 2 0 00-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
            <?= h($email) ?>
          </a>
        <?php endif; ?>
        <span class="top-bar__item top-bar__item--badge">Konya merkezli kurumsal yapi</span>
      </div>

      <div class="top-bar__social">
        <?php if ($fb): ?><a href="<?= h($fb) ?>" target="_blank" rel="noopener" aria-label="Facebook"><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95c5.05-.5 9-4.76 9-9.95z"/></svg></a><?php endif; ?>
        <?php if ($ig): ?><a href="<?= h($ig) ?>" target="_blank" rel="noopener" aria-label="Instagram"><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M7 2C4.24 2 2 4.24 2 7v10c0 2.76 2.24 5 5 5h10c2.76 0 5-2.24 5-5V7c0-2.76-2.24-5-5-5H7zm10 2c1.66 0 3 1.34 3 3v10c0 1.66-1.34 3-3 3H7c-1.66 0-3-1.34-3-3V7c0-1.66 1.34-3 3-3h10zm-5 3a5 5 0 100 10 5 5 0 000-10zm5.5-.5a1 1 0 11-2 0 1 1 0 012 0zM12 9a3 3 0 110 6 3 3 0 010-6z"/></svg></a><?php endif; ?>
        <?php if ($yt): ?><a href="<?= h($yt) ?>" target="_blank" rel="noopener" aria-label="YouTube"><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M22.54 6.42a2.78 2.78 0 00-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.42a2.78 2.78 0 00-1.94 2A29 29 0 001 12a29 29 0 00.46 5.58 2.78 2.78 0 001.94 2C5.12 20 12 20 12 20s6.88 0 8.6-.42a2.78 2.78 0 001.94-2A29 29 0 0023 12a29 29 0 00-.46-5.58zM10 15V9l5.2 3-5.2 3z"/></svg></a><?php endif; ?>
        <?php if ($li): ?><a href="<?= h($li) ?>" target="_blank" rel="noopener" aria-label="LinkedIn"><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3A2 2 0 0121 5v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h14M8.34 18.34V9.67H5.67v8.67h2.67M7 8.33a1.55 1.55 0 100-3.1 1.55 1.55 0 000 3.1M18.34 18.34v-4.74c0-2.48-1.32-3.63-3.08-3.63a2.66 2.66 0 00-2.41 1.32V9.67h-2.67v8.67h2.67v-4.81c0-1.04.2-2.05 1.48-2.05 1.27 0 1.34 1.19 1.34 2.12v4.74h2.67z"/></svg></a><?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Main header -->
  <div class="main-header">
    <div class="container main-header__inner">

      <a class="brand" href="/" aria-label="<?= h($siteName) ?>">
        <?php if ($logo): ?>
          <img src="<?= h(ru_upload_url($logo)) ?>" alt="<?= h($siteName) ?>" class="brand__logo">
        <?php else: ?>
          <img src="/assets/img/logo.svg"
               alt="<?= h($siteName) ?>"
               class="brand__logo brand__logo--default">
        <?php endif; ?>
      </a>

      <nav class="main-nav" id="mainNav" aria-label="Ana navigasyon">
        <ul class="main-nav__list">
          <?php foreach ($navItems as $item):
            $active = ru_is_current($item['url']);
          ?>
            <li class="main-nav__item">
              <a class="main-nav__link <?= $active ? 'is-active' : '' ?>"
                 href="<?= h($item['url']) ?>"
                 <?= $item['target'] === '_blank' ? 'target="_blank" rel="noopener"' : '' ?>>
                <?= h($item['label']) ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </nav>

      <div class="header-actions">
        <?php if ($wpp): ?>
          <a class="btn btn--wpp" href="https://wa.me/<?= h(preg_replace('/[^0-9]/', '', $wpp)) ?>"
             target="_blank" rel="noopener" aria-label="WhatsApp">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163a11.867 11.867 0 01-1.587-5.946C.16 5.335 5.495 0 12.05 0a11.817 11.817 0 018.413 3.488 11.824 11.824 0 013.48 8.414c-.003 6.557-5.338 11.892-11.893 11.892a11.9 11.9 0 01-5.688-1.448L.057 24zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884a9.86 9.86 0 001.51 5.26l.6.953-1.005 3.667 3.764-.987z"/></svg>
            WhatsApp
          </a>
        <?php endif; ?>

        <button class="nav-toggle" id="navToggle" aria-label="Menuyu ac/kapat" aria-controls="mainNav" aria-expanded="false">
          <span></span><span></span><span></span>
        </button>
      </div>

    </div>
  </div>

</header>
