<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/inc/bootstrap.php';
$user = ru_admin_require();
if (!ru_admin_can('write', $user) && $_SERVER['REQUEST_METHOD'] === 'POST') ru_abort(403);

$action = (string)($_GET['action'] ?? 'list');
$id = (int)($_GET['id'] ?? 0);

if ($action === 'delete' && $id > 0) {
    if (!ru_csrf_check()) ru_abort(400);
    ru_exec('DELETE FROM ' . ru_t('products') . ' WHERE id = ?', [$id]);
    ru_audit('delete', 'product', $id);
    ru_flash('success', 'Ürün silindi.');
    ru_redirect('/admin/products.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!ru_csrf_check()) ru_abort(400);
    try {
        $image = ru_admin_upload('image_file', 'products') ?: ru_admin_input('image');
    } catch (Throwable $e) {
        ru_flash('error', $e->getMessage());
        ru_redirect($id > 0 ? '/admin/products.php?action=edit&id=' . $id : '/admin/products.php?action=new');
    }
    $name = ru_admin_input('name');
    $slug = ru_admin_input('slug') ?: ru_slugify($name);
    $specs = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string)($_POST['specs'] ?? '')) ?: [])));
    $params = [
        (int)($_POST['category_id'] ?? 0) ?: null,
        $slug,
        $name,
        ru_admin_input('badge'),
        ru_admin_input('summary'),
        ru_admin_input('description'),
        json_encode($specs, JSON_UNESCAPED_UNICODE),
        $image,
        (int)($_POST['sort_order'] ?? 0),
        isset($_POST['is_featured']) ? 1 : 0,
        isset($_POST['is_active']) ? 1 : 0,
    ];
    if ($id > 0) {
        $params[] = $id;
        ru_exec('UPDATE ' . ru_t('products') . ' SET category_id=?, slug=?, name=?, badge=?, summary=?, description=?, specs_json=?, image=?, sort_order=?, is_featured=?, is_active=?, updated_at=NOW() WHERE id=?', $params);
        ru_audit('update', 'product', $id);
    } else {
        ru_exec('INSERT INTO ' . ru_t('products') . ' (category_id, slug, name, badge, summary, description, specs_json, image, sort_order, is_featured, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', $params);
        ru_audit('create', 'product', ru_last_id());
    }
    ru_flash('success', 'Ürün kaydedildi.');
    ru_redirect('/admin/products.php');
}

$categories = ru_fetch_all('SELECT id, title FROM ' . ru_t('product_categories') . ' ORDER BY sort_order ASC, title ASC');

ob_start();
if ($action === 'new' || ($action === 'edit' && $id > 0)):
    $row = $id > 0 ? ru_fetch('SELECT * FROM ' . ru_t('products') . ' WHERE id = ?', [$id]) : [];
    $specs = json_decode((string)($row['specs_json'] ?? '[]'), true);
?>
<section class="admin-panel">
  <div class="admin-panel__head"><h2><?= $id ? 'Ürün Düzenle' : 'Ürün Ekle' ?></h2><a class="btn" href="/admin/products.php">Listeye Dön</a></div>
  <form class="admin-form" method="post" enctype="multipart/form-data">
    <?= ru_csrf_field() ?>
    <div class="admin-row">
      <label>Ürün Adı<input name="name" value="<?= h($row['name'] ?? '') ?>" required></label>
      <label>Slug<input name="slug" value="<?= h($row['slug'] ?? '') ?>" placeholder="otomatik"></label>
    </div>
    <div class="admin-row admin-row--3">
      <label>Kategori<select name="category_id"><?php foreach ($categories as $cat): ?><option value="<?= h($cat['id']) ?>" <?= (int)($row['category_id'] ?? 0) === (int)$cat['id'] ? 'selected' : '' ?>><?= h($cat['title']) ?></option><?php endforeach; ?></select></label>
      <label>Rozet<input name="badge" value="<?= h($row['badge'] ?? '') ?>"></label>
      <label>Sıra<input type="number" name="sort_order" value="<?= h($row['sort_order'] ?? 0) ?>"></label>
    </div>
    <label>Kısa Açıklama<textarea name="summary"><?= h($row['summary'] ?? '') ?></textarea></label>
    <label>Detay<textarea name="description"><?= h($row['description'] ?? '') ?></textarea></label>
    <label>Teknik Özellikler<textarea name="specs" placeholder="Her satıra bir özellik"><?= h(implode("\n", is_array($specs) ? $specs : [])) ?></textarea></label>
    <div class="admin-row">
      <label>Görsel yolu<input name="image" value="<?= h($row['image'] ?? '') ?>" placeholder="uploads/products/ornek.webp"></label>
      <label>Görsel yükle<input type="file" name="image_file" accept="image/*"></label>
    </div>
    <div class="admin-actions">
      <label><input type="checkbox" name="is_featured" value="1" <?= (int)($row['is_featured'] ?? 0) ? 'checked' : '' ?>> Öne çıkar</label>
      <label><input type="checkbox" name="is_active" value="1" <?= (int)($row['is_active'] ?? 1) ? 'checked' : '' ?>> Aktif</label>
    </div>
    <button class="btn btn--primary" type="submit">Kaydet</button>
  </form>
</section>
<?php else:
    $rows = ru_fetch_all('SELECT p.*, c.title category_title FROM ' . ru_t('products') . ' p LEFT JOIN ' . ru_t('product_categories') . ' c ON c.id = p.category_id ORDER BY p.sort_order ASC, p.id DESC');
?>
<section class="admin-panel">
  <div class="admin-panel__head"><h2>Ürünler</h2><a class="btn btn--primary" href="/admin/products.php?action=new">Yeni Ürün</a></div>
  <table class="admin-table"><thead><tr><th>Ürün</th><th>Kategori</th><th>Durum</th><th>Sıra</th><th></th></tr></thead><tbody>
  <?php foreach ($rows as $row): ?><tr>
    <td><strong><?= h($row['name']) ?></strong><br><span><?= h($row['slug']) ?></span></td>
    <td><?= h($row['category_title']) ?></td>
    <td><span class="badge <?= (int)$row['is_active'] ? '' : 'badge--off' ?>"><?= (int)$row['is_active'] ? 'Aktif' : 'Pasif' ?></span></td>
    <td><?= h($row['sort_order']) ?></td>
    <td class="admin-actions"><a class="btn" href="/admin/products.php?action=edit&id=<?= h($row['id']) ?>">Düzenle</a><form method="post" action="/admin/products.php?action=delete&id=<?= h($row['id']) ?>" onsubmit="return confirm('Silinsin mi?')"><?= ru_csrf_field() ?><button class="btn btn--danger">Sil</button></form></td>
  </tr><?php endforeach; ?>
  </tbody></table>
</section>
<?php endif; ru_admin_layout('Ürünler', ob_get_clean(), $user);
