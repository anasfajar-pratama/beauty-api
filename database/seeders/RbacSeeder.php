<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use App\Models\Setting;
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
            ['name' => 'Upload File', 'slug' => 'upload_files', 'group' => 'files'],
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
            ['key' => 'site_name', 'value' => 'Lumière', 'type' => 'string', 'group' => 'general', 'description' => 'Nama website'],
            ['key' => 'site_description', 'value' => 'Kecantikan untuk Setiap Jiwa', 'type' => 'text', 'group' => 'general', 'description' => 'Deskripsi website'],
            ['key' => 'site_logo', 'value' => '', 'type' => 'image', 'group' => 'general', 'description' => 'Logo website (URL)'],
            ['key' => 'seo_title', 'value' => 'Lumière - Kecantikan untuk Setiap Jiwa', 'type' => 'string', 'group' => 'seo', 'description' => 'SEO title default'],
            ['key' => 'seo_description', 'value' => 'Temukan produk kecantikan premium dengan bahan alami terbaik untuk wanita, pria, dan anak-anak.', 'type' => 'text', 'group' => 'seo', 'description' => 'SEO meta description'],
            ['key' => 'seo_keywords', 'value' => 'kecantikan, skincare, perawatan kulit, produk kecantikan, natural', 'type' => 'text', 'group' => 'seo', 'description' => 'SEO keywords (dipisah koma)'],
            ['key' => 'social_facebook', 'value' => '', 'type' => 'string', 'group' => 'social', 'description' => 'URL Facebook'],
            ['key' => 'social_instagram', 'value' => '', 'type' => 'string', 'group' => 'social', 'description' => 'URL Instagram'],
            ['key' => 'social_twitter', 'value' => '', 'type' => 'string', 'group' => 'social', 'description' => 'URL Twitter / X'],
            ['key' => 'social_youtube', 'value' => '', 'type' => 'string', 'group' => 'social', 'description' => 'URL YouTube'],
            ['key' => 'contact_email', 'value' => 'hello@lumiere.com', 'type' => 'string', 'group' => 'contact', 'description' => 'Email kontak'],
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
        ];

        foreach ($defaultSettings as $s) {
            Setting::firstOrCreate(['key' => $s['key']], $s);
        }

        echo "RBAC dan Settings berhasil di-seed!\n";
    }
}
