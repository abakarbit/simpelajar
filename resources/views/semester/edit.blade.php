@extends('layouts.app')

@section('title', 'Edit Semester')

@section('breadcrumb')
<li class="breadcrumb-item text-muted">GKMP</li>
<li class="breadcrumb-item"><a href="{{ route('semester.index') }}">Semester</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Semester: {{ $semester->label }}</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('semester.update', $semester) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Semester <span class="text-danger">*</span></label>
                        <input type="text" name="nama_semester" class="form-control @error('nama_semester') is-invalid @enderror"
                               value="{{ old('nama_semester', $semester->nama_semester) }}" required>
                        @error('nama_semester')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tahun Ajaran <span class="text-danger">*</span></label>
                        <input type="text" name="tahun_ajaran" class="form-control @error('tahun_ajaran') is-invalid @enderror"
                               value="{{ old('tahun_ajaran', $semester->tahun_ajaran) }}" placeholder="cth: 2025/2026" required>
                        <div class="form-text">Format: YYYY/YYYY (contoh: 2025/2026)</div>
                        @error('tahun_ajaran')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipe Semester <span class="text-danger">*</span></label>
                        <select name="tipe" class="form-select @error('tipe') is-invalid @enderror" required>
                            <option value="ganjil" {{ old('tipe', $semester->tipe) === 'ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="genap"  {{ old('tipe', $semester->tipe) === 'genap'  ? 'selected' : '' }}>Genap</option>
                        </select>
                        @error('tipe')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                                   {{ old('is_active', $semester->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_active">
                                Set sebagai Semester Aktif
                            </label>
                            <div class="form-text">Mengaktifkan semester ini akan menonaktifkan semester lainnya.</div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-save me-1"></i> Update
                        </button>
                        <a href="{{ route('semester.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
