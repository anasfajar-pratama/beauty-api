<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\HomepageContent;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SeedMissingData extends Seeder
{
    public function run(): void
    {
        // $this->seedAdmins();
        $this->seedHomepageContents();
        $this->seedSettings();
    }

    private function seedAdmins(): void
    {
        $defaults = [
            [
                'username' => 'admin',
                'password' => Hash::make('rindangcemarasukses2026'),
                'name'     => 'Admin',
                'email'    => 'admin@rindangcemarasukses.com',
            ],
        ];

        foreach ($defaults as $data) {
            $admin = Admin::where('username', $data['username'])->first();

            if (!$admin) {
                Admin::create($data);
                $this->command->info("[Admin] Created: {$data['username']}");
            } else {
                $dirty = false;
                if (!$admin->name) {
                    $admin->name = $data['name'];
                    $dirty = true;
                }
                if (!$admin->email) {
                    $admin->email = $data['email'];
                    $dirty = true;
                }
                if ($dirty) {
                    $admin->save();
                    $this->command->info("[Admin] Updated missing fields for: {$data['username']}");
                }
            }
        }
    }

    private function seedHomepageContents(): void
    {
        $defaults = [
            // Homepage sections
            'section_kategori_title'      => 'Koleksi Berdasarkan Kategori',
            'section_kategori_subtitle'   => 'Temukan alat kecantikan yang sesuai dengan kebutuhanmu',
            'section_unggulan_title'      => 'Pilihan Terbaik Kami',
            'section_unggulan_subtitle'   => 'Rekomendasi produk terbaik yang wajib kamu coba',
            'section_promo_title'         => 'Penawaran Terbatas',
            'section_promo_subtitle'      => 'Dapatkan produk favorit dengan harga spesial sebelum kehabisan!',
            'section_terbaru_title'       => 'Produk Terbaru',
            'section_terbaru_subtitle'    => 'Kenalan dengan produk-produk baru kami',
            'section_features_title'      => 'Mengapa Produk Kami?',
            'section_features_subtitle'   => 'Kami berkomitmen menghadirkan yang terbaik untuk kecantikan Anda',
            'section_testimonials_title'  => 'Apa Kata Mereka',
            'section_testimonials_subtitle' => 'Testimoni dari pelanggan setia Rindang Cemara Sukses',

            // Homepage — Tentang Kami
            'about_title'                 => 'Inovasi untuk Kecantikan',
            'about_text'                  => 'Rindang Cemara Sukses menghadirkan alat kecantikan berkualitas tinggi yang menggabungkan teknologi modern dengan desain elegan.',
            'about_quote'                 => 'Kecantikan sejati memancar ketika Anda merasa nyaman dengan kulit Anda sendiri.',

            // Newsletter
            'newsletter_title'            => 'Dapatkan Update Terbaru',
            'newsletter_subtitle'         => 'Berlangganan untuk info produk baru dan penawaran eksklusif.',
        ];

        foreach ($defaults as $key => $value) {
            $row = HomepageContent::where('key', $key)->first();

            if (!$row) {
                HomepageContent::create(['key' => $key, 'value' => $value]);
                $this->command->info("[HomepageContent] Created: {$key}");
            } elseif (!$row->value || trim($row->value) === '') {
                $row->update(['value' => $value]);
                $this->command->info("[HomepageContent] Updated empty value for: {$key}");
            }
        }
    }

    private function seedSettings(): void
    {
        $defaults = [
            // General
            ['key' => 'site_name', 'value' => 'Rindang Cemara Sukses', 'type' => 'string', 'group' => 'general', 'description' => 'Nama toko'],
            ['key' => 'site_description', 'value' => 'Toko alat kecantikan terpercaya', 'type' => 'text', 'group' => 'general', 'description' => 'Deskripsi singkat toko'],
            ['key' => 'site_logo', 'value' => '', 'type' => 'image', 'group' => 'general', 'description' => 'Logo utama toko'],
            ['key' => 'site_favicon', 'value' => '', 'type' => 'image', 'group' => 'general', 'description' => 'Favicon toko'],
            ['key' => 'announcement_bar', 'value' => 'Free Ongkir untuk pembelian minimal Rp150.000', 'type' => 'string', 'group' => 'general', 'description' => 'Teks pengumuman di bagian atas halaman'],

            // SEO
            ['key' => 'meta_title', 'value' => 'Rindang Cemara Sukses — Alat Kecantikan Premium', 'type' => 'string', 'group' => 'seo', 'description' => 'Meta title default'],
            ['key' => 'meta_description', 'value' => 'Toko alat kecantikan premium untuk wanita, pria, dan anak-anak. Produk berkualitas tinggi dengan bahan alami terbaik.', 'type' => 'text', 'group' => 'seo', 'description' => 'Meta description default'],

            // Contact
            ['key' => 'contact_email', 'value' => 'hello@rindangcemarasukses.com', 'type' => 'string', 'group' => 'contact', 'description' => 'Email kontak'],
            ['key' => 'contact_phone', 'value' => '', 'type' => 'string', 'group' => 'contact', 'description' => 'Nomor telepon'],
            ['key' => 'contact_address', 'value' => '', 'type' => 'text', 'group' => 'contact', 'description' => 'Alamat lengkap'],

            // Social
            ['key' => 'social_instagram', 'value' => '', 'type' => 'string', 'group' => 'social', 'description' => 'URL Instagram'],
            ['key' => 'social_tiktok', 'value' => '', 'type' => 'string', 'group' => 'social', 'description' => 'URL TikTok'],
            ['key' => 'social_facebook', 'value' => '', 'type' => 'string', 'group' => 'social', 'description' => 'URL Facebook'],
            ['key' => 'social_youtube', 'value' => '', 'type' => 'string', 'group' => 'social', 'description' => 'URL YouTube'],

            // WhatsApp
            ['key' => 'whatsapp_phone', 'value' => '6281234567890', 'type' => 'string', 'group' => 'contact', 'description' => 'Nomor WhatsApp (format: 628xxx tanpa + dan spasi)'],

            // Brands
            ['key' => 'brand_blisera_name', 'value' => 'BLISERA', 'type' => 'string', 'group' => 'brands', 'description' => 'Nama brand wanita'],
            ['key' => 'brand_pijar_nala_name', 'value' => 'PIJAR NALA', 'type' => 'string', 'group' => 'brands', 'description' => 'Nama brand anak & bayi'],
            ['key' => 'brand_fokka_name', 'value' => 'FOKKA', 'type' => 'string', 'group' => 'brands', 'description' => 'Nama brand pria'],
        ];

        foreach ($defaults as $s) {
            $setting = Setting::where('key', $s['key'])->first();

            if (!$setting) {
                Setting::create($s);
                $this->command->info("[Setting] Created: {$s['key']}");
            } elseif (!$setting->value || trim($setting->value) === '') {
                $setting->update(['value' => $s['value']]);
                $this->command->info("[Setting] Updated empty value for: {$s['key']}");
            }
        }
    }
}
