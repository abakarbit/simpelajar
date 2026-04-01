@extends('layouts.app')

@section('title', $semester->label)

@section('breadcrumb')
<li class="breadcrumb-item text-muted">GKMP</li>
<li class="breadcrumb-item"><a href="{{ route('semester.index') }}">Semester</a></li>
<li class="breadcrumb-item active">{{ $semester->label }}</li>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-1 fw-bold">{{ $semester->nama_semester }}</h4>
        <p class="text-muted mb-2">Tahun Ajaran: <strong>{{ $semester->tahun_ajaran }}</strong></p>
        <div>
            <span class="badge bg-{{ $semester->tipe === 'ganjil' ? 'primary' : 'success' }} me-2">
                {{ ucfirst($semester->tipe) }}
            </span>
            @if($semester->is_active)
            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Aktif</span>
            @else
            <span class="badge bg-secondary">Tidak Aktif</span>
            @endif
        </div>
    </div>
    <a href="{{ route('semester.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<!-- Filter Form -->
<div class="card mb-3">
    <div class="card-body py-2 px-3">
        <form method="GET" action="{{ route('semester.show', $semester) }}" class="row g-2 align-items-end">
            <div class="col-md-6">
                <label class="form-label small mb-1">Filter Dosen</label>
                <select name="dosen_id" class="form-select form-select-sm">
                    <option value="">Semua Dosen</option>
                    @foreach($dosenList as $dosen)
                    <option value="{{ $dosen->id }}" {{ request('dosen_id') == $dosen->id ? 'selected' : '' }}>
                        {{ $dosen->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bi bi-search me-1"></i>Filter
                </button>
                <a href="{{ route('semester.show', $semester) }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Mata Kuliah Table -->
<div class="card">
    <div class="card-header bg-white">
        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-book me-2"></i>Daftar Mata Kuliah
            <span class="badge bg-light text-dark ms-2">{{ $mataKuliah->total() }} MK</span>
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Kode MK</th>
                        <th>Nama Mata Kuliah</th>
                        <th>Dosen</th>
                        <th>Total Dokumen</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mataKuliah as $mk)
                    <tr>
                        <td class="text-muted">{{ $mataKuliah->firstItem() + $loop->index }}</td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $mk->kode_mk }}</span>
                        </td>
                        <td class="fw-semibold">{{ $mk->nama_mk }}</td>
                        <td>
                            @if($mk->dosen && count($mk->dosen) > 0)
                                @foreach($mk->dosen as $dosen)
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <div class="avatar avatar-sm">
                                        <span class="avatar-initials bg-primary text-white rounded-circle" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:0.75rem;">
                                            {{ strtoupper(substr($dosen->name, 0, 2)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="fw-semibold small">{{ $dosen->name }}</div>
                                        <small class="text-muted">{{ $dosen->email ?? '-' }}</small>
                                    </div>
                                </div>
                                @endforeach
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark">{{ $mk->dokumen()->count() }} dokumen</span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('mata-kuliah.show', $mk) }}" class="btn btn-sm btn-outline-primary" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('mata-kuliah.edit', $mk) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-book-x fs-4 d-block mb-2"></i>
                            @if(request('dosen_id'))
                            Tidak ada mata kuliah untuk dosen yang dipilih.
                            @else
                            Belum ada mata kuliah untuk semester ini.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($mataKuliah->hasPages())
    <div class="card-footer bg-white">
        {{ $mataKuliah->links() }}
    </div>
    @endif
</div>
@endsection
