<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tahap', function (Blueprint $table) {
            $table->foreignId('semester_id')->nullable()->after('id')->constrained('semester')->onDelete('cascade');
            // Add unique constraint on semester_id + urutan combination
            $table->unique(['semester_id', 'urutan']);
        });
    }

    public function down(): void
    {
        // Use raw SQL for safer constraint dropping
        try {
            DB::statement('ALTER TABLE tahap DROP FOREIGN KEY tahap_semester_id_foreign');
        } catch (\Exception $e) {
            // Foreign key might not exist
        }

        try {
            DB::statement('ALTER TABLE tahap DROP INDEX tahap_semester_id_urutan_unique');
        } catch (\Exception $e) {
            // Unique constraint might not exist
        }

        Schema::table('tahap', function (Blueprint $table) {
            if (Schema::hasColumn('tahap', 'semester_id')) {
                $table->dropColumn('semester_id');
            }
        });
    }
};
