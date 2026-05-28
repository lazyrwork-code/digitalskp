@extends('layouts.app')

@section('title', 'Kelola User')

@section('content')
@php
function formatNip($nip) {
    $raw = preg_replace('/\D/', '', $nip ?? '');
    if (strlen($raw) === 0) return '-';
    $formatted = substr($raw, 0, 8);
    if (strlen($raw) > 8)  $formatted .= ' ' . substr($raw, 8, 6);
    if (strlen($raw) > 14) $formatted .= ' ' . substr($raw, 14, 1);
    if (strlen($raw) > 15) $formatted .= ' ' . substr($raw, 15, 3);
    return $formatted;
}
@endphp
<div class="content">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">Kelola User</h3>
        <a href="{{ route('kepala.user.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i> Tambah User
        </a>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Card Gabungan --}}
    <div class="bg-white p-4 rounded-4">

        {{-- Search + Info --}}
        <div class="d-flex justify-content-between align-items-center gap-3 mb-4">
            <div class="text-muted small">
                Total <strong class="text-dark">{{ $users->total() }}</strong> user ditemukan
            </div>
            <form method="GET" action="{{ route('kepala.user.index') }}" class="d-flex gap-2">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="search"
                           class="form-control border-start-0 ps-0"
                           placeholder="Cari nama, NIP, atau username..."
                           value="{{ $search }}">
                </div>
                <button class="btn btn-primary px-3" type="submit">Cari</button>
                @if($search)
                    <a href="{{ route('kepala.user.index') }}" class="btn btn-light border px-3">
                        <i class="bi bi-x"></i>
                    </a>
                @endif
            </form>
        </div>

        {{-- Tabel --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="45" class="text-center">No</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>NIP</th>
                        <th>Email</th>
                        <th width="90" class="text-center">Role</th>
                        <th width="100" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="text-center text-muted small">
                            {{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $user->nama }}</div>
                        </td>
                        <td class="text-muted small">{{ $user->username }}</td>
                        <td class="text-muted small">{{ formatNip($user->nip) }}</td>
                        <td class="text-muted small" style="max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                            {{ $user->email }}
                        </td>
                        <td class="text-center">
                            @if($user->role === 'admin')
                                <span class="badge rounded-pill bg-warning text-dark px-3">Admin</span>
                            @elseif($user->role === 'kepala')
                                <span class="badge rounded-pill bg-danger text-white px-3">Kepala</span>
                            @else
                                <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary px-3">Pegawai</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('kepala.user.edit', $user->id) }}"
                               class="btn btn-sm btn-outline-primary me-1"
                               title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('kepala.user.destroy', $user->id) }}"
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Hapus user {{ $user->nama }}?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-people text-muted opacity-50 mb-1" style="font-size:1.5rem;"></i>
                            <div class="small mt-1">
                                Belum ada user
                                @if($search)
                                    dengan kata kunci <strong>"{{ $search }}"</strong>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
            <div class="text-muted small">
                Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }}
                dari {{ $users->total() }} user
            </div>
            <nav>
                <ul class="pagination pagination-sm mb-0">

                    {{-- Prev --}}
                    <li class="page-item {{ $users->onFirstPage() ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $users->previousPageUrl() ?? '#' }}">
                            <i class="bi bi-chevron-left" style="font-size:11px;"></i>
                        </a>
                    </li>

                    @php
                        $current  = $users->currentPage();
                        $last     = $users->lastPage();
                        $pages    = [];

                        // Selalu tampilkan halaman 1
                        $pages[] = 1;

                        // Ellipsis kiri
                        if ($current > 4) $pages[] = '...';

                        // Halaman sekitar current
                        for ($i = max(2, $current - 1); $i <= min($last - 1, $current + 1); $i++) {
                            $pages[] = $i;
                        }

                        // Ellipsis kanan
                        if ($current < $last - 3) $pages[] = '...';

                        // Selalu tampilkan halaman terakhir
                        if ($last > 1) $pages[] = $last;
                    @endphp

                    @foreach($pages as $page)
                        @if($page === '...')
                            <li class="page-item disabled">
                                <span class="page-link" style="letter-spacing:1px;">···</span>
                            </li>
                        @else
                            <li class="page-item {{ $page == $current ? 'active' : '' }}">
                                <a class="page-link" href="{{ $users->url($page) }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach

                    {{-- Next --}}
                    <li class="page-item {{ !$users->hasMorePages() ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $users->nextPageUrl() ?? '#' }}">
                            <i class="bi bi-chevron-right" style="font-size:11px;"></i>
                        </a>
                    </li>

                </ul>
            </nav>
        </div>
        @endif
    </div>
</div>
@endsection