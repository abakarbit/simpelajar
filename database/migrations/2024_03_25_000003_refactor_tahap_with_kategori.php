<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop kategori_tahap if exists from previous migration attempts
        Schema::dropIfExists('kategori_tahap');

        // Create tabel kategori tahap
        Schema::create('kategori_tahap', function (Blueprint $table) {
            $table->id();
            $table->string('nama_tahap');
            $table->text('deskripsi')->nullable();
            $table->unsignedTinyInteger('urutan')->unique();
            $table->json('jenis_dokumen')->nullable();
            $table->timestamps();
        });

        // Drop the old unique constraint - try both DROP INDEX and DROP KEY
        try {
            DB::statement('ALTER TABLE tahap DROP KEY tahap_semester_id_urutan_unique');
        } catch (\Exception $e) {
            try {
                DB::statement('ALTER TABLE tahap DROP INDEX tahap_semester_id_urutan_unique');
            } catch (\Exception $e2) {
                // Constraint might not exist, continue
            }
        }

        // Drop foreign key using raw statement
        try {
            DB::statement('ALTER TABLE tahap DROP FOREIGN KEY tahap_semester_id_foreign');
        } catch (\Exception $e) {
            // Foreign key might not exist, continue
        }

        // Drop columns using raw SQL to avoid issues
        $alter_statements = [];
        $alter_statements[] = "ALTER TABLE tahap DROP COLUMN IF EXISTS nama_tahap";
        $alter_statements[] = "ALTER TABLE tahap DROP COLUMN IF EXISTS deskripsi";
        $alter_statements[] = "ALTER TABLE tahap DROP COLUMN IF EXISTS urutan";
        $alter_statements[] = "ALTER TABLE tahap DROP COLUMN IF EXISTS jenis_dokumen";

        // Execute each drop
        foreach ($alter_statements as $statement) {
            try {
                DB::statement($statement);
            } catch (\Exception $e) {
                // Column might not exist, continue
            }
        }

        Schema::table('tahap', function (Blueprint $table) {
            // Re-add semester foreign key if not exists
            if (!Schema::hasColumn('tahap', 'semester_id')) {
                $table->foreignId('semester_id')->after('id')->constrained('semester')->onDelete('cascade');
            }

            // Add kategori_tahap_id if not exists
            if (!Schema::hasColumn('tahap', 'kategori_tahap_id')) {
                $table->foreignId('kategori_tahap_id')->after('semester_id')->constrained('kategori_tahap')->onDelete('cascade');
            }

            // Add new unique constraint
            try {
                $table->unique(['semester_id', 'kategori_tahap_id']);
            } catch (\Exception $e) {
                // Constraint already exists
            }
        });
    }

    public function down(): void
    {
        // Drop unique constraint if it exists
        try {
            DB::statement('ALTER TABLE tahap DROP INDEX tahap_semester_id_kategori_tahap_id_unique');
        } catch (\Exception $e) {
            // Constraint might not exist
        }

        // Drop foreign keys using raw SQL
        try {
            DB::statement('ALTER TABLE tahap DROP FOREIGN KEY tahap_kategori_tahap_id_foreign');
        } catch (\Exception $e) {
            // Foreign key might not exist
        }

        try {
            DB::statement('ALTER TABLE tahap DROP FOREIGN KEY tahap_semester_id_foreign');
        } catch (\Exception $e) {
            // Foreign key might not exist
        }

        // Drop kategori_tahap_id column if it exists
        try {
            DB::statement('ALTER TABLE tahap DROP COLUMN IF EXISTS kategori_tahap_id');
        } catch (\Exception $e) {
            // Column might not exist
        }

        // Recreate old columns and constraints
        Schema::table('tahap', function (Blueprint $table) {
            // Add back old columns if they don't exist
            if (!Schema::hasColumn('tahap', 'nama_tahap')) {
                $table->string('nama_tahap')->nullable()->after('id');
            }
            if (!Schema::hasColumn('tahap', 'deskripsi')) {
                $table->text('deskripsi')->nullable()->after('nama_tahap');
            }
            if (!Schema::hasColumn('tahap', 'urutan')) {
                $table->unsignedTinyInteger('urutan')->nullable()->after('deskripsi');
            }
            if (!Schema::hasColumn('tahap', 'jenis_dokumen')) {
                $table->json('jenis_dokumen')->nullable()->after('urutan');
            }
        });

        // Re-create unique constraint and foreign key
        try {
            DB::statement('ALTER TABLE tahap ADD UNIQUE tahap_semester_id_urutan_unique (semester_id, urutan)');
        } catch (\Exception $e) {
            // Constraint already exists
        }

        try {
            DB::statement('ALTER TABLE tahap ADD CONSTRAINT tahap_semester_id_foreign FOREIGN KEY (semester_id) REFERENCES semester (id) ON DELETE CASCADE');
        } catch (\Exception $e) {
            // Foreign key already exists
        }

        Schema::dropIfExists('kategori_tahap');
    }
};
