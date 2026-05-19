<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/inc/bootstrap.php';
require_once RU_BASE . '/inc/updater.php';
$user = ru_admin_require();
if (!ru_admin_can('admin', $user)) ru_abort(403);

$result = null;
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!ru_csrf_check()) ru_abort(400);
    try {
        $updater = new RuSmartUpdater();
        $result = ($_POST['action'] ?? 'check') === 'apply' ? $updater->apply() : $updater->check();
        ru_flash('success', ($_POST['action'] ?? 'check') === 'apply' ? 'Güncelleme uygulandı.' : 'Güncelleme kontrol edildi.');
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

ob_start();
?>
<section class="admin-panel">
  <div class="admin-panel__head"><h2>Smart Update v5</h2><a class="btn" href="/admin/update.php?action=check&token=<?= h((string)(ru_config('update.web_token') ?? '')) ?>" target="_blank">JSON Endpoint</a></div>
  <?php if ($error): ?><div class="admin-alert admin-alert--error"><?= h($error) ?></div><?php endif; ?>
  <p>GitHub Release üzerinden son sürüm kontrol edilir, ZIP indirilir, yedek alınır ve korunan dosyalara dokunmadan güncelleme yapılır.</p>
  <form method="post" class="admin-actions">
    <?= ru_csrf_field() ?>
    <button class="btn" name="action" value="check">Kontrol Et</button>
    <button class="btn btn--primary" name="action" value="apply" onclick="return confirm('Güncelleme uygulanacak. Devam edilsin mi?')">Güncellemeyi Uygula</button>
  </form>
  <?php if ($result): ?>
    <pre style="white-space:pre-wrap;background:#f5f7f3;border:1px solid var(--line);padding:14px;border-radius:6px"><?= h(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
  <?php endif; ?>
</section>
<?php ru_admin_layout('Smart Update', ob_get_clean(), $user);
