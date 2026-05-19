<?php
/**
 * RAY-U TARIM — Basit Router
 *
 * Faz 2: anasayfa, kurumsal sayfalar (slug ile), iletisim, 404
 * Faz 3: /urunler, /urunler/{kategori}, /urunler/{kategori}/{urun}
 * Faz 5: /ikinci-el, /ikinci-el/{ilan-id}
 */

declare(strict_types=1);

function ru_dispatch(): void
{
    // _route GET parametresi (.htaccess'ten gelir) veya REQUEST_URI'den cek
    $route = (string)($_GET['_route'] ?? '');
    if ($route === '') {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $route = parse_url($uri, PHP_URL_PATH) ?: '/';
    }
    $route = '/' . trim($route, '/');

    // Bakim modu
    if (ru_setting('maintenance_mode', '0') === '1') {
        // Admin oturumu varsa gec
        if (empty($_SESSION['user_id'])) {
            ru_render('maintenance', [
                'page_title' => 'Bakim',
            ]);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // Sabit rotalar
    // ─────────────────────────────────────────────────────────────
    if ($route === '/' || $route === '') {
        ru_handle_home();
        return;
    }

    // Iletisim sayfasi (ozel sablon)
    if ($route === '/iletisim') {
        ru_handle_contact();
        return;
    }

    // Faz 3+ icin reserved rotalar (gecici karsilama)
    if (str_starts_with($route, '/urunler')) {
        ru_handle_placeholder('Urunler', 'Urun katalogumuz Faz 3 (v0.3.0) ile yayina alinacak.');
        return;
    }
    if (str_starts_with($route, '/ikinci-el')) {
        ru_handle_placeholder('2. El Pazari', '2. el satis modulu Faz 5 (v0.5.0) ile yayina alinacak.');
        return;
    }

    // ─────────────────────────────────────────────────────────────
    // Dinamik sayfa (slug)
    // ─────────────────────────────────────────────────────────────
    $slug = ltrim($route, '/');
    $page = ru_page_by_slug($slug);
    if ($page) {
        // Sablon secimi
        $tpl = match ($page['template'] ?? 'default') {
            'contact' => 'contact',
            default   => 'page',
        };
        ru_render($tpl, [
            'page'          => $page,
            'page_title'    => $page['title'] ?? '',
            'page_meta'     => $page['meta_description'] ?? '',
            'page_keywords' => $page['meta_keywords'] ?? '',
            'page_og_image' => $page['og_image'] ?? '',
            'page_class'    => 'page-' . $slug,
        ]);
        return;
    }

    // ─────────────────────────────────────────────────────────────
    // 404
    // ─────────────────────────────────────────────────────────────
    http_response_code(404);
    ru_render('404', [
        'page_title' => 'Sayfa Bulunamadi',
        'page_class' => 'page-404',
    ]);
}

// ──────────────────────────────────────────────────────────────────────
// Handlerlar
// ──────────────────────────────────────────────────────────────────────

function ru_handle_home(): void
{
    $slides = ru_active_sliders(10);
    $headerPages = ru_fetch_all(
        'SELECT slug, title, subtitle FROM ' . ru_t('pages') . '
         WHERE is_active = 1 AND show_in_header = 1
         ORDER BY sort_order ASC LIMIT 4'
    );

    ru_render('home', [
        'slides'      => $slides,
        'headerPages' => $headerPages,
        'page_title'  => null, // Anasayfa — site adi
        'page_class'  => 'page-home',
    ]);
}

function ru_handle_contact(): void
{
    $page = ru_page_by_slug('iletisim');
    ru_render('contact', [
        'page'        => $page ?: ['title' => 'Iletisim', 'content' => ''],
        'page_title'  => 'Iletisim',
        'page_class'  => 'page-contact',
    ]);
}

function ru_handle_placeholder(string $title, string $message): void
{
    ru_render('placeholder', [
        'placeholder_title'   => $title,
        'placeholder_message' => $message,
        'page_title'          => $title,
        'page_class'          => 'page-placeholder',
    ]);
}
