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
    <p class="page-hero__sub">Tarım makineleri, zirai ilaçlama çözümleri, yedek parça ve servis.</p>
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
            <h2 class="section__title"><?= h($title) ?> çözümleri</h2>
          </div>
          <p class="section__lead">Her ürün için traktör gücü, kullanım senaryosu ve servis kapsamı birlikte değerlendirilir.</p>
        </div>

        <div class="product-grid product-grid--catalog">
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
                <a href="/iletisim" class="product-card__cta">Teknik teklif al</a>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>
