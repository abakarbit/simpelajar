<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\MataKuliah;
use App\Models\Semester;
use App\Models\Tahap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ValidasiController extends Controller
{
    public function index(Request $request)
    {
        $semesterAktif = Semester::getActive();
        $tahapList = Tahap::where('semester_id', $semesterAktif->id)->orderBy('kategori_tahap_id')->get();
        $mataKuliahList = MataKuliah::orderBy('nama_mk')->get();

        $query = Dokumen::with(['mataKuliah', 'tahap', 'uploader',]);

        if ($request->filled('tahap_id')) {
            $query->where('tahap_id', $request->tahap_id);
        }
        if ($request->filled('mata_kuliah_id')) {
            $query->where('mata_kuliah_id', $request->mata_kuliah_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'pending');
        }

        $dokumen = $query->latest()->paginate(15)->withQueryString();

        return view('validasi.index', compact('dokumen', 'tahapList', 'mataKuliahList'));
    }

    public function update(Request $request, Dokumen $dokumen)
    {
        $request->validate([
            'status'   => 'required|in:approved,revisi',
            'komentar' => 'nullable|string|max:1000',
        ]);

        // Jika approve dokumen yang adalah revisi dari dokumen lama
        if ($request->status === 'approved' && $dokumen->parent_id) {
            // Mark parent (dokumen lama) as no longer current
            $dokumen->parent()->update(['is_current' => false]);
            // Mark this revision as current
            $dokumen->is_current = true;
        } elseif ($request->status === 'approved') {
            // Dokumen baru yang di-approve, mark as current
            $dokumen->is_current = true;
        }

        $dokumen->update([
            'status'     => $request->status,
            'komentar'   => $request->komentar,
            'is_current' => $dokumen->is_current ?? $dokumen->is_current,
        ]);

        $msg = $request->status === 'approved' ? 'Dokumen berhasil di-approve.' : 'Dokumen dikembalikan untuk revisi.';
        return back()->with('success', $msg);
    }

    public function show(Dokumen $dokumen)
    {
        $dokumen->load(['mataKuliah.dosen', 'tahap', 'uploader']);
        return view('validasi.show', compact('dokumen'));
    }

    public function bulkApprove(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:dokumen,id']);
        Dokumen::whereIn('id', $request->ids)->update(['status' => 'approved']);
        return back()->with('success', count($request->ids) . ' dokumen berhasil di-approve.');
    }
}
