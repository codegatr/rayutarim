<?php
/**
 * Bakim modu — admin disindaki tum kullanicilara
 */
declare(strict_types=1);

http_response_code(503);
header('Retry-After: 3600');
?>
<section class="maintenance">
  <div class="container container--narrow">
    <div class="maintenance__card">
      <div class="maintenance__icon">
        <svg width="80" height="80" viewBox="0 0 24 24" fill="currentColor"><path d="M22.7 19l-9.1-9.1c.9-2.3.4-5-1.5-6.9-2-2-5-2.4-7.4-1.3L9 6 6 9 1.6 4.7C.4 7.1.9 10.1 2.9 12.1c1.9 1.9 4.6 2.4 6.9 1.5l9.1 9.1c.4.4 1 .4 1.4 0l2.3-2.3c.5-.4.5-1.1.1-1.4z"/></svg>
      </div>
      <h1 class="maintenance__title">Bakim Calismasi</h1>
      <p class="maintenance__text">
        Sitemiz su anda planli bakim nedeniyle gecici olarak kapali.
        Kisa sure sonra tekrar hizmetinizdeyiz.
      </p>
      <?php $phone = (string)ru_setting('site_phone', ''); $email = (string)ru_setting('site_email', ''); ?>
      <?php if ($phone || $email): ?>
        <div class="maintenance__contact">
          <?php if ($phone): ?><div><strong>Telefon:</strong> <a href="tel:<?= h(preg_replace('/[^0-9+]/', '', $phone)) ?>"><?= h($phone) ?></a></div><?php endif; ?>
          <?php if ($email): ?><div><strong>E-posta:</strong> <a href="mailto:<?= h($email) ?>"><?= h($email) ?></a></div><?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>
