@extends('layouts.app')

@section('title', 'Laporan Dokumen')

@section('breadcrumb')
<li class="breadcrumb-item active">Laporan</li>
@endsection

@push('styles')
<style>
    .filter-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,.04);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .tahap-btn-group .btn {
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 500;
        padding: 0.45rem 1rem;
    }
    .tahap-btn-group .btn-check:checked + .btn {
        background: #0f172a;
        border-color: #0f172a;
        color: #fff;
    }
    .table thead th {
        background: #0f172a;
        color: #fff;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: .5px;
        white-space: nowrap;
        vertical-align: middle;
        text-align: center;
    }
    .table thead th.col-mk {
        text-align: left;
        min-width: 200px;
    }
    .table thead .tahap-group-header {
        background: #1e293b;
        border-right: 2px solid #475569;
    }
    .table tbody td { vertical-align: middle; }
    .table tfoot td {
        font-weight: 600;
        background: #f8fafc;
        font-size: 0.8rem;
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: .78rem;
        padding: .3rem .65rem;
        border-radius: 6px;
        font-weight: 600;
    }
    .badge-approved  { background: #d1fae5; color: #065f46; }
    .badge-revisi    { background: #fef3c7; color: #92400e; }
    .badge-pending   { background: #e0f2fe; color: #0369a1; }
    .badge-empty     { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }
    .pct-badge       { font-size: .78rem; padding: .3rem .65rem; border-radius: 6px; }
    .pct-100         { background: #d1fae5; color: #065f46; }
    .pct-75          { background: #dbeafe; color: #1d4ed8; }
    .pct-50          { background: #fef3c7; color: #92400e; }
    .pct-low         { background: #fee2e2; color: #b91c1c; }
    .empty-state     { padding: 4rem 2rem; text-align: center; color: #94a3b8; }
    .empty-state i   { font-size: 3rem; display: block; margin-bottom: 1rem; }
    .group-divider   { border-right: 2px solid #475569 !important; }
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-1 fw-bold">Laporan Dokumen</h4>
        <p class="text-muted mb-0">Rekap kelengkapan dokumen per mata kuliah &amp; tahap</p>
    </div>
</div>

{{-- ══════ FILTER CARD ══════ --}}
<div class="filter-card">
    <form method="GET" action="{{ route('laporan.index') }}" id="filterForm">
        <div class="row g-3 align-items-end">

            {{-- Semester --}}
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold mb-1 small text-uppercase text-muted">
                    <i class="bi bi-calendar-week me-1"></i>Semester
                </label>
                <select name="semester_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">— Semua Semester —</option>
                    @foreach($semesterList as $sem)
                    <option value="{{ $sem->id }}" {{ $semesterId == $sem->id ? 'selected' : '' }}>
                        {{ $sem->nama_semester }} – {{ $sem->tahun_ajaran }}
                        @if($sem->is_active) (Aktif) @endif
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Tahap --}}
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold mb-1 small text-uppercase text-muted">
                    <i class="bi bi-layers me-1"></i>Tahap
                </label>
                <div class="tahap-btn-group d-flex flex-wrap gap-2">
                    @foreach(['semua' => 'Semua', '1' => 'Tahap 1', '2' => 'Tahap 2', '3' => 'Tahap 3', '4' => 'Tahap 4', '5' => 'Tahap 5'] as $val => $label)
                    <input type="radio" class="btn-check" name="tahap" id="tahap_{{ $val }}"
                           value="{{ $val }}" {{ $tahapFilter == $val ? 'checked' : '' }}
                           onchange="this.form.submit()">
                    <label class="btn btn-outline-secondary" for="tahap_{{ $val }}">{{ $label }}</label>
                    @endforeach
                </div>
            </div>

            {{-- Tombol Export --}}
            <div class="col-12 col-md-2 d-flex align-items-end">
                <a href="{{ route('laporan.export', request()->query()) }}"
                   class="btn btn-success w-100">
                    <i class="bi bi-file-earmark-excel me-1"></i>
                    Export Excel
                </a>
            </div>

        </div>
    </form>
</div>

{{-- ══════ SUMMARY CARDS ══════ --}}
@php
    $totalMk       = $mataKuliahList->count();
    $allJenisDok   = [];
    foreach ($selectedTahapList as $t) {
        foreach ($t->kategoriTahap->jenis_dokumen ?? [] as $j) {
            $allJenisDok[] = ['tahap' => $t, 'jenis' => $j];
        }
    }
    $totalSlots    = $totalMk * count($allJenisDok);
    $totalApproved = 0; $totalPending = 0; $totalRevisi = 0; $totalKosong = 0;
    foreach ($mataKuliahList as $mk) {
        foreach ($allJenisDok as $item) {
            $dok = $mk->dokumen()
                ->where('tahap_id', $item['tahap']->id)
                ->where('jenis_dokumen', $item['jenis'])
                ->where('is_current', true)
                ->first();
            if (!$dok)                            $totalKosong++;
            elseif ($dok->status === 'approved')  $totalApproved++;
            elseif ($dok->status === 'revisi')     $totalRevisi++;
            else                                   $totalPending++;
        }
    }
@endphp

{{-- @if($totalMk > 0 && count($allJenisDok) > 0)
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Total Mata Kuliah</div>
                        <div class="fs-3 fw-bold">{{ $totalMk }}</div>
                    </div>
                    <i class="bi bi-book fs-2 text-primary opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Approved</div>
                        <div class="fs-3 fw-bold text-success">{{ $totalApproved }}</div>
                    </div>
                    <i class="bi bi-check-circle fs-2 text-success opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Pending / Verifikasi</div>
                        <div class="fs-3 fw-bold text-info">{{ $totalPending }}</div>
                    </div>
                    <i class="bi bi-hourglass-split fs-2 text-info opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Perlu Revisi</div>
                        <div class="fs-3 fw-bold text-warning">{{ $totalRevisi }}</div>
                    </div>
                    <i class="bi bi-pencil-square fs-2 text-warning opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>
@endif --}}

{{-- ══════ TABEL ══════ --}}
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <div>
            <h6 class="mb-0 fw-semibold">
                <i class="bi bi-table me-2"></i>Rekap Kelengkapan Dokumen
            </h6>
            <small class="text-muted">
                @if($semester) {{ $semester->nama_semester }} – {{ $semester->tahun_ajaran }} &nbsp;|&nbsp; @endif
                @if($tahapFilter === 'semua') Semua Tahap @else Tahap {{ $tahapFilter }} @endif
                &nbsp;|&nbsp; {{ $totalMk }} Mata Kuliah
            </small>
        </div>
    </div>

    @if($totalMk === 0)
    <div class="empty-state">
        <i class="bi bi-inbox"></i>
        <p class="mb-0">Tidak ada mata kuliah ditemukan untuk filter yang dipilih.</p>
    </div>

    @elseif(count($allJenisDok) === 0)
    <div class="empty-state">
        <i class="bi bi-layers"></i>
        <p class="mb-0">Tidak ada tahap yang ditemukan untuk semester &amp; filter yang dipilih.</p>
    </div>

    @else
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    {{-- Baris 1: Group tahap (hanya ketika > 1 tahap) --}}
                    @if($selectedTahapList->count() > 1)
                    <tr>
                        <th class="col-mk" rowspan="2">Mata Kuliah</th>
                        <th rowspan="2" style="min-width:140px;">Penanggung Jawab</th>
                        @foreach($selectedTahapList as $tahap)
                        @php
                            $cols = count($tahap->kategoriTahap->jenis_dokumen ?? []);
                            $isLast = $loop->last;
                        @endphp
                        @if($cols > 0)
                        <th colspan="{{ $cols }}"
                            class="text-center tahap-group-header {{ !$isLast ? 'group-divider' : '' }}">
                            {{ $tahap->kategoriTahap->nama_tahap }}
                            <div class="fw-normal text-white-50" style="font-size:.7rem;">
                                Deadline: {{ $tahap->deadline?->format('d M Y') ?? '-' }}
                            </div>
                        </th>
                        @endif
                        @endforeach
                    </tr>
                    @endif

                    {{-- Baris 2 (atau 1 jika single): Jenis dokumen --}}
                    <tr>
                        @if($selectedTahapList->count() === 1)
                        <th class="col-mk">Mata Kuliah</th>
                        <th style="min-width:140px;">Penanggung Jawab</th>
                        @endif
                        @foreach($selectedTahapList as $tahap)
                        @php
                            $jenisDokumen = $tahap->kategoriTahap->jenis_dokumen ?? [];
                            $isLastTahap  = $loop->last;
                        @endphp
                        @foreach($jenisDokumen as $jenis)
                        <th class="text-center {{ !$loop->last && !$isLastTahap ? '' : ($isLastTahap ? '' : 'group-divider') }}">
                            {{ $jenis }}
                        </th>
                        @endforeach
                        @endforeach
                    </tr>
                </thead>

                <tbody>
                    @forelse($mataKuliahList as $mk)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $mk->nama_mk }}</div>
                            <small class="text-muted">{{ $mk->kode_mk }}</small>
                        </td>
                        <td>{{ $mk->dosen->pluck('name')->join(', ') ?: '-' }}</td>
                        @foreach($selectedTahapList as $tahap)
                        @php
                            $jenisDokumen = $tahap->kategoriTahap->jenis_dokumen ?? [];
                            $isLastTahap  = $loop->last;
                        @endphp
                        @foreach($jenisDokumen as $jenis)
                        @php
                            $dok = $mk->dokumen()
                                ->where('tahap_id', $tahap->id)
                                ->where('jenis_dokumen', $jenis)
                                ->where('is_current', true)
                                ->first();
                        @endphp
                        <td class="text-center {{ $loop->last && !$isLastTahap ? 'group-divider' : '' }}">
                            @if(! $dok)
                                <span class="status-badge badge-empty">
                                    <i class="bi bi-circle"></i> Belum Ada
                                </span>
                            @elseif($dok->status === 'approved')
                                <span class="status-badge badge-approved">
                                    <i class="bi bi-check-circle-fill"></i> Lengkap
                                </span>
                            @elseif($dok->status === 'revisi')
                                <div>
                                    <span class="status-badge badge-revisi">
                                        <i class="bi bi-exclamation-circle-fill"></i> Revisi
                                    </span>
                                    @if($dok->komentar)
                                    <div class="mt-1 text-muted" style="font-size:.72rem; max-width:160px; margin:0 auto;">
                                        {{ $dok->komentar }}
                                    </div>
                                    @endif
                                </div>
                            @else
                                <span class="status-badge badge-pending">
                                    <i class="bi bi-hourglass-split"></i> Verifikasi
                                </span>
                            @endif
                        </td>
                        @endforeach
                        @endforeach
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ count($allJenisDok) + 2 }}" class="text-center text-muted py-5">
                            Belum ada data.
                        </td>
                    </tr>
                    @endforelse
                </tbody>

                {{-- Footer: Persentase kelengkapan --}}
                <tfoot>
                    <tr>
                        <td class="text-start text-uppercase" style="font-size:.75rem; letter-spacing:.5px;">
                            Kelengkapan
                        </td>
                        <td></td>
                        @foreach($selectedTahapList as $tahap)
                        @php
                            $jenisDokumen = $tahap->kategoriTahap->jenis_dokumen ?? [];
                            $isLastTahap  = $loop->last;
                        @endphp
                        @foreach($jenisDokumen as $jenis)
                        @php
                            $approvedCount = 0;
                            foreach ($mataKuliahList as $mk) {
                                if ($mk->dokumen()
                                    ->where('tahap_id', $tahap->id)
                                    ->where('jenis_dokumen', $jenis)
                                    ->where('is_current', true)
                                    ->where('status', 'approved')
                                    ->exists()) {
                                    $approvedCount++;
                                }
                            }
                            $percentage = $totalMk > 0
                                ? round(($approvedCount / $totalMk) * 100)
                                : 0;
                            $pctClass = $percentage === 100 ? 'pct-100'
                                : ($percentage >= 75 ? 'pct-75'
                                : ($percentage >= 50 ? 'pct-50' : 'pct-low'));
                        @endphp
                        <td class="text-center {{ $loop->last && !$isLastTahap ? 'group-divider' : '' }}">
                            <span class="pct-badge {{ $pctClass }}">
                                {{ $approvedCount }}/{{ $totalMk }} ({{ $percentage }}%)
                            </span>
                        </td>
                        @endforeach
                        @endforeach
                    </tr>
                </tfoot>

            </table>
        </div>
    </div>
    @endif
</div>

@endsection
