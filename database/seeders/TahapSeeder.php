<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tahap;
use App\Models\KategoriTahap;
use App\Models\Semester;

class TahapSeeder extends Seeder
{
    public function run(): void
    {
        // Create kategori tahap (baku/predefined)
        $kategoriTahapData = [
            [
                'urutan'       => 1,
                'nama_tahap'   => 'Tahap 1',
                'deskripsi'    => 'Kelengkapan dokumen awal perkuliahan',
                'jenis_dokumen' => ['RPS', 'Kontrak Kuliah', 'Materi 1', 'Materi 2', 'Materi 3', 'Materi 4'],
            ],
            [
                'urutan'       => 2,
                'nama_tahap'   => 'Tahap 2',
                'deskripsi'    => 'Persiapan UTS',
                'jenis_dokumen' => ['Soal UTS', 'Materi 5', 'Materi 6', 'Materi 7'],
            ],
            [
                'urutan'       => 3,
                'nama_tahap'   => 'Tahap 3',
                'deskripsi'    => 'Pasca UTS',
                'jenis_dokumen' => ['Nilai UTS', 'Materi 9', 'Materi 10', 'Materi 11', 'Materi 12', 'LJU Terbaik'],
            ],
            [
                'urutan'       => 4,
                'nama_tahap'   => 'Tahap 4',
                'deskripsi'    => 'Persiapan UAS',
                'jenis_dokumen' => ['Soal UAS', 'Rubrik Penilaian', 'Materi 13', 'Materi 14', 'Materi 15'],
            ],
            [
                'urutan'       => 5,
                'nama_tahap'   => 'Tahap 5',
                'deskripsi'    => 'Pasca UAS',
                'jenis_dokumen' => ['DNA', 'Nilai UAS', 'Portofolio', 'LJU UAS'],
            ],
        ];

        foreach ($kategoriTahapData as $data) {
            KategoriTahap::updateOrCreate(
                ['urutan' => $data['urutan']],
                $data
            );
        }

        // Get active semester or create one if none exists
        $semesterActive = Semester::orderBy('tahun_ajaran', 'desc')->first();

        if (!$semesterActive) {
            // Create a default semester
            $semesterActive = Semester::create([
                'nama_semester' => 'Semester Aktif',
                'tahun_ajaran' => now()->year . '/' . (now()->year + 1),
                'tipe' => now()->month <= 6 ? 'ganjil' : 'genap',
                'is_active' => true,
            ]);
        }

        // Create tahap entries for each kategori
        $kategoriTahapList = KategoriTahap::all();
        foreach ($kategoriTahapList as $kategori) {
            Tahap::updateOrCreate(
                [
                    'semester_id' => $semesterActive->id,
                    'kategori_tahap_id' => $kategori->id,
                ],
                [
                    'deadline' => now()->addWeeks($kategori->urutan * 2),
                ]
            );
        }
    }
}
