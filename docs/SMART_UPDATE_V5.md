# Smart Update v5

RAYU Smart Update v5, GitHub Release üzerinden yayınlanan ZIP paketini indirir. Release asset bulunamazsa en güncel GitHub tag ZIP arşivini kullanır. Güncelleme öncesi yedek alır, `manifest.json` içindeki `paths.tracked` yollarını senkronize eder ve `paths.preserved` altındaki kullanıcı dosyalarına dokunmaz.

## Release paketi

1. GitHub release etiketi `v0.6.0` formatında olmalıdır.
2. Asset adı manifestteki kalıba uymalı: `rayutarim-v{version}.zip`.
3. ZIP kökünde `manifest.json` bulunmalıdır. Tek bir üst klasör içine alınmış ZIP de desteklenir.
4. GitHub Release oluşturulmamışsa updater en güncel `vX.Y.Z` tag arşivini indirerek devam eder.

## Korunan yollar

Aşağıdaki yollar güncellemede korunur:

- `uploads/`
- `inc/config.php`
- `.user.ini`
- `backups/`
- `logs/`

## CLI kullanımı

```bash
php admin/update.php check
php admin/update.php apply
```

`check` yalnızca son release bilgisini okur. `apply` yedek alır, release ZIP'ini indirir, dosyaları kopyalar ve `migrations/migration.sql` dosyasını çalıştırır.

## Web endpoint

Web endpoint token ile korunur. `inc/config.php` içinde güçlü bir token tanımlayın:

```php
'update' => [
    'github_token' => '',
    'web_token' => 'UZUN_RASTGELE_TOKEN',
    'backup_dir' => 'backups',
    'auto_check' => true,
    'check_interval' => 3600,
],
```

Sonra:

```text
https://rayutarim.com/admin/update.php?action=check&token=UZUN_RASTGELE_TOKEN
https://rayutarim.com/admin/update.php?action=apply&token=UZUN_RASTGELE_TOKEN
```

Private repo kullanılıyorsa `github_token` alanına sadece release okuma yetkisi olan GitHub tokeni girilmelidir.
