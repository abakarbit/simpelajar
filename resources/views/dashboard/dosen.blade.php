@extends('layouts.app')

@section('title', 'Dashboard Dosen')

@section('breadcrumb')
<li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-1 fw-bold">Dashboard Dosen</h4>
        <p class="text-muted mb-0">Selamat datang, {{ Auth::user()->name }}</p>
        @if($semesterAktif)
        <small class="text-info">
            <i class="bi bi-calendar-event"></i> Semester Aktif: <strong>{{ $semesterAktif->nama_semester }} ({{ $semesterAktif->tahun_ajaran }})</strong>
        </small>
        @else
        <small class="text-danger">
            <i class="bi bi-exclamation-circle"></i> Tidak ada semester aktif
        </small>
        @endif
    </div>
    <a href="{{ route('dokumen.create') }}" class="btn btn-primary">
        <i class="bi bi-upload me-1"></i> Upload Dokumen
    </a>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card stat-card border-primary h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small">Mata Kuliah</div>
                        <div class="fs-3 fw-bold">{{ $mataKuliahList->count() }}</div>
                    </div>
                    <i class="bi bi-book fs-2 text-primary opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card border-warning h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small">Pending</div>
                        <div class="fs-3 fw-bold text-warning">
                            {{ $mataKuliahList->sum(fn($mk) => $mk->dokumen->where('is_current', true)->where('status','pending')->count()) }}
                        </div>
                    </div>
                    <i class="bi bi-hourglass fs-2 text-warning opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card border-danger h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small">Perlu Revisi</div>
                        <div class="fs-3 fw-bold text-danger">
                            {{ $mataKuliahList->sum(fn($mk) => $mk->dokumen->where('is_current', true)->where('status','revisi')->count()) }}
                        </div>
                    </div>
                    <i class="bi bi-pencil-square fs-2 text-danger opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card border-success h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small">Approved</div>
                        <div class="fs-3 fw-bold text-success">
                            {{ $mataKuliahList->sum(fn($mk) => $mk->dokumen->where('is_current', true)->where('status','approved')->count()) }}
                        </div>
                    </div>
                    <i class="bi bi-check-circle fs-2 text-success opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mata Kuliah Progress -->
@forelse($mataKuliahList as $mk)
<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h6 class="mb-0 fw-semibold">{{ $mk->nama_mk }}</h6>
                <small class="text-muted">{{ $mk->kode_mk }}</small>

            </div>
            <a href="{{ route('mata-kuliah.show', $mk) }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-eye me-1"></i> Detail
            </a>
        </div>

        <div class="row g-2">
            @foreach($tahapList as $tahap)
            @php
                $total = count($tahap->kategoriTahap->jenis_dokumen ?? []);
                $approved = $mk->dokumen()->where('tahap_id', $tahap->id)->where('status','approved')->count();
                $pending  = $mk->dokumen()->where('tahap_id', $tahap->id)->where('status','pending')->count();
                $revisi   = $mk->dokumen()->where('tahap_id', $tahap->id)->where('status','revisi')->count();
                $pct = $total > 0 ? round(($approved/$total)*100) : 0;
                $color = $pct == 100 ? 'success' : ($pct > 0 ? 'warning' : 'danger');
            @endphp
            <div class="col-12 col-md-6 col-lg">
                <div class="border rounded p-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="fw-semibold">{{ $tahap->kategoriTahap->nama_tahap }}</small>
                        <span class="badge bg-{{ $color }}">{{ $pct }}%</span>
                    </div>
                    <div class="progress" style="height:6px">
                        <div class="progress-bar bg-{{ $color }}" style="width:{{ $pct }}%"></div>
                    </div>
                    <div class="d-flex gap-1 mt-1 flex-wrap">
                        @if($approved > 0)<span class="badge bg-success" style="font-size:.65rem;">{{ $approved }} approved</span>@endif
                        @if($pending > 0)<span class="badge bg-secondary" style="font-size:.65rem;">{{ $pending }} pending</span>@endif
                        @if($revisi > 0)<span class="badge bg-warning text-dark" style="font-size:.65rem;">{{ $revisi }} revisi</span>@endif
                        @if($approved == 0 && $pending == 0 && $revisi == 0)<span class="badge bg-danger" style="font-size:.65rem;">Belum ada</span>@endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@empty
<div class="card">
    <div class="card-body text-center py-5">
        <i class="bi bi-book display-4 text-muted"></i>
        <p class="mt-3 text-muted">Belum ada mata kuliah yang terdaftar.</p>

    </div>
</div>
@endforelse
@endsection
