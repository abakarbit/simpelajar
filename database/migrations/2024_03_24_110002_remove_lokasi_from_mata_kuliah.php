<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Remove lokasi from mata_kuliah table as it will be per-dosen in pivot table
     */
    public function up(): void
    {
        if (Schema::hasColumn('mata_kuliah', 'lokasi')) {
            Schema::table('mata_kuliah', function (Blueprint $table) {
                $table->dropColumn('lokasi');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mata_kuliah', function (Blueprint $table) {
            $table->string('lokasi')->nullable();
        });
    }
};
