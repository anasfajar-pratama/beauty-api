<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use App\Models\Setting;
use App\Models\Hero;
use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'Lihat Produk', 'slug' => 'view_products', 'group' => 'products'],
            ['name' => 'Buat Produk', 'slug' => 'create_products', 'group' => 'products'],
            ['name' => 'Edit Produk', 'slug' => 'edit_products', 'group' => 'products'],
            ['name' => 'Hapus Produk', 'slug' => 'delete_products', 'group' => 'products'],

            ['name' => 'Lihat Subkategori', 'slug' => 'view_subcategories', 'group' => 'subcategories'],
            ['name' => 'Buat Subkategori', 'slug' => 'create_subcategories', 'group' => 'subcategories'],
            ['name' => 'Edit Subkategori', 'slug' => 'edit_subcategories', 'group' => 'subcategories'],
            ['name' => 'Hapus Subkategori', 'slug' => 'delete_subcategories', 'group' => 'subcategories'],

            ['name' => 'Lihat Testimoni', 'slug' => 'view_testimonials', 'group' => 'testimonials'],
            ['name' => 'Buat Testimoni', 'slug' => 'create_testimonials', 'group' => 'testimonials'],
            ['name' => 'Edit Testimoni', 'slug' => 'edit_testimonials', 'group' => 'testimonials'],
            ['name' => 'Hapus Testimoni', 'slug' => 'delete_testimonials', 'group' => 'testimonials'],

            ['name' => 'Lihat Galeri', 'slug' => 'view_gallery', 'group' => 'gallery'],
            ['name' => 'Buat Galeri', 'slug' => 'create_gallery', 'group' => 'gallery'],
            ['name' => 'Edit Galeri', 'slug' => 'edit_gallery', 'group' => 'gallery'],
            ['name' => 'Hapus Galeri', 'slug' => 'delete_gallery', 'group' => 'gallery'],

            ['name' => 'Lihat Homepage', 'slug' => 'view_homepage', 'group' => 'homepage'],
            ['name' => 'Edit Homepage', 'slug' => 'edit_homepage', 'group' => 'homepage'],

            ['name' => 'Lihat Admin', 'slug' => 'view_admins', 'group' => 'admins'],
            ['name' => 'Buat Admin', 'slug' => 'create_admins', 'group' => 'admins'],
            ['name' => 'Edit Admin', 'slug' => 'edit_admins', 'group' => 'admins'],
            ['name' => 'Hapus Admin', 'slug' => 'delete_admins', 'group' => 'admins'],

            ['name' => 'Lihat Role', 'slug' => 'view_roles', 'group' => 'roles'],
            ['name' => 'Buat Role', 'slug' => 'create_roles', 'group' => 'roles'],
            ['name' => 'Edit Role', 'slug' => 'edit_roles', 'group' => 'roles'],
            ['name' => 'Hapus Role', 'slug' => 'delete_roles', 'group' => 'roles'],

            ['name' => 'Lihat Pengaturan', 'slug' => 'view_settings', 'group' => 'settings'],
            ['name' => 'Buat Pengaturan', 'slug' => 'create_settings', 'group' => 'settings'],
            ['name' => 'Edit Pengaturan', 'slug' => 'edit_settings', 'group' => 'settings'],
            ['name' => 'Hapus Pengaturan', 'slug' => 'delete_settings', 'group' => 'settings'],

            ['name' => 'Lihat Log Aktivitas', 'slug' => 'view_activity_logs', 'group' => 'activity_logs'],
            ['name' => 'Lihat Brand', 'slug' => 'view_brands', 'group' => 'brands'],
            ['name' => 'Edit Brand', 'slug' => 'edit_brands', 'group' => 'brands'],
            ['name' => 'Lihat About', 'slug' => 'view_about', 'group' => 'about'],
            ['name' => 'Upload File', 'slug' => 'upload_files', 'group' => 'files'],

            // Hero permissions
            ['name' => 'Lihat Hero', 'slug' => 'view_heroes', 'group' => 'heroes'],
            ['name' => 'Buat Hero', 'slug' => 'create_heroes', 'group' => 'heroes'],
            ['name' => 'Edit Hero', 'slug' => 'edit_heroes', 'group' => 'heroes'],
            ['name' => 'Hapus Hero', 'slug' => 'delete_heroes', 'group' => 'heroes'],
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['slug' => $p['slug']], $p);
        }

        $allPermissionIds = Permission::pluck('id');
        $viewPermissionIds = Permission::where('slug', 'like', 'view_%')->pluck('id');

        // Super Admin — all permissions
        $superAdmin = Role::firstOrCreate(
            ['slug' => 'super-admin'],
            ['name' => 'Super Admin', 'description' => 'Akses penuh ke semua fitur']
        );
        $superAdmin->permissions()->sync($allPermissionIds);

        // Admin — all view + create & edit (no delete for admins/roles/settings)
        $adminRole = Role::firstOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Admin', 'description' => 'Akses manajemen konten tanpa hapus data sensitif']
        );
        $adminPerms = Permission::whereNotIn('slug', [
            'delete_admins', 'delete_roles',
            'create_admins', 'edit_admins',
            'create_roles', 'edit_roles',
            'delete_settings',
        ])->pluck('id');
        $adminRole->permissions()->sync($adminPerms);

        // Editor — only view + create & edit for products, subcategories, testimonials, gallery
        $editor = Role::firstOrCreate(
            ['slug' => 'editor'],
            ['name' => 'Editor', 'description' => 'Kelola konten produk, testimoni, dan galeri']
        );
        $editorPerms = Permission::whereIn('slug', [
            'view_products', 'create_products', 'edit_products',
            'view_subcategories', 'create_subcategories', 'edit_subcategories',
            'view_testimonials', 'create_testimonials', 'edit_testimonials',
            'view_gallery', 'create_gallery', 'edit_gallery',
            'view_homepage', 'edit_homepage',
            'view_heroes', 'edit_heroes',
            'upload_files',
        ])->pluck('id');
        $editor->permissions()->sync($editorPerms);

        // Assign super-admin to existing admin
        $admin = Admin::where('username', 'admin')->first();
        if ($admin && $admin->roles()->count() === 0) {
            $admin->roles()->attach($superAdmin->id);
        }

        // Default settings
        $defaultSettings = [
            ['key' => 'site_name', 'value' => 'Rindang Cemara Sukses', 'type' => 'string', 'group' => 'general', 'description' => 'Nama website'],
            ['key' => 'site_description', 'value' => 'Kecantikan untuk Setiap Jiwa', 'type' => 'text', 'group' => 'general', 'description' => 'Deskripsi website'],
            ['key' => 'site_logo', 'value' => '', 'type' => 'image', 'group' => 'general', 'description' => 'Logo website (URL)'],
            ['key' => 'seo_title', 'value' => 'Lumière - Kecantikan untuk Setiap Jiwa', 'type' => 'string', 'group' => 'seo', 'description' => 'SEO title default'],
            ['key' => 'seo_description', 'value' => 'Temukan produk kecantikan premium dengan bahan alami terbaik untuk wanita, pria, dan anak-anak.', 'type' => 'text', 'group' => 'seo', 'description' => 'SEO meta description'],
            ['key' => 'seo_keywords', 'value' => 'kecantikan, skincare, perawatan kulit, produk kecantikan, natural', 'type' => 'text', 'group' => 'seo', 'description' => 'SEO keywords (dipisah koma)'],
            ['key' => 'social_facebook', 'value' => '', 'type' => 'string', 'group' => 'social', 'description' => 'URL Facebook'],
            ['key' => 'social_instagram', 'value' => '', 'type' => 'string', 'group' => 'social', 'description' => 'URL Instagram'],
            ['key' => 'social_twitter', 'value' => '', 'type' => 'string', 'group' => 'social', 'description' => 'URL Twitter / X'],
            ['key' => 'social_youtube', 'value' => '', 'type' => 'string', 'group' => 'social', 'description' => 'URL YouTube'],
            ['key' => 'contact_email', 'value' => 'hello@rindangcemarasukses.com', 'type' => 'string', 'group' => 'contact', 'description' => 'Email kontak'],
            ['key' => 'contact_phone', 'value' => '', 'type' => 'string', 'group' => 'contact', 'description' => 'Nomor telepon'],
            ['key' => 'contact_address', 'value' => '', 'type' => 'text', 'group' => 'contact', 'description' => 'Alamat lengkap'],
            ['key' => 'contact_map_url', 'value' => '', 'type' => 'string', 'group' => 'contact', 'description' => 'Google Maps embed URL'],

            // Brand logos
            ['key' => 'brand_blisera_name', 'value' => 'BLISERA', 'type' => 'string', 'group' => 'brands', 'description' => 'Nama brand wanita'],
            ['key' => 'brand_pijar_nala_name', 'value' => 'PIJAR NALA', 'type' => 'string', 'group' => 'brands', 'description' => 'Nama brand anak & bayi'],
            ['key' => 'brand_fokka_name', 'value' => 'FOKKA', 'type' => 'string', 'group' => 'brands', 'description' => 'Nama brand pria'],
            ['key' => 'logo_blisera', 'value' => '/logo/blisera - text bawah.png', 'type' => 'image', 'group' => 'brands', 'description' => 'Logo brand BLISERA (Wanita)'],
            ['key' => 'logo_pijar_nala', 'value' => '/logo/pijar nala - text bawah.png', 'type' => 'image', 'group' => 'brands', 'description' => 'Logo brand PIJAR NALA (Anak)'],
            ['key' => 'logo_fokka', 'value' => '', 'type' => 'image', 'group' => 'brands', 'description' => 'Logo brand FOKKA (Pria)'],
            ['key' => 'hero_image_blisera', 'value' => '', 'type' => 'image', 'group' => 'brands', 'description' => 'Gambar hero slider BLISERA (Wanita)'],
            ['key' => 'hero_image_pijar_nala', 'value' => '', 'type' => 'image', 'group' => 'brands', 'description' => 'Gambar hero slider PIJAR NALA (Anak)'],
            ['key' => 'hero_image_fokka', 'value' => '', 'type' => 'image', 'group' => 'brands', 'description' => 'Gambar hero slider FOKKA (Pria)'],
            ['key' => 'logo_style_blisera', 'value' => 'rounded', 'type' => 'string', 'group' => 'brands', 'description' => 'Bentuk logo: circle, rounded, atau square'],
            ['key' => 'logo_style_pijar_nala', 'value' => 'rounded', 'type' => 'string', 'group' => 'brands', 'description' => 'Bentuk logo: circle, rounded, atau square'],
            ['key' => 'logo_style_fokka', 'value' => 'rounded', 'type' => 'string', 'group' => 'brands', 'description' => 'Bentuk logo: circle, rounded, atau square'],

            // Brand descriptions & taglines
            ['key' => 'brand_blisera_tagline', 'value' => 'Elegansi untuk Setiap Momen', 'type' => 'string', 'group' => 'brands', 'description' => 'Tagline brand BLISERA'],
            ['key' => 'brand_fokka_tagline', 'value' => 'Ketegasan dalam Gaya', 'type' => 'string', 'group' => 'brands', 'description' => 'Tagline brand FOKKA'],
            ['key' => 'brand_pijar_nala_tagline', 'value' => 'Ceria dan Berkilau', 'type' => 'string', 'group' => 'brands', 'description' => 'Tagline brand PIJAR NALA'],
            ['key' => 'brand_blisera_description', 'value' => 'BLISERA adalah rangkaian perawatan kulit premium yang dirancang khusus untuk wanita modern. Setiap produk menggabungkan bahan alami terbaik dengan teknologi mutakhir untuk menghasilkan kulit yang sehat, cerah, dan bercahaya.', 'type' => 'text', 'group' => 'brands', 'description' => 'Deskripsi brand BLISERA'],
            ['key' => 'brand_fokka_description', 'value' => 'FOKKA hadir untuk pria tangguh yang menginginkan perawatan praktis tanpa ribet. Dengan formula menyegarkan dan kemasan travel-friendly, FOKKA adalah pilihan tepat untuk gaya hidup aktif.', 'type' => 'text', 'group' => 'brands', 'description' => 'Deskripsi brand FOKKA'],
            ['key' => 'brand_pijar_nala_description', 'value' => 'PIJAR NALA lahir dari kepedulian terhadap kulit sensitif si kecil. Menggunakan bahan alami yang lembut dan aman, PIJAR NALA menjadikan waktu perawatan sebagai momen bermain yang menyenangkan.', 'type' => 'text', 'group' => 'brands', 'description' => 'Deskripsi brand PIJAR NALA'],
            ['key' => 'brand_blisera_about', 'value' => 'BLISERA percaya bahwa setiap wanita berhak tampil percaya diri dengan kulit yang sehat. Kami menghadirkan rangkaian perawatan yang terinspirasi dari ritual kecantikan klasik yang dipadukan dengan inovasi modern. Dari serum pencerah hingga pelembap intensif, setiap produk BLISERA dirancang dengan cinta dan ketelitian untuk memberikan pengalaman mewah setiap hari.', 'type' => 'text', 'group' => 'brands', 'description' => 'Tentang brand BLISERA (halaman brand)'],
            ['key' => 'brand_fokka_about', 'value' => 'FOKKA didesain untuk pria yang menghargai efisiensi tanpa mengorbankan kualitas. Dengan kemasan minimalis dan formula cepat meresap, FOKKA cocok untuk pria sibuk yang tetap ingin tampil prima. Kami percaya perawatan diri adalah investasi, bukan sekadar rutinitas.', 'type' => 'text', 'group' => 'brands', 'description' => 'Tentang brand FOKKA (halaman brand)'],
            ['key' => 'brand_pijar_nala_about', 'value' => 'Terinspirasi dari keceriaan anak-anak, PIJAR NALA menghadirkan produk perawatan yang aman, lembut, dan menyenangkan. Setiap formula kami uji secara dermatologis untuk memastikan keamanan pada kulit sensitif si kecil. Karena senyum mereka adalah prioritas utama kami.', 'type' => 'text', 'group' => 'brands', 'description' => 'Tentang brand PIJAR NALA (halaman brand)'],

            ['key' => 'legal_achievements', 'value' => '[]', 'type' => 'json', 'group' => 'legal', 'description' => 'Daftar legal & achievement (JSON)'],

            // Feature toggles
            ['key' => 'testimonial_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'features', 'description' => 'Aktifkan fitur tulis testimoni publik'],
        ];

        foreach ($defaultSettings as $s) {
            Setting::firstOrCreate(['key' => $s['key']], $s);
        }

        // Default heroes
        $defaultHeroes = [
            [
                'type' => 'brand',
                'brand_key' => 'blisera',
                'theme' => 'rose',
                'title' => 'Elegan & Mewah',
                'subtitle' => 'Untuk Wanita Modern',
                'description' => 'Rangkaian perawatan kulit premium dengan bahan alami terbaik untuk kecantikan yang bersinar.',
                'button_text' => 'Koleksi Wanita',
                'button_link' => '/brand/blisera',
                'logo_style' => 'rounded',
                'sort_order' => 0,
                'is_active' => true,
            ],
            [
                'type' => 'brand',
                'brand_key' => 'pijar_nala',
                'theme' => 'sky',
                'title' => 'Lembut & Aman',
                'subtitle' => 'Untuk Baby & Kids',
                'description' => 'Perawatan lembut dengan bahan alami yang aman untuk kulit si kecil.',
                'button_text' => 'Koleksi Anak',
                'button_link' => '/brand/pijar-nala',
                'logo_style' => 'rounded',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'type' => 'brand',
                'brand_key' => 'fokka',
                'theme' => 'slate',
                'title' => 'Tegas & Percaya Diri',
                'subtitle' => 'Untuk Pria Tangguh',
                'description' => 'Perawatan pria modern yang praktis dan menyegarkan untuk aktivitas sehari-hari.',
                'button_text' => 'Koleksi Pria',
                'button_link' => '/brand/fokka',
                'logo_style' => 'rounded',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'type' => 'product',
                'product_type' => 'featured',
                'theme' => 'rose',
                'title' => 'Pilihan Terbaik Kami',
                'subtitle' => 'Produp Unggulan',
                'description' => 'Rekomendasi produk terbaik yang wajib kamu coba.',
                'button_text' => 'Lihat Produk Unggulan',
                'button_link' => '/products?filter=featured',
                'label' => 'Produk Unggulan',
                'label_color' => '#B76E79',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'type' => 'product',
                'product_type' => 'promo',
                'theme' => 'amber',
                'title' => 'Penawaran Terbatas',
                'subtitle' => 'Promo Spesial',
                'description' => 'Dapatkan produk favorit dengan harga spesial sebelum kehabisan!',
                'button_text' => 'Lihat Promo',
                'button_link' => '/products?filter=promo',
                'label' => 'Promo Spesial',
                'label_color' => '#F59E0B',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'type' => 'product',
                'product_type' => 'new',
                'theme' => 'sky',
                'title' => 'Produk Terbaru',
                'subtitle' => 'Baru Datang',
                'description' => 'Kenalan dengan produk-produk baru kami.',
                'button_text' => 'Lihat Produk Baru',
                'button_link' => '/products?filter=new',
                'label' => 'Baru Datang',
                'label_color' => '#0EA5E9',
                'sort_order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($defaultHeroes as $h) {
            Hero::firstOrCreate(
                ['type' => $h['type'], 'brand_key' => $h['brand_key'] ?? null, 'product_type' => $h['product_type'] ?? null],
                $h
            );
        }

        echo "RBAC, Settings, dan Heroes berhasil di-seed!\n";
    }
}
