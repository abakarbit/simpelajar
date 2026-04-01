@extends('layouts.app')

@section('title', 'Tambah Tahap')

@section('breadcrumb')
<li class="breadcrumb-item text-muted">GKMP</li>
<li class="breadcrumb-item"><a href="{{ route('tahap.index') }}">Tahap</a></li>
<li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-layers-plus me-2"></i>Tambah Tahap</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('tahap.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Semester <span class="text-danger">*</span></label>
                        <select name="semester_id" class="form-select @error('semester_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Semester --</option>
                            @foreach($semesterList as $semester)
                            <option value="{{ $semester->id }}" {{ old('semester_id') == $semester->id ? 'selected' : '' }}>
                                {{ $semester->label }} - {{ $semester->tahun_ajaran }}
                            </option>
                            @endforeach
                        </select>
                        @error('semester_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kategori Tahap <span class="text-danger">*</span></label>
                        <select name="kategori_tahap_id" class="form-select @error('kategori_tahap_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Kategori Tahap --</option>
                            @foreach($kategoriTahapList as $kategori)
                            <option value="{{ $kategori->id }}" {{ old('kategori_tahap_id') == $kategori->id ? 'selected' : '' }}>
                                {{ $kategori->nama_tahap }} ({{ count($kategori->jenis_dokumen ?? []) }} dokumen)
                            </option>
                            @endforeach
                        </select>
                        <div class="form-text">Pilih kategori tahap baku dengan jenis dokumen yang sudah ditentukan</div>
                        @error('kategori_tahap_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Deadline Upload <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="deadline" class="form-control @error('deadline') is-invalid @enderror"
                               value="{{ old('deadline') }}" required>
                        <div class="form-text">Tentukan batas waktu upload dokumen untuk tahap ini</div>
                        @error('deadline')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Simpan
                        </button>
                        <a href="{{ route('tahap.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
