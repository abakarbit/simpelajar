@extends('layouts.app')

@section('title', 'Detail Dokumen')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dokumen.index') }}">Dokumen</a></li>
<li class="breadcrumb-item active">Detail</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header bg-white d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-file-earmark-text me-2"></i>Detail Dokumen</h6>
                <a href="{{ route('dokumen.index') }}" class="btn btn-sm btn-light">Kembali</a>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-4 text-muted">Mata Kuliah</dt>
                    <dd class="col-8">{{ $dokumen->mataKuliah->nama_mk }} ({{ $dokumen->mataKuliah->kode_mk }})</dd>
                    <dt class="col-4 text-muted">Tahap</dt>
                    <dd class="col-8"><span class="badge bg-primary">{{ $dokumen->tahap->nama_tahap }}</span></dd>
                    <dt class="col-4 text-muted">Jenis Dokumen</dt>
                    <dd class="col-8 fw-semibold">{{ $dokumen->jenis_dokumen }}</dd>
                    <dt class="col-4 text-muted">File</dt>
                    <dd class="col-8">
                        <a href="{{ Storage::url($dokumen->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-download me-1"></i>{{ $dokumen->nama_file }}
                        </a>
                    </dd>
                    <dt class="col-4 text-muted">Diupload Oleh</dt>
                    <dd class="col-8">{{ $dokumen->uploader->name }}</dd>
                    <dt class="col-4 text-muted">Tanggal</dt>
                    <dd class="col-8">{{ $dokumen->created_at->format('d F Y, H:i') }}</dd>
                    <dt class="col-4 text-muted">Status</dt>
                    <dd class="col-8"><span class="badge bg-{{ $dokumen->status_badge }} fs-6">{{ $dokumen->status_label }}</span></dd>
                    @if($dokumen->komentar)
                    <dt class="col-4 text-muted">Komentar GKMP</dt>
                    <dd class="col-8">
                        <div class="alert alert-warning py-2 mb-0 small">
                            <i class="bi bi-chat-quote me-1"></i>{{ $dokumen->komentar }}
                        </div>
                    </dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
