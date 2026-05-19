<?php
/**
 * Placeholder — henüz aktifleşmemiş bölümler (Ürünler, 2.El)
 */
declare(strict_types=1);

$title = $placeholder_title ?? 'Yakında';
$msg   = $placeholder_message ?? 'Bu bölüm yakında yayına alınacak.';
?>

<section class="page-hero">
  <div class="page-hero__overlay"></div>
  <div class="container page-hero__inner">
    <h1 class="page-hero__title"><?= h($title) ?></h1>
    <p class="page-hero__sub"><?= h($msg) ?></p>
  </div>
</section>

<?php
ru_partial('breadcrumb', [
    'items' => [
        ['label' => 'Anasayfa', 'url' => '/'],
        ['label' => $title],
    ],
]);
?>

<section class="placeholder section">
  <div class="container container--narrow">
    <div class="placeholder__card">
      <div class="placeholder__icon" aria-hidden="true">
        <svg width="80" height="80" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2A10 10 0 002 12a10 10 0 0010 10 10 10 0 0010-10A10 10 0 0012 2zm0 4a6 6 0 016 6 6 6 0 01-6 6 6 6 0 01-6-6 6 6 0 016-6m0 2a4 4 0 00-4 4 4 4 0 004 4 4 4 0 004-4 4 4 0 00-4-4z"/></svg>
      </div>
      <h2 class="placeholder__title">Yapım aşamasında</h2>
      <p class="placeholder__text">
        <?= h($msg) ?> Bu sırada ürünlerimiz, hizmetlerimiz veya bayilik başvurularınız için
        bizimle doğrudan iletişime geçebilirsiniz.
      </p>
      <div class="placeholder__cta">
        <a class="btn btn--primary btn--lg" href="/iletisim">İletişime Geç</a>
        <a class="btn btn--ghost btn--lg" href="/">Anasayfaya Dön</a>
      </div>
    </div>
  </div>
</section>
