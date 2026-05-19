<?php
/**
 * Anasayfa
 */
declare(strict_types=1);

$slides = $slides ?? [];
$categories = ru_catalog_categories();
$products = array_slice(ru_catalog_products(), 0, 3);
?>

<?php ru_partial('slider', ['slides' => $slides]); ?>

<section class="trust-strip" aria-label="Kurumsal guven">
  <div class="container trust-strip__inner">
    <div><strong>Türkiye geneli satış</strong><span>Bölge, ürün deseni ve traktör gücüne göre doğru ekipman seçimi.</span></div>
    <div><strong>Satış sonrası destek</strong><span>Yedek parça, servis kaydı ve sezon öncesi bakım planı.</span></div>
    <div><strong>Kurumsal teklif süreci</strong><span>Teklif, termin, teslimat ve eğitim adımları tek dosyada izlenir.</span></div>
  </div>
</section>

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

<section class="section category-suite">
  <div class="container">
    <header class="section__head">
      <span class="section__eyebrow">Ürün Grupları</span>
      <h2 class="section__title">Makine, ilaçlama ve servis aynı kurumsal çatı altında</h2>
      <p class="section__lead">Sadece ürün listeleyen bir site değil; çiftçinin sezon kararını hızlandıran, bayilik ve filo satışını destekleyen bir satış platformu.</p>
    </header>

    <div class="category-suite__grid">
      <?php foreach ($categories as $slug => $cat): ?>
        <a class="category-tile" href="/urunler/<?= h($slug) ?>">
          <span class="category-tile__icon" aria-hidden="true">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><path d="<?= h($cat['icon']) ?>"/></svg>
          </span>
          <h3><?= h($cat['title']) ?></h3>
          <p><?= h($cat['summary']) ?></p>
          <span class="category-tile__link">Ürünleri incele</span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section--soft product-showcase">
  <div class="container">
    <div class="section-split">
      <div>
        <span class="section__eyebrow">Öne Çıkanlar</span>
        <h2 class="section__title">Satın alma kararını kolaylaştıran net teknik sunum</h2>
      </div>
      <p class="section__lead">Global ekipman bayilerindeki güçlü filtreleme ve servis vurgusunu, Türkiye pazarının hızlı teklif beklentisiyle birleştirdik.</p>
    </div>

    <div class="product-grid">
      <?php foreach ($products as $product): ?>
        <article class="product-card">
          <div class="product-card__media" aria-hidden="true">
            <span><?= h($product['badge']) ?></span>
          </div>
          <div class="product-card__body">
            <h3><?= h($product['name']) ?></h3>
            <p><?= h($product['summary']) ?></p>
            <ul>
              <?php foreach ($product['specs'] as $spec): ?><li><?= h($spec) ?></li><?php endforeach; ?>
            </ul>
            <a href="/iletisim" class="product-card__cta">Teklif iste</a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section corporate-flow">
  <div class="container corporate-flow__inner">
    <div>
      <span class="section__eyebrow">Kurumsal Yapı</span>
      <h2 class="section__title">Satıştan servise kadar ölçülebilir operasyon</h2>
      <p class="section__lead">RAYU Tarım Makineleri; ürün danışmanlığı, bölgesel bayi yönetimi, servis planlama ve yedek parça tedariğini tek süreçte toplar.</p>
      <div class="about-strip__cta">
        <a class="btn btn--primary btn--lg" href="/hakkimizda">Kurumsal profili gör</a>
        <a class="btn btn--ghost btn--lg" href="/iletisim">Bayilik başvurusu</a>
      </div>
    </div>
    <div class="process-list">
      <article><span>01</span><strong>İhtiyaç analizi</strong><p>Toprak yapısı, ekim deseni, traktör gücü ve bütçe birlikte değerlendirilir.</p></article>
      <article><span>02</span><strong>Teknik teklif</strong><p>Ürün, opsiyon, termin, teslimat ve eğitim kapsamı net dokümante edilir.</p></article>
      <article><span>03</span><strong>Teslimat ve eğitim</strong><p>Saha kurulumu, operatör kullanımı ve ilk bakım kontrolleri planlanır.</p></article>
      <article><span>04</span><strong>Servis yaşam döngüsü</strong><p>Yedek parça ve periyodik bakım takibiyle sezon kaybı azaltılır.</p></article>
    </div>
  </div>
</section>

<section class="cta-band">
  <div class="container cta-band__inner">
    <div>
      <h2 class="cta-band__title">Yeni sezon için doğru ekipmanı birlikte seçelim</h2>
      <p class="cta-band__sub">Teknik ekibimiz işletmenizin büyüklüğüne, ürün desenine ve çalışma takviminize göre makine ve ilaçlama programını planlar.</p>
    </div>
    <div class="cta-band__actions">
      <a class="btn btn--primary btn--lg" href="/iletisim">Teklif alın</a>
      <?php $phone = (string)ru_setting('site_phone', ''); if ($phone): ?>
        <a class="btn btn--ghost btn--lg" href="tel:<?= h(preg_replace('/[^0-9+]/', '', $phone)) ?>"><?= h($phone) ?></a>
      <?php endif; ?>
    </div>
  </div>
</section>
