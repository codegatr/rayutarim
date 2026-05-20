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
        ru_abort(404, "Sayfa bulunamadı: $page");
    }

    // View ortak verileri
    $vars = array_merge([
        'site_name'    => (string)ru_setting('site_name', 'RAYU Tarım Makineleri'),
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
    try {
        $page = ru_fetch(
            'SELECT * FROM ' . ru_t('pages') . ' WHERE slug = ? AND is_active = 1 LIMIT 1',
            [$slug]
        );
        if ($page) {
            return $page;
        }
    } catch (Throwable) {
    }
    $fallback = ru_static_pages();
    return $fallback[$slug] ?? null;
}

function ru_static_pages(): array
{
    $pages = [
        'hakkimizda' => [
            'title' => 'Hakkımızda',
            'subtitle' => 'Tarım ekipmanlarında çok markalı satış ve danışmanlık ağı',
            'meta_description' => 'RAYU Tarım, tarım makineleri, zirai ilaçlama ekipmanları, yedek parça ve ikinci el ürünlerde çok markalı kurumsal satış platformudur.',
            'content' => '<p>RAYU Tarım, tek bir üretici vitrini olmak yerine üretici, bayi, ithalatçı ve çiftçiyi aynı ticari akışta buluşturan çok markalı tarım satış platformu olarak konumlanır.</p><p>Amacımız; traktörden toprağa, ilaçlamadan hasada, yedek parçadan ikinci ele kadar üreticinin ihtiyacını doğru marka, doğru fiyat, doğru teslimat ve doğru servis güvencesiyle karşılamaktır.</p><h2>Çalışma modelimiz</h2><p>Ürün talebini teknik ihtiyaç, bölge, sezon, bütçe ve servis erişimiyle birlikte değerlendirir; uygun firmalardan teklifleri toplar, karşılaştırır ve satın alma kararını netleştiririz.</p><h2>RAYU farkı</h2><ul><li>Çok markalı ürün havuzu</li><li>Tedarikçi ve bayi başvuru altyapısı</li><li>Kurumsal teklif ve talep yönetimi</li><li>Yeni, ikinci el, yedek parça ve servis akışının tek çatı altında toplanması</li></ul>',
        ],
        'misyon-vizyon' => [
            'title' => 'Misyon & Vizyon',
            'subtitle' => 'Tarım ticaretinde güvenilir aracı kurum standardı',
            'meta_description' => 'RAYU Tarım misyon ve vizyonu: Türkiye genelinde tarım makineleri ve ekipmanlarında güvenilir çok markalı satış ağı kurmak.',
            'content' => '<h2>Misyonumuz</h2><p>Çiftçinin doğru ürüne, üreticinin doğru müşteriye, bayinin doğru satış kanalına ulaşmasını sağlayan şeffaf ve güvenilir bir tarım ticareti altyapısı kurmak.</p><h2>Vizyonumuz</h2><p>Türkiye genelinde tarım makineleri, zirai ilaçlama ekipmanları, yedek parça, servis ve ikinci el satışında ilk akla gelen çok markalı kurumsal platform olmak.</p><h2>İlkelerimiz</h2><ul><li>Markalar arası şeffaf karşılaştırma</li><li>Belgelendirilebilir teklif ve teslimat süreci</li><li>Satış sonrası servis ve parça sürekliliği</li><li>Üretici, bayi ve çiftçi için kazan-kazan modeli</li></ul>',
        ],
        'markalar' => [
            'title' => 'Markalar ve Tedarik Ağı',
            'subtitle' => 'Farklı firmaların ürünlerini tek satış standardında buluşturuyoruz',
            'meta_description' => 'RAYU Tarım markalar ve tedarik ağı: tarım makineleri, ekipman, yedek parça ve ikinci el ürünlerde çok markalı satış platformu.',
            'content' => '<p>RAYU Tarım; yerli üreticiler, ithalatçılar, bölge bayileri, yedek parça tedarikçileri ve ikinci el makine sahipleri için tek merkezli satış kanalı oluşturur.</p><h2>Platforma alınan ürün grupları</h2><ul><li>Traktör ve güç ekipmanları</li><li>Toprak işleme, ekim, gübreleme ve hasat ekipmanları</li><li>Zirai ilaçlama makineleri ve uygulama ekipmanları</li><li>Yedek parça, sarf malzeme ve servis paketleri</li><li>Ekspertizli ikinci el tarım makineleri</li></ul><h2>Nasıl çalışır?</h2><p>Tedarikçi ürününü, teknik bilgisini ve ticari şartlarını iletir. RAYU ekibi ürün sınıflandırmasını, satış metnini, talep akışını ve teklif yönetimini kurumsal standartla yönetir.</p><p><a class="btn btn--primary" href="/tedarikci-basvurusu">Tedarikçi başvurusu yap</a></p>',
        ],
        'tedarikci-basvurusu' => [
            'title' => 'Tedarikçi Başvurusu',
            'subtitle' => 'Ürünlerinizi RAYU satış ağına dahil edin',
            'meta_description' => 'Tarım makineleri, ekipman ve yedek parça firmaları için RAYU Tarım tedarikçi başvuru sayfası.',
            'content' => '<p>Tarım makinesi, ekipman, yedek parça, zirai uygulama teknolojisi veya ikinci el ürün portföyünüz varsa RAYU satış ağına başvurabilirsiniz.</p><h2>Kimler başvurabilir?</h2><ul><li>Üretici ve ithalatçı firmalar</li><li>Bölge bayileri ve distribütörler</li><li>Servis ve yedek parça tedarikçileri</li><li>Kurumsal ikinci el makine satıcıları</li></ul><h2>Başvuru için gereken bilgiler</h2><p>Firma adı, şehir, ürün grupları, marka bilgisi, garanti/servis koşulları, teslimat bölgeleri ve satış temsilcisi iletişim bilgileri yeterlidir.</p><p><a class="btn btn--primary" href="/iletisim?type=supplier">Başvuru formuna git</a></p>',
        ],
        'insan-kaynaklari' => [
            'title' => 'İnsan Kaynakları',
            'subtitle' => 'Satış, saha ve dijital tarım ticareti ekibi',
            'meta_description' => 'RAYU Tarım insan kaynakları ve kariyer yaklaşımı.',
            'content' => '<p>RAYU Tarım; saha satış, bayi ilişkileri, ürün yönetimi, dijital pazarlama, servis koordinasyonu ve müşteri deneyimi alanlarında büyüyen bir ekip kültürü kurar.</p><p>Tarım sektörünü bilen, teknik ürünü öğrenmeye açık, şeffaf iletişim kuran ve üreticinin gerçek ihtiyacını önemseyen ekip arkadaşlarıyla çalışmak isteriz.</p><p>Başvurularınızı iletişim formu üzerinden veya ik@rayutarim.com adresine iletebilirsiniz.</p>',
        ],
        'gizlilik' => [
            'title' => 'Gizlilik Politikası',
            'subtitle' => 'Veri güvenliği ve iletişim gizliliği',
            'meta_description' => 'RAYU Tarım gizlilik politikası.',
            'content' => '<p>RAYU Tarım web sitesi üzerinden iletilen iletişim, teklif, tedarikçi başvurusu ve servis bilgilerini yalnızca talebin değerlendirilmesi ve hizmet sunumu amacıyla işler.</p><p>Paylaşılan bilgiler yetkisiz erişime karşı korunur; ticari amaçla üçüncü kişilere satılmaz.</p>',
        ],
        'kvkk' => [
            'title' => 'KVKK Aydınlatma Metni',
            'subtitle' => 'Kişisel verilerin işlenmesi hakkında bilgilendirme',
            'meta_description' => 'RAYU Tarım KVKK aydınlatma metni.',
            'content' => '<p>RAYU Tarım; ad soyad, telefon, e-posta, firma, şehir, ürün talebi ve mesaj içeriklerini teklif hazırlama, müşteri ilişkileri, tedarikçi başvurusu ve yasal kayıt amaçlarıyla işler.</p><p>KVKK kapsamındaki başvuru haklarınız için iletişim kanallarımızdan bize ulaşabilirsiniz.</p>',
        ],
        'cerez-politikasi' => [
            'title' => 'Çerez Politikası',
            'subtitle' => 'Web sitesi deneyimi ve analitik kullanımı',
            'meta_description' => 'RAYU Tarım çerez politikası.',
            'content' => '<p>Web sitemizde zorunlu oturum çerezleri, güvenlik amaçlı kayıtlar ve kullanıcı deneyimini geliştirmeye yönelik ölçümleme araçları kullanılabilir.</p><p>Tarayıcı ayarlarınızdan çerez tercihlerinizi yönetebilirsiniz.</p>',
        ],
    ];

    foreach ($pages as $slug => $page) {
        $pages[$slug] = $page + [
            'slug' => $slug,
            'template' => 'default',
            'hero_image' => '',
            'og_image' => '',
            'is_active' => 1,
        ];
    }
    return $pages;
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
    try {
        $rows = ru_fetch_all(
            'SELECT * FROM ' . ru_t('menu') . '
             WHERE location = ? AND is_active = 1
             ORDER BY sort_order ASC, id ASC',
            [$location]
        );
    } catch (Throwable) {
        $rows = [];
    }
    if (!$rows) {
        $rows = ru_default_menu_items($location);
    } elseif (in_array($location, ['header', 'footer_1', 'footer_2', 'footer_3'], true)) {
        $seen = [];
        foreach ($rows as $row) {
            $seen[(string)($row['url'] ?? '')] = true;
        }
        foreach (ru_default_menu_items($location) as $item) {
            if (!isset($seen[$item['url']])) {
                $rows[] = $item;
            }
        }
    }
    return $cache[$location] = $rows;
}

function ru_default_menu_items(string $location): array
{
    $sets = [
        'header' => [
            ['label' => 'Anasayfa', 'url' => '/', 'target' => '_self'],
            ['label' => 'Ürünler', 'url' => '/urunler', 'target' => '_self'],
            ['label' => 'Markalar', 'url' => '/markalar', 'target' => '_self'],
            ['label' => '2. El', 'url' => '/ikinci-el', 'target' => '_self'],
            ['label' => 'Kurumsal', 'url' => '/hakkimizda', 'target' => '_self'],
            ['label' => 'İletişim', 'url' => '/iletisim', 'target' => '_self'],
        ],
        'footer_1' => [
            ['label' => 'Hakkımızda', 'url' => '/hakkimizda', 'target' => '_self'],
            ['label' => 'Misyon & Vizyon', 'url' => '/misyon-vizyon', 'target' => '_self'],
            ['label' => 'Markalar', 'url' => '/markalar', 'target' => '_self'],
            ['label' => 'Tedarikçi Başvurusu', 'url' => '/tedarikci-basvurusu', 'target' => '_self'],
            ['label' => 'İnsan Kaynakları', 'url' => '/insan-kaynaklari', 'target' => '_self'],
        ],
        'footer_2' => [
            ['label' => 'Tüm Ürünler', 'url' => '/urunler', 'target' => '_self'],
            ['label' => 'Traktör & Ekipman', 'url' => '/urunler/traktor-ekipman', 'target' => '_self'],
            ['label' => 'Toprak İşleme', 'url' => '/urunler/toprak-isleme', 'target' => '_self'],
            ['label' => 'Zirai İlaçlama', 'url' => '/urunler/ilaclama', 'target' => '_self'],
            ['label' => '2. El', 'url' => '/ikinci-el', 'target' => '_self'],
        ],
        'footer_3' => [
            ['label' => 'Gizlilik', 'url' => '/gizlilik', 'target' => '_self'],
            ['label' => 'KVKK', 'url' => '/kvkk', 'target' => '_self'],
            ['label' => 'Çerez Politikası', 'url' => '/cerez-politikasi', 'target' => '_self'],
        ],
    ];

    return array_map(static function (array $item): array {
        return $item + ['id' => 0, 'location' => '', 'is_active' => 1];
    }, $sets[$location] ?? []);
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
