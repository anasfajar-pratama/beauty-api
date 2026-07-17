<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gallery_items', function (Blueprint $table) {
            $table->string('title')->nullable()->after('image_url');
            $table->string('tagline')->nullable()->after('title');
            $table->text('description')->nullable()->after('tagline');
        });
    }

    public function down(): void
    {
        Schema::table('gallery_items', function (Blueprint $table) {
            $table->dropColumn(['title', 'tagline', 'description']);
        });
    }
};
