@extends('layouts.app')

@section('title', 'Daftar Dokumen')

@section('breadcrumb')
<li class="breadcrumb-item active">Dokumen</li>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="mb-0 fw-bold">Daftar Dokumen</h4>
    @if(Auth::user()->isDosen())
    <a href="{{ route('dokumen.create') }}" class="btn btn-primary">
        <i class="bi bi-upload me-1"></i> Upload Dokumen
    </a>
    @endif
</div>

<!-- Filter Form -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-2">
                <label class="form-label small mb-1">Semester</label>
                <select name="semester_id" id="semesterFilter" class="form-select form-select-sm">
                    <option value="">Semua Semester</option>
                    @foreach($semesterList as $sem)
                    <option value="{{ $sem->id }}" {{ request('semester_id') == $sem->id ? 'selected' : '' }}>
                        {{ $sem->nama_semester }} ({{ $sem->tahun_ajaran }})
                    </option>
                    @endforeach
                </select>
            </div>
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
                <select name="tahap_id" id="tahapFilter" class="form-select form-select-sm">
                    <option value="">Semua Tahap</option>

                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small mb-1">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
                    <option value="revisi" {{ request('status')=='revisi' ? 'selected' : '' }}>Revisi</option>
                    <option value="approved" {{ request('status')=='approved' ? 'selected' : '' }}>Approved</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search me-1"></i>Filter</button>
                <a href="{{ route('dokumen.index') }}" class="btn btn-sm btn-light ms-1">Reset</a>
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
                        <th>Semester</th>
                        <th>Tahap</th>
                        <th>Jenis Dokumen</th>
                        <th>File</th>
                        <th>Uploader</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        @if(Auth::user()->isGkmp())<th>Aksi</th>@endif
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
                        <td>
                            <small>{{ $dok->tahap->semester->nama_semester ?? '-' }}</small><br>
                            <small class="text-muted">{{ $dok->tahap->semester->tahun_ajaran ?? '' }}</small>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $dok->tahap->kategoriTahap->nama_tahap }}</span></td>
                        <td class="small">{{ $dok->jenis_dokumen }}</td>
                        <td>
                            <a href="{{ Storage::url($dok->file_path) }}" target="_blank" class="text-decoration-none small">
                                <i class="bi bi-file-earmark me-1"></i>{{ Str::limit($dok->nama_file, 25) }}
                            </a>
                        </td>
                        <td class="small">
                            <span class="d-inline-block" style="background: #e3f2fd; padding: 4px 8px; border-radius: 4px; color: #1976d2;">
                                <i class="bi bi-person-circle me-1"></i><strong>{{ $dok->uploader->name }}</strong>
                            </span>
                        </td>
                        <td class="small text-muted">{{ $dok->created_at->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge bg-{{ $dok->status_badge }}">{{ $dok->status_label }}</span>
                        </td>
                        @if(Auth::user()->isGkmp())
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('validasi.show', $dok) }}" class="btn btn-sm btn-outline-primary" title="Validasi">
                                    <i class="bi bi-check2-circle"></i>
                                </a>

                                <form action="{{ route('dokumen.destroy', $dok) }}" method="POST" onsubmit="return confirm('Hapus dokumen ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>

                            </div>
                        </td>
                        @endif
                    </tr>
                    @if($dok->komentar)
                    <tr class="table-warning">
                        <td colspan="10" class="py-1 ps-4 small">
                            <i class="bi bi-chat-quote me-1"></i><strong>Komentar GKMP:</strong> {{ $dok->komentar }}
                        </td>
                    </tr>
                    @endif
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-5">
                            <i class="bi bi-file-earmark display-6 d-block mb-2"></i>
                            Tidak ada dokumen ditemukan.
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

@push('scripts')
<script>
    const semesterFilter = document.getElementById('semesterFilter');
    const tahapFilter = document.getElementById('tahapFilter');

    semesterFilter.addEventListener('change', function () {
        const semesterId = this.value;

        if (!semesterId) {
            tahapFilter.innerHTML = '<option value="">Semua Tahap</option>';
            return;
        }

        fetch(`{{ route('dokumen.tahap-by-semester') }}?semester_id=${semesterId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            let html = '<option value="">Semua Tahap</option>';
            (data.tahap || []).forEach(t => {
                html += `<option value="${t.id}">${t.nama_tahap}</option>`;
            });
            tahapFilter.innerHTML = html;
        })
        .catch(() => {
            tahapFilter.innerHTML = '<option value="">Semua Tahap</option>';
        });
    });
</script>
@endpush
