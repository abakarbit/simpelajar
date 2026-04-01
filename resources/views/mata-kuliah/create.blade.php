@extends('layouts.app')

@section('title', 'Tambah Mata Kuliah')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('mata-kuliah.index') }}">Mata Kuliah</a></li>
<li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Tambah Mata Kuliah</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('mata-kuliah.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kode Mata Kuliah <span class="text-danger">*</span></label>
                        <input type="text" name="kode_mk" class="form-control @error('kode_mk') is-invalid @enderror"
                               value="{{ old('kode_mk') }}" placeholder="Contoh: TIF301" required>
                        @error('kode_mk')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Nama Mata Kuliah <span class="text-danger">*</span></label>
                        <input type="text" name="nama_mk" class="form-control @error('nama_mk') is-invalid @enderror"
                               value="{{ old('nama_mk') }}" placeholder="Contoh: Pemrograman Web" required>
                        @error('nama_mk')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Simpan
                        </button>
                        <a href="{{ route('mata-kuliah.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
