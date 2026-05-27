@extends('layouts.app')

@section('title', 'Dashboard GKMP')

@section('breadcrumb')
<li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-1 fw-bold">Dashboard GKMP</h4>
        <p class="text-muted mb-0">Sistem Monitoring Dokumen Pembelajaran</p>
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
    <a href="{{ route('validasi.index') }}" class="btn btn-primary">
        <i class="bi bi-check2-circle me-1"></i> Validasi Dokumen
    </a>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="text-muted small">Total Mata Kuliah</div>
                        <div class="fs-3 fw-bold">{{ $stats['total_mk'] }}</div>
                    </div>
                    <i class="bi bi-book fs-2 text-primary opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3" style="display: none">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="text-muted small">Pending Review</div>
                        <div class="fs-3 fw-bold text-secondary">{{ $stats['pending'] }}</div>
                    </div>
                    <i class="bi bi-hourglass fs-2 text-secondary opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="text-muted small">Perlu Revisi</div>
                        <div class="fs-3 fw-bold text-warning">{{ $stats['revisi'] }}</div>
                    </div>
                    <i class="bi bi-pencil-square fs-2 text-warning opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="text-muted small">Approved</div>
                        <div class="fs-3 fw-bold text-success">{{ $stats['approved'] }}</div>
                    </div>
                    <i class="bi bi-check-circle fs-2 text-success opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Monitoring Table -->
<div class="card">
    <div class="card-header bg-white d-flex align-items-center justify-content-between">
        <div>
            <h6 class="mb-0 fw-semibold"><i class="bi bi-table me-2"></i>Status Pengumpulan Dokumen Aktif</h6>
            @if($activeTahap)
            <small class="text-muted mt-2">
                <i class="bi bi-calendar-event"></i>
                {{ $activeTahap->kategoriTahap->nama_tahap }} - Deadline:
                <span class="badge bg-{{ now()->diffInDays($activeTahap->deadline) <= 3 ? 'danger' : 'warning' }}">
                    {{ $activeTahap->deadline->format('d M Y H:i') }}
                </span>
            </small>
            @else
            <small class="text-danger mt-2"><i class="bi bi-exclamation-circle"></i> Tidak ada tahap aktif</small>
            @endif
        </div>
    </div>

    @if($activeTahap)
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="min-width: 200px;">Mata Kuliah</th>
                        <th style="min-width: 170px;">Penanggung Jawab</th>
                        @php
                            $jenisDokumen = $activeTahap->kategoriTahap->jenis_dokumen ?? [];
                        @endphp
                        @foreach($jenisDokumen as $dokType)
                        <th class="text-center" style="min-width: 180px;">{{ $dokType }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($mataKuliahList as $mk)
                    <tr>
                        <td class="fw-semibold">
                            <div>{{ $mk->nama_mk }}</div>
                            <small class="text-muted">{{ $mk->kode_mk }}</small>
                        </td>
                        <td>{{ $mk->dosen->pluck('name')->join(', ') ?: '-' }}</td>
                        @foreach($jenisDokumen as $dokType)
                        @php
                            $dokumen = $mk->dokumen()
                                ->where('tahap_id', $activeTahap->id)
                                ->where('jenis_dokumen', $dokType)
                                ->where('is_current', true)
                                ->first();
                        @endphp
                        <td class="text-center">
                            @if($dokumen)
                                @if($dokumen->status === 'approved')
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle"></i> Lengkap
                                </span>
                                @elseif($dokumen->status === 'revisi')
                                <div>
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-exclamation-circle"></i> Perbaikan
                                    </span>
                                    @if($dokumen->komentar)
                                    <div class="small text-muted mt-2 border-top pt-2">
                                        <strong>Keterangan:</strong><br>
                                        {{ $dokumen->komentar }}
                                    </div>
                                    @endif
                                </div>
                                @else
                                <span class="badge bg-info text-dark">
                                    <i class="bi bi-hourglass-split"></i> Verifikasi
                                </span>
                                @endif
                            @else
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-circle"></i> Belum Ada
                            </span>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ count($jenisDokumen) + 1 }}" class="text-center text-muted py-4">
                            Belum ada data mata kuliah.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light fw-bold border-top">
                    <tr>
                        <td class="text-start">Kelengkapan</td>
                        <td></td>
                        @foreach($jenisDokumen as $dokType)
                        @php
                            $approvedCount = 0;
                            foreach($mataKuliahList as $mk) {
                                $isDokumenAda = $mk->dokumen()
                                    ->where('tahap_id', $activeTahap->id)
                                    ->where('jenis_dokumen', $dokType)
                                    ->where('is_current', true)
                                    ->where('status', 'approved')
                                    ->exists();
                                if($isDokumenAda) {
                                    $approvedCount++;
                                }
                            }
                            $totalMk = $mataKuliahList->count();
                            $percentage = $totalMk > 0 ? round(($approvedCount / $totalMk) * 100) : 0;
                        @endphp
                        <td class="text-center">
                            <span class="badge bg-{{ $percentage === 100 ? 'success' : ($percentage >= 75 ? 'info' : ($percentage >= 50 ? 'warning' : 'danger')) }}">
                                {{ $approvedCount }}/{{ $totalMk }} ({{ $percentage }}%)
                            </span>
                        </td>
                        @endforeach
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @else
    <div class="card-body text-center text-muted py-5">
        <i class="bi bi-calendar-check fs-4 d-block mb-2"></i>
        <p>Semua tahap telah melewati deadline. Tidak ada tahap aktif saat ini.</p>
    </div>
    @endif
</div>
@endsection
