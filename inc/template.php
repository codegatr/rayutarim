<?php
/**
 * RAYU TARIM MAKİNELERİ — Template / View Sistemi
 *
 * Basit, klasor tabanli view sistemi:
 *  - views/layouts/main.php   -> ana iskelet
 *  - views/partials/...php    -> header, footer, slider gibi parcalar
 *  - views/pages/...php       -> sayfa govdeleri
 *
 * Kullanim:
 *   ru_render('home', ['slides' => $slides, 'pages' => $pages]);
 */

declare(strict_types=1);

// ──────────────────────────────────────────────────────────────────────
// View Render
// ──────────────────────────────────────────────────────────────────────

/**
 * Bir sayfayi ana layout icinde render eder.
 *
 * @param string $page     views/pages/$page.php
 * @param array  $vars     view'a aktarilacak degiskenler
 * @param string $layout   views/layouts/$layout.php (default: main)
 * @return never
 */
function ru_render(string $page, array $vars = [], string $layout = 'main'): never
{
    $pageFile = RU_BASE . '/views/pages/' . $page . '.php';
    if (!is_file($pageFile)) {
        ru_abort(404, "Sayfa bulunamadi: $page");
    }

    // View ortak verileri
    $vars = array_merge([
        'site_name'    => (string)ru_setting('site_name', 'RAYU Tarim Makineleri'),
        'site_tagline' => (string)ru_setting('site_tagline', ''),
        'page_title'   => '',
        'page_meta'    => '',
        'page_keywords'=> '',
        'page_og_image'=> '',
        'page_class'   => 'page-' . preg_replace('/[^a-z0-9-]+/', '-', strtolower($page)),
    ], $vars);

    // Page icerigini bufferla
    extract($vars, EXTR_SKIP);
    ob_start();
    require $pageFile;
    $page_content = ob_get_clean();

    // Layout
    $layoutFile = RU_BASE . '/views/layouts/' . $layout . '.php';
    if (!is_file($layoutFile)) {
        echo $page_content;
        exit;
    }

    require $layoutFile;
    exit;
}

/**
 * Bir partial'i echo'lar (header, footer, vs.)
 */
function ru_partial(string $name, array $vars = []): void
{
    $file = RU_BASE . '/views/partials/' . $name . '.php';
    if (!is_file($file)) {
        return;
    }
    extract($vars, EXTR_SKIP);
    require $file;
}

// ──────────────────────────────────────────────────────────────────────
// Page / Menu Lookup
// ──────────────────────────────────────────────────────────────────────

/**
 * Slug ile sayfa cek
 */
function ru_page_by_slug(string $slug): ?array
{
    $slug = trim($slug, '/');
    if ($slug === '') return null;
    return ru_fetch(
        'SELECT * FROM ' . ru_t('pages') . ' WHERE slug = ? AND is_active = 1 LIMIT 1',
        [$slug]
    );
}

/**
 * Bir lokasyondaki menu ogelerini hiyerarsik dondurur (parent_id sirasi ile flat).
 * Faz 2'de sadece flat (parent_id=NULL) menu kullaniyoruz; submenu Faz 4'te admin'den.
 */
function ru_menu_items(string $location = 'header'): array
{
    static $cache = [];
    if (isset($cache[$location])) {
        return $cache[$location];
    }
    $rows = ru_fetch_all(
        'SELECT * FROM ' . ru_t('menu') . '
         WHERE location = ? AND is_active = 1
         ORDER BY sort_order ASC, id ASC',
        [$location]
    );
    return $cache[$location] = $rows;
}

/**
 * Header navigasyonunda goz parlatma — su anki URL'e gore active class
 */
function ru_is_current(string $url): bool
{
    $current = $_SERVER['REQUEST_URI'] ?? '/';
    $current = strtok($current, '?');
    $url = strtok($url, '?');
    if ($url === '/') {
        return $current === '/' || $current === '';
    }
    return rtrim($current, '/') === rtrim($url, '/');
}

// ──────────────────────────────────────────────────────────────────────
// Slider
// ──────────────────────────────────────────────────────────────────────

function ru_active_sliders(int $limit = 10): array
{
    $sql = 'SELECT * FROM ' . ru_t('slider') . '
            WHERE is_active = 1
              AND (start_at IS NULL OR start_at <= NOW())
              AND (end_at IS NULL OR end_at >= NOW())
            ORDER BY sort_order ASC, id ASC
            LIMIT ' . max(1, $limit);
    return ru_fetch_all($sql);
}

// ──────────────────────────────────────────────────────────────────────
// Asset URL'leri (cache-busting destekli)
// ──────────────────────────────────────────────────────────────────────

function ru_asset(string $path): string
{
    $path = ltrim($path, '/');
    $fs = RU_BASE . '/' . $path;
    $v = is_file($fs) ? '?v=' . filemtime($fs) : '?v=' . ru_version();
    return '/' . $path . $v;
}

function ru_upload_url(string $path): string
{
    if ($path === '') return '';
    if (str_starts_with($path, 'http')) return $path;
    return '/' . ltrim($path, '/');
}
