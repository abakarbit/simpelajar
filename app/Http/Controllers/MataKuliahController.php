<?php

namespace App\Http\Controllers;

use App\Models\MataKuliah;
use App\Models\User;
use App\Models\Tahap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MataKuliahController extends Controller
{
    /**
     * List all mata kuliah
     * Dosen: view only MK they are assigned to (via dosen_mata_kuliah pivot table)
     * GKMP: view all MK
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = MataKuliah::query();

        if ($user->isDosen()) {
            // For dosen: show only MK they teach (via pivot table)
            $query->whereExists(function ($subQuery) use ($user) {
                $subQuery->select(DB::raw(1))
                    ->from('dosen_mata_kuliah')
                    ->whereColumn('dosen_mata_kuliah.mata_kuliah_id', 'mata_kuliah.id')
                    ->where('dosen_mata_kuliah.dosen_id', $user->id);
            });
        }

        $mataKuliah = $query->paginate(10)->withQueryString();

        return view('mata-kuliah.index', compact('mataKuliah'));
    }

    public function create()
    {
        abort_unless(Auth::user()->isGkmp(), 403, 'Hanya GKMP yang dapat menambah mata kuliah.');

        return view('mata-kuliah.create');
    }

    public function store(Request $request)
    {
        abort_unless(Auth::user()->isGkmp(), 403);

        $request->validate([
            'nama_mk' => 'required|string|max:255',
            'kode_mk' => 'required|string|max:20|unique:mata_kuliah,kode_mk',
        ]);

        MataKuliah::create([
            'nama_mk' => $request->nama_mk,
            'kode_mk' => strtoupper($request->kode_mk),
        ]);

        return redirect()->route('mata-kuliah.index')
            ->with('success', 'Mata kuliah berhasil ditambahkan.');
    }

    public function show(MataKuliah $mataKuliah)
    {
        $this->authorizeView($mataKuliah);

        $tahapList = Tahap::orderBy('kategori_tahap_id')->get();
        $dokumenPerTahap = [];
        foreach ($tahapList as $tahap) {
            $dokumenPerTahap[$tahap->id] = $mataKuliah->dokumen()
                ->where('tahap_id', $tahap->id)
                ->where('is_current', true)
                ->with('uploader')
                ->get();
        }
        return view('mata-kuliah.show', compact('mataKuliah', 'tahapList', 'dokumenPerTahap'));
    }

    public function edit(MataKuliah $mataKuliah)
    {
        abort_unless(Auth::user()->isGkmp(), 403, 'Hanya GKMP yang dapat mengedit mata kuliah.');
        return view('mata-kuliah.edit', compact('mataKuliah'));
    }

    public function update(Request $request, MataKuliah $mataKuliah)
    {
        abort_unless(Auth::user()->isGkmp(), 403);

        $request->validate([
            'nama_mk' => 'required|string|max:255',
            'kode_mk' => 'required|string|max:20|unique:mata_kuliah,kode_mk,' . $mataKuliah->id,
        ]);

        $mataKuliah->update([
            'nama_mk' => $request->nama_mk,
            'kode_mk' => strtoupper($request->kode_mk),
        ]);

        return redirect()->route('mata-kuliah.index')
            ->with('success', 'Mata kuliah berhasil diupdate.');
    }

    public function destroy(MataKuliah $mataKuliah)
    {
        abort_unless(Auth::user()->isGkmp(), 403);

        $mataKuliah->delete();
        return redirect()->route('mata-kuliah.index')
            ->with('success', 'Mata kuliah berhasil dihapus.');
    }

    private function authorizeView(MataKuliah $mk): void
    {
        $user = Auth::user();
        // Dosen can only view MK they are assigned to in pivot table; GKMP can view all
        if ($user->isDosen()) {
            $isAssigned = DB::table('dosen_mata_kuliah')
                ->where('mata_kuliah_id', $mk->id)
                ->where('dosen_id', $user->id)
                ->exists();
            if (!$isAssigned) {
                abort(403, 'Anda tidak memiliki akses ke mata kuliah ini.');
            }
        }
    }
}
