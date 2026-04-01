<?php

namespace App\Http\Controllers;

use App\Models\MataKuliah;
use App\Models\Dokumen;
use App\Models\Tahap;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $semesterAktif = Semester::getActive();
        $tahapList = Tahap::with('kategoriTahap')
            ->when($semesterAktif, fn($q) => $q->where('semester_id', $semesterAktif->id))
            ->orderBy('kategori_tahap_id')
            ->get();

        // Find current active tahap (first tahap with deadline > now)
        $activeTahap = $tahapList->first(function($tahap) {
            return $tahap->deadline && now()->lessThan($tahap->deadline);
        });

        if ($user->isGkmp()) {
            // Filter mata kuliah berdasarkan semester aktif via pivot table
            $query = MataKuliah::with([
                'dokumen',
                'dosen' => function ($q) use ($semesterAktif) {
                    $q->wherePivot('status_dosen', 'Penanggung Jawab');
                    if ($semesterAktif) {
                        $q->wherePivot('semester_id', $semesterAktif->id);
                    }
                },
            ])->orderBy('nama_mk');

            if ($semesterAktif) {
                $query->whereIn('id',
                    \DB::table('dosen_mata_kuliah')
                        ->where('dosen_mata_kuliah.status_dosen', 'Penanggung Jawab')
                        ->where('semester_id', $semesterAktif->id)
                        ->distinct()
                        ->pluck('mata_kuliah_id')
                );
            }

            $mataKuliahList = $query->get();

            $stats = [
                'total_mk'       => $mataKuliahList->count(),
                'total_dokumen'  => Dokumen::where('is_current', true)->count(),
                'pending'        => Dokumen::where('is_current', true)->where('status', 'pending')->count(),
                'revisi'         => Dokumen::where('is_current', true)->where('status', 'revisi')->count(),
                'approved'       => Dokumen::where('is_current', true)->where('status', 'approved')->count(),
            ];
            return view('dashboard.gkmp', compact('mataKuliahList', 'activeTahap', 'stats', 'semesterAktif'));
        }

        // Dosen
        $query = MataKuliah::whereIn('id',
            DB::table('dosen_mata_kuliah')
                ->where('dosen_id', $user->id)
                ->distinct()
                ->pluck('mata_kuliah_id')
        )->with('dokumen');

        if ($semesterAktif) {
            $query->whereIn('id',
                DB::table('dosen_mata_kuliah')
                    ->where('dosen_id', $user->id)
                    ->where('semester_id', $semesterAktif->id)
                    ->distinct()
                    ->pluck('mata_kuliah_id')
            );
        }

        $mataKuliahList = $query->get();
        return view('dashboard.dosen', compact('mataKuliahList', 'activeTahap', 'tahapList', 'semesterAktif'));
    }
}
