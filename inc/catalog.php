<?php
/**
 * RAYU TARIM MAKINELERI - Public katalog verisi
 */
declare(strict_types=1);

function ru_catalog_categories(): array
{
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
    return array_values(array_filter(
        ru_catalog_products(),
        static fn (array $product): bool => $product['category'] === $category
    ));
}
