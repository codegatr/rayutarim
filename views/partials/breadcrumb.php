<?php
/**
 * Breadcrumb — basit hiyerarsi
 * $items = [['label' => 'Anasayfa', 'url' => '/'], ['label' => 'Hakkimizda']]
 */
declare(strict_types=1);

$items = $items ?? [];
if (empty($items)) return;
?>
<nav class="breadcrumb" aria-label="Breadcrumb">
  <div class="container">
    <ol class="breadcrumb__list" itemscope itemtype="https://schema.org/BreadcrumbList">
      <?php foreach ($items as $i => $item):
        $isLast = ($i === count($items) - 1);
      ?>
        <li class="breadcrumb__item <?= $isLast ? 'is-current' : '' ?>"
            itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
          <?php if (!$isLast && !empty($item['url'])): ?>
            <a href="<?= h($item['url']) ?>" itemprop="item"><span itemprop="name"><?= h($item['label']) ?></span></a>
          <?php else: ?>
            <span itemprop="name" aria-current="page"><?= h($item['label']) ?></span>
          <?php endif; ?>
          <meta itemprop="position" content="<?= $i + 1 ?>">
        </li>
      <?php endforeach; ?>
    </ol>
  </div>
</nav>
