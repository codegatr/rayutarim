<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/inc/bootstrap.php';
$user = ru_admin_require();
$id = (int)($_GET['id'] ?? 0);
$action = (string)($_GET['action'] ?? 'list');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!ru_csrf_check()) ru_abort(400);
    if ($action === 'delete' && $id > 0) {
        ru_exec('DELETE FROM ' . ru_t('product_categories') . ' WHERE id=?', [$id]);
        ru_flash('success', 'Kategori silindi.');
        ru_redirect('/admin/categories.php');
    }
    $title = ru_admin_input('title');
    $slug = ru_admin_input('slug') ?: ru_slugify($title);
    $params = [$slug, $title, ru_admin_input('summary'), ru_admin_input('icon'), (int)($_POST['sort_order'] ?? 0), isset($_POST['is_active']) ? 1 : 0];
    if ($id) {
        $params[] = $id;
        ru_exec('UPDATE ' . ru_t('product_categories') . ' SET slug=?, title=?, summary=?, icon=?, sort_order=?, is_active=?, updated_at=NOW() WHERE id=?', $params);
    } else {
        ru_exec('INSERT INTO ' . ru_t('product_categories') . ' (slug,title,summary,icon,sort_order,is_active) VALUES (?,?,?,?,?,?)', $params);
    }
    ru_flash('success', 'Kategori kaydedildi.');
    ru_redirect('/admin/categories.php');
}
ob_start();
if ($action === 'new' || ($action === 'edit' && $id)):
  $row = $id ? ru_fetch('SELECT * FROM ' . ru_t('product_categories') . ' WHERE id=?', [$id]) : [];
?>
<section class="admin-panel"><div class="admin-panel__head"><h2><?= $id ? 'Kategori Düzenle' : 'Kategori Ekle' ?></h2><a class="btn" href="/admin/categories.php">Listeye Dön</a></div>
<form class="admin-form" method="post"><?= ru_csrf_field() ?><div class="admin-row"><label>Başlık<input name="title" value="<?= h($row['title'] ?? '') ?>" required></label><label>Slug<input name="slug" value="<?= h($row['slug'] ?? '') ?>"></label></div><label>Özet<textarea name="summary"><?= h($row['summary'] ?? '') ?></textarea></label><label>SVG path ikon<input name="icon" value="<?= h($row['icon'] ?? '') ?>"></label><div class="admin-row"><label>Sıra<input type="number" name="sort_order" value="<?= h($row['sort_order'] ?? 0) ?>"></label><label><input type="checkbox" name="is_active" value="1" <?= (int)($row['is_active'] ?? 1) ? 'checked' : '' ?>> Aktif</label></div><button class="btn btn--primary">Kaydet</button></form></section>
<?php else: $rows = ru_fetch_all('SELECT * FROM ' . ru_t('product_categories') . ' ORDER BY sort_order ASC, title ASC'); ?>
<section class="admin-panel"><div class="admin-panel__head"><h2>Kategoriler</h2><a class="btn btn--primary" href="/admin/categories.php?action=new">Yeni Kategori</a></div><table class="admin-table"><thead><tr><th>Başlık</th><th>Slug</th><th>Durum</th><th></th></tr></thead><tbody><?php foreach($rows as $row): ?><tr><td><?= h($row['title']) ?></td><td><?= h($row['slug']) ?></td><td><span class="badge <?= (int)$row['is_active'] ? '' : 'badge--off' ?>"><?= (int)$row['is_active'] ? 'Aktif' : 'Pasif' ?></span></td><td class="admin-actions"><a class="btn" href="/admin/categories.php?action=edit&id=<?= h($row['id']) ?>">Düzenle</a><form method="post" action="/admin/categories.php?action=delete&id=<?= h($row['id']) ?>" onsubmit="return confirm('Silinsin mi?')"><?= ru_csrf_field() ?><button class="btn btn--danger">Sil</button></form></td></tr><?php endforeach; ?></tbody></table></section>
<?php endif; ru_admin_layout('Kategoriler', ob_get_clean(), $user);
