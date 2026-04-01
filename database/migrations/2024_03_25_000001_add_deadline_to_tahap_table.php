<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tahap', function (Blueprint $table) {
            $table->dateTime('deadline')->nullable()->after('jenis_dokumen')->comment('Deadline untuk upload dokumen tahap ini');
        });
    }

    public function down(): void
    {
        Schema::table('tahap', function (Blueprint $table) {
            $table->dropColumn('deadline');
        });
    }
};
