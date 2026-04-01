@extends('layouts.app')

@section('title', 'Review Dokumen')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('validasi.index') }}">Validasi</a></li>
<li class="breadcrumb-item active">Review</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <!-- Dokumen Info -->
        <div class="card mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-file-earmark-text me-2"></i>Detail Dokumen</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small mb-1">Mata Kuliah</div>
                        <div class="fw-semibold">{{ $dokumen->mataKuliah->nama_mk }}</div>
                        <small class="text-muted">{{ $dokumen->mataKuliah->kode_mk }}</small>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small mb-1">Dosen</div>
                        <div class="fw-semibold">
                            @php
                                $dosenNames = \DB::table('dosen_mata_kuliah')
                                    ->join('users', 'users.id', '=', 'dosen_mata_kuliah.dosen_id')
                                    ->where('mata_kuliah_id', $dokumen->mataKuliah->id)
                                    ->pluck('users.name')
                                    ->toArray();
                            @endphp
                            {{ !empty($dosenNames) ? implode(', ', $dosenNames) : '-' }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small mb-1">Tahap</div>
                        <span class="badge bg-primary">{{ $dokumen->tahap->nama_tahap }}</span>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small mb-1">Jenis Dokumen</div>
                        <div class="fw-semibold">{{ $dokumen->jenis_dokumen }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small mb-1">Nama File</div>
                        <a href="{{ Storage::url($dokumen->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-download me-1"></i> {{ $dokumen->nama_file }}
                        </a>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small mb-1">Diupload Pada</div>
                        <div>{{ $dokumen->created_at->format('d F Y, H:i') }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small mb-1">Status Saat Ini</div>
                        <span class="badge bg-{{ $dokumen->status_badge }} fs-6">{{ $dokumen->status_label }}</span>
                    </div>
                    @if($dokumen->komentar)
                    <div class="col-12">
                        <div class="text-muted small mb-1">Komentar Sebelumnya</div>
                        <div class="alert alert-warning mb-0 py-2 small">
                            <i class="bi bi-chat-quote me-1"></i>{{ $dokumen->komentar }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Validasi Form -->
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-check2-circle me-2"></i>Berikan Keputusan</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('validasi.update', $dokumen) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" value="approved" id="statusApprove"
                                    {{ $dokumen->status == 'approved' ? 'checked' : '' }}>
                                <label class="form-check-label" for="statusApprove">
                                    <span class="badge bg-success">Approve</span>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" value="revisi" id="statusRevisi"
                                    {{ $dokumen->status == 'revisi' ? 'checked' : '' }}>
                                <label class="form-check-label" for="statusRevisi">
                                    <span class="badge bg-warning text-dark">Minta Revisi</span>
                                </label>
                            </div>
                        </div>
                        @error('status')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Komentar</label>
                        <textarea name="komentar" class="form-control @error('komentar') is-invalid @enderror"
                            rows="4" placeholder="Tulis komentar atau catatan untuk dosen...">{{ old('komentar', $dokumen->komentar) }}</textarea>
                        @error('komentar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Simpan Keputusan
                        </button>
                        <a href="{{ route('validasi.index') }}" class="btn btn-light">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
