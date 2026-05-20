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
            'content' => '<p>RAYU Tarım, tek bir üretici vitrini olmak yerine üretici, bayi, ithalatçı ve çiftçiyi aynı ticari akışta buluşturan çok markalı tarım satış platformu olarak konumlanır.</p><p>Amacımız; traktörden toprağa, ilaçlamadan hasada, yedek parçadan ikinci ele kadar üreticinin ihtiyacını doğru marka, doğru fiyat, doğru teslimat ve doğru servis güvencesiyle karşılamaktır.</p><h2>Çalışma modelimiz</h2><p>Ürün talebini teknik ihtiyaç, bölge, sezon, bütçe ve servis erişimiyle birlikte değerlendirir; uygun firmalardan teklifleri toplar, karşılaştırır ve satın alma kararını netleştiririz. RAYU tarafsız bir satış koordinasyonu kurar: alıcı zaman kazanır, firma doğru müşteriye ulaşır, satış sonrası süreç kayıt altında kalır.</p><h2>Kimlerle çalışıyoruz?</h2><ul><li>Tarım makinesi üreticileri ve ithalatçıları</li><li>Bölge bayileri ve distribütörler</li><li>Yedek parça ve servis tedarikçileri</li><li>Kurumsal ikinci el makine satıcıları</li><li>Hassas tarım, drone, sensör ve akıllı ekipman firmaları</li></ul><h2>RAYU farkı</h2><ul><li>Çok markalı ürün havuzu</li><li>Tedarikçi ve bayi başvuru altyapısı</li><li>Kurumsal teklif ve talep yönetimi</li><li>Yeni, ikinci el, yedek parça ve servis akışının tek çatı altında toplanması</li></ul>',
        ],
        'misyon-vizyon' => [
            'title' => 'Misyon & Vizyon',
            'subtitle' => 'Tarım ticaretinde güvenilir aracı kurum standardı',
            'meta_description' => 'RAYU Tarım misyon ve vizyonu: Türkiye genelinde tarım makineleri ve ekipmanlarında güvenilir çok markalı satış ağı kurmak.',
            'content' => '<h2>Misyonumuz</h2><p>Çiftçinin doğru ürüne, üreticinin doğru müşteriye, bayinin doğru satış kanalına ulaşmasını sağlayan şeffaf ve güvenilir bir tarım ticareti altyapısı kurmak.</p><h2>Vizyonumuz</h2><p>Türkiye genelinde tarım makineleri, zirai ilaçlama ekipmanları, yedek parça, servis ve ikinci el satışında ilk akla gelen çok markalı kurumsal platform olmak.</p><h2>İlkelerimiz</h2><ul><li>Markalar arası şeffaf karşılaştırma</li><li>Belgelendirilebilir teklif ve teslimat süreci</li><li>Satış sonrası servis ve parça sürekliliği</li><li>Üretici, bayi ve çiftçi için kazan-kazan modeli</li></ul>',
        ],
        'sektor-analizi' => [
            'title' => 'Sektör Analizi',
            'subtitle' => 'Tarım makineleri pazarında güncel eğilimler',
            'meta_description' => 'Tarım makineleri sektöründe çok markalı satış, ikinci el, servis, yedek parça ve dijital teklif eğilimleri.',
            'content' => '<p>Tarım makineleri pazarı artık yalnızca ekipman satışıyla değil; finansman, servis, yedek parça, ikinci el değeri ve dijital teklif yönetimiyle birlikte şekilleniyor. Küresel pazarda hassas tarım, telemetri, otomasyon ve IoT destekli ekipmanlar öne çıkarken, Türkiye pazarında alıcı kararını çoğu zaman kredi erişimi, teslimat süresi, servis ağı ve toplam sahip olma maliyeti belirliyor.</p><h2>RAYU bu tabloya nasıl cevap verir?</h2><p>RAYU, alıcının tek tek firma gezmesini beklemez. Talebi toplar, marka ve tedarikçi seçeneklerini karşılaştırır, yeni ve ikinci el alternatifleri aynı dosyada değerlendirir, satış sonrası servis ve parça sürecini görünür hale getirir.</p><h2>Odak alanlarımız</h2><ul><li>Çok markalı teklif karşılaştırması</li><li>Ekspertizli ikinci el makine akışı</li><li>Yedek parça ve servis sürekliliği</li><li>Bölgesel bayi ve tedarikçi eşleştirme</li><li>Hassas tarım ve verimlilik odaklı ekipman seçimi</li></ul>',
        ],
        'markalar' => [
            'title' => 'Markalar ve Tedarik Ağı',
            'subtitle' => 'Farklı firmaların ürünlerini tek satış standardında buluşturuyoruz',
            'meta_description' => 'RAYU Tarım markalar ve tedarik ağı: tarım makineleri, ekipman, yedek parça ve ikinci el ürünlerde çok markalı satış platformu.',
            'content' => '<p>RAYU Tarım; yerli üreticiler, ithalatçılar, bölge bayileri, yedek parça tedarikçileri, servis firmaları ve ikinci el makine sahipleri için tek merkezli satış kanalı oluşturur. Platformun amacı markaları karıştırmak değil, her markayı doğru teknik bilgi ve güvenilir ticari süreçle sunmaktır.</p><h2>Platforma alınan ürün grupları</h2><ul><li>Traktör, ön yükleyici ve güç aktarım ekipmanları</li><li>Toprak işleme, ekim, gübreleme ve hasat ekipmanları</li><li>Zirai ilaçlama makineleri, pülverizatör, atomizör ve uygulama ekipmanları</li><li>Yedek parça, sarf malzeme, bakım ve servis paketleri</li><li>Ekspertizli ikinci el tarım makineleri</li></ul><h2>Marka kabul kriterleri</h2><p>RAYU ağına dahil edilen ürünlerde teknik bilgi doğruluğu, garanti koşulları, servis erişimi, parça temini ve teslimat taahhüdü dikkate alınır. Böylece alıcı yalnızca fiyatı değil, ürünün sezon içindeki gerçek çalışma güvenliğini de karşılaştırabilir.</p><h2>Nasıl çalışır?</h2><p>Tedarikçi ürününü, teknik bilgisini ve ticari şartlarını iletir. RAYU ekibi ürün sınıflandırmasını, satış metnini, talep akışını ve teklif yönetimini kurumsal standartla yönetir.</p><p><a class="btn btn--primary" href="/tedarikci-basvurusu">Tedarikçi başvurusu yap</a></p>',
        ],
        'tedarikci-basvurusu' => [
            'title' => 'Tedarikçi Başvurusu',
            'subtitle' => 'Ürünlerinizi RAYU satış ağına dahil edin',
            'meta_description' => 'Tarım makineleri, ekipman ve yedek parça firmaları için RAYU Tarım tedarikçi başvuru sayfası.',
            'content' => '<p>Tarım makinesi, ekipman, yedek parça, zirai uygulama teknolojisi veya ikinci el ürün portföyünüz varsa RAYU satış ağına başvurabilirsiniz. Başvurular yalnızca listeleme amacıyla değil; satışa dönüşebilecek doğru ürün bilgisi, doğru bölge ve doğru teklif yapısı için değerlendirilir.</p><h2>Kimler başvurabilir?</h2><ul><li>Üretici ve ithalatçı firmalar</li><li>Bölge bayileri ve distribütörler</li><li>Servis ve yedek parça tedarikçileri</li><li>Kurumsal ikinci el makine satıcıları</li><li>Hassas tarım, sensör, drone ve akıllı ekipman firmaları</li></ul><h2>Başvuru için gereken bilgiler</h2><p>Firma adı, şehir, ürün grupları, marka bilgisi, stok/teslimat durumu, garanti ve servis koşulları, parça tedarik süresi, hedef bölgeler ve satış temsilcisi iletişim bilgileri yeterlidir.</p><h2>Değerlendirme süreci</h2><ol><li>Ürün ve firma bilgileri alınır.</li><li>Teknik bilgi, görsel ve ticari şartlar kontrol edilir.</li><li>Ürün RAYU katalog yapısına uygun şekilde sınıflandırılır.</li><li>Gelen alıcı talepleri uygun tedarikçilerle eşleştirilir.</li></ol><p><a class="btn btn--primary" href="/iletisim?type=supplier">Başvuru formuna git</a></p>',
        ],
        'tarihce' => [
            'title' => 'Tarihçe',
            'subtitle' => 'Saha ticaretinden dijital tarım pazaryerine',
            'meta_description' => 'RAYU Tarım tarihçesi ve çok markalı tarım ticareti yaklaşımı.',
            'content' => '<p>RAYU Tarım yaklaşımı, tarım makineleri ticaretindeki en temel sorundan doğar: alıcı doğru ekipmanı bulmak için çok fazla firma ile görüşmek zorunda kalır, firmalar ise doğru müşteriye ulaşmakta zorlanır.</p><p>Bu ihtiyaçtan hareketle RAYU; ürün listeleyen bir vitrin yerine talep, tedarikçi, teklif, teslimat, servis ve yedek parça sürecini tek dosyada toplayan bir satış modeli geliştirmeyi hedefler.</p><h2>Dönüşüm çizgimiz</h2><ul><li>Saha ihtiyaçlarının okunması</li><li>Çok markalı ürün ve tedarikçi yapısının kurulması</li><li>Yeni ve ikinci el makinelerin aynı satın alma akışında değerlendirilmesi</li><li>Servis ve yedek parça bilgisinin satış kararına dahil edilmesi</li><li>Dijital teklif ve talep yönetiminin standart hale getirilmesi</li></ul>',
        ],
        'kalite-politikasi' => [
            'title' => 'Kalite Politikası',
            'subtitle' => 'Listeleme değil, güvenilir satış standardı',
            'meta_description' => 'RAYU Tarım kalite politikası: ürün bilgisi doğruluğu, tedarikçi güvenilirliği, servis ve satış sonrası takip.',
            'content' => '<p>RAYU kalite anlayışı, ürünü yalnızca katalogda göstermekle sınırlı değildir. Alıcının karar verebilmesi için teknik bilgi, marka güvenilirliği, teslimat koşulları, garanti, servis ve yedek parça erişimi birlikte değerlendirilir.</p><h2>Kalite ilkelerimiz</h2><ul><li>Ürün bilgilerinde doğruluk ve güncellik</li><li>Tedarikçi ve bayi bilgilerinde şeffaflık</li><li>Tekliflerde fiyat, teslimat, garanti ve servis ayrımının net gösterilmesi</li><li>İkinci elde ekspertiz, fotoğraf, belge ve çalışma durumu kontrolü</li><li>Satış sonrası parça ve servis sürecinin izlenebilir olması</li></ul><h2>Tedarikçi değerlendirme standardı</h2><p>Platforma dahil olan firmalarda ürün grubu, teslimat bölgesi, garanti yaklaşımı, servis erişimi, yedek parça sürekliliği, görsel ve teknik doküman kalitesi dikkate alınır. RAYU, alıcının yalnızca fiyatı değil toplam sahip olma maliyetini de görmesini hedefler.</p><h2>İkinci el kalite yaklaşımı</h2><p>İkinci elde makine geçmişi, çalışma saati, hidrolik ve şase durumu, aşınan parçalar, belge seti ve fotoğraf standardı kontrol edilir. Amaç en ucuz ürünü öne çıkarmak değil, üreticinin sezon boyunca güvenle kullanabileceği doğru seçeneği bulmasını sağlamaktır.</p>',
        ],
        'insan-kaynaklari' => [
            'title' => 'İnsan Kaynakları',
            'subtitle' => 'Satış, saha ve dijital tarım ticareti ekibi',
            'meta_description' => 'RAYU Tarım insan kaynakları ve kariyer yaklaşımı.',
            'content' => '<p>RAYU Tarım; saha satış, bayi ilişkileri, ürün yönetimi, dijital pazarlama, servis koordinasyonu, müşteri deneyimi ve veri odaklı teklif yönetimi alanlarında büyüyen bir ekip kültürü kurar.</p><h2>Aradığımız profil</h2><ul><li>Tarım sektörünü, üretici alışkanlıklarını ve sezon baskısını anlayan</li><li>Teknik ürün bilgisini öğrenmeye açık</li><li>Fiyat, termin, servis ve garanti gibi ticari detayları net yönetebilen</li><li>Şeffaf iletişim kuran ve takip disiplinine sahip</li></ul><h2>Çalışma alanları</h2><p>Saha satış, çağrı ve teklif yönetimi, tedarikçi ilişkileri, içerik ve katalog yönetimi, ikinci el ekspertiz koordinasyonu, servis ve yedek parça operasyonu RAYU ekibinin ana çalışma alanlarıdır.</p><p>Başvurularınızı iletişim formu üzerinden veya ik@rayutarim.com adresine iletebilirsiniz.</p>',
        ],
        'gizlilik' => [
            'title' => 'Gizlilik Politikası',
            'subtitle' => 'Veri güvenliği ve iletişim gizliliği',
            'meta_description' => 'RAYU Tarım gizlilik politikası.',
            'content' => '<h2>Gizlilik yaklaşımımız</h2><p>RAYU Tarım web sitesi üzerinden iletilen iletişim, teklif, tedarikçi başvurusu, ikinci el ilan, servis ve yedek parça bilgilerini yalnızca talebin değerlendirilmesi, teklif hazırlanması, firma eşleştirmesi ve hizmet sunumu amacıyla işler.</p><h2>Toplanan bilgiler</h2><p>Ad soyad, firma adı, telefon, e-posta, şehir, ürün grubu, makine bilgileri, mesaj içeriği, talep konusu, IP adresi ve teknik güvenlik kayıtları işlenebilir. Tedarikçi başvurularında marka, ürün, stok, teslimat bölgesi, servis ve garanti bilgileri de alınabilir.</p><h2>Paylaşım ilkesi</h2><p>Bilgiler ticari amaçla üçüncü kişilere satılmaz. Talebin niteliğine göre, teklif hazırlanması veya servis yönlendirmesi için yalnızca ilgili tedarikçi, bayi ya da hizmet sağlayıcı ile sınırlı veri paylaşımı yapılabilir.</p><h2>Güvenlik</h2><p>Veriler rol bazlı yönetim paneli, güvenli oturum, erişim kısıtları ve sunucu güvenlik önlemleriyle korunur. Yetkisiz erişim riskini azaltmak için admin alanı, yedekler ve sistem klasörleri arama motorlarına kapalı tutulur.</p>',
        ],
        'kvkk' => [
            'title' => 'KVKK Aydınlatma Metni',
            'subtitle' => 'Kişisel verilerin işlenmesi hakkında bilgilendirme',
            'meta_description' => 'RAYU Tarım KVKK aydınlatma metni.',
            'content' => '<h2>Veri sorumlusu</h2><p>RAYU Tarım, web sitesi ve satış süreçleri kapsamında iletilen kişisel verileri veri sorumlusu sıfatıyla işler.</p><h2>İşlenen veri kategorileri</h2><p>Kimlik ve iletişim bilgileri, firma bilgileri, şehir, ürün veya hizmet talebi, ikinci el makine bilgileri, teklif ve mesaj içerikleri, işlem güvenliği kayıtları işlenebilir.</p><h2>İşleme amaçları</h2><ul><li>Teklif hazırlama ve talep değerlendirme</li><li>Alıcı ile uygun tedarikçi veya bayi eşleştirme</li><li>Tedarikçi ve firma başvurularını inceleme</li><li>İkinci el başvurularında makine dosyası oluşturma</li><li>Servis, yedek parça ve satış sonrası süreçleri yürütme</li><li>Yasal yükümlülükleri ve kayıt güvenliğini yerine getirme</li></ul><h2>Aktarım</h2><p>Kişisel veriler, talebin karşılanması için gerekli olduğu ölçüde ilgili tedarikçi, bayi, servis sağlayıcı veya lojistik çözüm ortağı ile paylaşılabilir. Bunun dışında veriler ticari amaçla satılmaz.</p><h2>Haklarınız</h2><p>KVKK kapsamındaki erişim, düzeltme, silme, itiraz ve bilgi talebi haklarınız için iletişim sayfasındaki kanallardan bize ulaşabilirsiniz.</p>',
        ],
        'cerez-politikasi' => [
            'title' => 'Çerez Politikası',
            'subtitle' => 'Web sitesi deneyimi ve analitik kullanımı',
            'meta_description' => 'RAYU Tarım çerez politikası.',
            'content' => '<h2>Çerez kullanımı</h2><p>RAYU Tarım web sitesi; güvenli oturum, form güvenliği, admin panel işlevleri, performans ölçümü ve kullanıcı deneyimini iyileştirme amacıyla çerezlerden yararlanabilir.</p><h2>Zorunlu çerezler</h2><p>Oturum güvenliği, CSRF koruması, form gönderimleri ve yönetim paneli işlemleri için gerekli çerezlerdir. Bu çerezler kapatıldığında bazı site işlevleri çalışmayabilir.</p><h2>Performans ve ölçümleme</h2><p>Site trafiğini, en çok ziyaret edilen sayfaları ve kullanıcıların hangi ürün gruplarına ilgi gösterdiğini anlamak için anonim veya sınırlı ölçümleme araçları kullanılabilir.</p><h2>Tercihlerinizi yönetme</h2><p>Tarayıcı ayarlarınız üzerinden çerezleri silebilir, engelleyebilir veya belirli siteler için izinleri değiştirebilirsiniz. Mevzuat gereği açık rıza gerektiren çerezler için kullanıcı tercihlerine uygun hareket edilir.</p>',
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
            ['label' => 'Sektör Analizi', 'url' => '/sektor-analizi', 'target' => '_self'],
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
