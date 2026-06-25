<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\Testimonial;
use App\Models\HomepageContent;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('username', $request->username)->first();

        if (! $admin || ! Hash::check($request->password, $admin->password)) {
            return response()->json(['error' => 'Username atau password salah'], 401);
        }

        if (! $admin->is_active) {
            return response()->json(['error' => 'Akun Anda dinonaktifkan'], 403);
        }

        // Delete old tokens and create a new one
        $admin->tokens()->delete();
        $token = $admin->createToken('admin-token')->plainTextToken;

        // Update last login info
        $admin->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        // Log login activity
        ActivityLog::create([
            'admin_id'   => $admin->id,
            'action'     => 'login',
            'description' => "Admin {$admin->username} login",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'token'    => $token,
            'username' => $admin->username,
            'name'     => $admin->name,
            'permissions' => $admin->roles->flatMap->permissions->pluck('slug')->unique()->values(),
        ]);
    }

    public function seed(Request $request)
    {
        $request->validate(['setupKey' => 'required|string']);

        $validKey = env('SETUP_KEY', 'rindangcemarasukses-setup-2026');
        if ($request->setupKey !== $validKey) {
            return response()->json(['error' => 'Setup key tidak valid'], 403);
        }

        if (Admin::count() > 0) {
            return response()->json(['error' => 'Data sudah pernah diinisialisasi sebelumnya. Silakan login.'], 409);
        }

        // Create admin
        $admin = Admin::create([
            'username' => 'admin',
            'name' => 'Super Admin',
            'password' => Hash::make('rindangcemarasukses2026'),
        ]);

        // Assign super-admin role if exists
        $superRole = \App\Models\Role::where('slug', 'super-admin')->first();
        if ($superRole) {
            $admin->roles()->attach($superRole->id);
        }

        // Create sample products
        $products = [
            ['name' => 'Serum Glow Intensif',   'category' => 'Wanita', 'tagline' => 'Cerahkan kulit dalam 7 hari',     'ingredients' => 'Niacinamide 10%, Vitamin C, Hyaluronic Acid, Aloe Vera Extract, Rose Hip Oil', 'benefits' => json_encode(['Mencerahkan kulit kusam','Meratakan warna kulit','Melembapkan intensif','Anti penuaan dini']), 'how_to_use' => json_encode(['Bersihkan wajah terlebih dahulu','Oleskan 3-4 tetes serum ke wajah','Tepuk-tepuk hingga meresap sempurna','Lanjutkan dengan pelembap']), 'sort_order' => 1],
            ['name' => 'Moisturizer Intensif',  'category' => 'Wanita', 'tagline' => 'Kelembapan tahan 24 jam',         'ingredients' => 'Hyaluronic Acid, Ceramide, Green Tea Extract, Panthenol, Peptide',               'benefits' => json_encode(['Melembapkan sepanjang hari','Memperkuat skin barrier','Menghaluskan tekstur kulit','Mudah meresap']),                                       'how_to_use' => json_encode(['Gunakan setelah serum','Ambil secukupnya','Ratakan ke seluruh wajah dan leher','Gunakan pagi dan malam']),                       'sort_order' => 2],
            ['name' => 'Face Wash Pencerah',    'category' => 'Wanita', 'tagline' => 'Bersih maksimal, kulit lembut',   'ingredients' => 'Hyaluronic Acid, Ceramide, Green Tea Extract, Panthenol, Peptide',               'benefits' => json_encode(['Membersihkan kotoran dan debu','Menjaga kelembapan alami','Kulit terasa lembut setelah cuci','Cocok pagi dan malam']),                 'how_to_use' => json_encode(['Basahi wajah dengan air hangat','Tuangkan sabun ke tangan','Pijat lembut ke wajah','Bilas hingga bersih']),                         'sort_order' => 3],
            ['name' => "Men's Active Cleanser", 'category' => 'Pria',   'tagline' => 'Segar dan bersih seharian',       'ingredients' => 'Charcoal Extract, Tea Tree Oil, Salicylic Acid, Glycerin, Menthol',              'benefits' => json_encode(['Membersihkan pori terdalam','Mengurangi minyak berlebih','Mencegah jerawat','Menyegarkan kulit']),                                    'how_to_use' => json_encode(['Basahi wajah','Tuangkan secukupnya ke telapak tangan','Pijat lembut pada wajah','Bilas hingga bersih']),                             'sort_order' => 4],
            ['name' => "Men's Hydra Gel",       'category' => 'Pria',   'tagline' => 'Hidrasi tanpa rasa lengket',      'ingredients' => 'Charcoal Extract, Tea Tree Oil, Salicylic Acid, Glycerin, Menthol',              'benefits' => json_encode(['Hidrasi cepat meresap','Tidak lengket','Cocok kulit aktif','Aroma segar']),                                                           'how_to_use' => json_encode(['Cuci wajah','Ambil seukuran kacang','Ratakan ke wajah','Biarkan meresap']),                                                        'sort_order' => 5],
            ['name' => 'Kids Gentle Wash',      'category' => 'Anak',   'tagline' => 'Lembut untuk kulit si kecil',     'ingredients' => 'Oat Extract, Calendula, Chamomile, Aloe Vera, Vitamin E',                       'benefits' => json_encode(['Sangat lembut di kulit','Menenangkan kulit sensitif','Tear-free (tidak pedih di mata)','Menjaga kelembapan alami']),                  'how_to_use' => json_encode(['Gunakan saat mandi','Usapkan dengan lembut ke seluruh tubuh','Bilas dengan air hangat','Keringkan dengan handuk lembut']),          'sort_order' => 6],
        ];

        foreach ($products as $p) {
            Product::create($p);
        }

        $testimonials = [
            ['name' => 'Siti A.',  'content' => 'Kulit saya tidak pernah secerah ini. Serum glow-nya benar-benar bekerja!',                      'rating' => '5', 'is_active' => true],
            ['name' => 'Budi P.',  'content' => 'Produk pria sangat praktis. Tidak lengket dan menyegarkan setelah olahraga.',                   'rating' => '5', 'is_active' => true],
            ['name' => 'Rina M.',  'content' => 'Sabun mandi anak sangat lembut, anak saya tidak pernah komplain pedih di mata lagi.',           'rating' => '5', 'is_active' => true],
            ['name' => 'Dewi K.',  'content' => 'Pengalaman mewah dengan harga yang sangat sepadan. Kemasannya sangat cantik.',                  'rating' => '5', 'is_active' => true],
        ];

        foreach ($testimonials as $t) {
            Testimonial::create($t);
        }

        $contents = [
            'hero_title'            => 'Kecantikan untuk',
            'hero_title_highlight'  => 'Setiap Jiwa',
            'hero_subtitle'         => 'Perawatan kulit mewah untuk wanita, pria, dan anak-anak. Diformulasikan dengan bahan alami terbaik untuk memancarkan kilau sejati Anda.',
            'about_title'           => 'Filosofi Kemurnian',
            'about_text1'           => 'Lumière lahir dari kepercayaan bahwa alam menyimpan rahasia terbaik untuk kulit yang sehat dan bercahaya. Kami menggabungkan kekayaan botani Indonesia dengan inovasi dermatologi modern.',
            'about_text2'           => 'Setiap tetes produk kami diracik dengan ketelitian tinggi, menghadirkan pengalaman layaknya perawatan di spa mewah, langsung di rumah Anda.',
            'about_quote'           => 'Kecantikan sejati memancar ketika Anda merasa nyaman dengan kulit Anda sendiri.',
            'newsletter_title'      => 'Bergabung dengan Komunitas Lumière',
            'newsletter_subtitle'   => 'Dapatkan informasi terbaru mengenai peluncuran produk, penawaran eksklusif, dan tips perawatan kulit.',
            'brand_name'            => 'LUMIÈRE',
            'brand_tagline'         => 'Kecantikan untuk Setiap Jiwa',
        ];

        foreach ($contents as $key => $value) {
            HomepageContent::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return response()->json(['message' => 'Data berhasil diinisialisasi! Akun admin telah dibuat.']);
    }

    public function stats(Request $request)
    {
        return response()->json([
            'products'     => Product::count(),
            'testimonials' => Testimonial::count(),
        ]);
    }

    public function me(Request $request)
    {
        $admin = $request->user()->load('roles.permissions');
        return response()->json([
            'id' => $admin->id,
            'username' => $admin->username,
            'name' => $admin->name,
            'email' => $admin->email,
            'permissions' => $admin->roles->flatMap->permissions->pluck('slug')->unique()->values(),
            'roles' => $admin->roles->pluck('slug'),
        ]);
    }
}
