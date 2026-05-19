# Ray-U Tarım

> Kurumsal tarım makineleri ve ziraai ilaç satış platformu
> PHP 8.3+ • MySQL 5.7+ • DirectAdmin/LiteSpeed uyumlu • GitHub Release tabanlı Smart Update v5

🌐 **Canlı:** https://rayutarim.com
🛠️ **Repo:** https://github.com/codegatr/rayutarim
🏭 **Geliştirici:** [CODEGA](https://codega.com.tr) — Konya, Türkiye

---

## 📋 Faz Haritası

| Faz | Sürüm | Durum | Kapsam |
|----:|------:|:-----:|--------|
|  1  | v0.1.0 | ✅ **Şu an** | Çekirdek altyapı, kurulum sihirbazı, idempotent migration |
|  2  | v0.2.0 | ⏳ Sıradaki | Public tema: slider, anasayfa, kurumsal sayfalar |
|  3  | v0.3.0 | 🔜 | Ürün kataloğu: kategoriler, ürünler, detay, arama/filtre |
|  4  | v0.4.0 | 🔜 | Yönetim Paneli: auth, dashboard, CRUD |
|  5  | v0.5.0 | 🔜 | 2. El satış modülü, iletişim/teklif formları |
|  6  | v0.6.0 | 🔜 | Smart Update v5 (GitHub Release tabanlı otomatik güncelleme) |
|  7  | v0.7.0 → v1.0.0 | 🔜 | i18n (TR/EN/AR/RU), yurt dışı satış altyapısı |

---

## 🚀 Kurulum

### Gereksinimler

- PHP **8.3+** (extensions: `pdo_mysql`, `mbstring`, `json`, `gd` veya `imagick`, `openssl`, `curl`, `fileinfo`)
- MySQL **5.7+** veya MariaDB **10.3+**
- Apache 2.4 veya LiteSpeed (`mod_rewrite` aktif)

### Adımlar

1. **Repo'yu çek:**
   ```bash
   git clone https://github.com/codegatr/rayutarim.git public_html
   ```
   veya GitHub Release ZIP'ini indirip `public_html/` içine açın.

2. **Yazma izinleri:**
   ```bash
   chmod 755 inc uploads uploads/slider uploads/products uploads/pages
   ```

3. **Tarayıcıdan kurulum:**
   ```
   https://rayutarim.com/install.php
   ```
   5 adımlık sihirbaz: gereksinim kontrolü → DB → admin hesabı → uygulama → tamam.

4. **Kurulumdan sonra:**
   - `install.php` dosyasını sunucudan **silin** (güvenlik).
   - `inc/.installed` markeri otomatik oluşur, yeniden kurulumu engeller.

---

## 🗂️ Dizin Yapısı

```
rayutarim/
├── index.php                  # Public giriş noktası
├── 404.php                    # Hata sayfası
├── install.php                # Kurulum sihirbazı (kurulumdan sonra silinir)
├── manifest.json              # Sürüm + güncelleme metadata (SSOT)
├── .htaccess                  # LiteSpeed/Apache config
│
├── inc/                       # Çekirdek PHP kodu (web'den erişim kapalı)
│   ├── bootstrap.php          # Tek require: her şey başlar
│   ├── version.php            # manifest.json'dan sürüm okur
│   ├── config.example.php     # Yapılandırma şablonu
│   ├── config.php             # Üretilen (gitignore'da)
│   ├── db.php                 # PDO, settings cache, idempotent şema
│   └── functions.php          # h(), csrf, slug, redirect, json
│
├── admin/                     # Yönetim Paneli (Faz 4'te dolacak)
│
├── assets/
│   ├── css/                   # Public stiller (Faz 2)
│   ├── js/                    # Public scriptler (Faz 2)
│   └── img/                   # Statik görseller
│
├── uploads/                   # Kullanıcı yüklemeleri (preserved)
│   ├── slider/
│   ├── products/
│   └── pages/
│
├── migrations/                # SQL migration'ları (idempotent)
│   └── migration.sql
│
└── docs/                      # Geliştirici dokümanları
```

---

## 🛡️ Mimari Kararlar

### `manifest.json` = Single Source of Truth
- Sürüm, faz haritası, korunan/izlenen yol listesi, DB prefix — hepsi burada.
- PHP'de **hiçbir yerde** sürüm hard-code edilmez. `ru_version()` çağrılır.

### Idempotent Migrations
- Tüm `CREATE TABLE` → `CREATE TABLE IF NOT EXISTS`
- Tüm `ALTER TABLE` → `INFORMATION_SCHEMA` ile kolon kontrolü
- Aynı migration kaç kez çalışırsa çalışsın bozulmaz.

### Smart Update v5 Hazırlığı (Faz 6'da aktif)
- `paths.tracked` → güncelleme her seferinde üzerine yazılır.
- `paths.preserved` → kullanıcı dosyaları (uploads, config), asla dokunulmaz.
- `paths.writable` → kurulum sırasında izin kontrolü.

### Türkçe Karakter Politikası
- **Display text** (HTML çıktı, settings değerleri): TR karakter serbest.
- **URL/slug/dosya adı/SQL kolon adı**: ASCII zorunlu — Turkish/Latin haritalama `ru_slugify()` ile yapılır.
- PHP string literal'larda Türkçe apostrof (`'`) **kullanılmaz** (parse error riski).

### Güvenlik
- CSRF token tüm POST formlarında zorunlu (`ru_csrf_check()`).
- Bcrypt cost 12 ile şifre hash'leme.
- Login rate-limit + 15 dk lockout.
- `inc/`, `migrations/`, `backups/`, `logs/` → `.htaccess` ile web'den engellenmiş.
- `uploads/` içinde PHP yürütme yasak.

---

## 🔄 Smart Update (Faz 6'dan itibaren)

Her release otomatik ZIP üretir. Admin panelden veya CLI'dan tetiklenir:

1. `manifest.json`'daki `update.endpoint` GitHub Release API'den son sürümü çeker.
2. Yerel sürümle karşılaştırır.
3. Güncelleme varsa: **otomatik yedek** → ZIP indir → `tracked` yolları üzerine yaz → `preserved` yollara dokunma → `migrations/migration.sql` çalıştır.
4. Hata varsa rollback.

---

## 🌍 Uluslararasılaşma (Faz 7)

Faz 7'de aktiflenecek diller (DB'de stub olarak hazır):
- 🇹🇷 Türkçe (varsayılan)
- 🇬🇧 İngilizce
- 🇸🇦 Arapça (RTL)
- 🇷🇺 Rusça

`ru_languages` ve `ru_translations` (Faz 7'de gelecek) tabloları ile field-level çeviri.

---

## 📜 Lisans

Proprietary — © CODEGA, 2026. Tüm hakları saklıdır.

Bu yazılım Ray-U Tarım için özel olarak geliştirilmiştir; izinsiz çoğaltma, dağıtım veya yeniden satışı yasaktır.
