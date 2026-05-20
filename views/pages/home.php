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
    <div><strong>Çok markalı ürün havuzu</strong><span>Üretici, ithalatçı, bayi ve ikinci el satıcılarını tek satış akışında toplar.</span></div>
    <div><strong>Tek talep, çok teklif</strong><span>Bölge, bütçe, traktör gücü ve termin ihtiyacına göre seçenekler karşılaştırılır.</span></div>
    <div><strong>Satış sonrası takip</strong><span>Yedek parça, servis, garanti ve teslimat bilgileri aynı dosyada izlenir.</span></div>
  </div>
</section>

<section class="market-command">
  <div class="container market-command__inner">
    <div>
      <span class="section__eyebrow">Aracı Kurum Modeli</span>
      <h2>Tarım makinelerinde alıcı ile firmayı doğru noktada buluşturuyoruz</h2>
      <p>RAYU; sadece kendi ürününü anlatan bir web sitesi değil, çiftçinin ihtiyacını tedarikçi ağıyla eşleştiren kurumsal satış merkezidir. Ürün, marka, fiyat, teslimat, servis ve yedek parça aynı satın alma dosyasında değerlendirilir.</p>
    </div>
    <div class="market-command__stats">
      <article><strong>Yeni</strong><span>Makine ve ekipman</span></article>
      <article><strong>2. El</strong><span>Ekspertizli ilan akışı</span></article>
      <article><strong>Parça</strong><span>Marka uyumlu tedarik</span></article>
      <article><strong>Servis</strong><span>Bölgesel destek planı</span></article>
    </div>
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
      <h2 class="section__title">Bir markaya sıkışmayan, ihtiyaca göre ürün seçen satış yapısı</h2>
      <p class="section__lead">Traktör, ekipman, ilaçlama, hasat, yedek parça ve ikinci el seçenekleri tek talep formunda toplanır; uygun firmalarla eşleştirilir.</p>
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
            <div class="product-card__brand"><?= h((string)($product['brand'] ?? 'RAYU tedarik ağı')) ?></div>
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

<section class="section brand-network">
  <div class="container">
    <div class="section-split">
      <div>
        <span class="section__eyebrow">Marka ve Firma Ağı</span>
        <h2 class="section__title">Üretici, bayi ve ithalatçı için satış kanalı</h2>
      </div>
      <p class="section__lead">RAYU tarafsız bir ürün vitrini kurar: firmalar ürünlerini ekler, alıcılar tek yerden talep oluşturur, satış ekibi teknik karşılaştırmayı yönetir.</p>
    </div>
    <div class="network-grid">
      <article><strong>Üreticiler</strong><span>Yerli tarım makineleri ve ekipman portföyleri</span></article>
      <article><strong>İthalatçılar</strong><span>Türkiye dağıtımı, stok ve teslimat planı</span></article>
      <article><strong>Bölge Bayileri</strong><span>Şehir bazlı fiyat, servis ve sevkiyat avantajı</span></article>
      <article><strong>Servis & Parça</strong><span>Marka uyumlu yedek parça ve bakım paketleri</span></article>
    </div>
    <div class="brand-network__cta">
      <a class="btn btn--primary btn--lg" href="/tedarikci-basvurusu">Firma olarak başvur</a>
      <a class="btn btn--ghost btn--lg" href="/markalar">Marka ağını incele</a>
    </div>
  </div>
</section>

<section class="section corporate-flow">
  <div class="container corporate-flow__inner">
    <div>
      <span class="section__eyebrow">Kurumsal Yapı</span>
      <h2 class="section__title">Satıştan servise kadar ölçülebilir operasyon</h2>
      <p class="section__lead">RAYU; ürün danışmanlığı, tedarikçi yönetimi, fiyat karşılaştırma, teslimat, servis planlama ve yedek parça tedariğini tek süreçte toplar.</p>
      <div class="about-strip__cta">
        <a class="btn btn--primary btn--lg" href="/hakkimizda">Kurumsal profili gör</a>
        <a class="btn btn--ghost btn--lg" href="/tedarikci-basvurusu">Tedarikçi başvurusu</a>
      </div>
    </div>
    <div class="process-list">
      <article><span>01</span><strong>Talep toplama</strong><p>Alıcının şehir, ürün, bütçe, traktör gücü ve teslimat beklentisi alınır.</p></article>
      <article><span>02</span><strong>Tedarikçi eşleştirme</strong><p>Uygun üretici, bayi, ithalatçı veya ikinci el satıcıları belirlenir.</p></article>
      <article><span>03</span><strong>Karşılaştırmalı teklif</strong><p>Fiyat, garanti, servis, teslimat ve opsiyonlar tek dosyada sunulur.</p></article>
      <article><span>04</span><strong>Satış sonrası kayıt</strong><p>Teslimat, servis ve yedek parça süreci takip edilir.</p></article>
    </div>
  </div>
</section>

<section class="cta-band">
  <div class="container cta-band__inner">
    <div>
      <h2 class="cta-band__title">Tek talep bırakın, uygun firmaları sizin için karşılaştıralım</h2>
      <p class="cta-band__sub">Makine, ekipman, yedek parça veya ikinci el ihtiyacınızı iletin; RAYU satış ekibi marka, fiyat, servis ve teslimat seçeneklerini netleştirsin.</p>
    </div>
    <div class="cta-band__actions">
      <a class="btn btn--primary btn--lg" href="/iletisim">Teklif alın</a>
      <?php $phone = (string)ru_setting('site_phone', ''); if ($phone): ?>
        <a class="btn btn--ghost btn--lg" href="tel:<?= h(preg_replace('/[^0-9+]/', '', $phone)) ?>"><?= h($phone) ?></a>
      <?php endif; ?>
    </div>
  </div>
</section>
