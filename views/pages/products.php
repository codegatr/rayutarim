<?php
declare(strict_types=1);

$categories = $categories ?? [];
$products = $products ?? [];
$activeCategory = (string)($activeCategory ?? '');
$title = $activeCategory && isset($categories[$activeCategory]) ? $categories[$activeCategory]['title'] : 'Ürünler';
?>

<section class="page-hero">
  <div class="page-hero__overlay"></div>
  <div class="container page-hero__inner">
    <h1 class="page-hero__title"><?= h($title) ?></h1>
    <p class="page-hero__sub">Farklı firmalardan tarım makineleri, zirai ilaçlama çözümleri, yedek parça, servis ve ikinci el seçenekleri.</p>
  </div>
</section>

<section class="catalog-intro">
  <div class="container catalog-intro__grid">
    <article><strong>Çok Markalı Katalog</strong><span>Ürünler tek üreticiyle sınırlı değildir; uygun marka ve tedarikçi birlikte değerlendirilir.</span></article>
    <article><strong>Karşılaştırmalı Teklif</strong><span>Fiyat, garanti, teslimat, servis ve yedek parça koşulları aynı talepte toplanır.</span></article>
    <article><strong>Kurumsal Aracılık</strong><span>RAYU, alıcı ile firma arasında şeffaf satış dosyası oluşturur.</span></article>
  </div>
</section>

<section class="section product-page">
  <div class="container">
    <div class="catalog-layout">
      <aside class="catalog-filter" aria-label="Ürün kategorileri">
        <a class="<?= $activeCategory === '' ? 'is-active' : '' ?>" href="/urunler">Tüm ürün grupları</a>
        <?php foreach ($categories as $slug => $cat): ?>
          <a class="<?= $activeCategory === $slug ? 'is-active' : '' ?>" href="/urunler/<?= h($slug) ?>"><?= h($cat['title']) ?></a>
        <?php endforeach; ?>
      </aside>

      <div>
        <div class="section-split section-split--compact">
          <div>
            <span class="section__eyebrow">Katalog</span>
            <h2 class="section__title"><?= h($title) ?> için uygun firmaları karşılaştırın</h2>
          </div>
          <p class="section__lead">Her ürün için traktör gücü, kullanım senaryosu, servis kapsamı, teslimat bölgesi ve marka güvenilirliği birlikte değerlendirilir.</p>
        </div>

        <div class="product-grid product-grid--catalog">
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
                <a href="/iletisim?type=quote" class="product-card__cta">Karşılaştırmalı teklif al</a>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section section--soft procurement-flow">
  <div class="container">
    <header class="section__head">
      <span class="section__eyebrow">Satın Alma Akışı</span>
      <h2 class="section__title">Doğru ürünü bulmak için tek tek firma gezmenize gerek yok</h2>
      <p class="section__lead">Talebinizi bırakın; RAYU ekibi uygun tedarikçilerden bilgi toplar ve satın alma kararını sadeleştirir.</p>
    </header>
    <div class="procurement-flow__grid">
      <article><span>1</span><strong>İhtiyacı yazın</strong><p>Ürün grubu, şehir, traktör gücü, bütçe ve teslimat beklentinizi iletin.</p></article>
      <article><span>2</span><strong>Firmalar eşleşsin</strong><p>Üretici, bayi, ithalatçı veya ikinci el satıcıları arasından uygun seçenekler belirlenir.</p></article>
      <article><span>3</span><strong>Teklifleri karşılaştırın</strong><p>Fiyat, garanti, servis, teslimat ve parça koşulları aynı tabloda netleşir.</p></article>
      <article><span>4</span><strong>Satış sonrası izleyin</strong><p>Teslimat, servis ve yedek parça süreci kayıt altında tutulur.</p></article>
    </div>
  </div>
</section>
