<?php

namespace App\Http\Controllers;

use App\Models\MataKuliah;
use App\Models\User;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DosenController extends Controller
{
    /**
     * Display a listing of all dosen assignments
     */
    public function index(Request $request)
    {
        abort_unless(Auth::user()->isGkmp(), 403, 'Hanya GKMP yang dapat mengelola dosen.');

        $query = DB::table('dosen_mata_kuliah')
                    ->join('users', 'users.id', '=', 'dosen_mata_kuliah.dosen_id')
                    ->join('mata_kuliah', 'mata_kuliah.id', '=', 'dosen_mata_kuliah.mata_kuliah_id')
                    ->leftJoin('semester', 'semester.id', '=', 'dosen_mata_kuliah.semester_id')
                    ->select(
                        'dosen_mata_kuliah.id',
                        'dosen_mata_kuliah.dosen_id',
                        'dosen_mata_kuliah.mata_kuliah_id',
                        'dosen_mata_kuliah.lokasi',
                        'dosen_mata_kuliah.semester_id',
                        'dosen_mata_kuliah.status_dosen',
                        'users.name as dosen_name',
                        'users.email as dosen_email',
                        'mata_kuliah.kode_mk',
                        'mata_kuliah.nama_mk',
                        'semester.nama_semester as semester_label'
                    );

        // Filter by mata kuliah if provided
        if ($request->filled('mata_kuliah_id')) {
            $query->where('dosen_mata_kuliah.mata_kuliah_id', $request->mata_kuliah_id);
        }

        // Filter by dosen if provided
        if ($request->filled('dosen_id')) {
            $query->where('dosen_mata_kuliah.dosen_id', $request->dosen_id);
        }

        // Filter by semester if provided
        if ($request->filled('semester_id')) {
            $query->where('dosen_mata_kuliah.semester_id', $request->semester_id);
        }

        $assignments = $query->orderBy('mata_kuliah.nama_mk')
                             ->orderBy('users.name')
                             ->paginate(15)
                             ->withQueryString();

        // Get filter options
        $mataKuliahList = MataKuliah::orderBy('nama_mk')->get();
        $dosenList = User::where('role', 'dosen')
                         ->where('is_active', true)
                         ->orderBy('name')
                         ->get();
        $semesterList = Semester::orderByDesc('is_active')
                                ->orderByDesc('id')
                                ->get();

        return view('dosen.index', compact('assignments', 'mataKuliahList', 'dosenList', 'semesterList'));
    }

    /**
     * Show form to create a new dosen assignment
     */
    public function create()
    {
        abort_unless(Auth::user()->isGkmp(), 403, 'Hanya GKMP yang dapat menambah penugasan dosen.');

        $dosenList = User::where('role', 'dosen')
                        ->where('is_active', true)
                        ->orderBy('name')
                        ->get();

        $mataKuliahList = MataKuliah::orderBy('nama_mk')->get();

        $semesterList = Semester::orderByDesc('is_active')
                                ->orderByDesc('id')
                                ->get();

        return view('dosen.create', compact('dosenList', 'mataKuliahList', 'semesterList'));
    }

    /**
     * Store a newly created dosen assignment
     */
    public function store(Request $request)
    {
        abort_unless(Auth::user()->isGkmp(), 403);

        $request->validate([
            'dosen_id' => 'required|exists:users,id',
            'mata_kuliah_id' => 'required|exists:mata_kuliah,id',
            'lokasi' => 'required|string|max:100',
            'status_dosen' => 'required|string|in:Penanggung Jawab,Pengampu',
            'semester_id' => 'required|exists:semester,id',
        ]);

        // Check if assignment already exists (same dosen, mata kuliah, and semester)
        $exists = DB::table('dosen_mata_kuliah')
                    ->where('dosen_id', $request->dosen_id)
                    ->where('mata_kuliah_id', $request->mata_kuliah_id)
                    ->where('semester_id', $request->semester_id)
                    ->exists();

        if ($exists) {
            return redirect()->back()
                            ->with('error', 'Penugasan dosen untuk mata kuliah ini sudah ada.')
                            ->withInput();
        }

        DB::table('dosen_mata_kuliah')->insert([
            'dosen_id' => $request->dosen_id,
            'mata_kuliah_id' => $request->mata_kuliah_id,
            'lokasi' => $request->lokasi,
            'semester_id' => $request->semester_id,
            'status_dosen' => $request->status_dosen,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $dosen = User::find($request->dosen_id);
        $mataKuliah = MataKuliah::find($request->mata_kuliah_id);

        return redirect()->route('dosen.index')
                        ->with('success', 'Dosen "' . $dosen->name . '" berhasil ditugaskan ke mata kuliah "' . $mataKuliah->nama_mk . '".');
    }

    /**
     * Show form to edit a dosen assignment
     */
    public function edit($id)
    {
        abort_unless(Auth::user()->isGkmp(), 403, 'Hanya GKMP yang dapat mengedit penugasan dosen.');

        $assignment = DB::table('dosen_mata_kuliah')
                        ->where('dosen_mata_kuliah.id', $id)
                        ->join('users', 'users.id', '=', 'dosen_mata_kuliah.dosen_id')
                        ->join('mata_kuliah', 'mata_kuliah.id', '=', 'dosen_mata_kuliah.mata_kuliah_id')
                        ->select(
                            'dosen_mata_kuliah.id',
                            'dosen_mata_kuliah.dosen_id',
                            'dosen_mata_kuliah.mata_kuliah_id',
                            'dosen_mata_kuliah.lokasi',
                            'dosen_mata_kuliah.semester_id',
                            'users.name as dosen_name',
                            'mata_kuliah.nama_mk',
                            'mata_kuliah.kode_mk',
                            'dosen_mata_kuliah.status_dosen'
                        )
                        ->first();

        abort_if(!$assignment, 404, 'Penugasan tidak ditemukan.');

        $dosenList = User::where('role', 'dosen')
                        ->where('is_active', true)
                        ->orderBy('name')
                        ->get();

        $mataKuliahList = MataKuliah::orderBy('nama_mk')->get();

        $semesterList = Semester::orderByDesc('is_active')
                                ->orderByDesc('id')
                                ->get();

        return view('dosen.edit', compact('assignment', 'dosenList', 'mataKuliahList', 'semesterList'));
    }

    /**
     * Update a dosen assignment
     */
    public function update(Request $request, $id)
    {
        abort_unless(Auth::user()->isGkmp(), 403);

        $assignment = DB::table('dosen_mata_kuliah')->find($id);
        abort_if(!$assignment, 404, 'Penugasan tidak ditemukan.');

        $request->validate([
            'dosen_id' => 'required|exists:users,id',
            'mata_kuliah_id' => 'required|exists:mata_kuliah,id',
            'lokasi' => 'required|string|max:100',
            'semester_id' => 'required|exists:semester,id',
            'status_dosen' => 'required|string|in:Penanggung Jawab,Pengampu',
        ]);

        // Check if another assignment with same dosen-matakuliah-semester exists
        $exists = DB::table('dosen_mata_kuliah')
                    ->where('dosen_mata_kuliah.id', '!=', $id)
                    ->where('dosen_id', $request->dosen_id)
                    ->where('mata_kuliah_id', $request->mata_kuliah_id)
                    ->where('semester_id', $request->semester_id)
                    ->exists();

        if ($exists) {
            return redirect()->back()
                            ->with('error', 'Penugasan dosen untuk mata kuliah ini sudah ada.')
                            ->withInput();
        }

        DB::table('dosen_mata_kuliah')
            ->where('dosen_mata_kuliah.id', $id)
            ->update([
                'dosen_id' => $request->dosen_id,
                'mata_kuliah_id' => $request->mata_kuliah_id,
                'lokasi' => $request->lokasi,
                'semester_id' => $request->semester_id,
                'status_dosen' => $request->status_dosen,
                'updated_at' => now(),
            ]);

        return redirect()->route('dosen.index')
                        ->with('success', 'Penugasan dosen berhasil diperbarui.');
    }

    /**
     * Delete a dosen assignment
     */
    public function destroy($id)
    {
        abort_unless(Auth::user()->isGkmp(), 403);

        $assignment = DB::table('dosen_mata_kuliah')
                        ->where('id', $id)
                        ->join('users', 'users.id', '=', 'dosen_mata_kuliah.dosen_id')
                        ->join('mata_kuliah', 'mata_kuliah.id', '=', 'dosen_mata_kuliah.mata_kuliah_id')
                        ->select('dosen_mata_kuliah.id', 'users.name as dosen_name', 'mata_kuliah.nama_mk')
                        ->first();

        abort_if(!$assignment, 404, 'Penugasan tidak ditemukan.');

        DB::table('dosen_mata_kuliah')->where('dosen_mata_kuliah.id', $id)->delete();

        return redirect()->route('dosen.index')
                        ->with('success', 'Penugasan dosen "' . $assignment->dosen_name . '" dihapus.');
    }
}
