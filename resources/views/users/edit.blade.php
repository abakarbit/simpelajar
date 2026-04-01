@extends('layouts.app')

@section('title', 'Edit Dosen')

@section('breadcrumb')
<li class="breadcrumb-item text-muted">GKMP</li>
<li class="breadcrumb-item"><a href="{{ route('users.index') }}">Manajemen User</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Akun Dosen: {{ $user->name }}</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('users.update', $user) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <hr>
                    <p class="text-muted small mb-3">Kosongkan kolom password jika tidak ingin mengganti password.</p>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password Baru</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                               placeholder="Min. 8 karakter (opsional)">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-control"
                               placeholder="Ulangi password baru">
                    </div>

                    <hr>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="dosen" {{ old('role', $user->role) === 'dosen' ? 'selected' : '' }}>Dosen</option>
                            <option value="gkmp" {{ old('role', $user->role) === 'gkmp' ? 'selected' : '' }}>GKMP (Admin)</option>
                        </select>
                        @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-save me-1"></i> Update
                        </button>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
