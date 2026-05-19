<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/inc/bootstrap.php';
$user = ru_admin_require();
$keys = ['site_name','site_tagline','site_url','site_phone','site_email','site_whatsapp','site_address','contact_address_full','contact_working_hours','hero_title','hero_subtitle','footer_about','site_facebook','site_instagram','site_youtube','site_linkedin','maintenance_mode'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!ru_csrf_check()) ru_abort(400);
    foreach ($keys as $key) ru_setting_set($key, (string)($_POST[$key] ?? ''));
    ru_audit('update', 'settings');
    ru_flash('success', 'Ayarlar kaydedildi.');
    ru_redirect('/admin/settings.php');
}
ob_start();
?>
<section class="admin-panel"><div class="admin-panel__head"><h2>Ayarlar</h2></div><form class="admin-form" method="post"><?= ru_csrf_field() ?>
<div class="admin-row"><label>Site adı<input name="site_name" value="<?= h(ru_setting('site_name','')) ?>"></label><label>Slogan<input name="site_tagline" value="<?= h(ru_setting('site_tagline','')) ?>"></label></div>
<div class="admin-row"><label>Site URL<input name="site_url" value="<?= h(ru_setting('site_url','')) ?>"></label><label>Telefon<input name="site_phone" value="<?= h(ru_setting('site_phone','')) ?>"></label></div>
<div class="admin-row"><label>E-posta<input name="site_email" value="<?= h(ru_setting('site_email','')) ?>"></label><label>WhatsApp<input name="site_whatsapp" value="<?= h(ru_setting('site_whatsapp','')) ?>"></label></div>
<label>Adres<textarea name="contact_address_full"><?= h(ru_setting('contact_address_full','')) ?></textarea></label>
<label>Çalışma saatleri<input name="contact_working_hours" value="<?= h(ru_setting('contact_working_hours','')) ?>"></label>
<div class="admin-row"><label>Hero başlık<input name="hero_title" value="<?= h(ru_setting('hero_title','')) ?>"></label><label>Hero alt başlık<input name="hero_subtitle" value="<?= h(ru_setting('hero_subtitle','')) ?>"></label></div>
<label>Footer açıklama<textarea name="footer_about"><?= h(ru_setting('footer_about','')) ?></textarea></label>
<div class="admin-row admin-row--3"><label>Facebook<input name="site_facebook" value="<?= h(ru_setting('site_facebook','')) ?>"></label><label>Instagram<input name="site_instagram" value="<?= h(ru_setting('site_instagram','')) ?>"></label><label>YouTube<input name="site_youtube" value="<?= h(ru_setting('site_youtube','')) ?>"></label></div>
<div class="admin-row"><label>LinkedIn<input name="site_linkedin" value="<?= h(ru_setting('site_linkedin','')) ?>"></label><label>Bakım modu<select name="maintenance_mode"><option value="0">Kapalı</option><option value="1" <?= ru_setting('maintenance_mode','0')==='1'?'selected':'' ?>>Açık</option></select></label></div>
<button class="btn btn--primary">Kaydet</button></form></section>
<?php ru_admin_layout('Ayarlar', ob_get_clean(), $user);
