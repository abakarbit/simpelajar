@extends('layouts.app')

@section('title', 'Edit Penugasan Dosen')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dosen.index') }}">Kelola Dosen</a></li>
<li class="breadcrumb-item active">Edit Penugasan</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold">Edit Penugasan Dosen</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('dosen.update', $assignment->id) }}" method="POST">
                    @csrf @method('PUT')

                    <!-- Mata Kuliah -->
                    <div class="mb-3">
                        <label for="mata_kuliah_id" class="form-label">Mata Kuliah <span class="text-danger">*</span></label>
                        <select name="mata_kuliah_id" id="mata_kuliah_id" class="form-select @error('mata_kuliah_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Mata Kuliah --</option>
                            @foreach($mataKuliahList as $mk)
                            <option value="{{ $mk->id }}"
                                {{ old('mata_kuliah_id', $assignment->mata_kuliah_id) == $mk->id ? 'selected' : '' }}>
                                {{ $mk->nama_mk }} ({{ $mk->kode_mk }})
                            </option>
                            @endforeach
                        </select>
                        @error('mata_kuliah_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Dosen -->
                    <div class="mb-3">
                        <label for="dosen_id" class="form-label">Dosen <span class="text-danger">*</span></label>
                        <select name="dosen_id" id="dosen_id" class="form-select @error('dosen_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Dosen --</option>
                            @foreach($dosenList as $dosen)
                            <option value="{{ $dosen->id }}"
                                {{ old('dosen_id', $assignment->dosen_id) == $dosen->id ? 'selected' : '' }}>
                                {{ $dosen->name }} ({{ $dosen->email }})
                            </option>
                            @endforeach
                        </select>
                        @error('dosen_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                        <!-- Status Dosen -->
                        <div class="mb-3">
                            <label for="status_dosen" class="form-label">Status Dosen <span class="text-danger">*</span></label>
                            <select name="status_dosen" id="status_dosen" class="form-select @error('status_dosen') is-invalid @enderror" required>
                                <option value="">-- Pilih Status Dosen --</option>
                                <option value="Penanggung Jawab" {{ old('status_dosen', $assignment->status_dosen) == 'Penanggung Jawab' ? 'selected' : '' }}>Penanggung Jawab</option>
                                <option value="Pengampu" {{ old('status_dosen', $assignment->status_dosen) == 'Pengampu' ? 'selected' : '' }}>Pengampu</option>
                        </select>
                        @error('status_dosen')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Lokasi -->
                    <div class="mb-3">
                        <label for="lokasi" class="form-label">Ruang/Kelas <span class="text-danger">*</span></label>
                        <input type="text" name="lokasi" id="lokasi" class="form-control @error('lokasi') is-invalid @enderror"
                               placeholder="Contoh: Ruang 101, Lab A, etc"
                               value="{{ old('lokasi', $assignment->lokasi) }}" required>
                        @error('lokasi')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Semester -->
                    <div class="mb-3">
                        <label for="semester_id" class="form-label">Semester <span class="text-danger">*</span></label>
                        <select name="semester_id" id="semester_id" class="form-select @error('semester_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Semester --</option>
                            @foreach($semesterList as $semester)
                            <option value="{{ $semester->id }}"
                                {{ old('semester_id', $assignment->semester_id) == $semester->id ? 'selected' : '' }}>
                                {{ $semester->nama_semester }}
                            </option>
                            @endforeach
                        </select>
                        @error('semester_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('dosen.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg me-1"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Info Box -->
    <div class="col-md-4">
        <div class="alert alert-info">
            <h6 class="alert-heading">
                <i class="bi bi-info-circle me-2"></i> Informasi
            </h6>
            <p class="small mb-2">
                Anda dapat mengubah data penugasan dosen ke mata kuliah.
            </p>
            <hr class="my-2">
            <p class="small mb-0">
                <strong>ID Penugasan:</strong><br>
                {{ $assignment->id }}
            </p>
        </div>

        <!-- Delete Section -->
        <div class="alert alert-warning">
            <h6 class="alert-heading">
                <i class="bi bi-exclamation-triangle me-2"></i> Hapus Penugasan
            </h6>
            <p class="small mb-2">
                Klik tombol di bawah untuk menghapus penugasan ini secara permanen.
            </p>
            <form action="{{ route('dosen.destroy', $assignment->id) }}" method="POST"
                  onsubmit="return confirm('Anda yakin ingin menghapus penugasan ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                    <i class="bi bi-trash me-1"></i> Hapus Penugasan
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
