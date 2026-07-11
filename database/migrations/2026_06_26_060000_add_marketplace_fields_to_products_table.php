<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('original_price', 12, 2)->nullable()->after('price');
            $table->string('shopee_url')->nullable()->after('original_price');
            $table->string('tokopedia_url')->nullable()->after('shopee_url');
            $table->string('tiktok_url')->nullable()->after('tokopedia_url');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['original_price', 'shopee_url', 'tokopedia_url', 'tiktok_url']);
        });
    }
};
