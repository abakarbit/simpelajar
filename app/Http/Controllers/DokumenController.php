<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\MataKuliah;
use App\Models\Tahap;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class DokumenController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $tahapList = Tahap::with('kategoriTahap')
            ->when($request->filled('semester_id'), fn($q) => $q->where('semester_id', $request->semester_id))
            ->orderBy('kategori_tahap_id')
            ->get();

        // Get semester list based on user role
        if ($user->isGkmp()) {
            $semesterList = Semester::orderBy('tahun_ajaran', 'desc')->get();
            $mataKuliahList = MataKuliah::orderBy('nama_mk')->get();
        } else {
            // For Dosen, only show semesters from their mata kuliah
            $semesterList = Semester::whereIn('id',
                DB::table('dosen_mata_kuliah')->where('dosen_id', $user->id)->distinct()->pluck('semester_id')
            )->orderBy('tahun_ajaran', 'desc')->get();
            $mataKuliahList = MataKuliah::whereIn('id',
                DB::table('dosen_mata_kuliah')->where('dosen_id', $user->id)->distinct()->pluck('mata_kuliah_id')
            )->orderBy('nama_mk')->get();
        }

        // Eager load relationships with necessary fields
        $query = Dokumen::with([
            'mataKuliah:id,nama_mk,kode_mk',
            'tahap:id,kategori_tahap_id,deadline,semester_id',
            'tahap.kategoriTahap:id,nama_tahap,urutan',
            'tahap.semester:id,nama_semester,tahun_ajaran',
            'uploader:id,name,email'
        ]);

        if ($user->isDosen()) {
            // Filter dokumen yang mata kuliahnya diberikan ke dosen via pivot table
            $query->whereIn(
                'mata_kuliah_id',
                DB::table('dosen_mata_kuliah')->where('dosen_id', $user->id)->distinct()->pluck('mata_kuliah_id')
            );
        }

        if ($request->filled('tahap_id')) {
            $query->where('tahap_id', $request->tahap_id);
        }
        if ($request->filled('mata_kuliah_id')) {
            $query->where('mata_kuliah_id', $request->mata_kuliah_id);
        }
        if ($request->filled('semester_id')) {
            // Filter dokumen by semester through pivot table
            $query->whereIn('mata_kuliah_id',
                DB::table('dosen_mata_kuliah')
                    ->where('semester_id', $request->semester_id)
                    ->distinct()
                    ->pluck('mata_kuliah_id')
            );
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $dokumen = $query->where('is_current', true)->latest()->paginate(15)->withQueryString();

        return view('dokumen.index', compact('dokumen', 'tahapList', 'mataKuliahList', 'semesterList'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $tahapList = Tahap::with('kategoriTahap')->orderBy('kategori_tahap_id')->get();
        $selectedMk = $request->mata_kuliah_id;
        $selectedTahap = $request->tahap_id;
        $selectedSemester = $request->semester_id;

        // Get semester list based on user role
        if ($user->isGkmp()) {
            $semesterList = Semester::orderBy('tahun_ajaran', 'desc')->get();
            $mataKuliahList = MataKuliah::orderBy('nama_mk')->get();
        } else {
            // For Dosen, only show mata kuliah they are assigned to
            $semesterList = Semester::whereIn('id',
                DB::table('dosen_mata_kuliah')
                    ->where('dosen_id', $user->id)
                    ->distinct()
                    ->pluck('semester_id')
            )->orderBy('tahun_ajaran', 'desc')->get();

            $mataKuliahList = MataKuliah::whereIn('id',
                DB::table('dosen_mata_kuliah')
                    ->where('dosen_id', $user->id)
                    ->distinct()
                    ->pluck('mata_kuliah_id')
            )->orderBy('nama_mk')->get();
        }

        // Check if there are any approved documents for this user
        $hasApprovedDocs = Dokumen::where('uploaded_by', $user->id)
            ->where('status', 'approved')
            ->exists();

        return view('dokumen.create', compact('mataKuliahList', 'tahapList', 'selectedMk', 'selectedTahap', 'selectedSemester', 'semesterList', 'hasApprovedDocs'));
    }

    public function store(Request $request)
    {
        // Validate required fields
        $request->validate([
            'mata_kuliah_id' => 'required|exists:mata_kuliah,id',
            'tahap_id'       => 'required|exists:tahap,id',
        ]);

        $mk = MataKuliah::findOrFail($request->mata_kuliah_id);

        // Check if current dosen is assigned to this mata kuliah
        $isAssigned = DB::table('dosen_mata_kuliah')
            ->where('dosen_id', Auth::id())
            ->where('mata_kuliah_id', $mk->id)
            ->exists();

        if ($isAssigned === false) {
            abort(403);
        }

        $tahap = Tahap::findOrFail($request->tahap_id);

        // Check if there are any files uploaded
        $hasFile = false;
        $fileCount = 0;
        while ($request->hasFile("file_$fileCount")) {
            if ($request->file("file_$fileCount")->isValid()) {
                $hasFile = true;
            }
            $fileCount++;
        }

        // Check existing dokumen for this mata kuliah + tahap (shared across all dosen)
        // This prevents duplicate uploads of the same jenis from different dosens
        $existingDokumen = Dokumen::where('mata_kuliah_id', $request->mata_kuliah_id)
            ->where('tahap_id', $request->tahap_id)
            ->where('is_current', true)
            ->get();

        $hasRevisiDoc = $existingDokumen->where('status', 'revisi')->isNotEmpty();

        // Parse jenis dokumen - bisa JSON string atau array
        $jenisDokumenList = $tahap->jenis_dokumen ?? [];
        if (is_string($jenisDokumenList)) {
            $jenisDokumenList = json_decode($jenisDokumenList, true) ?? [];
        }
        $totalJenisDokumen = is_array($jenisDokumenList) ? count($jenisDokumenList) : 0;

        // PERBAIKAN: Validasi fokus pada revisi, pending, dan belum ada
        // Abaikan dokumen dengan status approved
        $needsNewDocument = false;
        $jenisDenganRevisiPending = [];
        $jenisDenganApproved = [];

        if ($totalJenisDokumen > 0) {
            foreach ($jenisDokumenList as $jenis) {
                $docsForJenis = $existingDokumen->where('jenis_dokumen', $jenis)->values();

                // Cek apakah semua dokumen approved
                $allApproved = $docsForJenis->isNotEmpty() && $docsForJenis->every(fn($doc) => $doc->status === 'approved');

                if ($allApproved) {
                    // Semua approved - tidak perlu validasi
                    $jenisDenganApproved[] = $jenis;
                } else {
                    // Filter dokumen yang BUKAN approved
                    $nonApprovedDocs = $docsForJenis->where('status', '!=', 'approved');

                    if ($nonApprovedDocs->isNotEmpty()) {
                        // Ada revisi/pending - bisa diupload ulang
                        $jenisDenganRevisiPending[] = $jenis;
                    } elseif ($docsForJenis->isEmpty()) {
                        // Belum ada dokumen - wajib upload
                        $needsNewDocument = true;
                        $jenisDenganRevisiPending[] = $jenis;
                    }
                }
            }
        } else {
            // Jika tidak ada jenis dokumen spesifik
            $needsNewDocument = false;
        }

        // LOGIKA VALIDASI YANG DIPERBAIKI:
        // 1. Jika ada jenis belum ada (needsNewDocument=true) → harus ada file baru
        // 2. Jika semua jenis sudah ada (approved/revisi/pending) → boleh submit tanpa file
        // if (!$hasFile && $needsNewDocument) {
        //     return back()->with('error', 'Harap upload minimal satu file dokumen untuk jenis yang masih kosong atau perlu revisi.')
        //                  ->withInput();
        // }

        $uploadedCount = 0;
        $timestamp = time();
        $uploadErrors = [];

        // Detect all files berdasarkan request->files, bukan hardcoded loop
        // Ini memastikan semua file yang di-upload terdeteksi, bahkan jika hanya 1 jenis
        $allFiles = $request->files->all();

        foreach ($allFiles as $fieldName => $fileInput) {
            // Parse field name: file_0, file_1, etc
            if (!preg_match('/^file_(\d+)$/', $fieldName, $matches)) {
                continue;
            }

            $fileIndex = $matches[1];
            $jenisDokumen = $request->input("jenis_$fileIndex");

            if (!$jenisDokumen) {
                continue;
            }

            // Handle single or multiple files
            $files = is_array($fileInput) ? $fileInput : [$fileInput];

            foreach ($files as $file) {
                if ($file === null || !$file->isValid()) {
                    $uploadErrors[] = "File tidak valid untuk jenis: $jenisDokumen";
                    continue;
                }

                // Validate file extension
                $allowedExtensions = ['pdf', 'doc', 'docx'];
                if (!in_array(strtolower($file->getClientOriginalExtension()), $allowedExtensions)) {
                    $uploadErrors[] = "Format file '{$file->getClientOriginalName()}' tidak didukung. Gunakan PDF, DOC, atau DOCX.";
                    continue;
                }

                // Validate file size (10MB)
                $maxSize = 10485760;
                if ($file->getSize() > $maxSize) {
                    $uploadErrors[] = "File '{$file->getClientOriginalName()}' terlalu besar (max 10MB).";
                    continue;
                }

                try {
                    $filename = $timestamp . '_' . uniqid() . '_' . $file->getClientOriginalName();
                    $path = Storage::disk('public')->putFileAs(
                        "mata_kuliah/{$mk->id}/tahap-{$request->tahap_id}",
                        $file,
                        $filename
                    );

                    if (!$path) {
                        throw new \Exception("Gagal menyimpan file ke storage");
                    }

                    // Delete dokumen pending lama DARI DOSEN INI untuk jenis yang sama (diganti dengan yang baru)
                    // Ini hanya berlaku untuk dokumen yang diupload oleh dosen saat ini
                    $pendingDokumen = Dokumen::where('mata_kuliah_id', $request->mata_kuliah_id)
                        ->where('tahap_id', $request->tahap_id)
                        ->where('jenis_dokumen', $jenisDokumen)
                        ->where('status', 'pending')
                        ->where('uploaded_by', Auth::id())
                        ->where('is_current', true)
                        ->first();

                    if ($pendingDokumen) {
                        // Hapus file lama dari storage
                        if ($pendingDokumen->file_path && Storage::disk('public')->exists($pendingDokumen->file_path)) {
                            Storage::disk('public')->delete($pendingDokumen->file_path);
                        }
                        // Delete record dokumen pending lama
                        $pendingDokumen->delete();
                        \Log::info('Dokumen pending diganti', [
                            'old_dokumen_id' => $pendingDokumen->id,
                            'jenis' => $jenisDokumen,
                            'file' => $pendingDokumen->file_path
                        ]);
                    }

                    // Check if there's an existing dokumen of same jenis with revisi status dari dosen saat ini
                    // Setiap dosen melacak revisi mereka sendiri
                    $parentDokumen = Dokumen::where('mata_kuliah_id', $request->mata_kuliah_id)
                        ->where('tahap_id', $request->tahap_id)
                        ->where('jenis_dokumen', $jenisDokumen)
                        ->where('status', 'revisi')
                        ->where('uploaded_by', Auth::id())
                        ->where('is_current', true)
                        ->first();

                    // Create new dokumen
                    $newDokumen = Dokumen::create([
                        'mata_kuliah_id' => $request->mata_kuliah_id,
                        'tahap_id'       => $request->tahap_id,
                        'nama_file'      => $file->getClientOriginalName(),
                        'jenis_dokumen'  => $jenisDokumen,
                        'file_path'      => $path,
                        'uploaded_by'    => Auth::id(),
                        'status'         => 'pending',
                        'parent_id'      => $parentDokumen?->id,
                        'is_current'     => true,
                    ]);

                    // If replacing revisi dokumen, mark parent as no longer current
                    if ($parentDokumen) {
                        $parentDokumen->update(['is_current' => false]);
                        \Log::info('Revisi dokumen diganti', [
                            'old_dokumen_id' => $parentDokumen->id,
                            'new_dokumen_id' => $newDokumen->id,
                            'jenis' => $jenisDokumen
                        ]);
                    }

                    $uploadedCount++;
                    \Log::info('Dokumen berhasil diupload', [
                        'dokumen_id' => $newDokumen->id,
                        'jenis' => $jenisDokumen,
                        'file' => $file->getClientOriginalName()
                    ]);
                } catch (\Exception $e) {
                    $errorMsg = $e->getMessage();
                    $uploadErrors[] = "Gagal upload '{$file->getClientOriginalName()}': $errorMsg";
                    \Log::error('Dokumen upload error: ' . $errorMsg, [
                        'file' => $file->getClientOriginalName(),
                        'jenis' => $jenisDokumen,
                        'user_id' => Auth::id(),
                        'mata_kuliah_id' => $request->mata_kuliah_id,
                        'tahap_id' => $request->tahap_id
                    ]);
                    continue;
                }
            }
        }

        // Handle result
        if ($uploadedCount === 0) {
            // Jika tidak ada file yang berhasil diupload
            if (!$hasFile) {
                // Jika user tidak submit file apapun
                return redirect()->route('dokumen.create', [
                    'mata_kuliah_id' => $request->mata_kuliah_id,
                    'tahap_id' => $request->tahap_id,
                ])->with('info', 'Tidak ada perubahan. Silakan pilih file untuk di-upload atau lakukan perubahan lainnya.');
            }

            // Jika ada file disubmit tapi tidak ada yang berhasil diupload
            $errorMessage = 'Tidak ada file yang berhasil diupload.';
            if (!empty($uploadErrors)) {
                $errorMessage .= ' Detail: ' . implode(' | ', $uploadErrors);
            } else {
                $errorMessage .= ' Pastikan file format PDF/DOC/DOCX dan ukuran kurang dari 10MB.';
            }

            return back()->with('error', $errorMessage)->withInput();
        }

        // Hanya tampilkan success jika ada file yang berhasil diupload
        $message = $uploadedCount === 1
            ? "1 dokumen berhasil diupload."
            : "$uploadedCount dokumen berhasil diupload.";

        if (!empty($uploadErrors)) {
            $message .= ' Namun ada beberapa file yang gagal: ' . implode(' | ', array_slice($uploadErrors, 0, 3));
        }

        return redirect()->route('dokumen.index')
            ->with('success', $message);
    }

    public function show(Dokumen $dokumen)
    {
        $this->authorizeView($dokumen);
        return view('dokumen.show', compact('dokumen'));
    }

    public function destroy(Dokumen $dokumen)
    {
        $user = Auth::user();

        // Dosen hanya bisa delete dokumen miliknya
        if ($user->isDosen() && $dokumen->uploaded_by !== $user->id) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Anda tidak memiliki otorisasi untuk menghapus dokumen ini.'], 403);
            }
            abort(403);
        }

        // Dosen tidak bisa delete dokumen approved, tapi GKMP bisa
        if ($dokumen->status === 'approved' && $user->isDosen()) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Dokumen yang sudah approved tidak dapat dihapus.'], 403);
            }
            return back()->with('error', 'Dokumen yang sudah approved tidak dapat dihapus.');
        }

        Storage::disk('public')->delete($dokumen->file_path);
        $dokumen->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Dokumen berhasil dihapus.']);
        }
        return back()->with('success', 'Dokumen berhasil dihapus.');
    }

    public function getJenisDokumen(Request $request)
    {
        $tahap = Tahap::find($request->tahap_id);
        return response()->json($tahap?->jenis_dokumen ?? []);
    }

    public function getMatakuliahBySemester(Request $request)
    {
        $request->validate([
            'semester_id' => 'required|exists:semester,id',
        ]);

        $user = Auth::user();
        $semesterId = $request->semester_id;

        if ($user->isGkmp()) {
            // GKMP: Get all mata kuliah in this semester
            $mataKuliahList = MataKuliah::whereIn('id',
                DB::table('dosen_mata_kuliah')
                    ->where('semester_id', $semesterId)
                    ->distinct()
                    ->pluck('mata_kuliah_id')
            )->orderBy('kode_mk')
                ->get();
        } else {
            // Dosen: Get only their assigned mata kuliah in this semester
            $mataKuliahList = MataKuliah::whereIn('id',
                DB::table('dosen_mata_kuliah')
                    ->where('dosen_id', $user->id)
                    ->where('semester_id', $semesterId)
                    ->distinct()
                    ->pluck('mata_kuliah_id')
            )->orderBy('kode_mk')
                ->get();
        }

        return response()->json([
            'mataKuliah' => $mataKuliahList,
            'count' => $mataKuliahList->count(),
        ]);
    }

    public function getTahapBySemester(Request $request)
    {
        $request->validate([
            'semester_id' => 'required|exists:semester,id',
        ]);

        $tahapList = Tahap::with('kategoriTahap')
            ->where('semester_id', $request->semester_id)
            ->orderBy('kategori_tahap_id')
            ->get()
            ->map(fn($t) => [
                'id'           => $t->id,
                'nama_tahap'   => $t->kategoriTahap->nama_tahap ?? '-',
                'deskripsi'    => $t->kategoriTahap->deskripsi ?? '',
                'jenis_dokumen' => $t->kategoriTahap->jenis_dokumen ?? [],
                'deadline'     => $t->deadline?->format('d M Y'),
            ]);

        return response()->json(['tahap' => $tahapList]);
    }

    public function checkExistingDokumen(Request $request)
    {
        $request->validate([
            'mata_kuliah_id' => 'required|exists:mata_kuliah,id',
            'tahap_id'       => 'required|exists:tahap,id',
        ]);

        $user = Auth::user();
        $mk = MataKuliah::findOrFail($request->mata_kuliah_id);

        // Check if user is authorized (dosen must be assigned to this MK)
        if ($user->isDosen()) {
            $isAssigned = DB::table('dosen_mata_kuliah')
                ->where('dosen_id', $user->id)
                ->where('mata_kuliah_id', $mk->id)
                ->exists();

            if (!$isAssigned) {
                abort(403);
            }
        }

        // Get ALL dokumen for this mata kuliah + tahap (shared across all dosen)
        // This allows multiple dosens to see each other's uploads and complement them
        $dokumen = Dokumen::where('mata_kuliah_id', $request->mata_kuliah_id)
            ->where('tahap_id', $request->tahap_id)
            ->where('is_current', true)
            ->with('uploader:id,name,email')
            ->get(['id', 'nama_file', 'jenis_dokumen', 'status', 'created_at', 'uploaded_by']);

        // Group dokumen by jenis_dokumen
        $dokumenByJenis = $dokumen->groupBy('jenis_dokumen')->map(function($items) {
            return $items->values()->all();
        })->toArray();

        return response()->json([
            'dokumen' => $dokumen,
            'dokumenByJenis' => $dokumenByJenis,
            'hasApproved' => $dokumen->where('status', 'approved')->isNotEmpty(),
            'isGkmp' => $user->isGkmp(),
            'currentUserId' => $user->id,
        ]);
    }

    private function authorizeView(Dokumen $dokumen): void
    {
        $user = Auth::user();
        if ($user->isDosen() && $dokumen->uploaded_by !== $user->id) {
            abort(403);
        }
    }
}
