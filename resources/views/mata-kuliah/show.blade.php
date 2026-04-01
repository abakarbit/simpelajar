@extends('layouts.app')

@section('title', $mataKuliah->nama_mk)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('mata-kuliah.index') }}">Mata Kuliah</a></li>
<li class="breadcrumb-item active">{{ $mataKuliah->kode_mk }}</li>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-1 fw-bold">{{ $mataKuliah->nama_mk }}</h4>
        <span class="badge bg-light text-dark border">{{ $mataKuliah->kode_mk }}</span>
    </div>
    @if(Auth::user()->isDosen())
    <a href="{{ route('dokumen.create', ['mata_kuliah_id' => $mataKuliah->id]) }}" class="btn btn-primary">
        <i class="bi bi-upload me-1"></i> Upload Dokumen
    </a>
    @endif
</div>

@foreach($tahapList as $tahap)
@php
    $docs = $dokumenPerTahap[$tahap->id] ?? collect();
    $required = $tahap->jenis_dokumen ?? [];
    $approvedCount = $docs->where('status','approved')->count();
    $pct = count($required) > 0 ? round(($approvedCount / count($required)) * 100) : 0;
    $color = $pct == 100 ? 'success' : ($pct > 0 ? 'warning' : 'danger');
@endphp
<div class="card mb-3">
    <div class="card-header bg-white d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-primary rounded-pill">{{ $tahap->urutan }}</span>
            <div>
                <h6 class="mb-0 fw-semibold">{{ $tahap->nama_tahap }}</h6>
                <small class="text-muted">{{ $tahap->deskripsi }}</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center gap-2" style="min-width:120px">
                <div class="progress flex-grow-1" style="height:8px">
                    <div class="progress-bar bg-{{ $color }}" style="width:{{ $pct }}%"></div>
                </div>
                <span class="badge bg-{{ $color }}">{{ $pct }}%</span>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Required docs checklist -->
        <div class="row g-2 mb-3">
            @foreach($required as $jenis)
            @php
                $uploaded = $docs->where('jenis_dokumen', $jenis)->first();
            @endphp
            <div class="col-6 col-md-4 col-lg-3">
                <div class="border rounded p-2 d-flex align-items-center gap-2 {{ $uploaded ? 'border-'.($uploaded->status == 'approved' ? 'success' : ($uploaded->status == 'revisi' ? 'warning' : 'secondary')) : 'border-danger' }}" style="font-size:.82rem">
                    @if($uploaded)
                        @if($uploaded->status == 'approved')
                        <i class="bi bi-check-circle-fill text-success"></i>
                        @elseif($uploaded->status == 'revisi')
                        <i class="bi bi-exclamation-circle-fill text-warning"></i>
                        @else
                        <i class="bi bi-clock-fill text-secondary"></i>
                        @endif
                    @else
                    <i class="bi bi-x-circle-fill text-danger"></i>
                    @endif
                    <span class="{{ !$uploaded ? 'text-danger' : '' }}">{{ $jenis }}</span>
                </div>
            </div>
            @endforeach
        </div>

        @if($docs->isNotEmpty())
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Jenis Dokumen</th>
                        <th>File</th>
                        <th>Diupload</th>
                        <th>Status</th>
                        @if(Auth::user()->isGkmp())<th></th>@endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($docs as $dok)
                    <tr>
                        <td class="fw-semibold small">{{ $dok->jenis_dokumen }}</td>
                        <td>
                            <a href="{{ Storage::url($dok->file_path) }}" target="_blank" class="text-decoration-none small">
                                <i class="bi bi-file-earmark me-1"></i>{{ $dok->nama_file }}
                            </a>
                        </td>
                        <td class="small text-muted">{{ $dok->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <span class="badge bg-{{ $dok->status_badge }}">{{ $dok->status_label }}</span>
                            @if($dok->komentar)
                            <button class="btn btn-sm btn-link p-0 ms-1" data-bs-toggle="tooltip" title="{{ $dok->komentar }}">
                                <i class="bi bi-chat-text text-muted"></i>
                            </button>
                            @endif
                        </td>
                        @if(Auth::user()->isGkmp())
                        <td>
                            <form action="{{ route('dokumen.destroy', $dok) }}" method="POST" onsubmit="return confirm('Hapus dokumen ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                        @endif
                    </tr>
                    @if($dok->komentar)
                    <tr class="table-warning">
                        <td colspan="5" class="py-1 ps-4 small">
                            <i class="bi bi-chat-quote me-1"></i><strong>Komentar GKMP:</strong> {{ $dok->komentar }}
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-muted small mb-0"><i class="bi bi-info-circle me-1"></i>Belum ada dokumen yang diupload untuk tahap ini.</p>
        @endif
    </div>
</div>
@endforeach
@endsection

@push('scripts')
<script>
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
</script>
@endpush
