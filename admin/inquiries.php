<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/inc/bootstrap.php';
$user = ru_admin_require();
$id = (int)($_GET['id'] ?? 0);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!ru_csrf_check()) ru_abort(400);
    ru_exec('UPDATE ' . ru_t('inquiries') . ' SET status=?, updated_at=NOW() WHERE id=?', [ru_admin_input('status'), $id]);
    ru_flash('success', 'Talep güncellendi.');
    ru_redirect('/admin/inquiries.php');
}
$where = '';
$params = [];
if (!empty($_GET['status'])) { $where = ' WHERE status = ?'; $params[] = (string)$_GET['status']; }
$rows = ru_fetch_all('SELECT * FROM ' . ru_t('inquiries') . $where . ' ORDER BY id DESC LIMIT 200', $params);
ob_start();
?>
<section class="admin-panel">
  <div class="admin-panel__head"><h2>Talepler</h2><div class="admin-actions"><a class="btn" href="/admin/inquiries.php">Tümü</a><a class="btn" href="/admin/inquiries.php?status=new">Yeni</a><a class="btn" href="/admin/inquiries.php?status=in_progress">İşlemde</a></div></div>
  <table class="admin-table"><thead><tr><th>Kişi</th><th>Konu</th><th>Mesaj</th><th>Durum</th><th>Tarih</th></tr></thead><tbody>
  <?php foreach ($rows as $row): ?><tr>
    <td><strong><?= h($row['full_name']) ?></strong><br><?= h($row['phone']) ?><br><?= h($row['email']) ?></td>
    <td><?= h($row['subject']) ?><br><span><?= h($row['type']) ?> / <?= h($row['city']) ?></span></td>
    <td><?= h($row['message']) ?></td>
    <td><form method="post" action="/admin/inquiries.php?id=<?= h($row['id']) ?>"><?= ru_csrf_field() ?><select name="status"><option value="new" <?= $row['status']==='new'?'selected':'' ?>>new</option><option value="in_progress" <?= $row['status']==='in_progress'?'selected':'' ?>>in_progress</option><option value="closed" <?= $row['status']==='closed'?'selected':'' ?>>closed</option><option value="spam" <?= $row['status']==='spam'?'selected':'' ?>>spam</option></select><button class="btn" style="margin-top:6px">Kaydet</button></form></td>
    <td><?= h(ru_date($row['created_at'], 'd.m.Y H:i')) ?></td>
  </tr><?php endforeach; ?>
  <?php if (!$rows): ?><tr><td colspan="5">Kayıt yok.</td></tr><?php endif; ?>
  </tbody></table>
</section>
<?php ru_admin_layout('Talepler', ob_get_clean(), $user);
