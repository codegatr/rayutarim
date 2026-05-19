<?php
/**
 * Anasayfa
 */
declare(strict_types=1);

$slides = $slides ?? [];
$headerPages = $headerPages ?? [];
?>

<?php ru_partial('slider', ['slides' => $slides]); ?>

<!-- Ozellikler / Highlights -->
<section class="features">
  <div class="container">
    <div class="features__grid">
      <?php for ($i = 1; $i <= 4; $i++):
        $title = (string)ru_setting("feature_{$i}_title", '');
        $text  = (string)ru_setting("feature_{$i}_text", '');
        $icon  = (string)ru_setting("feature_{$i}_icon", '');
        if (!$title) continue;
      ?>
        <article class="feature">
          <div class="feature__icon" aria-hidden="true">
            <?php if (str_starts_with($icon, '<svg')): ?>
              <?= $icon ?>
            <?php elseif ($icon): ?>
              <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor"><path d="<?= h($icon) ?>"/></svg>
            <?php else: ?>
              <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10"/></svg>
            <?php endif; ?>
          </div>
          <h3 class="feature__title"><?= h($title) ?></h3>
          <p class="feature__text"><?= h($text) ?></p>
        </article>
      <?php endfor; ?>
    </div>
  </div>
</section>

<!-- Kategoriler / Onizleme (Faz 3'te dolacak) -->
<section class="cat-preview section">
  <div class="container">
    <header class="section__head">
      <span class="section__eyebrow">Urun Kategorilerimiz</span>
      <h2 class="section__title">Topraktan hasada, her ihtiyaca cevap</h2>
      <p class="section__lead">Tarla hazirligindan hasada, ekim sezonundan ilaclamaya kadar tum tarimsal sureclerinizde guvenilir cozum ortaginiz.</p>
    </header>

    <div class="cat-grid">
      <a class="cat-card cat-card--lg" href="/urunler/tarim-aletleri">
        <div class="cat-card__bg cat-card__bg--1" aria-hidden="true"></div>
        <div class="cat-card__overlay"></div>
        <div class="cat-card__body">
          <span class="cat-card__eyebrow">Kategori</span>
          <h3 class="cat-card__title">Tarim Aletleri</h3>
          <p class="cat-card__text">Ekim makineleri, toprak isleme ekipmanlari, hasat aletleri ve daha fazlasi.</p>
          <span class="cat-card__cta">Incele <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M14 5l7 7-7 7M21 12H3"/></svg></span>
        </div>
      </a>

      <a class="cat-card" href="/urunler/ziraai-ilaclar">
        <div class="cat-card__bg cat-card__bg--2" aria-hidden="true"></div>
        <div class="cat-card__overlay"></div>
        <div class="cat-card__body">
          <span class="cat-card__eyebrow">Kategori</span>
          <h3 class="cat-card__title">Ziraai Ilaclar</h3>
          <p class="cat-card__text">Bitki saglik korumasinda lisansli urun yelpazesi.</p>
          <span class="cat-card__cta">Incele <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M14 5l7 7-7 7M21 12H3"/></svg></span>
        </div>
      </a>

      <a class="cat-card" href="/ikinci-el">
        <div class="cat-card__bg cat-card__bg--3" aria-hidden="true"></div>
        <div class="cat-card__overlay"></div>
        <div class="cat-card__body">
          <span class="cat-card__eyebrow">Pazar</span>
          <h3 class="cat-card__title">2. El</h3>
          <p class="cat-card__text">Ekspertiz onayli ikinci el makineleri Ray-U guvencesiyle.</p>
          <span class="cat-card__cta">Pazara Gir <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M14 5l7 7-7 7M21 12H3"/></svg></span>
        </div>
      </a>
    </div>
  </div>
</section>

<!-- Kurumsal kisa kart -->
<section class="about-strip section section--dark">
  <div class="container about-strip__inner">
    <div class="about-strip__text">
      <span class="section__eyebrow section__eyebrow--light">Hakkimizda</span>
      <h2 class="section__title section__title--light">
        Anadolu mhendisligi, <span class="ac">dunya standartlari</span>
      </h2>
      <p class="section__lead section__lead--light">
        <?= h((string)ru_setting('about_short', 'Tarim teknolojisinin kalbinde, ureticinin yaninda.')) ?>
        Konya'da kurulan ve buyuyen yapimiz, bugun Turkiye'nin dort bir yaninda binlerce uretici ile bulusuyor.
      </p>
      <div class="about-strip__cta">
        <a class="btn btn--primary btn--lg" href="/hakkimizda">Bizi Tani</a>
        <a class="btn btn--ghost btn--lg btn--ghost-light" href="/iletisim">Bayilik Basvurusu</a>
      </div>
    </div>
    <div class="about-strip__stats">
      <div class="stat">
        <div class="stat__num"><?= date('Y') - (int)(ru_setting('founded_year', '2010')) ?>+</div>
        <div class="stat__label">Yillik tecrube</div>
      </div>
      <div class="stat">
        <div class="stat__num">200+</div>
        <div class="stat__label">Yetkili bayi</div>
      </div>
      <div class="stat">
        <div class="stat__num">50+</div>
        <div class="stat__label">Ilde hizmet</div>
      </div>
      <div class="stat">
        <div class="stat__num">10K+</div>
        <div class="stat__label">Mutlu uretici</div>
      </div>
    </div>
  </div>
</section>

<!-- CTA Bandi -->
<section class="cta-band">
  <div class="container cta-band__inner">
    <div>
      <h2 class="cta-band__title">Ihtiyaciniza ozel cozum mu ariyorsunuz?</h2>
      <p class="cta-band__sub">Teknik ekibimiz, isletmenizin buyuklugune ve urun deseninize gore en uygun makine ve ilaclama programini birlikte planlar.</p>
    </div>
    <div class="cta-band__actions">
      <a class="btn btn--primary btn--lg" href="/iletisim">Teklif Alin</a>
      <?php $phone = (string)ru_setting('site_phone', ''); if ($phone): ?>
        <a class="btn btn--ghost btn--lg" href="tel:<?= h(preg_replace('/[^0-9+]/', '', $phone)) ?>">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M6.62 10.79a15.05 15.05 0 006.59 6.59l2.2-2.2a1 1 0 011.05-.24c1.12.37 2.33.57 3.57.57a1 1 0 011 1V20a1 1 0 01-1 1A17 17 0 013 4a1 1 0 011-1h3.5a1 1 0 011 1c0 1.24.2 2.45.57 3.57a1 1 0 01-.24 1.05z"/></svg>
          <?= h($phone) ?>
        </a>
      <?php endif; ?>
    </div>
  </div>
</section>
