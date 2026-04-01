<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tahap', function (Blueprint $table) {
            $table->id();
            $table->string('nama_tahap');
            $table->text('deskripsi')->nullable();
            $table->unsignedTinyInteger('urutan');
            $table->json('jenis_dokumen')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tahap');
    }
};
