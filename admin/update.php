<?php
/**
 * Smart Update v5 endpoint / CLI runner
 */
declare(strict_types=1);

require_once dirname(__DIR__) . '/inc/bootstrap.php';
require_once RU_BASE . '/inc/updater.php';

$isCli = PHP_SAPI === 'cli';

if (!$isCli) {
    $token = (string)(ru_config('update.web_token') ?? '');
    $given = (string)($_GET['token'] ?? '');
    if ($token === '' || !hash_equals($token, $given)) {
        http_response_code(403);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => false, 'error' => 'Smart Update token gerekli.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

$action = $isCli ? (string)($argv[1] ?? 'check') : (string)($_GET['action'] ?? 'check');
$updater = new RuSmartUpdater();

try {
    $result = $action === 'apply' ? $updater->apply() : $updater->check();
    $payload = ['ok' => true, 'action' => $action, 'result' => $result];
} catch (Throwable $e) {
    $payload = ['ok' => false, 'action' => $action, 'error' => $e->getMessage()];
    if (!$isCli) {
        http_response_code(500);
    }
}

if ($isCli) {
    echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
    exit($payload['ok'] ? 0 : 1);
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
