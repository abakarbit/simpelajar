<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Drop dosen_id column after data has been migrated to pivot table
     */
    public function up(): void
    {
        Schema::table('mata_kuliah', function (Blueprint $table) {
            // Drop the foreign key first
            $table->dropForeign(['dosen_id']);
            // Then drop the column
            $table->dropColumn('dosen_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mata_kuliah', function (Blueprint $table) {
            // Only recreate the column if it doesn't exist
            // Don't recreate the foreign key as data integrity may be compromised
            if (!Schema::hasColumn('mata_kuliah', 'dosen_id')) {
                $table->unsignedBigInteger('dosen_id')->nullable();
            }
        });
    }
};
