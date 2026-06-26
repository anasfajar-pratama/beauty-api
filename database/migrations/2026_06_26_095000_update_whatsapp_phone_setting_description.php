<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->where('key', 'whatsapp_phone')->update([
            'description' => 'Nomor WhatsApp admin untuk tombol chat di halaman produk. Harus diawali 62 (tanpa +), contoh: 6281234567890',
        ]);
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'whatsapp_phone')->update([
            'description' => 'Nomor WhatsApp admin untuk tombol chat di halaman produk',
        ]);
    }
};
