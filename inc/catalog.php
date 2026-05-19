<?php
/**
 * RAYU TARIM MAKINELERI - Public katalog verisi
 */
declare(strict_types=1);

function ru_catalog_categories(): array
{
    try {
        $rows = ru_fetch_all('SELECT * FROM ' . ru_t('product_categories') . ' WHERE is_active = 1 ORDER BY sort_order ASC, title ASC');
        if ($rows) {
            $out = [];
            foreach ($rows as $row) {
                $out[(string)$row['slug']] = [
                    'id' => (int)$row['id'],
                    'title' => (string)$row['title'],
                    'summary' => (string)($row['summary'] ?? ''),
                    'icon' => (string)($row['icon'] ?? ''),
                ];
            }
            return $out;
        }
    } catch (Throwable) {
    }
    return [
        'toprak-isleme' => [
            'title' => 'Toprak İşleme',
            'summary' => 'Pulluk, kültivatör, çizel, merdane ve tarla hazırlık ekipmanları.',
            'icon' => 'M4 18c5-8 10-11 16-12-2 7-1 12 3 16 4-4 1-9 2-16 8Z',
        ],
        'ekim-dikim' => [
            'title' => 'Ekim ve Dikim',
            'summary' => 'Hassas ekim makineleri, mibzerler ve sezon verimini artıran çözümler.',
            'icon' => 'M12 3c4 4 6 8 6 12a6 6 0 0 1-12 0c0-4 2-8 6-12Z',
        ],
        'ilaclama' => [
            'title' => 'Zirai İlaçlama',
            'summary' => 'Lisanslı ürün portföyü, atomizörler ve doğru uygulama ekipmanları.',
            'icon' => 'M7 3h10v4l-2 3v8a3 3 0 0 1-6 0v-8L7 7V3Z',
        ],
        'yedek-parca' => [
            'title' => 'Yedek Parça ve Servis',
            'summary' => 'Sezon içinde hızlı tedarik, teknik servis ve bakım planlama.',
            'icon' => 'M19 13a7 7 0 1 1-8-8l2 2-2 4 4-2 2 2a7 7 0 0 1 2 4Z',
        ],
    ];
}

function ru_catalog_products(): array
{
    try {
        $rows = ru_fetch_all('SELECT p.*, c.slug category_slug FROM ' . ru_t('products') . ' p LEFT JOIN ' . ru_t('product_categories') . ' c ON c.id = p.category_id WHERE p.is_active = 1 ORDER BY p.is_featured DESC, p.sort_order ASC, p.name ASC');
        if ($rows) {
            return array_map('ru_catalog_product_from_row', $rows);
        }
    } catch (Throwable) {
    }
    return [
        [
            'category' => 'toprak-isleme',
            'name' => 'Ağır Tip Çizel',
            'badge' => 'Yoğun toprak',
            'summary' => 'Derin patlatma, düşük yakıt tüketimi ve güçlendirilmiş şase.',
            'specs' => ['9-13 ayak', '70-120 HP', 'Opsiyonel merdane'],
        ],
        [
            'category' => 'toprak-isleme',
            'name' => 'Diskli Goble',
            'badge' => 'Saha hazırlığı',
            'summary' => 'Anız parçalama ve homojen karıştırma için dengeli disk geometrisi.',
            'specs' => ['20-32 disk', 'Hidrolik ayar', 'Ağır hizmet rulman'],
        ],
        [
            'category' => 'ekim-dikim',
            'name' => 'Pnömatik Hassas Ekim',
            'badge' => 'Yüksek verim',
            'summary' => 'Tohum aralığı kontrolü, gübre ünitesi ve sezonluk kalibrasyon desteği.',
            'specs' => ['4-8 sıra', 'Vakum sistem', 'Gübre deposu'],
        ],
        [
            'category' => 'ilaclama',
            'name' => 'Asılır Tip Tarla Pülverizatörü',
            'badge' => 'Bitki sağlığı',
            'summary' => 'Dengeli bom yapısı ve kontrollü uygulama için nozül seçenekleri.',
            'specs' => ['600-1000 L', '12-16 m bom', 'Basınç regülatörü'],
        ],
        [
            'category' => 'yedek-parca',
            'name' => 'Sezon Bakım Paketi',
            'badge' => 'Servis',
            'summary' => 'Aşınan parçalar, rulman, bıçak, hortum ve saha servis planı.',
            'specs' => ['Hızlı sevk', 'Orijinal parça', 'Servis kaydı'],
        ],
    ];
}

function ru_catalog_products_by_category(string $category): array
{
    try {
        $rows = ru_fetch_all('SELECT p.*, c.slug category_slug FROM ' . ru_t('products') . ' p LEFT JOIN ' . ru_t('product_categories') . ' c ON c.id = p.category_id WHERE p.is_active = 1 AND c.slug = ? ORDER BY p.sort_order ASC, p.name ASC', [$category]);
        if ($rows) {
            return array_map('ru_catalog_product_from_row', $rows);
        }
    } catch (Throwable) {
    }
    return array_values(array_filter(
        ru_catalog_products(),
        static fn (array $product): bool => $product['category'] === $category
    ));
}

function ru_catalog_product_from_row(array $row): array
{
    $specs = json_decode((string)($row['specs_json'] ?? '[]'), true);
    return [
        'id' => (int)$row['id'],
        'category' => (string)($row['category_slug'] ?? ''),
        'slug' => (string)$row['slug'],
        'name' => (string)$row['name'],
        'badge' => (string)($row['badge'] ?? ''),
        'summary' => (string)($row['summary'] ?? ''),
        'description' => (string)($row['description'] ?? ''),
        'specs' => is_array($specs) ? $specs : [],
        'image' => (string)($row['image'] ?? ''),
    ];
}
