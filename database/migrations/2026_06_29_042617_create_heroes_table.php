<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('heroes', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // brand, product, event
            $table->string('brand_key')->nullable(); // blisera, fokka, pijar_nala
            $table->string('product_type')->nullable(); // featured, promo, new
            $table->string('theme')->default('rose'); // rose, sky, slate, amber, emerald
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_link')->nullable();
            $table->string('hero_image')->nullable();
            $table->string('logo')->nullable();
            $table->string('logo_style')->default('rounded'); // circle, rounded, square
            $table->string('label')->nullable();
            $table->string('label_color')->nullable();
            $table->date('event_date')->nullable();
            $table->date('event_end_date')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('heroes');
    }
};
