@extends('layouts.app')

@section('title', 'Manajemen Semester')

@section('breadcrumb')
<li class="breadcrumb-item text-muted">GKMP</li>
<li class="breadcrumb-item active">Semester</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Manajemen Semester</h4>
        <small class="text-muted">Kelola semester akademik aktif</small>
    </div>
    <a href="{{ route('semester.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Tambah Semester
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nama Semester</th>
                    <th>Tahun Ajaran</th>
                    <th>Tipe</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($semesters as $semester)
                <tr>
                    <td class="text-muted">{{ $loop->iteration }}</td>
                    <td class="fw-semibold">{{ $semester->nama_semester }}</td>
                    <td>{{ $semester->tahun_ajaran }}</td>
                    <td>
                        <span class="badge bg-{{ $semester->tipe === 'ganjil' ? 'primary' : 'success' }}">
                            {{ ucfirst($semester->tipe) }}
                        </span>
                    </td>
                    <td>
                        @if($semester->is_active)
                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Aktif</span>
                        @else
                            <span class="badge bg-secondary">Tidak Aktif</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="{{ route('semester.show', $semester) }}" class="btn btn-sm btn-outline-info" title="Lihat Mata Kuliah">
                                <i class="bi bi-book"></i>
                            </a>
                            @unless($semester->is_active)
                            <form action="{{ route('semester.set-active', $semester) }}" method="POST">
                                @csrf
                                <button class="btn btn-sm btn-outline-success" title="Set Aktif">
                                    <i class="bi bi-toggle-on"></i>
                                </button>
                            </form>
                            @endunless
                            <a href="{{ route('semester.edit', $semester) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('semester.destroy', $semester) }}" method="POST"
                                  onsubmit="return confirm('Hapus semester {{ $semester->label }}? Semester tidak dapat dihapus jika mempunyai mata kuliah.')">
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
                        <i class="bi bi-calendar-x fs-4 d-block mb-1"></i>
                        Belum ada semester. <a href="{{ route('semester.create') }}">Tambah sekarang</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
