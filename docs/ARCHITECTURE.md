# Mimari Notları — RAYU Tarım Makineleri

## Tablo Şeması (Faz Faz)

### Faz 1 (✅ Mevcut)
- `ru_settings` — k/v ayar deposu (skey, sval)
- `ru_users` — yönetici hesapları
- `ru_audit_log` — değişiklik kaydı
- `ru_login_attempts` — brute-force koruması
- `ru_migrations` — uygulanan migration'lar
- `ru_languages` — dil tanımları (Faz 7 için stub)

### Faz 2 (Planlanan)
- `ru_pages` — statik sayfalar (Hakkımızda, Misyon, vs.)
- `ru_slider` — anasayfa slider'ları
- `ru_menu` — navigasyon menü öğeleri

### Faz 3 (Planlanan)
- `ru_categories` — ürün kategori ağacı (parent_id ile self-referential)
- `ru_products` — ürünler
- `ru_product_images` — galeri
- `ru_product_features` — özellik tablosu (key/value)
- `ru_brands` — markalar (opsiyonel)

### Faz 5 (Planlanan)
- `ru_used_products` — 2. el ürün ilanları
- `ru_used_inquiries` — 2. el ilan başvuruları (kullanıcının ürün sat başvurusu)
- `ru_inquiries` — genel iletişim/teklif formu kayıtları
- `ru_newsletter` — bülten abonelikleri

### Faz 7 (Planlanan)
- `ru_translations` — alan bazlı çeviri (entity, entity_id, field, lang_code, value)

---

## Sürüm Numaralandırma (SemVer)

- **MAJOR** — Geriye uyumsuz değişiklikler (yapısal DB değişiklikleri)
- **MINOR** — Yeni faz/modül eklemesi (geriye uyumlu)
- **PATCH** — Hata düzeltmesi, küçük iyileştirme

Üretim sürümü: `1.0.0` — Faz 7 tamamlandığında.

---

## Dosya Yazma Konvansiyonları

### Türkçe Karakter Uyarısı

PHP string literal'larda **tek tırnaklı string içinde Türkçe apostrof**:

```php
// ❌ YANLIŞ — apostrof PHP'yi karıştırır
$msg = 'çiftçi'nin sorunu';

// ✅ DOĞRU — heredoc veya çift tırnak
$msg = "çiftçi'nin sorunu";
$msg = <<<TXT
çiftçi'nin sorunu
TXT;
```

### URL / Slug / Kolon Adları
- Latin harfler, rakamlar, tire (`-`), alt çizgi (`_`) — başka karakter yok.
- `ru_slugify()` Türkçe → ASCII haritalama yapar.

### Migration Yazımı
```sql
-- ❌ YANLIŞ — idempotent değil
CREATE TABLE ru_products (...);
ALTER TABLE ru_products ADD COLUMN price DECIMAL(10,2);

-- ✅ DOĞRU
CREATE TABLE IF NOT EXISTS `ru_products` (...) ENGINE=InnoDB ...;
-- ALTER için PHP'den ru_safe_alter_add() kullan
```

---

## Yedek Stratejisi

Faz 6 Smart Update v5 her güncellemeden önce:

1. `backups/YYYYMMDD-HHMMSS-pre-vX.Y.Z/` klasörü oluştur
2. **DB dump** → `db.sql.gz` (mysqldump ile)
3. **Tracked dosyalar** → `files.tar.gz`
4. Son 5 yedek tutulur, eskileri silinir (manifest.json'da `backup_keep_count`).

---

## Geliştirme Workflow (CODEGA Standardı)

1. Yerel düzenleme + test
2. `manifest.json` → `version` bump
3. Git commit + tag push (`git tag v0.X.0`)
4. GitHub Action veya manuel ZIP üretimi (`rayutarim-v0.X.0.zip`)
5. GitHub Release oluştur, ZIP'i asset olarak yükle
6. Sunucudaki admin panel → "Güncelleme Kontrol" → "Güncelle" (Faz 6'dan sonra)

---

## API Endpoint'leri (Planlanan)

Faz 4+ admin panel için JSON API'lar:
- `POST /admin/ajax/auth-login`
- `GET  /admin/ajax/products?page=1&limit=20&q=...`
- `POST /admin/ajax/product-save`
- `POST /admin/ajax/upload-image`
- vs.

Tüm endpoint'ler CSRF korumalı + role-based authorization.
