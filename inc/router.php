<?php
/**
 * RAYU TARIM MAKİNELERİ — Basit Router
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
                'page_title' => 'Bakım',
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

    // İletişim sayfası (özel şablon)
    if ($route === '/iletisim') {
        ru_handle_contact();
        return;
    }

    // Public katalog
    if (str_starts_with($route, '/urunler')) {
        $category = trim(substr($route, strlen('/urunler')), '/');
        ru_handle_products($category);
        return;
    }
    if (str_starts_with($route, '/ikinci-el')) {
        ru_handle_used_products();
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
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!ru_csrf_check()) {
            ru_flash('error', 'Güvenlik doğrulaması başarısız.');
            ru_redirect('/iletisim');
        }
        $name = trim((string)($_POST['full_name'] ?? ''));
        $phone = trim((string)($_POST['phone'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        if ($name === '' || ($phone === '' && $email === '')) {
            ru_flash('error', 'Lütfen ad soyad ve en az bir iletişim bilgisi girin.');
            ru_redirect('/iletisim');
        }
        ru_exec('INSERT INTO ' . ru_t('inquiries') . ' (type, full_name, email, phone, company, city, subject, message, source_url, ip, user_agent, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())', [
            (string)($_POST['type'] ?? 'contact'),
            $name,
            $email,
            $phone,
            trim((string)($_POST['company'] ?? '')),
            trim((string)($_POST['city'] ?? '')),
            trim((string)($_POST['subject'] ?? 'İletişim')),
            trim((string)($_POST['message'] ?? '')),
            (string)($_SERVER['REQUEST_URI'] ?? '/iletisim'),
            ru_client_ip(),
            substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255),
        ]);
        ru_flash('success', 'Talebiniz alındı. Ekibimiz en kısa sürede dönüş yapacak.');
        ru_redirect('/iletisim');
    }
    $page = ru_page_by_slug('iletisim');
    ru_render('contact', [
        'page'        => $page ?: ['title' => 'İletişim', 'content' => ''],
        'page_title'  => 'İletişim',
        'page_class'  => 'page-contact',
    ]);
}

function ru_handle_products(string $category = ''): void
{
    $categories = ru_catalog_categories();
    if ($category !== '' && !isset($categories[$category])) {
        http_response_code(404);
        ru_render('404', [
            'page_title' => 'Kategori Bulunamadi',
            'page_class' => 'page-404',
        ]);
    }

    ru_render('products', [
        'categories' => $categories,
        'products'   => $category === '' ? ru_catalog_products() : ru_catalog_products_by_category($category),
        'activeCategory' => $category,
        'page_title' => $category === '' ? 'Ürünler' : $categories[$category]['title'],
        'page_class' => 'page-products',
    ]);
}

function ru_handle_used_products(): void
{
    ru_render('used', [
        'page_title' => '2. El',
        'page_class' => 'page-used',
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
