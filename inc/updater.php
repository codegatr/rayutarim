<?php
/**
 * RAYU TARIM MAKINELERI - Smart Update v5
 */
declare(strict_types=1);

final class RuUpdateException extends RuntimeException {}

final class RuSmartUpdater
{
    private array $manifest;
    private array $update;
    private string $base;
    private string $backupDir;

    public function __construct(?array $manifest = null)
    {
        $this->manifest = $manifest ?? ru_manifest();
        $this->update = (array)($this->manifest['update'] ?? []);
        $this->base = RU_BASE;
        $this->backupDir = $this->base . '/' . trim((string)(ru_config('update.backup_dir') ?: 'backups'), '/');
    }

    public function check(): array
    {
        $release = $this->latestRelease();
        $latest = ltrim((string)($release['tag_name'] ?? ''), 'v');
        if ($latest === '') {
            throw new RuUpdateException('GitHub release tag bulunamadı.');
        }

        return [
            'current_version' => ru_version(),
            'latest_version' => $latest,
            'has_update' => version_compare($latest, ru_version(), '>'),
            'release_name' => (string)($release['name'] ?? ''),
            'published_at' => (string)($release['published_at'] ?? ''),
            'source' => (string)($release['source'] ?? 'release'),
            'asset' => $this->selectAsset($release, $latest),
        ];
    }

    public function apply(): array
    {
        $check = $this->check();
        if (!$check['has_update']) {
            return $check + ['updated' => false, 'message' => 'Sistem zaten güncel.'];
        }

        $asset = $check['asset'];
        if (!$asset || empty($asset['browser_download_url'])) {
            throw new RuUpdateException('Release asset ZIP bulunamadı.');
        }

        $work = $this->makeWorkDir();
        $backup = '';

        try {
            $backup = $this->backupCurrent();
            $zip = $work . '/release.zip';
            $this->download((string)$asset['browser_download_url'], $zip);
            $source = $this->extractRelease($zip, $work . '/extract');
            $this->syncTrackedPaths($source);
            $migrations = $this->runMigrations((string)$check['latest_version']);
            $this->cleanupBackups();
            $this->removeDir($work);

            return $check + [
                'updated' => true,
                'message' => 'Güncelleme tamamlandı.',
                'backup' => $backup,
                'migrations' => $migrations,
            ];
        } catch (Throwable $e) {
            $this->removeDir($work);
            throw new RuUpdateException('Güncelleme durduruldu: ' . $e->getMessage(), 0, $e);
        }
    }

    private function latestRelease(): array
    {
        $endpoint = (string)($this->update['endpoint'] ?? '');
        if ($endpoint === '') {
            $endpoint = 'https://api.github.com/repos/' . ru_repo() . '/releases/latest';
        }

        try {
            $json = $this->httpGet($endpoint);
        } catch (RuUpdateException $e) {
            if (!str_contains($endpoint, '/releases/latest')) {
                throw $e;
            }
            return $this->latestTagRelease();
        }

        $data = json_decode($json, true);
        if (!is_array($data)) {
            throw new RuUpdateException('GitHub release yanıtı okunamadı.');
        }
        $data['source'] = $data['source'] ?? 'release';

        if (str_contains($endpoint, '/releases/latest')) {
            try {
                $tag = $this->latestTagRelease();
                $releaseVersion = ltrim((string)($data['tag_name'] ?? ''), 'v');
                $tagVersion = ltrim((string)($tag['tag_name'] ?? ''), 'v');
                if ($releaseVersion === '' || version_compare($tagVersion, $releaseVersion, '>')) {
                    return $tag;
                }
            } catch (RuUpdateException $e) {
                // Release endpoint çalışıyorsa tag karşılaştırması zorunlu değildir.
            }
        }

        return $data;
    }

    private function latestTagRelease(): array
    {
        $json = $this->httpGet('https://api.github.com/repos/' . ru_repo() . '/tags');
        $tags = json_decode($json, true);
        if (!is_array($tags) || empty($tags[0]['name'])) {
            throw new RuUpdateException('GitHub release bulunamadı ve tag listesi okunamadı.');
        }

        $latest = null;
        foreach ($tags as $tag) {
            $name = (string)($tag['name'] ?? '');
            if ($name === '') {
                continue;
            }
            if ($latest === null || version_compare(ltrim($name, 'v'), ltrim((string)$latest['name'], 'v'), '>')) {
                $latest = $tag;
            }
        }
        if (!$latest || empty($latest['zipball_url'])) {
            throw new RuUpdateException('GitHub tag ZIP bağlantısı bulunamadı.');
        }

        $version = ltrim((string)$latest['name'], 'v');
        $downloadUrl = $this->tagDownloadUrl((string)$latest['name']);
        return [
            'tag_name' => (string)$latest['name'],
            'name' => (string)$latest['name'] . ' source archive',
            'published_at' => '',
            'source' => 'tag',
            'assets' => [[
                'name' => 'github-source-' . $version . '.zip',
                'browser_download_url' => $downloadUrl,
                'api_zipball_url' => (string)$latest['zipball_url'],
            ]],
        ];
    }

    private function tagDownloadUrl(string $tagName): string
    {
        return 'https://codeload.github.com/' . ru_repo() . '/zip/refs/tags/' . rawurlencode($tagName);
    }

    private function selectAsset(array $release, string $version): ?array
    {
        $pattern = (string)($this->update['asset_pattern'] ?? 'rayutarim-v{version}.zip');
        $expected = str_replace('{version}', $version, $pattern);
        foreach ((array)($release['assets'] ?? []) as $asset) {
            if (($asset['name'] ?? '') === $expected) {
                return $asset;
            }
        }
        foreach ((array)($release['assets'] ?? []) as $asset) {
            if (str_ends_with((string)($asset['name'] ?? ''), '.zip')) {
                return $asset;
            }
        }
        if (!empty($release['zipball_url'])) {
            $tagName = (string)($release['tag_name'] ?? ('v' . $version));
            return [
                'name' => 'github-source-' . $version . '.zip',
                'browser_download_url' => $this->tagDownloadUrl($tagName),
                'api_zipball_url' => (string)$release['zipball_url'],
            ];
        }
        return null;
    }

    private function download(string $url, string $target): void
    {
        $body = $this->httpGet($url, true);
        if (file_put_contents($target, $body) === false) {
            throw new RuUpdateException('ZIP dosyası yazılamadı.');
        }
    }

    private function httpGet(string $url, bool $binary = false): string
    {
        $headers = [
            'User-Agent: RAYU-Smart-Update/' . ru_version(),
            'Accept: ' . $this->acceptHeader($url, $binary),
        ];
        $token = (string)(ru_config('update.github_token') ?? '');
        if ($token !== '') {
            $headers[] = 'Authorization: Bearer ' . $token;
        }

        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_TIMEOUT => 90,
            ]);
            $body = curl_exec($ch);
            $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $err = curl_error($ch);
            curl_close($ch);
            if ($body === false || $code >= 400) {
                throw new RuUpdateException('GitHub isteği başarısız: HTTP ' . $code . ' ' . $err);
            }
            return (string)$body;
        }

        $context = stream_context_create(['http' => ['header' => implode("\r\n", $headers), 'timeout' => 90]]);
        $body = file_get_contents($url, false, $context);
        if ($body === false) {
            throw new RuUpdateException('GitHub isteği başarısız.');
        }
        return $body;
    }

    private function acceptHeader(string $url, bool $binary): string
    {
        if (!$binary) {
            return 'application/vnd.github+json';
        }
        if (str_contains($url, '/releases/assets/')) {
            return 'application/octet-stream';
        }
        return 'application/zip, application/octet-stream, */*';
    }

    private function backupCurrent(): string
    {
        if (!class_exists(ZipArchive::class)) {
            throw new RuUpdateException('ZipArchive eklentisi gerekli.');
        }
        if (!is_dir($this->backupDir) && !mkdir($this->backupDir, 0755, true)) {
            throw new RuUpdateException('Yedek klasörü oluşturulamadı.');
        }

        $file = $this->backupDir . '/rayutarim-backup-' . date('Ymd-His') . '.zip';
        $zip = new ZipArchive();
        if ($zip->open($file, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuUpdateException('Yedek ZIP oluşturulamadı.');
        }

        foreach ($this->trackedPaths() as $path) {
            $absolute = $this->safePath($path);
            if (is_file($absolute)) {
                $zip->addFile($absolute, $path);
            } elseif (is_dir($absolute)) {
                $this->zipDir($zip, $absolute, rtrim($path, '/'));
            }
        }
        $zip->close();
        return $file;
    }

    private function extractRelease(string $zipFile, string $target): string
    {
        if (!class_exists(ZipArchive::class)) {
            throw new RuUpdateException('ZipArchive eklentisi gerekli.');
        }
        if (!mkdir($target, 0755, true) && !is_dir($target)) {
            throw new RuUpdateException('Geçici klasör oluşturulamadı.');
        }

        $zip = new ZipArchive();
        if ($zip->open($zipFile) !== true) {
            throw new RuUpdateException('Release ZIP açılamadı.');
        }
        $zip->extractTo($target);
        $zip->close();

        if (is_file($target . '/manifest.json')) {
            return $target;
        }
        foreach (scandir($target) ?: [] as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $candidate = $target . '/' . $item;
            if (is_dir($candidate) && is_file($candidate . '/manifest.json')) {
                return $candidate;
            }
        }
        throw new RuUpdateException('ZIP içinde manifest.json bulunamadı.');
    }

    private function syncTrackedPaths(string $source): void
    {
        foreach ($this->trackedPaths() as $path) {
            if ($this->isPreserved($path)) {
                continue;
            }
            $from = $source . '/' . trim($path, '/');
            if (!file_exists($from)) {
                continue;
            }
            $to = $this->safePath($path);
            if (is_dir($from)) {
                $this->copyDir($from, $to, trim($path, '/'));
            } else {
                if (!is_dir(dirname($to)) && !mkdir(dirname($to), 0755, true)) {
                    throw new RuUpdateException('Hedef klasör oluşturulamadı: ' . dirname($to));
                }
                if (!copy($from, $to)) {
                    throw new RuUpdateException('Dosya kopyalanamadı: ' . $path);
                }
            }
        }
    }

    private function runMigrations(string $targetVersion): array
    {
        $dir = trim((string)($this->update['migration_dir'] ?? 'migrations'), '/');
        $file = $this->base . '/' . $dir . '/migration.sql';
        if (!is_file($file)) {
            return ['file' => $dir . '/migration.sql', 'executed' => 0, 'message' => 'Migration dosyası bulunamadı.'];
        }
        $sql = file_get_contents($file);
        if ($sql === false || trim($sql) === '') {
            return ['file' => $dir . '/migration.sql', 'executed' => 0, 'message' => 'Migration dosyası boş.'];
        }

        $statements = $this->splitSqlStatements($sql);
        $executed = 0;
        foreach ($statements as $statement) {
            $trimmed = trim($statement);
            if ($trimmed === '') {
                continue;
            }
            try {
                ru_db()->exec($trimmed);
                $executed++;
            } catch (Throwable $e) {
                $excerpt = preg_replace('/\s+/', ' ', substr($trimmed, 0, 180));
                throw new RuUpdateException('Migration SQL çalışmadı: ' . $excerpt . ' — ' . $e->getMessage(), 0, $e);
            }
        }

        $this->recordMigration($dir . '/migration.sql', $executed, $targetVersion);
        return [
            'file' => $dir . '/migration.sql',
            'executed' => $executed,
            'message' => $executed > 0 ? 'Migration başarıyla çalıştırıldı.' : 'Çalıştırılacak migration bulunamadı.',
        ];
    }

    private function splitSqlStatements(string $sql): array
    {
        $statements = [];
        $buffer = '';
        $quote = null;
        $len = strlen($sql);

        for ($i = 0; $i < $len; $i++) {
            $char = $sql[$i];
            $next = $i + 1 < $len ? $sql[$i + 1] : '';

            if ($quote === null && $char === '-' && $next === '-') {
                while ($i < $len && $sql[$i] !== "\n") {
                    $i++;
                }
                $buffer .= "\n";
                continue;
            }

            if ($quote === null && $char === '#') {
                while ($i < $len && $sql[$i] !== "\n") {
                    $i++;
                }
                $buffer .= "\n";
                continue;
            }

            if ($quote === null && $char === '/' && $next === '*') {
                $i += 2;
                while ($i < $len - 1 && !($sql[$i] === '*' && $sql[$i + 1] === '/')) {
                    $i++;
                }
                $i++;
                $buffer .= "\n";
                continue;
            }

            if ($quote === $char && ($char === '"' || $char === "'" || $char === '`') && $next === $char) {
                $buffer .= $char . $next;
                $i++;
                continue;
            }

            if (($char === '"' || $char === "'" || $char === '`') && ($i === 0 || $sql[$i - 1] !== '\\')) {
                if ($quote === $char) {
                    $quote = null;
                } elseif ($quote === null) {
                    $quote = $char;
                }
            }

            if ($char === ';' && $quote === null) {
                if (trim($buffer) !== '') {
                    $statements[] = $buffer;
                }
                $buffer = '';
                continue;
            }

            $buffer .= $char;
        }

        if (trim($buffer) !== '') {
            $statements[] = $buffer;
        }

        return $statements;
    }

    private function recordMigration(string $filename, int $executed, string $targetVersion): void
    {
        try {
            ru_db()->exec('CREATE TABLE IF NOT EXISTS ' . ru_t('migrations') . " (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `version` VARCHAR(20) NOT NULL,
                `filename` VARCHAR(190) NOT NULL,
                `applied_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `success` TINYINT(1) NOT NULL DEFAULT 1,
                `notes` TEXT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uniq_filename` (`filename`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
            ru_exec(
                'INSERT INTO ' . ru_t('migrations') . ' (`version`, `filename`, `success`, `notes`, `applied_at`) VALUES (?, ?, 1, ?, NOW())
                 ON DUPLICATE KEY UPDATE `version` = VALUES(`version`), `success` = 1, `notes` = VALUES(`notes`), `applied_at` = NOW()',
                [$targetVersion, $filename, $executed . ' SQL statement çalıştırıldı.']
            );
        } catch (Throwable) {
            // Migration başarıyla çalıştıysa takip kaydı yazılamaması güncellemeyi durdurmasın.
        }
    }

    private function cleanupBackups(): void
    {
        $keep = (int)($this->update['backup_keep_count'] ?? 5);
        $files = glob($this->backupDir . '/rayutarim-backup-*.zip') ?: [];
        rsort($files);
        foreach (array_slice($files, max(0, $keep)) as $file) {
            @unlink($file);
        }
    }

    private function trackedPaths(): array
    {
        return array_values(array_filter((array)($this->manifest['paths']['tracked'] ?? []), 'is_string'));
    }

    private function preservedPaths(): array
    {
        return array_values(array_filter((array)($this->manifest['paths']['preserved'] ?? []), 'is_string'));
    }

    private function isPreserved(string $path): bool
    {
        $path = trim(str_replace('\\', '/', $path), '/');
        foreach ($this->preservedPaths() as $preserved) {
            $preserved = trim(str_replace('\\', '/', $preserved), '/');
            if ($path === $preserved || str_starts_with($path . '/', $preserved . '/')) {
                return true;
            }
        }
        return false;
    }

    private function safePath(string $path): string
    {
        $path = trim(str_replace('\\', '/', $path), '/');
        if ($path === '' || str_contains($path, '..')) {
            throw new RuUpdateException('Geçersiz yol: ' . $path);
        }
        return $this->base . '/' . $path;
    }

    private function makeWorkDir(): string
    {
        $dir = $this->base . '/logs/update-' . date('Ymd-His') . '-' . bin2hex(random_bytes(3));
        if (!mkdir($dir, 0755, true) && !is_dir($dir)) {
            throw new RuUpdateException('Geçici update klasörü oluşturulamadı.');
        }
        return $dir;
    }

    private function zipDir(ZipArchive $zip, string $dir, string $prefix): void
    {
        $items = scandir($dir) ?: [];
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $dir . '/' . $item;
            $local = $prefix . '/' . $item;
            if (is_dir($path)) {
                $this->zipDir($zip, $path, $local);
            } else {
                $zip->addFile($path, $local);
            }
        }
    }

    private function copyDir(string $from, string $to, string $relative = ''): void
    {
        if (!is_dir($to) && !mkdir($to, 0755, true)) {
            throw new RuUpdateException('Hedef klasör oluşturulamadı: ' . $to);
        }
        foreach (scandir($from) ?: [] as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $itemRelative = trim($relative . '/' . $item, '/');
            if ($this->isPreserved($itemRelative)) {
                continue;
            }
            $src = $from . '/' . $item;
            $dst = $to . '/' . $item;
            if (is_dir($src)) {
                $this->copyDir($src, $dst, $itemRelative);
            } elseif (!copy($src, $dst)) {
                throw new RuUpdateException('Dosya kopyalanamadı: ' . $dst);
            }
        }
    }

    private function removeDir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        foreach (scandir($dir) ?: [] as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $dir . '/' . $item;
            is_dir($path) ? $this->removeDir($path) : @unlink($path);
        }
        @rmdir($dir);
    }
}
