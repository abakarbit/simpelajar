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
        Schema::table('mata_kuliah', function (Blueprint $table) {
            $table->string('lokasi')->nullable()->after('dosen_id')->comment('Lokasi/Kelas mengajar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mata_kuliah', function (Blueprint $table) {
            $table->dropColumn('lokasi');
        });
    }
};
