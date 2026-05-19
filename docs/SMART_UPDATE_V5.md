# Smart Update v5

RAYU Smart Update v5, GitHub Release uzerinden yayinlanan ZIP paketini indirir, guncelleme oncesi yedek alir, `manifest.json` icindeki `paths.tracked` yollarini senkronize eder ve `paths.preserved` altindaki kullanici dosyalarina dokunmaz.

## Release paketi

1. GitHub release etiketi `v0.6.0` formatinda olmalidir.
2. Asset adi manifestteki kaliba uymali: `rayutarim-v{version}.zip`.
3. ZIP kokunde `manifest.json` bulunmalidir. Tek bir ust klasor icine alinmis ZIP de desteklenir.

## Korunan yollar

Asagidaki yollar guncellemede korunur:

- `uploads/`
- `inc/config.php`
- `.user.ini`
- `backups/`
- `logs/`

## CLI kullanimi

```bash
php admin/update.php check
php admin/update.php apply
```

`check` yalnizca son release bilgisini okur. `apply` yedek alir, release ZIP'ini indirir, dosyalari kopyalar ve `migrations/migration.sql` dosyasini calistirir.

## Web endpoint

Yonetim paneli tamamlanana kadar endpoint token ile korunur. `inc/config.php` icinde guclu bir token tanimlayin:

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

Private repo kullaniliyorsa `github_token` alanina sadece release okuma yetkisi olan GitHub tokeni girilmelidir.
