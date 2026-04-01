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
        // Create pivot table for many-to-many relationship
        Schema::create('dosen_mata_kuliah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliah')->onDelete('cascade');
            $table->timestamps();

            // Prevent duplicate entries
            $table->unique(['dosen_id', 'mata_kuliah_id']);
        });

        // Migrate existing data from dosen_id column to pivot table
        if (Schema::hasColumn('mata_kuliah', 'dosen_id')) {
            DB::statement('
                INSERT INTO dosen_mata_kuliah (dosen_id, mata_kuliah_id, created_at, updated_at)
                SELECT dosen_id, id, NOW(), NOW()
                FROM mata_kuliah
                WHERE dosen_id IS NOT NULL
                ON DUPLICATE KEY UPDATE dosen_mata_kuliah.id = dosen_mata_kuliah.id
            ');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dosen_mata_kuliah');
    }
};
