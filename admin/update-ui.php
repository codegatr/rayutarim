<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/inc/bootstrap.php';
require_once RU_BASE . '/inc/updater.php';
$user = ru_admin_require();
if (!ru_admin_can('admin', $user)) ru_abort(403);

$result = null;
$error = '';
$action = (string)($_POST['action'] ?? 'check');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!ru_csrf_check()) ru_abort(400);
    try {
        $updater = new RuSmartUpdater();
        $result = $action === 'apply' ? $updater->apply() : $updater->check();
        $message = $action === 'apply' ? 'Güncelleme işlemi tamamlandı.' : 'Güncelleme kontrol edildi.';
        ru_flash('success', $message);
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

$hasResult = is_array($result);
$hasUpdate = $hasResult && !empty($result['has_update']);
$source = (string)($result['source'] ?? ($result['asset']['source'] ?? ''));
$sourceLabel = $source === 'tag' ? 'GitHub tag ZIP' : 'GitHub Release';
$assetName = (string)($result['asset']['name'] ?? 'Paket bekleniyor');
$publishedAt = (string)($result['published_at'] ?? '');
$publishedLabel = $publishedAt !== '' ? ru_date($publishedAt, 'd.m.Y H:i') : 'Tag arşivi';
$stateClass = $hasResult ? ($hasUpdate ? 'update-status--available' : 'update-status--current') : 'update-status--idle';
$stateTitle = !$hasResult ? 'Henüz kontrol yapılmadı' : ($hasUpdate ? 'Yeni güncelleme var' : 'Sistem güncel');
$stateText = !$hasResult
    ? 'Kontrol Et butonuna bastığında GitHub üzerindeki en yüksek sürüm bulunur.'
    : ($hasUpdate
        ? 'Yeni paket bulundu. Güncellemeyi Uygula butonu yedek alıp paketi kurar.'
        : 'Bu kurulum GitHub üzerindeki en güncel sürümle aynı seviyede.');

ob_start();
?>
<section class="admin-panel">
  <div class="admin-panel__head">
    <div>
      <h2>Smart Update v5</h2>
      <p class="admin-muted">GitHub Release ve tag arşivleri birlikte kontrol edilir; en yüksek sürüm esas alınır.</p>
    </div>
    <a class="btn" href="/admin/update.php?action=check&token=<?= h((string)(ru_config('update.web_token') ?? '')) ?>" target="_blank">Teknik JSON</a>
  </div>

  <?php if ($error): ?><div class="admin-alert admin-alert--error"><?= h($error) ?></div><?php endif; ?>

  <div class="update-status <?= h($stateClass) ?>">
    <div>
      <span class="update-status__eyebrow">Güncelleme Durumu</span>
      <strong><?= h($stateTitle) ?></strong>
      <p><?= h($stateText) ?></p>
    </div>
    <?php if ($hasResult): ?>
      <span class="update-status__version"><?= h((string)$result['current_version']) ?> → <?= h((string)$result['latest_version']) ?></span>
    <?php endif; ?>
  </div>

  <?php if ($hasResult): ?>
    <div class="update-summary">
      <div><span>Mevcut Sürüm</span><strong><?= h((string)$result['current_version']) ?></strong></div>
      <div><span>GitHub Son Sürüm</span><strong><?= h((string)$result['latest_version']) ?></strong></div>
      <div><span>Paket Kaynağı</span><strong><?= h($sourceLabel) ?></strong></div>
      <div><span>Paket</span><strong><?= h($assetName) ?></strong></div>
      <div><span>Yayın Bilgisi</span><strong><?= h($publishedLabel) ?></strong></div>
      <div><span>İşlem</span><strong><?= $hasUpdate ? 'Kurulabilir' : 'İşlem gerekmiyor' ?></strong></div>
    </div>
  <?php endif; ?>

  <form method="post" class="admin-actions update-actions">
    <?= ru_csrf_field() ?>
    <button class="btn" name="action" value="check">Kontrol Et</button>
    <button class="btn btn--primary" name="action" value="apply" onclick="return confirm('Önce yedek alınacak, ardından güncelleme uygulanacak. Devam edilsin mi?')">Güncellemeyi Uygula</button>
  </form>

  <div class="admin-note">
    Eski GitHub Release paketi yeni tag sürümlerinden düşükse artık ekranda eski sürüm gösterilmez. Sistem en yüksek semantik sürümü seçer.
  </div>

  <?php if ($hasResult): ?>
    <details class="update-debug">
      <summary>Teknik detayları göster</summary>
      <pre><?= h(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) ?></pre>
    </details>
  <?php endif; ?>
</section>
<?php ru_admin_layout('Smart Update', ob_get_clean(), $user);
