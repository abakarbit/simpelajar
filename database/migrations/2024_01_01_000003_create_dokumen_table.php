<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliah')->onDelete('cascade');
            $table->foreignId('tahap_id')->constrained('tahap')->onDelete('cascade');
            $table->string('nama_file');
            $table->string('jenis_dokumen');
            $table->string('file_path');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'revisi', 'approved'])->default('pending');
            $table->text('komentar')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen');
    }
};
