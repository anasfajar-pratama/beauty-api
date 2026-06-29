<?php

namespace Database\Seeders;

use App\Models\Subcategory;
use App\Models\Product;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // $this->call(SeedMissingData::class);

        $subcategories = [
            ['category' => 'Wanita', 'name' => 'Cleanser', 'slug' => 'cleanser'],
            ['category' => 'Wanita', 'name' => 'Toner', 'slug' => 'toner'],
            ['category' => 'Wanita', 'name' => 'Essence', 'slug' => 'essence'],
            ['category' => 'Wanita', 'name' => 'Serum', 'slug' => 'serum'],
            ['category' => 'Wanita', 'name' => 'Moisturizer', 'slug' => 'moisturizer'],
            ['category' => 'Wanita', 'name' => 'Sunscreen', 'slug' => 'sunscreen'],
            ['category' => 'Wanita', 'name' => 'Exfoliator', 'slug' => 'exfoliator'],
            ['category' => 'Wanita', 'name' => 'Masker Wajah', 'slug' => 'masker-wajah'],
            ['category' => 'Wanita', 'name' => 'Perawatan Jerawat', 'slug' => 'perawatan-jerawat'],
            ['category' => 'Wanita', 'name' => 'Perawatan Mata', 'slug' => 'perawatan-mata'],
            ['category' => 'Wanita', 'name' => 'Perawatan Bibir', 'slug' => 'perawatan-bibir'],
            ['category' => 'Wanita', 'name' => 'Perawatan Khusus', 'slug' => 'perawatan-khusus'],
            ['category' => 'Wanita', 'name' => 'Perawatan Leher', 'slug' => 'perawatan-leher'],
            ['category' => 'Wanita', 'name' => 'Perawatan Tubuh', 'slug' => 'perawatan-tubuh'],
            ['category' => 'Pria', 'name' => 'Shaving', 'slug' => 'shaving'],
            ['category' => 'Pria', 'name' => 'Moisturizer', 'slug' => 'pria-moisturizer'],
            ['category' => 'Pria', 'name' => 'Cleanser', 'slug' => 'pria-cleanser'],
            ['category' => 'Pria', 'name' => 'Serum', 'slug' => 'pria-serum'],
            ['category' => 'Pria', 'name' => 'Sunscreen', 'slug' => 'pria-sunscreen'],
            ['category' => 'Pria', 'name' => 'Beard Care', 'slug' => 'beard-care'],
            ['category' => 'Pria', 'name' => 'Face Mask', 'slug' => 'pria-face-mask'],
            ['category' => 'Pria', 'name' => 'Eye Care', 'slug' => 'pria-eye-care'],
            ['category' => 'Pria', 'name' => 'Body Care', 'slug' => 'pria-body-care'],
            ['category' => 'Anak', 'name' => 'Gentle Cleanser', 'slug' => 'gentle-cleanser'],
            ['category' => 'Anak', 'name' => 'Moisturizer', 'slug' => 'anak-moisturizer'],
            ['category' => 'Anak', 'name' => 'Sunscreen', 'slug' => 'anak-sunscreen'],
            ['category' => 'Anak', 'name' => 'Baby Oil', 'slug' => 'baby-oil'],
            ['category' => 'Anak', 'name' => 'Baby Shampoo', 'slug' => 'baby-shampoo'],
            ['category' => 'Anak', 'name' => 'Body Wash', 'slug' => 'anak-body-wash'],
            ['category' => 'Anak', 'name' => 'Baby Lotion', 'slug' => 'baby-lotion'],
            ['category' => 'Anak', 'name' => 'Baby Cream', 'slug' => 'baby-cream'],
        ];

        foreach ($subcategories as $s) {
            Subcategory::firstOrCreate(['slug' => $s['slug']], $s);
        }

        $subcategoryMap = [
            'Wanita' => ['serum' => 'Serum Glow Intensif', 'moisturizer' => 'Moisturizer Intensif', 'cleanser' => 'Face Wash Pencerah'],
            'Pria' => ['pria-cleanser' => "Men's Active Cleanser", 'pria-moisturizer' => "Men's Hydra Gel"],
            'Anak' => ['gentle-cleanser' => 'Kids Gentle Wash'],
        ];

        $products = [
            ['name' => 'Serum Glow Intensif',   'category' => 'Wanita', 'subcategory_slug' => 'serum', 'tagline' => 'Cerahkan kulit dalam 7 hari',     'ingredients' => 'Niacinamide 10%, Vitamin C, Hyaluronic Acid, Aloe Vera Extract, Rose Hip Oil', 'benefits' => json_encode(['Mencerahkan kulit kusam','Meratakan warna kulit','Melembapkan intensif','Anti penuaan dini']), 'how_to_use' => json_encode(['Bersihkan wajah terlebih dahulu','Oleskan 3-4 tetes serum ke wajah','Tepuk-tepuk hingga meresap sempurna','Lanjutkan dengan pelembap']), 'sort_order' => 1, 'is_new' => true, 'price' => 149000],
            ['name' => 'Moisturizer Intensif',  'category' => 'Wanita', 'subcategory_slug' => 'moisturizer', 'tagline' => 'Kelembapan tahan 24 jam',         'ingredients' => 'Hyaluronic Acid, Ceramide, Green Tea Extract, Panthenol, Peptide',               'benefits' => json_encode(['Melembapkan sepanjang hari','Memperkuat skin barrier','Menghaluskan tekstur kulit','Mudah meresap']),                                       'how_to_use' => json_encode(['Gunakan setelah serum','Ambil secukupnya','Ratakan ke seluruh wajah dan leher','Gunakan pagi dan malam']),                       'sort_order' => 2, 'is_promo' => true, 'price' => 179000],
            ['name' => 'Face Wash Pencerah',    'category' => 'Wanita', 'subcategory_slug' => 'cleanser', 'tagline' => 'Bersih maksimal, kulit lembut',   'ingredients' => 'Hyaluronic Acid, Ceramide, Green Tea Extract, Panthenol, Peptide',               'benefits' => json_encode(['Membersihkan kotoran dan debu','Menjaga kelembapan alami','Kulit terasa lembut setelah cuci','Cocok pagi dan malam']),                 'how_to_use' => json_encode(['Basahi wajah dengan air hangat','Tuangkan sabun ke tangan','Pijat lembut ke wajah','Bilas hingga bersih']),                         'sort_order' => 3, 'price' => 89000],
            ['name' => "Men's Active Cleanser", 'category' => 'Pria',   'subcategory_slug' => 'pria-cleanser', 'tagline' => 'Segar dan bersih seharian',       'ingredients' => 'Charcoal Extract, Tea Tree Oil, Salicylic Acid, Glycerin, Menthol',              'benefits' => json_encode(['Membersihkan pori terdalam','Mengurangi minyak berlebih','Mencegah jerawat','Menyegarkan kulit']),                                    'how_to_use' => json_encode(['Basahi wajah','Tuangkan secukupnya ke telapak tangan','Pijat lembut pada wajah','Bilas hingga bersih']),                             'sort_order' => 4, 'is_new' => true, 'price' => 99000],
            ['name' => "Men's Hydra Gel",       'category' => 'Pria',   'subcategory_slug' => 'pria-moisturizer', 'tagline' => 'Hidrasi tanpa rasa lengket',      'ingredients' => 'Charcoal Extract, Tea Tree Oil, Salicylic Acid, Glycerin, Menthol',              'benefits' => json_encode(['Hidrasi cepat meresap','Tidak lengket','Cocok kulit aktif','Aroma segar']),                                                           'how_to_use' => json_encode(['Cuci wajah','Ambil seukuran kacang','Ratakan ke wajah','Biarkan meresap']),                                                        'sort_order' => 5, 'price' => 129000],
            ['name' => 'Kids Gentle Wash',      'category' => 'Anak',   'subcategory_slug' => 'gentle-cleanser', 'tagline' => 'Lembut untuk kulit si kecil',     'ingredients' => 'Oat Extract, Calendula, Chamomile, Aloe Vera, Vitamin E',                       'benefits' => json_encode(['Sangat lembut di kulit','Menenangkan kulit sensitif','Tear-free (tidak pedih di mata)','Menjaga kelembapan alami']),                  'how_to_use' => json_encode(['Gunakan saat mandi','Usapkan dengan lembut ke seluruh tubuh','Bilas dengan air hangat','Keringkan dengan handuk lembut']),          'sort_order' => 6, 'is_promo' => true, 'price' => 79000],
        ];

        foreach ($products as $p) {
            $slug = $p['subcategory_slug'] ?? null;
            $subcategoryId = $slug ? Subcategory::where('slug', $slug)->value('id') : null;
            unset($p['subcategory_slug']);
            $p['subcategory_id'] = $subcategoryId;
            Product::firstOrCreate(['name' => $p['name']], $p);
        }

        $testimonials = [
            ['name' => 'Siti A.',  'content' => 'Kulit saya tidak pernah secerah ini. Serum glow-nya benar-benar bekerja!',                      'rating' => '5', 'is_active' => true],
            ['name' => 'Alex P.',  'content' => 'Produk pria sangat praktis. Tidak lengket dan menyegarkan setelah olahraga.',                   'rating' => '5', 'is_active' => true],
            ['name' => 'Rina M.',  'content' => 'Sabun mandi anak sangat lembut, anak saya tidak pernah komplain pedih di mata lagi.',           'rating' => '5', 'is_active' => true],
            ['name' => 'Dewi K.',  'content' => 'Pengalaman mewah dengan harga yang sangat sepadan. Kemasannya sangat cantik.',                  'rating' => '5', 'is_active' => true],
        ];

        foreach ($testimonials as $t) {
            Testimonial::firstOrCreate(['name' => $t['name'], 'content' => $t['content']], $t);
        }
    }
}
