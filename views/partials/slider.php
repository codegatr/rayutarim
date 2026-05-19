<?php
/**
 * Hero Slider
 * $slides — ru_active_sliders() ciktisi
 */
declare(strict_types=1);

$slides = $slides ?? [];
if (empty($slides)) {
    // Fallback hero — slider hic yoksa veya hepsi pasif ise
    $heroTitle    = (string)ru_setting('hero_title', 'Topragin gucunu teknolojiyle bulusturuyoruz');
    $heroSub      = (string)ru_setting('hero_subtitle', '');
    $ctaPriText   = (string)ru_setting('hero_cta_primary_text', 'Urunler');
    $ctaPriUrl    = (string)ru_setting('hero_cta_primary_url', '/urunler');
    $ctaSecText   = (string)ru_setting('hero_cta_secondary_text', 'Iletisim');
    $ctaSecUrl    = (string)ru_setting('hero_cta_secondary_url', '/iletisim');
?>
<section class="hero hero--fallback" aria-label="Hero">
  <div class="hero__bg" aria-hidden="true"></div>
  <div class="container hero__inner">
    <div class="hero__content hero__content--left">
      <span class="hero__badge">RAYU Tarım Makineleri</span>
      <h1 class="hero__title"><?= h($heroTitle) ?></h1>
      <?php if ($heroSub): ?><p class="hero__sub"><?= h($heroSub) ?></p><?php endif; ?>
      <div class="hero__metrics" aria-label="Kurumsal kapsam">
        <span><strong>50+</strong> ilde hizmet</span>
        <span><strong>200+</strong> bayi ve servis noktası</span>
        <span><strong>10K+</strong> üretici deneyimi</span>
      </div>
      <div class="hero__cta">
        <a class="btn btn--primary btn--lg" href="<?= h($ctaPriUrl) ?>"><?= h($ctaPriText) ?></a>
        <a class="btn btn--ghost btn--lg" href="<?= h($ctaSecUrl) ?>"><?= h($ctaSecText) ?></a>
      </div>
    </div>
  </div>
</section>
<?php return; } ?>

<section class="hero hero--slider" id="heroSlider" aria-roledescription="carousel">
  <div class="hero__track" id="heroTrack">
    <?php foreach ($slides as $i => $s):
      $img = $s['image'] ? ru_upload_url($s['image']) : '';
      $imgM = $s['image_mobile'] ? ru_upload_url($s['image_mobile']) : $img;
      $pos = $s['text_position'] ?: 'left';
      $colorClass = $s['text_color'] === 'dark' ? 'hero__content--dark' : '';
      $overlay = max(0, min(95, (int)$s['overlay_opacity']));
    ?>
      <div class="hero__slide <?= $i === 0 ? 'is-active' : '' ?>"
           role="group"
           aria-roledescription="slide"
           aria-label="<?= ($i + 1) . ' / ' . count($slides) ?>"
           data-index="<?= $i ?>">
        <?php if ($img): ?>
          <picture class="hero__bg">
            <?php if ($imgM): ?><source media="(max-width: 640px)" srcset="<?= h($imgM) ?>"><?php endif; ?>
            <img src="<?= h($img) ?>" alt="" loading="<?= $i === 0 ? 'eager' : 'lazy' ?>">
          </picture>
        <?php else: ?>
          <div class="hero__bg hero__bg--gradient hero__bg--<?= $i % 3 ?>" aria-hidden="true"></div>
        <?php endif; ?>
        <div class="hero__overlay" style="opacity: <?= $overlay / 100 ?>"></div>
        <div class="container hero__inner">
          <div class="hero__content hero__content--<?= h($pos) ?> <?= h($colorClass) ?>">
            <?php if ($s['subtitle']): ?>
              <span class="hero__badge"><?= h($s['subtitle']) ?></span>
            <?php endif; ?>
            <h2 class="hero__title"><?= h($s['title']) ?></h2>
            <?php if (!empty($s['description'])): ?>
              <p class="hero__sub"><?= h($s['description']) ?></p>
            <?php endif; ?>
            <?php if (!empty($s['link_url']) && !empty($s['link_text'])): ?>
              <div class="hero__cta">
                <a class="btn btn--primary btn--lg"
                   href="<?= h($s['link_url']) ?>"
                   <?= $s['link_target'] === '_blank' ? 'target="_blank" rel="noopener"' : '' ?>>
                  <?= h($s['link_text']) ?>
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" style="vertical-align:-3px;margin-left:4px"><path d="M14 5l7 7-7 7M21 12H3"/></svg>
                </a>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <?php if (count($slides) > 1): ?>
  <button class="hero__nav hero__nav--prev" id="heroPrev" aria-label="Onceki slayt">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M15 6l-6 6 6 6"/></svg>
  </button>
  <button class="hero__nav hero__nav--next" id="heroNext" aria-label="Sonraki slayt">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M9 6l6 6-6 6"/></svg>
  </button>

  <div class="hero__dots" id="heroDots" role="tablist">
    <?php foreach ($slides as $i => $s): ?>
      <button class="hero__dot <?= $i === 0 ? 'is-active' : '' ?>"
              data-index="<?= $i ?>"
              role="tab"
              aria-label="Slayt <?= $i + 1 ?>"
              aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"></button>
    <?php endforeach; ?>
  </div>

  <div class="hero__progress" id="heroProgress" aria-hidden="true">
    <div class="hero__progress-bar" id="heroProgressBar"></div>
  </div>
  <?php endif; ?>
</section>
