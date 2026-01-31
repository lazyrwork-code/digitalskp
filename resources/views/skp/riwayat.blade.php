@extends('layouts.app')

@section('title', 'Repository SKP')

@section('content')

@php
use Carbon\Carbon;

$riwayat = [
    (object)[
        'id' => 1,
        'user' => (object)['name' => 'Budi Santoso'],
        'unit' => 'IT',
        'bulan' => 'Januari',
        'tanggal_pengajuan' => Carbon::parse('2025-01-10'),
        'status' => 'Disetujui'
    ],
    (object)[
        'id' => 2,
        'user' => (object)['name' => 'Siti Aminah'],
        'unit' => 'Keuangan',
        'bulan' => 'Februari',
        'tanggal_pengajuan' => Carbon::parse('2025-02-12'),
        'status' => 'Menunggu'
    ],
    (object)[
        'id' => 3,
        'user' => (object)['name' => 'Andi Wijaya'],
        'unit' => 'SDM',
        'bulan' => 'Maret',
        'tanggal_pengajuan' => Carbon::parse('2025-03-05'),
        'status' => 'Ditolak'
    ],
];
@endphp

<div class="content">
    <h3 class="fw-bold mb-4">
        Repository SKP
        @if(auth()->user()->role === 'pegawai')
            - Saya
        @endif
    </h3>

    <div class="row g-4">
        <!-- FILTER TAHUN -->
       <div class="col-md-3">
            <div class="bg-white rounded-4 p-3 shadow-sm h-100">
                <div class="d-grid gap-2">
                    @php
                        // List tahun yang ingin ditampilkan
                        $daftarTahun = [2026, 2025, 2024];
                    @endphp
                    
                    @foreach($daftarTahun as $th)
                        <a href="{{ route('skp.riwayat', ['tahun' => $th]) }}" 
                        class="btn {{ $tahunDipilih == $th ? 'btn-primary' : 'btn-outline-secondary' }}">
                            {{ $th }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- TABLE -->
        <div class="col-md-9">
            <div class="bg-white rounded-4 p-4 shadow-sm">
                <h5 class="fw-bold mb-3">Riwayat Pengajuan SKP</h5>

                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Nama Pegawai</th>
                            <th>Unit</th>
                            <th>Bulan / Tahun</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $item)
                        <tr>
                            <td>{{ $item->user->nama ?? 'User Terhapus' }}</td>
                            <td>{{ $item->unit }}</td>
                            <td>{{ $item->bulan }} {{ $item->tahun }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d-m-Y') }}</td>
                            <td>
                                {{-- Status Mapping sesuai Database --}}
                                @if($item->status === 'selesai')
                                    <span class="badge bg-success text-white">Selesai</span>
                                @elseif($item->status === 'verifikasi')
                                    <span class="badge bg-warning text-dark">Verifikasi</span>
                                @elseif($item->status === 'perbaikan')
                                    <span class="badge bg-danger text-white">Perbaikan</span>
                                @else
                                    <span class="badge bg-secondary text-white">{{ $item->status }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('skp.show', $item->id) }}" class="btn btn-outline-success btn-sm rounded-pill">
                                    <i class="bi bi-search"></i> Lihat SKP
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted p-4">
                                Belum ada riwayat pengajuan SKP.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
