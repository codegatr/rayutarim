<?php
/**
 * Generic Page — kurumsal sayfalar (Hakkimizda, Misyon, Tarihce, vs.)
 * $page — DB'den gelen sayfa kaydi
 */
declare(strict_types=1);

$page = $page ?? [];
$title    = (string)($page['title'] ?? 'Sayfa');
$subtitle = (string)($page['subtitle'] ?? '');
$content  = (string)($page['content'] ?? '');
$hero     = (string)($page['hero_image'] ?? '');
?>

<section class="page-hero <?= $hero ? 'page-hero--img' : '' ?>" <?= $hero ? 'style="background-image:url(' . h(ru_upload_url($hero)) . ')"' : '' ?>>
  <div class="page-hero__overlay"></div>
  <div class="container page-hero__inner">
    <h1 class="page-hero__title"><?= h($title) ?></h1>
    <?php if ($subtitle): ?><p class="page-hero__sub"><?= h($subtitle) ?></p><?php endif; ?>
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

<section class="page-body section">
  <div class="container container--narrow">
    <article class="prose">
      <?= $content ?: '<p>Bu sayfa icin icerik henuz eklenmemis.</p>' ?>
    </article>
  </div>
</section>
