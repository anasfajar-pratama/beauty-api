<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('subcategory_id')->nullable()->constrained('subcategories')->nullOnDelete();
            $table->boolean('is_promo')->default(false);
            $table->boolean('is_new')->default(false);
            $table->text('description')->nullable();
            $table->string('weight')->nullable();
            $table->string('dimensions')->nullable();
            $table->string('bpom_number')->nullable();
            $table->text('certifications')->nullable();
            $table->boolean('halal_certified')->default(false);
            $table->string('warranty_info')->nullable();
            $table->decimal('price', 12, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['subcategory_id']);
            $table->dropColumn([
                'subcategory_id', 'is_promo', 'is_new', 'description',
                'weight', 'dimensions', 'bpom_number', 'certifications',
                'halal_certified', 'warranty_info', 'price',
            ]);
        });
    }
};
