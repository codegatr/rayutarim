<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/inc/bootstrap.php';
$user = ru_admin_require();

$counts = [
    'Ürün' => (int)ru_fetch_scalar('SELECT COUNT(*) FROM ' . ru_t('products')),
    'Kategori' => (int)ru_fetch_scalar('SELECT COUNT(*) FROM ' . ru_t('product_categories')),
    'Talep' => (int)ru_fetch_scalar('SELECT COUNT(*) FROM ' . ru_t('inquiries')),
    'Sayfa' => (int)ru_fetch_scalar('SELECT COUNT(*) FROM ' . ru_t('pages')),
];
$latest = ru_fetch_all('SELECT id, full_name, phone, subject, status, created_at FROM ' . ru_t('inquiries') . ' ORDER BY id DESC LIMIT 8');

ob_start();
?>
<section class="admin-grid">
  <?php foreach ($counts as $label => $count): ?>
    <article class="admin-card"><h3><?= h($label) ?></h3><strong><?= h($count) ?></strong></article>
  <?php endforeach; ?>
</section>
<section class="admin-panel">
  <div class="admin-panel__head">
    <h2>Son Talepler</h2>
    <a class="btn" href="/admin/inquiries.php">Tümünü Gör</a>
  </div>
  <table class="admin-table">
    <thead><tr><th>Ad</th><th>Konu</th><th>Telefon</th><th>Durum</th><th>Tarih</th></tr></thead>
    <tbody>
    <?php foreach ($latest as $row): ?>
      <tr><td><?= h($row['full_name']) ?></td><td><?= h($row['subject']) ?></td><td><?= h($row['phone']) ?></td><td><span class="badge"><?= h($row['status']) ?></span></td><td><?= h(ru_date($row['created_at'], 'd.m.Y H:i')) ?></td></tr>
    <?php endforeach; ?>
    <?php if (!$latest): ?><tr><td colspan="5">Henüz talep yok.</td></tr><?php endif; ?>
    </tbody>
  </table>
</section>
<section class="admin-panel">
  <div class="admin-panel__head"><h2>Hızlı İşlemler</h2></div>
  <div class="admin-actions">
    <a class="btn btn--primary" href="/admin/products.php?action=new">Ürün Ekle</a>
    <a class="btn" href="/admin/pages.php?action=new">Sayfa Ekle</a>
    <a class="btn" href="/admin/sliders.php?action=new">Slider Ekle</a>
    <a class="btn" href="/admin/update-ui.php">Güncelleme Kontrolü</a>
  </div>
</section>
<?php
ru_admin_layout('Dashboard', ob_get_clean(), $user);
