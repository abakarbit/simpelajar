<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use App\Models\MataKuliah;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SemesterController extends Controller
{
    public function __construct()
    {
        // GKMP only — enforced in routes and here as double check
        abort_unless(auth()->user()?->isGkmp(), 403);
    }

    public function index()
    {
        $semesters = Semester::orderBy('is_active', 'desc')->orderBy('tahun_ajaran', 'desc')->orderBy('tipe')->get();
        return view('semester.index', compact('semesters'));
    }

    public function create()
    {
        return view('semester.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_semester' => 'required|string|max:100',
            'tahun_ajaran'  => 'required|string|max:9|regex:/^\d{4}\/\d{4}$/',
            'tipe'          => 'required|in:ganjil,genap',
            'is_active'     => 'boolean',
        ]);

        if ($request->boolean('is_active')) {
            Semester::where('is_active', true)->update(['is_active' => false]);
        }

        Semester::create([
            'nama_semester' => $request->nama_semester,
            'tahun_ajaran'  => $request->tahun_ajaran,
            'tipe'          => $request->tipe,
            'is_active'     => $request->boolean('is_active'),
        ]);

        return redirect()->route('semester.index')
            ->with('success', 'Semester berhasil ditambahkan.');
    }

    public function edit(Semester $semester)
    {
        return view('semester.edit', compact('semester'));
    }

    public function show(Request $request, Semester $semester)
    {
        // Get MataKuliah in this semester via pivot table
        $query = MataKuliah::whereIn('id',
            DB::table('dosen_mata_kuliah')
                ->where('semester_id', $semester->id)
                ->distinct()
                ->pluck('mata_kuliah_id')
        );

        // Filter by dosen if provided
        if ($request->filled('dosen_id')) {
            $query->whereIn('id',
                DB::table('dosen_mata_kuliah')
                    ->where('semester_id', $semester->id)
                    ->where('dosen_id', $request->dosen_id)
                    ->distinct()
                    ->pluck('mata_kuliah_id')
            );
        }

        $mataKuliah = $query->with('dosen')->paginate(15)->withQueryString();

        // Get list of dosen teaching in this semester
        $dosenList = User::whereIn('id',
            DB::table('dosen_mata_kuliah')
                ->where('semester_id', $semester->id)
                ->distinct()
                ->pluck('dosen_id')
        )->where('is_active', true)->orderBy('name')->get();

        return view('semester.show', compact('semester', 'mataKuliah', 'dosenList'));
    }

    public function update(Request $request, Semester $semester)
    {
        $request->validate([
            'nama_semester' => 'required|string|max:100',
            'tahun_ajaran'  => 'required|string|max:9|regex:/^\d{4}\/\d{4}$/',
            'tipe'          => 'required|in:ganjil,genap',
            'is_active'     => 'boolean',
        ]);

        if ($request->boolean('is_active') && !$semester->is_active) {
            Semester::where('is_active', true)->update(['is_active' => false]);
        }

        $semester->update([
            'nama_semester' => $request->nama_semester,
            'tahun_ajaran'  => $request->tahun_ajaran,
            'tipe'          => $request->tipe,
            'is_active'     => $request->boolean('is_active'),
        ]);

        return redirect()->route('semester.index')
            ->with('success', 'Semester berhasil diupdate.');
    }

    public function setActive(Semester $semester)
    {
        Semester::where('is_active', true)->update(['is_active' => false]);
        $semester->update(['is_active' => true]);
        return back()->with('success', "Semester \"{$semester->nama_semester}\" dijadikan aktif.");
    }

    public function destroy(Semester $semester)
    {
        if ($semester->mataKuliah()->count() > 0) {
            return back()->with('error', 'Semester tidak bisa dihapus karena masih digunakan oleh mata kuliah.');
        }
        $semester->delete();
        return redirect()->route('semester.index')
            ->with('success', 'Semester berhasil dihapus.');
    }
}
