@extends('layouts.app')

@section('title', 'Mata Kuliah')

@section('breadcrumb')
<li class="breadcrumb-item active">Mata Kuliah</li>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0 fw-bold">Mata Kuliah</h4>
        @if(Auth::user()->isDosen())
        <small class="text-muted">Mata kuliah yang Anda ampu</small>
        @endif
    </div>
    @if(Auth::user()->isGkmp())
    <a href="{{ route('mata-kuliah.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Tambah Mata Kuliah
    </a>
    @endif
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Kode MK</th>
                        <th>Nama Mata Kuliah</th>
                        @if(Auth::user()->isGkmp())<th>Aksi</th>@endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($mataKuliah as $mk)
                    @php
                        $overall = $mk->overallProgress();
                        $color = $overall == 100 ? 'success' : ($overall > 0 ? 'warning' : 'danger');
                    @endphp
                    <tr>
                        <td class="text-muted">{{ $mataKuliah->firstItem() + $loop->index }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $mk->kode_mk }}</span></td>
                        <td class="fw-semibold">{{ $mk->nama_mk }}</td>
                        @if(Auth::user()->isGkmp())
                        <td>
                            <div class="d-flex gap-1 flex-wrap">

                                <a href="{{ route('mata-kuliah.edit', $mk) }}" class="btn btn-sm btn-outline-secondary" title="Edit Info">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <form action="{{ route('mata-kuliah.destroy', $mk) }}" method="POST" style="display:inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Hapus"
                                            onclick="return confirm('Yakin hapus mata kuliah {{ $mk->nama_mk }}?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-book display-6 d-block mb-2"></i>
                            Belum ada mata kuliah.
                            @if(Auth::user()->isGkmp())
                            <a href="{{ route('mata-kuliah.create') }}">Tambah sekarang</a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($mataKuliah->hasPages())
    <div class="card-footer bg-white">
        {{ $mataKuliah->links() }}
    </div>
    @endif
</div>
@endsection
