@extends('layouts.app')

@section('title', 'Kelola Dosen')

@section('breadcrumb')
<li class="breadcrumb-item active">Kelola Dosen</li>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0 fw-bold">Kelola Dosen</h4>
        <small class="text-muted">Mengelola penugasan dosen ke mata kuliah</small>
    </div>
    <a href="{{ route('dosen.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Tambah Penugasan
    </a>
</div>

<!-- Filter Section -->
<div class="card mb-3">
    <div class="card-body py-2 px-3">
        <form method="GET" action="{{ route('dosen.index') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <select name="mata_kuliah_id" class="form-select form-select-sm">
                    <option value="">Semua Mata Kuliah</option>
                    @foreach($mataKuliahList as $mk)
                    <option value="{{ $mk->id }}" {{ request('mata_kuliah_id') == $mk->id ? 'selected' : '' }}>
                        {{ $mk->nama_mk }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="dosen_id" class="form-select form-select-sm">
                    <option value="">Semua Dosen</option>
                    @foreach($dosenList as $dosen)
                    <option value="{{ $dosen->id }}" {{ request('dosen_id') == $dosen->id ? 'selected' : '' }}>
                        {{ $dosen->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="semester_id" class="form-select form-select-sm">
                    <option value="">Semua Semester</option>
                    @foreach($semesterList as $semester)
                    <option value="{{ $semester->id }}" {{ request('semester_id') == $semester->id ? 'selected' : '' }}>
                        {{ $semester->nama_semester }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                <a href="{{ route('dosen.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Table Section -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Kode MK</th>
                        <th>Mata Kuliah</th>
                        <th>Dosen</th>
                        <th>Status Dosen</th>
                        <th>Ruang/Kelas</th>
                        <th>Semester</th>
                        <th style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $assignment)
                    <tr>
                        <td class="text-muted">{{ $assignments->firstItem() + $loop->index }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $assignment->kode_mk }}</span></td>
                        <td class="fw-semibold">{{ $assignment->nama_mk }}</td>
                        <td>{{ $assignment->dosen_name }}</td>
                        <td>{{ $assignment->status_dosen }}</td>
                        <td>
                            @if($assignment->lokasi)
                                <span class="badge bg-info text-dark">
                                    <i class="bi bi-geo-alt-fill"></i> {{ $assignment->lokasi }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($assignment->semester_label)
                                <span class="badge bg-success">{{ $assignment->semester_label }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('dosen.edit', $assignment->id) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('dosen.destroy', $assignment->id) }}" method="POST" style="display:inline;"
                                      onsubmit="return confirm('Hapus penugasan dosen ini?')">
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
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-inbox display-6 d-block mb-2"></i>
                            Belum ada penugasan dosen.
                            <a href="{{ route('dosen.create') }}">Tambah sekarang</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($assignments->hasPages())
    <div class="card-footer bg-white">
        {{ $assignments->links() }}
    </div>
    @endif
</div>
@endsection
