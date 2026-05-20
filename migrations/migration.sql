-- ============================================================================
-- RAY-U TARIM — Migration v0.1.0
-- ============================================================================
-- Bu dosya idempotenttir: birden fazla kez calistirilabilir.
-- Tum CREATE/ALTER ifadeleri INFORMATION_SCHEMA kontrolu ile korunmustur.
--
-- Tablo prefixi: ru_
-- Charset: utf8mb4 / utf8mb4_unicode_ci
--
-- Calistirma:
-- 1) Otomatik: install.php (kurulum sirasinda)
-- 2) Manuel: migration runner (Faz 6 — Smart Update v5)
-- ============================================================================

-- ============================================================================
-- TABLO: ru_settings — Anahtar-deger ayar deposu
-- ============================================================================
CREATE TABLE IF NOT EXISTS `ru_settings` (
  `skey` VARCHAR(100) NOT NULL,
  `sval` LONGTEXT NULL,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`skey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLO: ru_users — Yonetici hesaplari (Faz 4'te dolacak)
-- ============================================================================
CREATE TABLE IF NOT EXISTS `ru_users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(190) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(120) NOT NULL DEFAULT '',
  `phone` VARCHAR(40) NOT NULL DEFAULT '',
  `role` ENUM('superadmin','admin','editor','viewer') NOT NULL DEFAULT 'admin',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `last_login_at` DATETIME NULL,
  `last_login_ip` VARCHAR(45) NULL,
  `failed_attempts` INT UNSIGNED NOT NULL DEFAULT 0,
  `locked_until` DATETIME NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_email` (`email`),
  KEY `idx_role` (`role`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLO: ru_audit_log — Yonetim islemleri kaydi
-- ============================================================================
CREATE TABLE IF NOT EXISTS `ru_audit_log` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NULL,
  `action` VARCHAR(80) NOT NULL,
  `entity` VARCHAR(80) NOT NULL DEFAULT '',
  `entity_id` INT UNSIGNED NULL,
  `ip` VARCHAR(45) NOT NULL DEFAULT '',
  `user_agent` VARCHAR(255) NOT NULL DEFAULT '',
  `meta_json` LONGTEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_entity` (`entity`, `entity_id`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLO: ru_login_attempts — Brute-force korumasi
-- ============================================================================
CREATE TABLE IF NOT EXISTS `ru_login_attempts` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(190) NOT NULL,
  `ip` VARCHAR(45) NOT NULL,
  `success` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_email_time` (`email`, `created_at`),
  KEY `idx_ip_time` (`ip`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLO: ru_migrations — Yapilan migration takibi (Faz 6'da kullanilacak)
-- ============================================================================
CREATE TABLE IF NOT EXISTS `ru_migrations` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `version` VARCHAR(20) NOT NULL,
  `filename` VARCHAR(190) NOT NULL,
  `applied_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `success` TINYINT(1) NOT NULL DEFAULT 1,
  `notes` TEXT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_filename` (`filename`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLO: ru_languages — Faz 7 icin altyapi (su an sadece tr aktif)
-- ============================================================================
CREATE TABLE IF NOT EXISTS `ru_languages` (
  `id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(5) NOT NULL,
  `name` VARCHAR(40) NOT NULL,
  `native_name` VARCHAR(40) NOT NULL,
  `is_rtl` TINYINT(1) NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `is_default` TINYINT(1) NOT NULL DEFAULT 0,
  `sort_order` INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- ILK VERILER (idempotent: INSERT IGNORE)
-- ============================================================================

-- Default dil: Turkce
INSERT IGNORE INTO `ru_languages` (`code`, `name`, `native_name`, `is_rtl`, `is_active`, `is_default`, `sort_order`)
VALUES ('tr', 'Turkish', 'Turkce', 0, 1, 1, 1);

-- Faz 7'de aktif edilecek diller
INSERT IGNORE INTO `ru_languages` (`code`, `name`, `native_name`, `is_rtl`, `is_active`, `is_default`, `sort_order`)
VALUES
  ('en', 'English', 'English', 0, 0, 0, 2),
  ('ar', 'Arabic',  'العربية', 1, 0, 0, 3),
  ('ru', 'Russian', 'Русский', 0, 0, 0, 4);

-- Cekirdek site ayarlari (varsayilan degerler)
INSERT IGNORE INTO `ru_settings` (`skey`, `sval`) VALUES
  ('site_name',         'RAYU Tarim Makineleri'),
  ('site_tagline',      'Toprak Islemede Uzman'),
  ('site_url',          'https://rayutarim.com'),
  ('site_phone',        '+90 332 000 00 00'),
  ('site_email',        'info@rayutarim.com'),
  ('site_address',      'Konya, Turkiye'),
  ('site_facebook',     ''),
  ('site_instagram',    ''),
  ('site_youtube',      ''),
  ('site_linkedin',     ''),
  ('site_whatsapp',     ''),
  ('site_logo',         ''),
  ('site_favicon',      ''),
  ('about_short',       'Toprak isleme makinelerinde uzmanlasmis, ureticinin yaninda kurumsal yapi.'),
  ('hero_title',        'Toprak Islemede Uzman'),
  ('hero_subtitle',     'Tarim makineleri ve ziraai ilac coziimlerinde guvenilir adresiniz.'),
  ('footer_about',      'RAYU Tarim Makineleri, toprak isleme alaninda uzmanlasmis ekipman ve coziimleriyle Anadolu topraklarinin yaninda.'),
  ('maintenance_mode',  '0'),
  ('founded_year',      '2010'),
  ('show_used_section', '1');

-- ============================================================================
-- FAZ 2 TABLOLARI (v0.2.0)
-- ============================================================================

-- ============================================================================
-- TABLO: ru_pages — Statik sayfalar (Hakkimizda, Misyon, Iletisim, vs.)
-- ============================================================================
CREATE TABLE IF NOT EXISTS `ru_pages` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id` INT UNSIGNED NULL,
  `slug` VARCHAR(160) NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `subtitle` VARCHAR(300) NOT NULL DEFAULT '',
  `excerpt` TEXT NULL,
  `content` LONGTEXT NULL,
  `meta_description` VARCHAR(300) NOT NULL DEFAULT '',
  `meta_keywords` VARCHAR(300) NOT NULL DEFAULT '',
  `template` VARCHAR(40) NOT NULL DEFAULT 'default',
  `hero_image` VARCHAR(255) NOT NULL DEFAULT '',
  `og_image` VARCHAR(255) NOT NULL DEFAULT '',
  `icon` VARCHAR(40) NOT NULL DEFAULT '',
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `show_in_header` TINYINT(1) NOT NULL DEFAULT 0,
  `show_in_footer` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_slug` (`slug`),
  KEY `idx_parent` (`parent_id`),
  KEY `idx_active_sort` (`is_active`, `sort_order`),
  KEY `idx_header` (`show_in_header`, `sort_order`),
  KEY `idx_footer` (`show_in_footer`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLO: ru_slider — Anasayfa slider'lari
-- ============================================================================
CREATE TABLE IF NOT EXISTS `ru_slider` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(200) NOT NULL DEFAULT '',
  `subtitle` VARCHAR(300) NOT NULL DEFAULT '',
  `description` TEXT NULL,
  `image` VARCHAR(255) NOT NULL DEFAULT '',
  `image_mobile` VARCHAR(255) NOT NULL DEFAULT '',
  `link_url` VARCHAR(500) NOT NULL DEFAULT '',
  `link_text` VARCHAR(80) NOT NULL DEFAULT '',
  `link_target` ENUM('_self','_blank') NOT NULL DEFAULT '_self',
  `text_position` ENUM('left','center','right') NOT NULL DEFAULT 'left',
  `text_color` VARCHAR(20) NOT NULL DEFAULT 'light',
  `overlay_opacity` TINYINT UNSIGNED NOT NULL DEFAULT 40,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `start_at` DATETIME NULL,
  `end_at` DATETIME NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_active_sort` (`is_active`, `sort_order`),
  KEY `idx_schedule` (`start_at`, `end_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLO: ru_menu — Navigasyon menu ogeleri (header / footer kolonlari)
-- ============================================================================
CREATE TABLE IF NOT EXISTS `ru_menu` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id` INT UNSIGNED NULL,
  `location` ENUM('header','footer_1','footer_2','footer_3','mobile') NOT NULL DEFAULT 'header',
  `label` VARCHAR(120) NOT NULL,
  `url` VARCHAR(500) NOT NULL DEFAULT '#',
  `target` ENUM('_self','_blank') NOT NULL DEFAULT '_self',
  `icon` VARCHAR(40) NOT NULL DEFAULT '',
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `css_class` VARCHAR(80) NOT NULL DEFAULT '',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_location_sort` (`location`, `sort_order`),
  KEY `idx_parent` (`parent_id`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- FAZ 2 — Seed verileri (idempotent)
-- ============================================================================

-- Yeni settings (Faz 2)
INSERT IGNORE INTO `ru_settings` (`skey`, `sval`) VALUES
  ('hero_cta_primary_text',  'Urunlerimizi Inceleyin'),
  ('hero_cta_primary_url',   '/urunler'),
  ('hero_cta_secondary_text','Bize Ulasin'),
  ('hero_cta_secondary_url', '/iletisim'),
  ('feature_1_icon',         'M3 21V8l9-7 9 7v13h-6v-7h-6v7H3z'),
  ('feature_1_title',        'Yerli Uretim'),
  ('feature_1_text',         'Anadolu mhendisligi, dunya standartlarinda kalite'),
  ('feature_2_icon',         'M12 2L1 21h22L12 2zm0 4l8 14H4l8-14z'),
  ('feature_2_title',        '7/24 Servis'),
  ('feature_2_text',         'Yetkin teknik kadromuz her zaman yaninizda'),
  ('feature_3_icon',         'M21 13.255V21h-9.5v-7.745L3 21l9-9 9 9z'),
  ('feature_3_title',        'Genis Bayi Agi'),
  ('feature_3_text',         '50+ il, 200+ bayi ile Turkiye genelinde'),
  ('feature_4_icon',         'M20 12a8 8 0 11-16 0 8 8 0 0116 0z'),
  ('feature_4_title',        'Garantili Hizmet'),
  ('feature_4_text',         '2 yil garanti + uzun donem yedek parca destegi'),
  ('contact_address_full',   'Konya Organize Sanayi Bolgesi, Konya / Turkiye'),
  ('contact_lat',            '37.8746'),
  ('contact_lng',            '32.4932'),
  ('contact_working_hours',  'Pzt - Cmt: 08:30 - 18:30');

-- Default sayfalar
INSERT IGNORE INTO `ru_pages`
  (`slug`, `title`, `subtitle`, `excerpt`, `content`, `template`, `sort_order`, `is_active`, `show_in_header`, `show_in_footer`)
VALUES
  ('hakkimizda', 'Hakkimizda', 'Topraktan gelen guc',
   'RAYU Tarim Makineleri, ureticinin yaninda olan teknoloji ortagidir.',
   '<p>RAYU Tarim Makineleri, Konya merkezli kurumsal yapisi ile tarim makineleri ve ziraai ilac sektorunde Turkiye genelinde hizmet veren oncu firmalardan biridir. Yillarin verdigi tecrube ile cifticilerimize en kaliteli urun ve hizmeti sunmayi misyon edinmistir.</p><p>Modern uretim tesislerimizde gelistirilen tarim makineleri, dunya kalite standartlarinda uretilmekte; teknik servis ve yedek parca agimiz ile ureticilerimize kesintisiz destek saglanmaktadir.</p>',
   'default', 1, 1, 1, 1),

  ('misyon-vizyon', 'Misyon & Vizyon', 'Yarinin tarimi icin bugun',
   NULL,
   '<h2>Misyonumuz</h2><p>Ureticinin verimliligini artiran, kullaniciya deger katan, cevre dostu tarim coziimleri sunmak.</p><h2>Vizyonumuz</h2><p>Tarim teknolojisinde sadece Turkiye degil, bolgesinde de ilk akla gelen kurumsal marka olmak.</p>',
   'default', 2, 1, 1, 0),

  ('tarihce', 'Tarihce', 'Kilometre taslari',
   NULL,
   '<p>RAYU Tarim Makineleri, kurulusundan bu yana sektorde emin adimlarla ilerlemis, her gecen yil portfoyunu ve servis agini genisletmistir.</p>',
   'default', 3, 1, 1, 0),

  ('kalite-politikasi', 'Kalite Politikasi', 'Standardin otesi',
   NULL,
   '<p>Tum urunlerimiz ISO 9001 kalite yonetim sistemi cercevesinde uretilmekte, CE belgesi ile uluslararasi standartlara uygunluk teyit edilmektedir.</p>',
   'default', 4, 1, 0, 1),

  ('insan-kaynaklari', 'Insan Kaynaklari', 'Bizimle calismak isteyenler icin',
   NULL,
   '<p>Tutkulu, yenilikci ve takim ruhuna sahip arkadaslarimizi aramizda gormekten mutlu oluruz. CV gonderiminiz icin: <strong>ik@rayutarim.com</strong></p>',
   'default', 5, 1, 0, 1),

  ('iletisim', 'Iletisim', 'Bize ulasin', NULL, NULL,
   'contact', 99, 1, 1, 1);

-- Default menuler — header
INSERT IGNORE INTO `ru_menu` (`location`, `label`, `url`, `sort_order`, `is_active`) VALUES
  ('header', 'Anasayfa',   '/',             1, 1),
  ('header', 'Kurumsal',   '/hakkimizda',   2, 1),
  ('header', 'Urunler',    '/urunler',      3, 1),
  ('header', '2. El',      '/ikinci-el',    4, 1),
  ('header', 'Iletisim',   '/iletisim',     5, 1);

-- Default menuler — footer kolon 1
INSERT IGNORE INTO `ru_menu` (`location`, `label`, `url`, `sort_order`, `is_active`) VALUES
  ('footer_1', 'Hakkimizda',         '/hakkimizda',         1, 1),
  ('footer_1', 'Misyon & Vizyon',    '/misyon-vizyon',      2, 1),
  ('footer_1', 'Tarihce',            '/tarihce',            3, 1),
  ('footer_1', 'Insan Kaynaklari',   '/insan-kaynaklari',   4, 1);

-- Default menuler — footer kolon 2
INSERT IGNORE INTO `ru_menu` (`location`, `label`, `url`, `sort_order`, `is_active`) VALUES
  ('footer_2', 'Tum Urunler',        '/urunler',            1, 1),
  ('footer_2', 'Tarim Aletleri',     '/urunler/tarim-aletleri', 2, 1),
  ('footer_2', 'Ziraai Ilaclar',     '/urunler/ziraai-ilaclar', 3, 1),
  ('footer_2', '2. El',              '/ikinci-el',          4, 1);

-- Default menuler — footer kolon 3
INSERT IGNORE INTO `ru_menu` (`location`, `label`, `url`, `sort_order`, `is_active`) VALUES
  ('footer_3', 'Iletisim',           '/iletisim',           1, 1),
  ('footer_3', 'Kalite Politikasi',  '/kalite-politikasi',  2, 1),
  ('footer_3', 'Gizlilik',           '/gizlilik',           3, 1),
  ('footer_3', 'KVKK',               '/kvkk',               4, 1);

-- Demo slider'lar (admin panelden silinebilir/duzenlenebilir)
INSERT IGNORE INTO `ru_slider`
  (`id`, `title`, `subtitle`, `description`, `image`, `link_url`, `link_text`, `text_position`, `sort_order`, `is_active`)
VALUES
  (1, 'Topragin Gucunu Teknolojiyle Bulusturuyoruz',
      'Yeni nesil tarim makineleri',
      'Hububat ekim makinelerinden topraj isleme ekipmanlarina, urun yelpazemiz ile her ureticinin yaninda.',
      '', '/urunler', 'Urunleri Kesfet', 'left', 1, 1),
  (2, 'Ziraai Ilac Kataloglari',
      'Bitki saglik koruma uzmanligi',
      'Lisansli ziraai ilac portfoyumuz, bilingli tarim uygulamalari icin guvenilir secimler sunar.',
      '', '/urunler/ziraai-ilaclar', 'Katalogu Goruntule', 'center', 2, 1),
  (3, '2. El Pazari',
      'Guvenli, garantili, ekspertiz onayli',
      'Ikinci el tarim makinelerini RAYU guvencesiyle alip satin. Tum ilanlar uzman ekibimizce kontrol edilir.',
      '', '/ikinci-el', '2. El Pazarina Gir', 'right', 3, 1);

-- ============================================================================
-- v0.2.1 — Marka Kimligi Migrasyonu
-- Sadece eski default degerlere uyanlari guncelle (kullanici degistirmisse dokunma)
-- ============================================================================
UPDATE `ru_settings` SET `sval` = 'RAYU Tarim Makineleri'
  WHERE `skey` = 'site_name' AND `sval` = 'Ray-U Tarim';

UPDATE `ru_settings` SET `sval` = 'Toprak Islemede Uzman'
  WHERE `skey` = 'site_tagline' AND `sval` = 'Tarim Makineleri ve Ziraai Ilaclar';

UPDATE `ru_settings` SET `sval` = 'Toprak Islemede Uzman'
  WHERE `skey` = 'hero_title' AND `sval` = 'Topragin Gucunu Teknolojiyle Bulusturuyoruz';

UPDATE `ru_settings` SET `sval` = 'Toprak isleme makinelerinde uzmanlasmis, ureticinin yaninda kurumsal yapi.'
  WHERE `skey` = 'about_short' AND `sval` = 'Tarim teknolojisinin kalbinde, ureticinin yaninda.';

UPDATE `ru_settings` SET `sval` = 'RAYU Tarim Makineleri, toprak isleme alaninda uzmanlasmis ekipman ve coziimleriyle Anadolu topraklarinin yaninda.'
  WHERE `skey` = 'footer_about' AND `sval` = 'Ray-U Tarim, ureticinin verimliligini artiran ekipman ve coziimleriyle Anadolu topraklarinin yaninda.';

-- site_url ayari eklenmediyse ekle (Faz 1'de yoktu)
INSERT IGNORE INTO `ru_settings` (`skey`, `sval`) VALUES ('site_url', 'https://rayutarim.com');

-- ru_pages icerigindeki marka referanslarini guncelle (idempotent: REPLACE)
UPDATE `ru_pages` SET
  `content` = REPLACE(`content`, 'Ray-U Tarim', 'RAYU Tarim Makineleri'),
  `excerpt` = REPLACE(IFNULL(`excerpt`, ''), 'Ray-U Tarim', 'RAYU Tarim Makineleri')
WHERE `content` LIKE '%Ray-U Tarim%' OR `excerpt` LIKE '%Ray-U Tarim%';

-- ru_slider'daki marka referanslarini guncelle
UPDATE `ru_slider` SET
  `description` = REPLACE(IFNULL(`description`, ''), 'Ray-U guvencesiyle', 'RAYU guvencesiyle')
WHERE `description` LIKE '%Ray-U guvencesiyle%';

-- ============================================================================
-- v0.6.0 - Kurumsal katalog ve Smart Update v5
-- ============================================================================
UPDATE `ru_settings` SET `sval` = 'RAYU Tarım Makineleri'
  WHERE `skey` = 'site_name';

UPDATE `ru_settings` SET `sval` = 'Toprak İşlemede Uzman'
  WHERE `skey` = 'site_tagline';

UPDATE `ru_settings` SET `sval` = 'Toprağın gücünü teknolojiyle buluşturuyoruz'
  WHERE `skey` = 'hero_title';

UPDATE `ru_settings` SET `sval` = 'Tarım makineleri, zirai ilaçlama ve servis çözümlerinde Türkiye geneli kurumsal satış platformu.'
  WHERE `skey` = 'hero_subtitle';

UPDATE `ru_settings` SET `sval` = 'RAYU Tarım Makineleri; toprak işleme, ekim, ilaçlama, yedek parça ve servis süreçlerini tek kurumsal çatı altında yöneten satış ve destek platformudur.'
  WHERE `skey` = 'footer_about';

UPDATE `ru_settings` SET `sval` = 'Yerli mühendislik'
  WHERE `skey` = 'feature_1_title';
UPDATE `ru_settings` SET `sval` = 'Anadolu saha tecrübesi, dünya kalite standardı'
  WHERE `skey` = 'feature_1_text';
UPDATE `ru_settings` SET `sval` = 'Satış sonrası servis'
  WHERE `skey` = 'feature_2_title';
UPDATE `ru_settings` SET `sval` = 'Bakım, yedek parça ve sezon desteği tek ekipte'
  WHERE `skey` = 'feature_2_text';
UPDATE `ru_settings` SET `sval` = 'Bayi ve filo satışı'
  WHERE `skey` = 'feature_3_title';
UPDATE `ru_settings` SET `sval` = 'Türkiye genelinde kurumsal teklif ve teslimat akışı'
  WHERE `skey` = 'feature_3_text';
UPDATE `ru_settings` SET `sval` = 'Garanti ve kayıt'
  WHERE `skey` = 'feature_4_title';
UPDATE `ru_settings` SET `sval` = 'Her teslimatta ürün, servis ve bakım kaydı'
  WHERE `skey` = 'feature_4_text';

UPDATE `ru_menu` SET `label` = 'Ürünler' WHERE `location` = 'header' AND `url` = '/urunler';
UPDATE `ru_menu` SET `label` = 'İletişim' WHERE `location` = 'header' AND `url` = '/iletisim';
UPDATE `ru_menu` SET `label` = 'Hakkımızda' WHERE `url` = '/hakkimizda';
UPDATE `ru_menu` SET `label` = 'Tüm Ürünler' WHERE `url` = '/urunler';
UPDATE `ru_menu` SET `label` = 'Toprak İşleme' WHERE `url` = '/urunler/tarim-aletleri';
UPDATE `ru_menu` SET `url` = '/urunler/toprak-isleme' WHERE `url` = '/urunler/tarim-aletleri';
UPDATE `ru_menu` SET `label` = 'Zirai İlaçlama' WHERE `url` = '/urunler/ziraai-ilaclar';
UPDATE `ru_menu` SET `url` = '/urunler/ilaclama' WHERE `url` = '/urunler/ziraai-ilaclar';

UPDATE `ru_pages` SET
  `title` = 'Hakkımızda',
  `subtitle` = 'Topraktan gelen güç, kurumsal satış disiplini',
  `excerpt` = 'RAYU Tarım Makineleri, üreticinin yanında olan teknoloji ve servis ortağıdır.',
  `content` = '<p>RAYU Tarım Makineleri, Konya merkezli yapısıyla tarım makineleri, zirai ilaçlama çözümleri, yedek parça ve saha servis süreçlerini tek kurumsal çatı altında sunar.</p><p>Satış ekibimiz ürün seçimini toprak yapısı, traktör gücü, ürün deseni ve sezon takvimine göre planlar. Teslimat sonrasında bakım, yedek parça ve operatör desteğiyle üreticinin yanında kalır.</p><h2>Kurumsal çalışma modelimiz</h2><p>Bayi yönetimi, filo satışı, teknik teklif, teslimat ve servis kayıtları ölçülebilir süreçlerle ilerler. Amacımız yalnızca makine satmak değil, sezon verimliliğini artıran sürdürülebilir bir iş ortaklığı kurmaktır.</p>'
  WHERE `slug` = 'hakkimizda';

UPDATE `ru_pages` SET
  `content` = '<h2>Misyonumuz</h2><p>Üreticinin verimliliğini artıran, kullanıcıya değer katan, çevreye duyarlı tarım makineleri ve bitki sağlığı çözümleri sunmak.</p><h2>Vizyonumuz</h2><p>Türkiye’de güvenilir satış ve servis standardını yükselten, bölgesel pazarlarda tercih edilen kurumsal tarım teknolojileri markası olmak.</p>'
  WHERE `slug` = 'misyon-vizyon';

UPDATE `ru_slider` SET
  `title` = 'Toprağın Gücünü Teknolojiyle Buluşturuyoruz',
  `subtitle` = 'Kurumsal tarım makineleri satışı',
  `description` = 'Toprak işleme, ekim, ilaçlama ve yedek parça çözümlerini teknik teklif ve servis desteğiyle sunuyoruz.',
  `link_text` = 'Ürünleri İncele'
  WHERE `id` = 1;

UPDATE `ru_slider` SET
  `title` = 'Zirai İlaçlama ve Bitki Sağlığı',
  `subtitle` = 'Doğru ürün, doğru uygulama',
  `description` = 'Lisanslı portföy ve saha danışmanlığıyla bitki sağlığı süreçlerinizi planlayın.',
  `link_url` = '/urunler/ilaclama',
  `link_text` = 'Kataloğu Gör'
  WHERE `id` = 2;

CREATE TABLE IF NOT EXISTS `ru_product_categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(160) NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `summary` TEXT NULL,
  `icon` TEXT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_slug` (`slug`),
  KEY `idx_active_sort` (`is_active`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `ru_products` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` INT UNSIGNED NULL,
  `slug` VARCHAR(180) NOT NULL,
  `name` VARCHAR(220) NOT NULL,
  `badge` VARCHAR(80) NOT NULL DEFAULT '',
  `summary` TEXT NULL,
  `description` LONGTEXT NULL,
  `specs_json` LONGTEXT NULL,
  `price` DECIMAL(12,2) NULL,
  `currency` VARCHAR(3) NOT NULL DEFAULT 'TRY',
  `image` VARCHAR(255) NOT NULL DEFAULT '',
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_slug` (`slug`),
  KEY `idx_category` (`category_id`),
  KEY `idx_active_sort` (`is_active`, `sort_order`),
  KEY `idx_featured` (`is_featured`, `is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `ru_inquiries` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` ENUM('contact','quote','dealer','service','used') NOT NULL DEFAULT 'contact',
  `full_name` VARCHAR(160) NOT NULL,
  `email` VARCHAR(190) NOT NULL DEFAULT '',
  `phone` VARCHAR(50) NOT NULL DEFAULT '',
  `company` VARCHAR(160) NOT NULL DEFAULT '',
  `city` VARCHAR(120) NOT NULL DEFAULT '',
  `subject` VARCHAR(180) NOT NULL DEFAULT '',
  `message` TEXT NULL,
  `status` ENUM('new','in_progress','closed','spam') NOT NULL DEFAULT 'new',
  `source_url` VARCHAR(500) NOT NULL DEFAULT '',
  `ip` VARCHAR(45) NOT NULL DEFAULT '',
  `user_agent` VARCHAR(255) NOT NULL DEFAULT '',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`, `created_at`),
  KEY `idx_type` (`type`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `ru_product_categories` (`slug`, `title`, `summary`, `icon`, `sort_order`, `is_active`) VALUES
  ('toprak-isleme', 'Toprak İşleme', 'Pulluk, kültivatör, çizel, merdane ve tarla hazırlık ekipmanları.', 'M4 18c5-8 10-11 16-12-2 7-1 12 3 16 4-4 1-9 2-16 8Z', 1, 1),
  ('ekim-dikim', 'Ekim ve Dikim', 'Hassas ekim makineleri, mibzerler ve sezon verimini artıran çözümler.', 'M12 3c4 4 6 8 6 12a6 6 0 0 1-12 0c0-4 2-8 6-12Z', 2, 1),
  ('ilaclama', 'Zirai İlaçlama', 'Lisanslı ürün portföyü, atomizörler ve doğru uygulama ekipmanları.', 'M7 3h10v4l-2 3v8a3 3 0 0 1-6 0v-8L7 7V3Z', 3, 1),
  ('yedek-parca', 'Yedek Parça ve Servis', 'Sezon içinde hızlı tedarik, teknik servis ve bakım planlama.', 'M19 13a7 7 0 1 1-8-8l2 2-2 4 4-2 2 2a7 7 0 0 1 2 4Z', 4, 1);

INSERT IGNORE INTO `ru_products` (`category_id`, `slug`, `name`, `badge`, `summary`, `description`, `specs_json`, `sort_order`, `is_featured`, `is_active`)
SELECT c.id, 'agir-tip-cizel', 'Ağır Tip Çizel', 'Yoğun toprak', 'Derin patlatma, düşük yakıt tüketimi ve güçlendirilmiş şase.', 'Derin toprak işlemede yüksek dayanım ve düşük bakım maliyeti için tasarlanmıştır.', '["9-13 ayak","70-120 HP","Opsiyonel merdane"]', 1, 1, 1 FROM `ru_product_categories` c WHERE c.slug = 'toprak-isleme';
INSERT IGNORE INTO `ru_products` (`category_id`, `slug`, `name`, `badge`, `summary`, `description`, `specs_json`, `sort_order`, `is_featured`, `is_active`)
SELECT c.id, 'diskli-goble', 'Diskli Goble', 'Saha hazırlığı', 'Anız parçalama ve homojen karıştırma için dengeli disk geometrisi.', 'Tarla hazırlığında homojen karıştırma ve güçlü gövde yapısı sunar.', '["20-32 disk","Hidrolik ayar","Ağır hizmet rulman"]', 2, 1, 1 FROM `ru_product_categories` c WHERE c.slug = 'toprak-isleme';
INSERT IGNORE INTO `ru_products` (`category_id`, `slug`, `name`, `badge`, `summary`, `description`, `specs_json`, `sort_order`, `is_featured`, `is_active`)
SELECT c.id, 'pnomatik-hassas-ekim', 'Pnömatik Hassas Ekim', 'Yüksek verim', 'Tohum aralığı kontrolü, gübre ünitesi ve sezonluk kalibrasyon desteği.', 'Hassas ekim performansı ve farklı ürün desenlerine uyum için tasarlanmıştır.', '["4-8 sıra","Vakum sistem","Gübre deposu"]', 1, 1, 1 FROM `ru_product_categories` c WHERE c.slug = 'ekim-dikim';
INSERT IGNORE INTO `ru_products` (`category_id`, `slug`, `name`, `badge`, `summary`, `description`, `specs_json`, `sort_order`, `is_featured`, `is_active`)
SELECT c.id, 'asilir-tip-pulverizator', 'Asılır Tip Tarla Pülverizatörü', 'Bitki sağlığı', 'Dengeli bom yapısı ve kontrollü uygulama için nozül seçenekleri.', 'Tarla ilaçlamasında dengeli dağılım, kolay bakım ve güvenli kullanım sunar.', '["600-1000 L","12-16 m bom","Basınç regülatörü"]', 1, 1, 1 FROM `ru_product_categories` c WHERE c.slug = 'ilaclama';
INSERT IGNORE INTO `ru_products` (`category_id`, `slug`, `name`, `badge`, `summary`, `description`, `specs_json`, `sort_order`, `is_featured`, `is_active`)
SELECT c.id, 'sezon-bakim-paketi', 'Sezon Bakım Paketi', 'Servis', 'Aşınan parçalar, rulman, bıçak, hortum ve saha servis planı.', 'Sezon öncesi bakım ve hızlı parça tedariği için kurumsal servis paketi.', '["Hızlı sevk","Orijinal parça","Servis kaydı"]', 1, 0, 1 FROM `ru_product_categories` c WHERE c.slug = 'yedek-parca';

-- ============================================================================
-- v0.6.2 - Türkçe karakter düzeltmeleri
-- ============================================================================
UPDATE `ru_settings` SET `sval` = 'RAYU Tarım Makineleri' WHERE `skey` = 'site_name';
UPDATE `ru_settings` SET `sval` = 'Toprak İşlemede Uzman' WHERE `skey` = 'site_tagline';
UPDATE `ru_settings` SET `sval` = 'Konya, Türkiye' WHERE `skey` = 'site_address';
UPDATE `ru_settings` SET `sval` = 'Toprağın gücünü teknolojiyle buluşturuyoruz' WHERE `skey` = 'hero_title';
UPDATE `ru_settings` SET `sval` = 'Tarım makineleri, zirai ilaçlama ve servis çözümlerinde Türkiye geneli kurumsal satış platformu.' WHERE `skey` = 'hero_subtitle';
UPDATE `ru_settings` SET `sval` = 'Ürünlerimizi İnceleyin' WHERE `skey` = 'hero_cta_primary_text';
UPDATE `ru_settings` SET `sval` = 'Bize Ulaşın' WHERE `skey` = 'hero_cta_secondary_text';
UPDATE `ru_settings` SET `sval` = 'RAYU Tarım Makineleri; toprak işleme, ekim, ilaçlama, yedek parça ve servis süreçlerini tek kurumsal çatı altında yöneten satış ve destek platformudur.' WHERE `skey` = 'footer_about';
UPDATE `ru_settings` SET `sval` = 'Toprak işleme makinelerinde uzmanlaşmış, üreticinin yanında kurumsal yapı.' WHERE `skey` = 'about_short';
UPDATE `ru_settings` SET `sval` = 'Yerli mühendislik' WHERE `skey` = 'feature_1_title';
UPDATE `ru_settings` SET `sval` = 'Anadolu saha tecrübesi, dünya kalite standardı' WHERE `skey` = 'feature_1_text';
UPDATE `ru_settings` SET `sval` = 'Satış sonrası servis' WHERE `skey` = 'feature_2_title';
UPDATE `ru_settings` SET `sval` = 'Bakım, yedek parça ve sezon desteği tek ekipte' WHERE `skey` = 'feature_2_text';
UPDATE `ru_settings` SET `sval` = 'Bayi ve filo satışı' WHERE `skey` = 'feature_3_title';
UPDATE `ru_settings` SET `sval` = 'Türkiye genelinde kurumsal teklif ve teslimat akışı' WHERE `skey` = 'feature_3_text';
UPDATE `ru_settings` SET `sval` = 'Garanti ve kayıt' WHERE `skey` = 'feature_4_title';
UPDATE `ru_settings` SET `sval` = 'Her teslimatta ürün, servis ve bakım kaydı' WHERE `skey` = 'feature_4_text';
UPDATE `ru_settings` SET `sval` = 'Konya Organize Sanayi Bölgesi, Konya / Türkiye' WHERE `skey` = 'contact_address_full';

UPDATE `ru_menu` SET `label` = 'Anasayfa' WHERE `url` = '/';
UPDATE `ru_menu` SET `label` = 'Kurumsal' WHERE `url` = '/hakkimizda' AND `location` = 'header';
UPDATE `ru_menu` SET `label` = 'Ürünler' WHERE `url` = '/urunler' AND `location` = 'header';
UPDATE `ru_menu` SET `label` = 'İletişim' WHERE `url` = '/iletisim';
UPDATE `ru_menu` SET `label` = 'Hakkımızda' WHERE `url` = '/hakkimizda';
UPDATE `ru_menu` SET `label` = 'Tüm Ürünler' WHERE `url` = '/urunler' AND `location` = 'footer_2';
UPDATE `ru_menu` SET `label` = 'Toprak İşleme', `url` = '/urunler/toprak-isleme' WHERE `url` IN ('/urunler/tarim-aletleri', '/urunler/toprak-isleme');
UPDATE `ru_menu` SET `label` = 'Zirai İlaçlama', `url` = '/urunler/ilaclama' WHERE `url` IN ('/urunler/ziraai-ilaclar', '/urunler/ilaclama');
UPDATE `ru_menu` SET `label` = 'Tarihçe' WHERE `url` = '/tarihce';
UPDATE `ru_menu` SET `label` = 'İnsan Kaynakları' WHERE `url` = '/insan-kaynaklari';
UPDATE `ru_menu` SET `label` = 'Kalite Politikası' WHERE `url` = '/kalite-politikasi';

UPDATE `ru_pages` SET
  `title` = 'Hakkımızda',
  `subtitle` = 'Topraktan gelen güç, kurumsal satış disiplini',
  `excerpt` = 'RAYU Tarım Makineleri, üreticinin yanında olan teknoloji ve servis ortağıdır.',
  `content` = '<p>RAYU Tarım Makineleri, Konya merkezli yapısıyla tarım makineleri, zirai ilaçlama çözümleri, yedek parça ve saha servis süreçlerini tek kurumsal çatı altında sunar.</p><p>Satış ekibimiz ürün seçimini toprak yapısı, traktör gücü, ürün deseni ve sezon takvimine göre planlar. Teslimat sonrasında bakım, yedek parça ve operatör desteğiyle üreticinin yanında kalır.</p><h2>Kurumsal çalışma modelimiz</h2><p>Bayi yönetimi, filo satışı, teknik teklif, teslimat ve servis kayıtları ölçülebilir süreçlerle ilerler. Amacımız yalnızca makine satmak değil, sezon verimliliğini artıran sürdürülebilir bir iş ortaklığı kurmaktır.</p>'
  WHERE `slug` = 'hakkimizda';
UPDATE `ru_pages` SET
  `title` = 'Misyon & Vizyon',
  `subtitle` = 'Yarının tarımı için bugün',
  `content` = '<h2>Misyonumuz</h2><p>Üreticinin verimliliğini artıran, kullanıcıya değer katan, çevreye duyarlı tarım makineleri ve bitki sağlığı çözümleri sunmak.</p><h2>Vizyonumuz</h2><p>Türkiye’de güvenilir satış ve servis standardını yükselten, bölgesel pazarlarda tercih edilen kurumsal tarım teknolojileri markası olmak.</p>'
  WHERE `slug` = 'misyon-vizyon';
UPDATE `ru_pages` SET `title` = 'Tarihçe', `subtitle` = 'Kilometre taşları' WHERE `slug` = 'tarihce';
UPDATE `ru_pages` SET `title` = 'Kalite Politikası', `subtitle` = 'Standardın ötesi' WHERE `slug` = 'kalite-politikasi';
UPDATE `ru_pages` SET `title` = 'İnsan Kaynakları', `subtitle` = 'Bizimle çalışmak isteyenler için' WHERE `slug` = 'insan-kaynaklari';
UPDATE `ru_pages` SET `title` = 'İletişim', `subtitle` = 'Bize ulaşın' WHERE `slug` = 'iletisim';

UPDATE `ru_slider` SET
  `title` = 'Toprağın Gücünü Teknolojiyle Buluşturuyoruz',
  `subtitle` = 'Kurumsal tarım makineleri satışı',
  `description` = 'Toprak işleme, ekim, ilaçlama ve yedek parça çözümlerini teknik teklif ve servis desteğiyle sunuyoruz.',
  `link_text` = 'Ürünleri İncele'
  WHERE `id` = 1;
UPDATE `ru_slider` SET
  `title` = 'Zirai İlaçlama ve Bitki Sağlığı',
  `subtitle` = 'Doğru ürün, doğru uygulama',
  `description` = 'Lisanslı portföy ve saha danışmanlığıyla bitki sağlığı süreçlerinizi planlayın.',
  `link_url` = '/urunler/ilaclama',
  `link_text` = 'Kataloğu Gör'
  WHERE `id` = 2;
UPDATE `ru_slider` SET
  `title` = '2. El Pazarı',
  `subtitle` = 'Güvenli, garantili, ekspertiz onaylı',
  `description` = 'İkinci el tarım makinelerini RAYU güvencesiyle alıp satın. Tüm ilanlar uzman ekibimizce kontrol edilir.',
  `link_text` = '2. El Pazarına Gir'
  WHERE `id` = 3;

UPDATE `ru_product_categories` SET `title` = 'Toprak İşleme', `summary` = 'Pulluk, kültivatör, çizel, merdane ve tarla hazırlık ekipmanları.' WHERE `slug` = 'toprak-isleme';
UPDATE `ru_product_categories` SET `title` = 'Ekim ve Dikim', `summary` = 'Hassas ekim makineleri, mibzerler ve sezon verimini artıran çözümler.' WHERE `slug` = 'ekim-dikim';
UPDATE `ru_product_categories` SET `title` = 'Zirai İlaçlama', `summary` = 'Lisanslı ürün portföyü, atomizörler ve doğru uygulama ekipmanları.' WHERE `slug` = 'ilaclama';
UPDATE `ru_product_categories` SET `title` = 'Yedek Parça ve Servis', `summary` = 'Sezon içinde hızlı tedarik, teknik servis ve bakım planlama.' WHERE `slug` = 'yedek-parca';

UPDATE `ru_products` SET `name` = 'Ağır Tip Çizel', `badge` = 'Yoğun toprak', `summary` = 'Derin patlatma, düşük yakıt tüketimi ve güçlendirilmiş şase.', `description` = 'Derin toprak işlemede yüksek dayanım ve düşük bakım maliyeti için tasarlanmıştır.' WHERE `slug` = 'agir-tip-cizel';
UPDATE `ru_products` SET `name` = 'Diskli Goble', `badge` = 'Saha hazırlığı', `summary` = 'Anız parçalama ve homojen karıştırma için dengeli disk geometrisi.', `description` = 'Tarla hazırlığında homojen karıştırma ve güçlü gövde yapısı sunar.', `specs_json` = '["20-32 disk","Hidrolik ayar","Ağır hizmet rulman"]' WHERE `slug` = 'diskli-goble';
UPDATE `ru_products` SET `name` = 'Pnömatik Hassas Ekim', `badge` = 'Yüksek verim', `summary` = 'Tohum aralığı kontrolü, gübre ünitesi ve sezonluk kalibrasyon desteği.', `description` = 'Hassas ekim performansı ve farklı ürün desenlerine uyum için tasarlanmıştır.', `specs_json` = '["4-8 sıra","Vakum sistem","Gübre deposu"]' WHERE `slug` = 'pnomatik-hassas-ekim';
UPDATE `ru_products` SET `name` = 'Asılır Tip Tarla Pülverizatörü', `badge` = 'Bitki sağlığı', `summary` = 'Dengeli bom yapısı ve kontrollü uygulama için nozül seçenekleri.', `description` = 'Tarla ilaçlamasında dengeli dağılım, kolay bakım ve güvenli kullanım sunar.', `specs_json` = '["600-1000 L","12-16 m bom","Basınç regülatörü"]' WHERE `slug` = 'asilir-tip-pulverizator';
UPDATE `ru_products` SET `name` = 'Sezon Bakım Paketi', `summary` = 'Aşınan parçalar, rulman, bıçak, hortum ve saha servis planı.', `description` = 'Sezon öncesi bakım ve hızlı parça tedariği için kurumsal servis paketi.', `specs_json` = '["Hızlı sevk","Orijinal parça","Servis kaydı"]' WHERE `slug` = 'sezon-bakim-paketi';

-- ============================================================================
-- v0.6.7 - Kurumsal içerik, SEO ve örnek slider görselleri
-- ============================================================================
INSERT IGNORE INTO `ru_settings` (`skey`, `sval`) VALUES
  ('google_site_verification', ''),
  ('seo_default_description', 'RAYU Tarım Makineleri; tarım makineleri, zirai ilaçlama, yedek parça, servis ve ikinci el makine süreçlerinde kurumsal satış platformudur.');

UPDATE `ru_slider` SET `image` = 'assets/img/slider/toprak-isleme.jpg', `link_url` = '/urunler/toprak-isleme' WHERE `id` = 1;
UPDATE `ru_slider` SET `image` = 'assets/img/slider/ilaclama.jpg', `link_url` = '/urunler/ilaclama' WHERE `id` = 2;
UPDATE `ru_slider` SET `image` = 'assets/img/slider/ikinci-el.jpg', `link_url` = '/ikinci-el' WHERE `id` = 3;

INSERT IGNORE INTO `ru_pages`
  (`slug`, `title`, `subtitle`, `excerpt`, `content`, `template`, `sort_order`, `is_active`, `show_in_header`, `show_in_footer`)
VALUES
  ('gizlilik', 'Gizlilik Politikası', 'Veri güvenliği ve şeffaf iletişim',
   'RAYU Tarım Makineleri web sitesi üzerinden paylaşılan kişisel verilerin kullanım esasları.',
   '<h2>Gizlilik yaklaşımımız</h2><p>RAYU Tarım Makineleri, web sitesi üzerinden iletilen iletişim, teklif, servis ve bayilik başvurusu bilgilerini yalnızca talebin değerlendirilmesi, hizmet sunumu ve yasal yükümlülüklerin yerine getirilmesi amacıyla işler.</p><h2>Toplanan bilgiler</h2><p>Ad soyad, firma bilgisi, telefon, e-posta, şehir, talep konusu, mesaj içeriği ve teknik güvenlik kayıtları işlenebilir. Bu bilgiler üçüncü kişilerle ticari amaçla paylaşılmaz.</p><h2>Güvenlik</h2><p>Veriler yetkisiz erişimi önlemek için rol bazlı yönetim paneli, güvenli oturum ve sunucu güvenlik önlemleriyle korunur.</p>',
   'default', 60, 1, 0, 1),
  ('kvkk', 'KVKK Aydınlatma Metni', 'Kişisel verilerin korunması',
   '6698 sayılı KVKK kapsamında bilgilendirme metni.',
   '<h2>Veri sorumlusu</h2><p>RAYU Tarım Makineleri, web sitesi ve satış süreçleri kapsamında iletilen kişisel verileri veri sorumlusu sıfatıyla işler.</p><h2>İşleme amaçları</h2><p>Teklif hazırlama, ürün ve servis taleplerini yanıtlama, ikinci el başvurularını değerlendirme, müşteri ilişkilerini yürütme ve yasal kayıt yükümlülüklerini yerine getirme amaçlarıyla veri işlenir.</p><h2>Haklarınız</h2><p>KVKK kapsamındaki erişim, düzeltme, silme, itiraz ve bilgi talebi haklarınız için iletişim sayfasındaki kanallardan bize ulaşabilirsiniz.</p>',
   'default', 61, 1, 0, 1),
  ('cerez-politikasi', 'Çerez Politikası', 'Web deneyimi ve ölçümleme',
   'RAYU web sitesinde kullanılan çerez türleri ve tercih yönetimi.',
   '<h2>Çerez kullanımı</h2><p>Web sitemiz güvenli oturum, performans, temel kullanım ölçümleme ve kullanıcı deneyimini iyileştirme amacıyla çerezlerden yararlanabilir.</p><h2>Zorunlu çerezler</h2><p>Oturum güvenliği, CSRF koruması ve admin panel işlevleri için gerekli çerezler kullanılır.</p><h2>Tercihler</h2><p>Tarayıcı ayarlarınız üzerinden çerezleri silebilir veya engelleyebilirsiniz. Bazı zorunlu çerezlerin kapatılması site işlevlerini etkileyebilir.</p>',
   'default', 62, 1, 0, 1);

UPDATE `ru_pages` SET
  `title` = 'Misyon & Vizyon',
  `subtitle` = 'Yarının tarımı için bugünden ölçülebilir değer',
  `excerpt` = 'Verimlilik, güvenilir satış ve sürdürülebilir servis standardı.',
  `meta_description` = 'RAYU Tarım Makineleri misyon ve vizyonu: tarım makineleri, zirai ilaçlama, servis ve yedek parçada kurumsal değer üretmek.',
  `content` = '<h2>Misyonumuz</h2><p>Üreticinin verimliliğini artıran, kullanıcının iş yükünü azaltan, çevreye duyarlı tarım makineleri ve bitki sağlığı çözümlerini doğru ürün, doğru servis ve doğru zamanlama ile sunmak.</p><h2>Vizyonumuz</h2><p>Türkiye genelinde güvenilir satış ve servis standardını yükselten, bölgesel pazarlarda tercih edilen kurumsal tarım teknolojileri markası olmak.</p><h2>Çalışma ilkelerimiz</h2><ul><li>Teknik veriye dayalı ürün önerisi</li><li>Şeffaf teklif ve teslimat süreci</li><li>Yedek parça ve servis sürekliliği</li><li>Uzun vadeli bayi ve üretici ilişkisi</li></ul>'
  WHERE `slug` = 'misyon-vizyon';

UPDATE `ru_pages` SET
  `title` = 'Hakkımızda',
  `subtitle` = 'Topraktan gelen güç, kurumsal satış disiplini',
  `meta_description` = 'RAYU Tarım Makineleri; tarım makineleri, zirai ilaçlama, yedek parça, servis ve ikinci el makine süreçlerini tek çatı altında sunar.',
  `content` = '<p>RAYU Tarım Makineleri, Konya merkezli yapısıyla tarım makineleri, zirai ilaçlama çözümleri, yedek parça ve saha servis süreçlerini tek kurumsal çatı altında sunar.</p><p>Satış ekibimiz ürün seçimini toprak yapısı, traktör gücü, ürün deseni, sezon takvimi ve işletme ölçeğine göre planlar. Teslimat sonrasında bakım, yedek parça ve operatör desteğiyle üreticinin yanında kalır.</p><h2>Kurumsal çalışma modelimiz</h2><p>Bayi yönetimi, filo satışı, teknik teklif, teslimat ve servis kayıtları ölçülebilir süreçlerle ilerler. Amacımız yalnızca makine satmak değil, sezon verimliliğini artıran sürdürülebilir bir iş ortaklığı kurmaktır.</p><h2>Neden RAYU?</h2><ul><li>Türkiye geneli kurumsal teklif akışı</li><li>Ürün, servis ve yedek parça süreçlerinin tek panelden yönetimi</li><li>İkinci el makinelerde ekspertiz ve belge kontrolü</li><li>Satış sonrası bakım ve sezon planlama desteği</li></ul>'
  WHERE `slug` = 'hakkimizda';

UPDATE `ru_pages` SET
  `title` = 'İnsan Kaynakları',
  `subtitle` = 'Saha bilgisi, teknik disiplin ve ekip kültürü',
  `meta_description` = 'RAYU Tarım Makineleri kariyer ve insan kaynakları yaklaşımı.',
  `content` = '<h2>Birlikte büyüyelim</h2><p>RAYU Tarım Makineleri; satış, servis, yedek parça, saha operasyonu, dijital pazarlama ve bayi ilişkileri alanlarında güçlü ekip kültürüne önem verir.</p><h2>Aradığımız yetkinlikler</h2><ul><li>Tarım sektörünü ve üretici ihtiyaçlarını anlama</li><li>Teknik ürün bilgisi öğrenmeye açıklık</li><li>Şeffaf iletişim ve sorumluluk alma</li><li>Saha ve müşteri deneyimine önem verme</li></ul><p>Başvurularınızı iletişim formu üzerinden veya <strong>ik@rayutarim.com</strong> adresine iletebilirsiniz.</p>'
  WHERE `slug` = 'insan-kaynaklari';

INSERT IGNORE INTO `ru_menu` (`location`, `label`, `url`, `sort_order`, `is_active`) VALUES
  ('footer_3', 'Çerez Politikası', '/cerez-politikasi', 5, 1);
UPDATE `ru_menu` SET `label` = 'Gizlilik Politikası' WHERE `url` = '/gizlilik';
UPDATE `ru_menu` SET `label` = 'KVKK Aydınlatma Metni' WHERE `url` = '/kvkk';
