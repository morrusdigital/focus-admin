<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('featured_works', function (Blueprint $table) {
            $table->id();
            $table->string('sector_slug', 120)->index();
            $table->string('sector_label', 150);
            $table->string('title');
            $table->longText('description');
            $table->string('cta_label', 150)->nullable();
            $table->string('cta_url', 255)->nullable();
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('featured_works');
    }
};
