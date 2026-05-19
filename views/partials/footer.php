<?php
/**
 * Footer — sirket / linkler / iletisim / alt bar
 */
declare(strict_types=1);

$siteName    = (string)ru_setting('site_name', 'RAYU Tarim Makineleri');
$about       = (string)ru_setting('footer_about', '');
$phone       = (string)ru_setting('site_phone', '');
$email       = (string)ru_setting('site_email', '');
$address     = (string)ru_setting('contact_address_full', (string)ru_setting('site_address', ''));
$hours       = (string)ru_setting('contact_working_hours', '');
$founded     = (string)ru_setting('founded_year', '2010');
$fb          = (string)ru_setting('site_facebook', '');
$ig          = (string)ru_setting('site_instagram', '');
$yt          = (string)ru_setting('site_youtube', '');
$li          = (string)ru_setting('site_linkedin', '');

$col1 = ru_menu_items('footer_1');
$col2 = ru_menu_items('footer_2');
$col3 = ru_menu_items('footer_3');
?>
<footer class="site-footer">

  <div class="container site-footer__top">

    <div class="site-footer__col site-footer__col--brand">
      <div class="brand brand--footer">
        <?php if ($logo = (string)ru_setting('site_logo', '')): ?>
          <img src="<?= h(ru_upload_url($logo)) ?>" alt="<?= h($siteName) ?>" class="brand__logo brand__logo--footer">
        <?php else: ?>
          <img src="/assets/img/favicon-192.png" alt="<?= h($siteName) ?>" class="brand__logo--icon" width="48" height="48">
          <div class="brand__text">
            <span class="brand__name"><?= h($siteName) ?></span>
            <span class="brand__sub">EST. <?= h($founded) ?> &middot; Konya</span>
          </div>
        <?php endif; ?>
      </div>
      <p class="site-footer__about"><?= h($about) ?></p>
      <div class="site-footer__social">
        <?php if ($fb): ?><a href="<?= h($fb) ?>" target="_blank" rel="noopener" aria-label="Facebook"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95c5.05-.5 9-4.76 9-9.95z"/></svg></a><?php endif; ?>
        <?php if ($ig): ?><a href="<?= h($ig) ?>" target="_blank" rel="noopener" aria-label="Instagram"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M7 2C4.24 2 2 4.24 2 7v10c0 2.76 2.24 5 5 5h10c2.76 0 5-2.24 5-5V7c0-2.76-2.24-5-5-5H7zm10 2c1.66 0 3 1.34 3 3v10c0 1.66-1.34 3-3 3H7c-1.66 0-3-1.34-3-3V7c0-1.66 1.34-3 3-3h10zm-5 3a5 5 0 100 10 5 5 0 000-10zm5.5-.5a1 1 0 11-2 0 1 1 0 012 0zM12 9a3 3 0 110 6 3 3 0 010-6z"/></svg></a><?php endif; ?>
        <?php if ($yt): ?><a href="<?= h($yt) ?>" target="_blank" rel="noopener" aria-label="YouTube"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M22.54 6.42a2.78 2.78 0 00-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.42a2.78 2.78 0 00-1.94 2A29 29 0 001 12a29 29 0 00.46 5.58 2.78 2.78 0 001.94 2C5.12 20 12 20 12 20s6.88 0 8.6-.42a2.78 2.78 0 001.94-2A29 29 0 0023 12a29 29 0 00-.46-5.58zM10 15V9l5.2 3-5.2 3z"/></svg></a><?php endif; ?>
        <?php if ($li): ?><a href="<?= h($li) ?>" target="_blank" rel="noopener" aria-label="LinkedIn"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3A2 2 0 0121 5v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h14M8.34 18.34V9.67H5.67v8.67h2.67M7 8.33a1.55 1.55 0 100-3.1 1.55 1.55 0 000 3.1M18.34 18.34v-4.74c0-2.48-1.32-3.63-3.08-3.63a2.66 2.66 0 00-2.41 1.32V9.67h-2.67v8.67h2.67v-4.81c0-1.04.2-2.05 1.48-2.05 1.27 0 1.34 1.19 1.34 2.12v4.74h2.67z"/></svg></a><?php endif; ?>
      </div>
    </div>

    <div class="site-footer__col">
      <h4 class="site-footer__h">Kurumsal</h4>
      <ul class="site-footer__list">
        <?php foreach ($col1 as $item): ?>
          <li><a href="<?= h($item['url']) ?>" <?= $item['target'] === '_blank' ? 'target="_blank" rel="noopener"' : '' ?>><?= h($item['label']) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>

    <div class="site-footer__col">
      <h4 class="site-footer__h">Urunler</h4>
      <ul class="site-footer__list">
        <?php foreach ($col2 as $item): ?>
          <li><a href="<?= h($item['url']) ?>" <?= $item['target'] === '_blank' ? 'target="_blank" rel="noopener"' : '' ?>><?= h($item['label']) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>

    <div class="site-footer__col">
      <h4 class="site-footer__h">Iletisim</h4>
      <ul class="site-footer__list site-footer__list--contact">
        <?php if ($address): ?>
          <li>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 110-5 2.5 2.5 0 010 5z"/></svg>
            <span><?= h($address) ?></span>
          </li>
        <?php endif; ?>
        <?php if ($phone): ?>
          <li>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M6.62 10.79a15.05 15.05 0 006.59 6.59l2.2-2.2a1 1 0 011.05-.24c1.12.37 2.33.57 3.57.57a1 1 0 011 1V20a1 1 0 01-1 1A17 17 0 013 4a1 1 0 011-1h3.5a1 1 0 011 1c0 1.24.2 2.45.57 3.57a1 1 0 01-.24 1.05z"/></svg>
            <a href="tel:<?= h(preg_replace('/[^0-9+]/', '', $phone)) ?>"><?= h($phone) ?></a>
          </li>
        <?php endif; ?>
        <?php if ($email): ?>
          <li>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4a2 2 0 00-2 2v12a2 2 0 002 2h16a2 2 0 002-2V6a2 2 0 00-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
            <a href="mailto:<?= h($email) ?>"><?= h($email) ?></a>
          </li>
        <?php endif; ?>
        <?php if ($hours): ?>
          <li>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 100 20 10 10 0 000-20zm.5 5h-1v6l5.25 3.15.5-.82-4.75-2.83V7z"/></svg>
            <span><?= h($hours) ?></span>
          </li>
        <?php endif; ?>
      </ul>
      <?php
        $extraLinks = $col3;
        if ($extraLinks):
      ?>
      <ul class="site-footer__list site-footer__list--inline">
        <?php foreach ($extraLinks as $item): ?>
          <li><a href="<?= h($item['url']) ?>"><?= h($item['label']) ?></a></li>
        <?php endforeach; ?>
      </ul>
      <?php endif; ?>
    </div>

  </div>

  <div class="site-footer__bottom">
    <div class="container site-footer__bottom-inner">
      <div>&copy; <?= date('Y') ?> <?= h($siteName) ?>. Tum haklari saklidir.</div>
      <div class="site-footer__credit">
        v<?= h(ru_version()) ?> &middot; <a href="https://codega.com.tr" target="_blank" rel="noopener">CODEGA</a> tarafindan gelistirildi
      </div>
    </div>
  </div>

</footer>
