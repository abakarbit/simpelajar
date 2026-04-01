<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drop old pivot table and create new one with lokasi and semester
     */
    public function up(): void
    {
        // Drop existing pivot table
        Schema::dropIfExists('dosen_mata_kuliah');

        // Create new pivot table with lokasi and semester_id
        Schema::create('dosen_mata_kuliah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliah')->onDelete('cascade');
            $table->string('lokasi')->nullable()->comment('Kelas/ruang tempat dosen mengajar');
            $table->foreignId('semester_id')->nullable()->constrained('semester')->onDelete('set null');
            $table->timestamps();

            // Unique: satu dosen tidak bisa mengajar mata kuliah yang sama di semester yang sama
            // Tapi bisa di semester berbeda atau kelas berbeda
            $table->unique(['dosen_id', 'mata_kuliah_id', 'semester_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dosen_mata_kuliah');
    }
};
