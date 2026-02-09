<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sectors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->integer('sort_order')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('project_sector', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sector_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['project_id', 'sector_id']);
        });

        $rows = DB::table('projects')->select('id', 'sector')->get();
        $sectorMap = [];
        $sortOrder = 1;

        foreach ($rows as $row) {
            $raw = trim((string) $row->sector);

            if ($raw === '') {
                continue;
            }

            $slug = Str::slug($raw);
            $slug = $slug !== '' ? $slug : 'sector-'.$sortOrder;

            if (!isset($sectorMap[$slug])) {
                $sectorId = DB::table('sectors')->insertGetId([
                    'name' => $raw,
                    'slug' => $slug,
                    'sort_order' => $sortOrder,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $sectorMap[$slug] = $sectorId;
                $sortOrder++;
            }

            DB::table('project_sector')->insert([
                'project_id' => $row->id,
                'sector_id' => $sectorMap[$slug],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('sector');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('sector')->nullable();
        });

        $rows = DB::table('project_sector')
            ->join('sectors', 'project_sector.sector_id', '=', 'sectors.id')
            ->select('project_sector.project_id', 'sectors.slug')
            ->orderBy('project_sector.created_at')
            ->get();

        $firstSector = [];
        foreach ($rows as $row) {
            if (!isset($firstSector[$row->project_id])) {
                $firstSector[$row->project_id] = $row->slug;
            }
        }

        foreach ($firstSector as $projectId => $slug) {
            DB::table('projects')->where('id', $projectId)->update(['sector' => $slug]);
        }

        Schema::dropIfExists('project_sector');
        Schema::dropIfExists('sectors');
    }
};
