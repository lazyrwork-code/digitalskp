@extends('layouts.app')

@section('title', 'Repository SKP')

@section('content')

@php
use Carbon\Carbon;

@endphp

<div class="content">
    <h3 class="fw-bold mb-4">
    Repository SKP - {{ $user->nama }}
</h3>
    <div class="row g-4">
        <!-- FILTER TAHUN -->
       <div class="col-md-3">
            <div class="bg-white rounded-4 p-3 shadow-sm h-100">
                <div class="d-grid gap-2">
                    @foreach(range(date('Y') + 2, date('Y') - 2) as $th)
                        <a href="{{ route('kepala.riwayat-user', ['id_user'=>$id_user, 'tahun'=>$th]) }}" 
                        class="btn {{ $tahunDipilih == $th ? 'btn-success' : 'btn-outline-secondary' }}">
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
                            <th>Bulan</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Unit</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $item)
                        <tr>
                            <td>{{ $item->bulan }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d-m-Y') }}</td>
                            <td>{{ $item->unit }}</td>
                            <td class="text-end">
                                <a href="{{ route('skp.showskpdone', $item->id) }}" class="btn btn-outline-success btn-sm rounded-pill">
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
                <div class="text-end mt-4">
                    <a class="btn btn-secondary px-4" href="{{ route('kepala.riwayat') }}"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
