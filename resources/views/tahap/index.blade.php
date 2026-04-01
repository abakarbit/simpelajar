@extends('layouts.app')

@section('title', 'Manajemen Tahap')

@section('breadcrumb')
<li class="breadcrumb-item text-muted">GKMP</li>
<li class="breadcrumb-item active">Tahap</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Manajemen Tahap Upload</h4>
        <small class="text-muted">Kelola tahap dan deadline upload dokumen per semester</small>
    </div>
    <a href="{{ route('tahap.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Tambah Tahap
    </a>
</div>

<!-- Filter Semester -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="d-flex gap-2 align-items-end">
            <div class="flex-grow-1">
                <label class="form-label fw-semibold mb-1">Filter Semester</label>
                <select name="semester_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Semua Semester --</option>
                    @foreach($semesterList as $semester)
                    <option value="{{ $semester->id }}" {{ $semesterId == $semester->id ? 'selected' : '' }}>
                        {{ $semester->label }} - {{ $semester->tahun_ajaran }}
                    </option>
                    @endforeach
                </select>
            </div>
            @if($semesterId)
            <a href="{{ route('tahap.index') }}" class="btn btn-outline-secondary">Reset</a>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Semester</th>
                    <th>Nama Tahap</th>
                    <th>Deskripsi</th>
                    <th>Deadline</th>
                    <th>Jenis Dokumen</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tahapList as $tahap)
                <tr>
                    <td class="text-muted">{{ $tahap->kategoriTahap->urutan }}</td>
                    <td>
                        <span class="badge bg-primary">{{ $tahap->semester?->label ?? '-' }}</span>
                    </td>
                    <td class="fw-semibold">{{ $tahap->kategoriTahap->nama_tahap }}</td>
                    <td class="text-muted">{{ Str::limit($tahap->kategoriTahap->deskripsi ?? '-', 50) }}</td>
                    <td>
                        @if($tahap->deadline)
                            <span class="badge bg-{{ now() > $tahap->deadline ? 'danger' : 'success' }}">
                                {{ $tahap->deadline->format('d M Y H:i') }}
                            </span>
                        @else
                            <span class="badge bg-secondary">Belum ditentukan</span>
                        @endif
                    </td>
                    <td>
                        @if($tahap->kategoriTahap->jenis_dokumen)
                            <span class="badge bg-info">{{ count($tahap->kategoriTahap->jenis_dokumen) }} jenis</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="{{ route('tahap.edit', $tahap) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('tahap.destroy', $tahap) }}" method="POST"
                                  onsubmit="return confirm('Hapus tahap {{ $tahap->nama_tahap }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="bi bi-layers fs-4 d-block mb-1"></i>
                        Belum ada tahap. <a href="{{ route('tahap.create') }}">Tambah sekarang</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
