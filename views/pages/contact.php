<?php
/**
 * İletişim Sayfası
 */
declare(strict_types=1);

$phone   = (string)ru_setting('site_phone', '');
$email   = (string)ru_setting('site_email', '');
$address = (string)ru_setting('contact_address_full', (string)ru_setting('site_address', ''));
$hours   = (string)ru_setting('contact_working_hours', '');
$wpp     = (string)ru_setting('site_whatsapp', '');
$lat     = (string)ru_setting('contact_lat', '37.8746');
$lng     = (string)ru_setting('contact_lng', '32.4932');
?>

<section class="page-hero">
  <div class="page-hero__overlay"></div>
  <div class="container page-hero__inner">
    <h1 class="page-hero__title">İletişim</h1>
    <p class="page-hero__sub">Bize ulaşın, sorularınız, teklif istekleriniz ve bayilik başvurularınız için buradayız.</p>
  </div>
</section>

<?php
ru_partial('breadcrumb', [
    'items' => [
        ['label' => 'Anasayfa', 'url' => '/'],
        ['label' => 'İletişim'],
    ],
]);
?>

<section class="contact section">
  <div class="container">

    <div class="contact__grid">

      <!-- Bilgiler -->
      <div class="contact__info">
        <h2 class="contact__h">İletişim Bilgileri</h2>

        <?php if ($address): ?>
          <div class="contact-card">
            <div class="contact-card__icon">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 110-5 2.5 2.5 0 010 5z"/></svg>
            </div>
            <div>
              <div class="contact-card__label">Adres</div>
              <div class="contact-card__value"><?= h($address) ?></div>
            </div>
          </div>
        <?php endif; ?>

        <?php if ($phone): ?>
          <div class="contact-card">
            <div class="contact-card__icon">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M6.62 10.79a15.05 15.05 0 006.59 6.59l2.2-2.2a1 1 0 011.05-.24c1.12.37 2.33.57 3.57.57a1 1 0 011 1V20a1 1 0 01-1 1A17 17 0 013 4a1 1 0 011-1h3.5a1 1 0 011 1c0 1.24.2 2.45.57 3.57a1 1 0 01-.24 1.05z"/></svg>
            </div>
            <div>
              <div class="contact-card__label">Telefon</div>
              <div class="contact-card__value"><a href="tel:<?= h(preg_replace('/[^0-9+]/', '', $phone)) ?>"><?= h($phone) ?></a></div>
            </div>
          </div>
        <?php endif; ?>

        <?php if ($email): ?>
          <div class="contact-card">
            <div class="contact-card__icon">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4a2 2 0 00-2 2v12a2 2 0 002 2h16a2 2 0 002-2V6a2 2 0 00-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
            </div>
            <div>
              <div class="contact-card__label">E-posta</div>
              <div class="contact-card__value"><a href="mailto:<?= h($email) ?>"><?= h($email) ?></a></div>
            </div>
          </div>
        <?php endif; ?>

        <?php if ($hours): ?>
          <div class="contact-card">
            <div class="contact-card__icon">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 100 20 10 10 0 000-20zm.5 5h-1v6l5.25 3.15.5-.82-4.75-2.83V7z"/></svg>
            </div>
            <div>
              <div class="contact-card__label">Çalışma Saatleri</div>
              <div class="contact-card__value"><?= h($hours) ?></div>
            </div>
          </div>
        <?php endif; ?>

        <?php if ($wpp): ?>
          <a class="btn btn--wpp btn--lg" href="https://wa.me/<?= h(preg_replace('/[^0-9]/', '', $wpp)) ?>" target="_blank" rel="noopener" style="margin-top:1rem;width:100%;justify-content:center">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163a11.867 11.867 0 01-1.587-5.946C.16 5.335 5.495 0 12.05 0a11.817 11.817 0 018.413 3.488 11.824 11.824 0 013.48 8.414c-.003 6.557-5.338 11.892-11.893 11.892a11.9 11.9 0 01-5.688-1.448L.057 24z"/></svg>
            WhatsApp ile Yazın
          </a>
        <?php endif; ?>
      </div>

      <!-- Form -->
      <div class="contact__form">
        <h2 class="contact__h">Mesaj Gönderin</h2>
        <?php foreach (ru_flash_get() as $flash): ?>
          <p class="contact__note"><?= h($flash['message'] ?? '') ?></p>
        <?php endforeach; ?>

        <form class="contact-form" method="post" action="/iletisim">
          <?= ru_csrf_field() ?>
          <div class="form-row">
            <div class="form-group">
              <label>Ad Soyad</label>
              <input type="text" name="full_name" required placeholder="Adınız Soyadınız">
            </div>
            <div class="form-group">
              <label>Telefon</label>
              <input type="tel" name="phone" placeholder="0 5xx xxx xx xx">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Firma</label>
              <input type="text" name="company" placeholder="Firma / işletme adı">
            </div>
            <div class="form-group">
              <label>Şehir</label>
              <input type="text" name="city" placeholder="Şehir">
            </div>
          </div>
          <div class="form-group">
            <label>E-posta</label>
            <input type="email" name="email" placeholder="ornek@email.com">
          </div>
          <div class="form-group">
            <label>Konu</label>
            <select name="type">
              <option value="contact">Genel Bilgi</option>
              <option value="quote">Ürün Teklifi</option>
              <option value="dealer">Bayilik Başvurusu</option>
              <option value="service">Teknik Servis</option>
              <option value="used">2. El Başvurusu</option>
            </select>
          </div>
          <input type="hidden" name="subject" value="Web sitesi iletişim formu">
          <div class="form-group">
            <label>Mesaj</label>
            <textarea rows="5" name="message" placeholder="Mesajınız..."></textarea>
          </div>
          <button type="submit" class="btn btn--primary btn--lg">Gönder</button>
        </form>
      </div>
    </div>

    <!-- Harita -->
    <?php if ($lat && $lng): ?>
    <div class="contact__map">
      <iframe
        src="https://www.openstreetmap.org/export/embed.html?bbox=<?= (float)$lng - 0.02 ?>%2C<?= (float)$lat - 0.01 ?>%2C<?= (float)$lng + 0.02 ?>%2C<?= (float)$lat + 0.01 ?>&amp;layer=mapnik&amp;marker=<?= h($lat) ?>%2C<?= h($lng) ?>"
        loading="lazy"
        title="Konum haritası"
        style="border:0;width:100%;height:100%;display:block"></iframe>
    </div>
    <?php endif; ?>

  </div>
</section>
