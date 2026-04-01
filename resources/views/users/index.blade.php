@extends('layouts.app')

@section('title', 'Manajemen User')

@section('breadcrumb')
<li class="breadcrumb-item text-muted">GKMP</li>
<li class="breadcrumb-item active">Manajemen User</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Manajemen User Dosen</h4>
        <small class="text-muted">Kelola akun dosen yang dapat mengakses sistem</small>
    </div>
    <a href="{{ route('users.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i> Tambah Dosen
    </a>
</div>

{{-- Filter --}}
<div class="card mb-3">
    <div class="card-body py-2 px-3">
        <form method="GET" action="{{ route('users.index') }}" class="row g-2 align-items-end">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Cari nama atau email..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="aktif"   {{ request('status') === 'aktif'   ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td class="text-muted">{{ $users->firstItem() + $loop->index }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-info d-flex align-items-center justify-content-center text-white"
                                 style="width:32px;height:32px;font-size:.75rem;flex-shrink:0">
                                {{ strtoupper(substr($user->name,0,2)) }}
                            </div>
                            <span class="fw-semibold">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td class="text-muted">{{ $user->email }}</td>
                    <td>
                        @if($user->is_active)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-danger">Nonaktif</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('users.toggle-active', $user) }}" method="POST">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-outline-{{ $user->is_active ? 'warning' : 'success' }}"
                                        title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                        onclick="return confirm('{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }} user {{ $user->name }}?')">
                                    <i class="bi bi-{{ $user->is_active ? 'person-dash' : 'person-check' }}"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="bi bi-people fs-4 d-block mb-1"></i>
                        Tidak ada dosen ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="card-footer">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection
