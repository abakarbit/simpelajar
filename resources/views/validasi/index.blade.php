@extends('layouts.app')

@section('title', 'Validasi Dokumen')

@section('breadcrumb')
<li class="breadcrumb-item active">Validasi Dokumen</li>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="mb-0 fw-bold">Validasi Dokumen</h4>
</div>

<!-- Filter -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-3">
                <label class="form-label small mb-1">Mata Kuliah</label>
                <select name="mata_kuliah_id" class="form-select form-select-sm">
                    <option value="">Semua MK</option>
                    @foreach($mataKuliahList as $mk)
                    <option value="{{ $mk->id }}" {{ request('mata_kuliah_id') == $mk->id ? 'selected' : '' }}>
                        {{ $mk->kode_mk }} - {{ $mk->nama_mk }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small mb-1">Tahap</label>
                <select name="tahap_id" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach($tahapList as $tahap)
                    <option value="{{ $tahap->id }}" {{ request('tahap_id') == $tahap->id ? 'selected' : '' }}>{{ $tahap->nama_tahap }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small mb-1">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    <option value="pending" {{ request('status','pending')=='pending' ? 'selected' : '' }}>Pending</option>
                    <option value="revisi" {{ request('status')=='revisi' ? 'selected' : '' }}>Revisi</option>
                    <option value="approved" {{ request('status')=='approved' ? 'selected' : '' }}>Approved</option>
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-sm btn-primary"><i class="bi bi-search me-1"></i>Filter</button>
                <a href="{{ route('validasi.index') }}" class="btn btn-sm btn-light ms-1">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Mata Kuliah</th>
                        <th>Dosen</th>
                        <th>Tahap</th>
                        <th>Jenis Dokumen</th>
                        <th>File</th>
                        <th>Tanggal Upload</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dokumen as $dok)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="fw-semibold small">{{ $dok->mataKuliah->nama_mk }}</div>
                            <small class="text-muted">{{ $dok->mataKuliah->kode_mk }}</small>
                        </td>
                        <td class="small">
                            {{ $dok->uploader->name }}
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $dok->tahap->nama_tahap }}</span></td>
                        <td class="small fw-semibold">{{ $dok->jenis_dokumen }}</td>
                        <td>
                            <a href="{{ Storage::url($dok->file_path) }}" target="_blank" class="text-decoration-none small">
                                <i class="bi bi-file-earmark me-1"></i>{{ Str::limit($dok->nama_file, 20) }}
                            </a>
                        </td>
                        <td class="small text-muted">{{ $dok->created_at->format('d/m/Y H:i') }}</td>
                        <td><span class="badge bg-{{ $dok->status_badge }}">{{ $dok->status_label }}</span></td>
                        <td>
                            <a href="{{ route('validasi.show', $dok) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-check2-circle me-1"></i>Review
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-5">
                            <i class="bi bi-inbox display-6 d-block mb-2"></i>
                            Tidak ada dokumen untuk divalidasi.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($dokumen->hasPages())
    <div class="card-footer bg-white">{{ $dokumen->links() }}</div>
    @endif
</div>
@endsection
