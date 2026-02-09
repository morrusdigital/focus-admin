<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('company_profile_downloads', function (Blueprint $table) {
            $table->string('company_name')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('city')->nullable();
        });

        DB::table('company_profile_downloads')
            ->whereNull('whatsapp')
            ->update(['whatsapp' => DB::raw('phone')]);

        DB::table('company_profile_downloads')
            ->whereNull('city')
            ->update(['city' => DB::raw('domicile')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_profile_downloads', function (Blueprint $table) {
            $table->dropColumn(['company_name', 'whatsapp', 'city']);
        });
    }
};
