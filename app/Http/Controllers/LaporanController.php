<?php

namespace App\Http\Controllers;

use App\Exports\LaporanExport;
use App\Models\MataKuliah;
use App\Models\Semester;
use App\Models\Tahap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    /**
     * Ambil data yang dibutuhkan untuk laporan berdasarkan filter.
     * Dipakai bersama oleh index() dan export().
     */
    private function getData(Request $request): array
    {
        $semesterId  = $request->input('semester_id');
        $tahapFilter = $request->input('tahap', 'semua'); // 'semua' | '1'..'5'

        $semester = $semesterId ? Semester::find($semesterId) : null;

        // Tahap list berdasarkan semester + urutan tahap
        $tahapQuery = Tahap::with('kategoriTahap')
            ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
            ->orderByRaw('(SELECT urutan FROM kategori_tahap WHERE kategori_tahap.id = tahap.kategori_tahap_id)');

        if ($tahapFilter !== 'semua') {
            $tahapQuery->whereHas('kategoriTahap', fn ($q) => $q->where('urutan', (int) $tahapFilter));
        }

        $selectedTahapList = $tahapQuery->get();

        // Mata kuliah pada semester terpilih
        $mkQuery = MataKuliah::query()->orderBy('nama_mk');
        if ($semesterId) {
            $mkQuery->whereIn('id',
                DB::table('dosen_mata_kuliah')
                    ->where('semester_id', $semesterId)
                    ->distinct()
                    ->pluck('mata_kuliah_id')
            );
        }
        $mataKuliahList = $mkQuery->with([
            'dosen' => function ($q) use ($semesterId) {
                $q->wherePivot('status_dosen', 'Penanggung Jawab');
                if ($semesterId) {
                    $q->wherePivot('semester_id', $semesterId);
                }
            },
        ])->get();

        return compact('semester', 'semesterId', 'tahapFilter', 'selectedTahapList', 'mataKuliahList');
    }

    public function index(Request $request)
    {
        $semesterList = Semester::orderBy('tahun_ajaran', 'desc')->orderBy('id', 'desc')->get();
        $semesterAktif = Semester::getActive();

        // Default ke semester aktif jika belum dipilih
        if (! $request->has('semester_id')) {
            $request->merge(['semester_id' => $semesterAktif?->id]);
        }

        $data = $this->getData($request);

        return view('laporan.index', array_merge($data, [
            'semesterList'  => $semesterList,
            'semesterAktif' => $semesterAktif,
        ]));
    }

    public function export(Request $request)
    {
        $data = $this->getData($request);

        /** @var \Illuminate\Support\Collection $selectedTahapList */
        $selectedTahapList = $data['selectedTahapList'];

        /** @var \Illuminate\Support\Collection $mataKuliahList */
        $mataKuliahList = $data['mataKuliahList'];

        $semester      = $data['semester'];
        $tahapFilter   = $data['tahapFilter'];
        $multiTahap    = $selectedTahapList->count() > 1;

        /*──────────────────────────────────────────
         | Header rows
         ──────────────────────────────────────────*/
        $rows = [];

        if ($multiTahap) {
            // Baris 1: grouping tahap (gabung sel per grup)
            $row1 = ['', 'Kode MK', 'Nama Mata Kuliah', ''];
            foreach ($selectedTahapList as $tahap) {
                $jenisDokumen = $tahap->kategoriTahap->jenis_dokumen ?? [];
                $count = count($jenisDokumen);
                if ($count > 0) {
                    $row1[] = $tahap->kategoriTahap->nama_tahap;
                    for ($i = 1; $i < $count; $i++) {
                        $row1[] = ''; // cells yang digabung nanti
                    }
                }
            }
            $rows[] = $row1;

            // Baris 2: detail jenis dokumen
            $row2 = ['No', '', '', 'Penanggung Jawab'];
            foreach ($selectedTahapList as $tahap) {
                foreach ($tahap->kategoriTahap->jenis_dokumen ?? [] as $jenis) {
                    $row2[] = $jenis;
                }
            }
            $rows[] = $row2;
            $headerRows = 2;
        } else {
            // Single header row
            $headers = ['No', 'Kode MK', 'Nama Mata Kuliah', 'Penanggung Jawab'];
            foreach ($selectedTahapList as $tahap) {
                foreach ($tahap->kategoriTahap->jenis_dokumen ?? [] as $jenis) {
                    $headers[] = $jenis;
                }
            }
            $rows[]     = $headers;
            $headerRows = 1;
        }

        /*──────────────────────────────────────────
         | Data rows
         ──────────────────────────────────────────*/
        foreach ($mataKuliahList as $i => $mk) {
            $penanggungJawab = $mk->dosen->pluck('name')->join(', ') ?: '-';
            $row = [$i + 1, $mk->kode_mk, $mk->nama_mk, $penanggungJawab];

            foreach ($selectedTahapList as $tahap) {
                foreach ($tahap->kategoriTahap->jenis_dokumen ?? [] as $jenis) {
                    $dok = $mk->dokumen()
                        ->where('tahap_id', $tahap->id)
                        ->where('jenis_dokumen', $jenis)
                        ->where('is_current', true)
                        ->first();

                    if (! $dok) {
                        $row[] = 'Belum Diupload';
                    } elseif ($dok->status === 'approved') {
                        $row[] = 'Lengkap';
                    } elseif ($dok->status === 'revisi') {
                        $row[] = 'Perlu Revisi';
                    } else {
                        $row[] = 'Pending Verifikasi';
                    }
                }
            }

            $rows[] = $row;
        }

        /*──────────────────────────────────────────
         | Footer – persentase kelengkapan
         ──────────────────────────────────────────*/
        $footerRow = ['', '', 'Kelengkapan (%)', ''];
        foreach ($selectedTahapList as $tahap) {
            foreach ($tahap->kategoriTahap->jenis_dokumen ?? [] as $jenis) {
                $approvedCount = 0;
                foreach ($mataKuliahList as $mk) {
                    if ($mk->dokumen()
                        ->where('tahap_id', $tahap->id)
                        ->where('jenis_dokumen', $jenis)
                        ->where('is_current', true)
                        ->where('status', 'approved')
                        ->exists()) {
                        $approvedCount++;
                    }
                }
                $total      = $mataKuliahList->count();
                $percentage = $total > 0 ? round(($approvedCount / $total) * 100) : 0;
                $footerRow[] = "{$approvedCount}/{$total} ({$percentage}%)";
            }
        }
        $rows[] = $footerRow;

        /*──────────────────────────────────────────
         | Generate filename
         ──────────────────────────────────────────*/
        $semLabel    = $semester ? str_replace([' ', '/'], '_', $semester->label) : 'semua';
        $tahapLabel  = $tahapFilter === 'semua' ? 'semua_tahap' : 'tahap_' . $tahapFilter;
        $filename    = 'laporan_dokumen_' . $semLabel . '_' . $tahapLabel . '_' . now()->format('Ymd') . '.xlsx';

        return Excel::download(new LaporanExport($rows, $headerRows), $filename);
    }
}
