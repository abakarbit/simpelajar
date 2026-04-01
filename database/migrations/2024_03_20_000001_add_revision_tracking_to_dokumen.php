<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dokumen', function (Blueprint $table) {
            // Parent dokumen untuk track revisi
            $table->foreignId('parent_id')->nullable()->constrained('dokumen')->onDelete('cascade');
            // Flag untuk dokumen yang aktif/current
            $table->boolean('is_current')->default(true)->index();
        });
    }

    public function down(): void
    {
        Schema::table('dokumen', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'is_current']);
        });
    }
};
