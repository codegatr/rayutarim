# Smart Update v5

RAYU Smart Update v5, GitHub Release ve GitHub tag listesini birlikte kontrol eder. En yüksek semantik sürüm hangisiyse onu esas alır; Release asset bulunamazsa ilgili GitHub tag ZIP arşivini kullanır. Güncelleme öncesi yedek alır, `manifest.json` içindeki `paths.tracked` yollarını senkronize eder ve `paths.preserved` altındaki kullanıcı dosyalarına dokunmaz.

## Release paketi

1. GitHub release etiketi `v0.6.0` formatında olmalıdır.
2. Asset adı manifestteki kalıba uymalı: `rayutarim-v{version}.zip`.
3. ZIP kökünde `manifest.json` bulunmalıdır. Tek bir üst klasör içine alınmış ZIP de desteklenir.
4. GitHub Release oluşturulmamışsa updater en güncel `vX.Y.Z` tag arşivini GitHub `codeload` ZIP bağlantısından indirerek devam eder.

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

`check` yalnızca son release/tag bilgisini okur. `apply` yedek alır, ZIP paketini indirir, dosyaları kopyalar ve `migrations/migration.sql` dosyasını çalıştırır.

Migration runner SQL dosyasını tek parça göndermek yerine statement statement çalıştırır. Böylece DirectAdmin/LiteSpeed ortamlarında çoklu SQL statement kapalı olsa bile migration güvenilir şekilde uygulanır. İşlem sonunda `ru_migrations` tablosuna sürüm, dosya adı ve çalıştırılan SQL sayısı kaydedilir.

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
