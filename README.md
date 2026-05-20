# RAYU Tarım Makineleri

Kurumsal tarım makineleri, zirai ilaçlama, yedek parça, servis ve ikinci el güven akışı için PHP 8.3+ / MySQL 5.7+ uyumlu web platformu.

- Canlı alan adı: https://rayutarim.com
- Repo: https://github.com/codegatr/rayutarim
- Sunucu uyumu: DirectAdmin, LiteSpeed, Apache rewrite
- Güncelleme: GitHub Release + tag arşivi uyumlu Smart Update v5

## Kapsam

v0.6.9 ile public yüzey, yönetim paneli, medya yükleme, SEO altyapısı, Türkçe metin standardı ve anlaşılır Smart Update ekranı kurumsal satış seviyesine taşındı:

- Temiz SVG marka seti: `assets/img/logo.svg`
- Kurumsal anasayfa: güven bandı, ürün grupları, öne çıkan ürünler, servis süreci
- Katalog rotaları: `/urunler`, `/urunler/toprak-isleme`, `/urunler/ekim-dikim`, `/urunler/ilaclama`, `/urunler/yedek-parca`
- 2. el güven sayfası: `/ikinci-el`
- Admin panel: `/admin/login.php`
- Boş kullanıcı tablosu için token korumalı ilk admin kurtarma: `/admin/recover.php`
- Ürün, kategori, sayfa, slider, menü, ayar, kullanıcı ve talep yönetimi
- Admin panelden ürün, slider, sayfa hero/OG, logo ve favicon görsel yükleme
- Google uyumlu `robots.txt`, dinamik `/sitemap.xml`, canonical, OpenGraph ve yapılandırılmış veri altyapısı
- Hakkımızda, Misyon/Vizyon, İnsan Kaynakları, Gizlilik, KVKK ve Çerez Politikası içerikleri
- 2. el bölümü için premium ekspertiz ve güven pazarı sunumu
- Aktif iletişim/teklif formu: kayıtlar admin panelde `Talepler` ekranına düşer
- Smart Update v5: Release/tag sürüm karşılaştırması, ZIP indirme, yedek, korunan dosyalar, migration runner

## Kurulum

Gereksinimler:

- PHP 8.3+
- MySQL 5.7+ veya MariaDB 10.3+
- PHP eklentileri: `pdo_mysql`, `mbstring`, `json`, `openssl`, `curl`, `zip`, `fileinfo`
- Apache/LiteSpeed rewrite

Adımlar:

```bash
git clone https://github.com/codegatr/rayutarim.git public_html
cd public_html
chmod 755 inc uploads uploads/slider uploads/products uploads/pages backups logs
```

Tarayıcıdan kurulum:

```text
https://rayutarim.com/install.php
```

Kurulumdan sonra `install.php` dosyasını sunucudan kaldırın veya `inc/.installed` marker dosyasının oluştuğunu kontrol edin.

## Smart Update v5

Kontrol:

```bash
php admin/update.php check
```

Uygula:

```bash
php admin/update.php apply
```

Web endpoint için `inc/config.php` içinde `update.web_token` tanımlayın:

```text
https://rayutarim.com/admin/update.php?action=check&token=TOKEN
https://rayutarim.com/admin/update.php?action=apply&token=TOKEN
```

Detaylar: `docs/SMART_UPDATE_V5.md`

## Korunan Dosyalar

Smart Update şu yolları korur:

- `uploads/`
- `inc/config.php`
- `.user.ini`
- `backups/`
- `logs/`

## Dizinler

```text
admin/              Yönetim paneli, talep yönetimi ve Smart Update ekranı
assets/             CSS, JS, logo ve statik görseller
docs/               Mimari ve update dokümanları
inc/                Bootstrap, DB, router, katalog, updater
migrations/         Idempotent SQL
uploads/            Kullanıcı dosyaları
views/              Layout, partial ve public sayfalar
```
