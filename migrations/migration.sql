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
  ('site_name',         'Ray-U Tarim'),
  ('site_tagline',      'Tarim Makineleri ve Ziraai Ilaclar'),
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
  ('about_short',       'Tarim teknolojisinin kalbinde, ureticinin yaninda.'),
  ('hero_title',        'Topragin Gucunu Teknolojiyle Bulusturuyoruz'),
  ('hero_subtitle',     'Tarim aletleri ve ziraai ilac coziimlerinde guvenilir adresiniz.'),
  ('footer_about',      'Ray-U Tarim, ureticinin verimliligini artiran ekipman ve coziimleriyle Anadolu topraklarinin yaninda.'),
  ('maintenance_mode',  '0'),
  ('founded_year',      '2010'),
  ('show_used_section', '1');
