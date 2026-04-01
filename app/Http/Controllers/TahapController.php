<?php

namespace App\Http\Controllers;

use App\Models\Tahap;
use App\Models\Semester;
use App\Models\KategoriTahap;
use Illuminate\Http\Request;

class TahapController extends Controller
{
    public function index(Request $request)
    {
        $semesterId = $request->semester_id;

        if ($semesterId) {
            $tahapList = Tahap::where('semester_id', $semesterId)
                ->with('kategoriTahap')
                ->orderBy('kategori_tahap_id', 'asc')
                ->get();
        } else {
            $tahapList = Tahap::with('kategoriTahap')
                ->orderBy('kategori_tahap_id', 'asc')
                ->get();
        }

        $semesterList = Semester::orderBy('tahun_ajaran', 'desc')->get();
        return view('tahap.index', compact('tahapList', 'semesterList', 'semesterId'));
    }

    public function create()
    {
        $semesterList = Semester::orderBy('tahun_ajaran', 'desc')->get();
        $kategoriTahapList = KategoriTahap::orderBy('urutan')->get();
        return view('tahap.create', compact('semesterList', 'kategoriTahapList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'semester_id' => 'required|exists:semester,id',
            'kategori_tahap_id' => 'required|exists:kategori_tahap,id',
            'deadline' => 'required|date_format:Y-m-d\TH:i',
        ]);

        Tahap::create([
            'semester_id' => $request->semester_id,
            'kategori_tahap_id' => $request->kategori_tahap_id,
            'deadline' => $request->deadline,
        ]);

        return redirect()->route('tahap.index')->with('success', 'Tahap berhasil ditambahkan.');
    }

    public function edit(Tahap $tahap)
    {
        $semesterList = Semester::orderBy('tahun_ajaran', 'desc')->get();
        $kategoriTahapList = KategoriTahap::orderBy('urutan')->get();
        return view('tahap.edit', compact('tahap', 'semesterList', 'kategoriTahapList'));
    }

    public function update(Request $request, Tahap $tahap)
    {
        $request->validate([
            'semester_id' => 'required|exists:semester,id',
            'kategori_tahap_id' => 'required|exists:kategori_tahap,id',
            'deadline' => 'required|date_format:Y-m-d\TH:i',
        ]);

        $tahap->update([
            'semester_id' => $request->semester_id,
            'kategori_tahap_id' => $request->kategori_tahap_id,
            'deadline' => $request->deadline,
        ]);

        return redirect()->route('tahap.index')->with('success', 'Tahap berhasil diperbarui.');
    }

    public function destroy(Tahap $tahap)
    {
        $tahap->delete();
        return redirect()->route('tahap.index')->with('success', 'Tahap berhasil dihapus.');
    }
}
