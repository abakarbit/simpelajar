@extends('layouts.app')

@section('title', 'Ubah Password')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Ubah Password</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="bi bi-key-fill me-2" style="color: var(--accent-color);"></i>
                        Ubah Password
                    </h5>

                    <p class="text-secondary mb-4">
                        Pastikan password Anda panjang dan acak untuk menjaga keamanan akun.
                    </p>

                    <form method="post" action="{{ route('password.update') }}">
                        @csrf
                        @method('put')

                        <!-- Current Password -->
                        <div class="mb-3">
                            <label for="update_password_current_password" class="form-label">
                                Password Saat Ini
                            </label>
                            <input
                                id="update_password_current_password"
                                name="current_password"
                                type="password"
                                class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                                autocomplete="current-password"
                                placeholder="Masukkan password saat ini"
                            />
                            @error('current_password', 'updatePassword')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- New Password -->
                        <div class="mb-3">
                            <label for="update_password_password" class="form-label">
                                Password Baru
                            </label>
                            <input
                                id="update_password_password"
                                name="password"
                                type="password"
                                class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                                autocomplete="new-password"
                                placeholder="Masukkan password baru"
                            />
                            @error('password', 'updatePassword')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label for="update_password_password_confirmation" class="form-label">
                                Konfirmasi Password
                            </label>
                            <input
                                id="update_password_password_confirmation"
                                name="password_confirmation"
                                type="password"
                                class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                                autocomplete="new-password"
                                placeholder="Konfirmasi password baru"
                            />
                            @error('password_confirmation', 'updatePassword')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2 align-items-center">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-2"></i>
                                Simpan Password
                            </button>

                            @if (session('status') === 'password-updated')
                                <span class="text-success small ms-2">
                                    <i class="bi bi-check-circle-fill me-1"></i>
                                    Password berhasil diperbarui
                                </span>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="col-md-4">
            <div class="card border-info">
                <div class="card-body">
                    <h6 class="card-title text-info mb-3">
                        <i class="bi bi-info-circle me-2"></i>
                        Panduan Keamanan
                    </h6>
                    <ul class="list-unstyled small text-secondary">
                        <li class="mb-2">
                            <i class="bi bi-check text-success me-2"></i>
                            Gunakan kombinasi huruf besar dan kecil
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check text-success me-2"></i>
                            Tambahkan angka dan simbol
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check text-success me-2"></i>
                            Minimal 8 karakter
                        </li>
                        <li>
                            <i class="bi bi-check text-success me-2"></i>
                            Jangan bagikan password dengan orang lain
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
