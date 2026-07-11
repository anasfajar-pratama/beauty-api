<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->updateOrInsert(
            ['key' => 'whatsapp_phone'],
            [
                'value' => '6281234567890',
                'type' => 'string',
                'group' => 'contact',
                'description' => 'Nomor WhatsApp admin untuk tombol chat di halaman produk',
            ]
        );
    }
    public function down(): void
    {
        DB::table('settings')->where('key', 'whatsapp_phone')->delete();
    }
};
