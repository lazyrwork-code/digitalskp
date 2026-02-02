@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="content">
    <h3 class="fw-bold mb-4">Dashboard</h3>

    <form action="{{ url()->current() }}" method="GET" class="d-flex gap-3 mb-4">
        <select name="bulan" class="form-select w-auto" onchange="this.form.submit()">
            @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $m)
                <option value="{{ $m }}" {{ $bulanDipilih == $m ? 'selected' : '' }}>{{ $m }}</option>
            @endforeach
        </select>
        <select name="tahun" class="form-select w-auto" onchange="this.form.submit()">
            @for($y = date('Y'); $y >= 2024; $y--)
                <option value="{{ $y }}" {{ $tahunDipilih == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
    </form>

    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card-status status-card rm-card active" data-status="ttd" style="cursor: pointer;">
                <div>
                    <div class="text-muted">Menunggu TTD</div>
                    <h2 class="fw-bold mt-2">{{ $counts['ttd'] }}</h2>
                </div>
                <div class="icon-circle icon-purple">
                    <i class="bi bi-qr-code"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card-status status-card rm-card" data-status="selesai" style="cursor: pointer;">
                <div>
                    <div class="text-muted">Pengajuan Selesai</div>
                    <h2 class="fw-bold mt-2">{{ $counts['selesai'] }}</h2>
                </div>
                <div class="icon-circle icon-green">
                    <i class="bi bi-file-earmark-check"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white p-4 rounded-4 shadow-sm">
        <h5 class="fw-bold mb-3">Riwayat Pengajuan SKP</h5>

        <div id="table-wrapper">
            <table class="table align-middle status-table rm-table" data-status="ttd">
                <thead>
                    <tr>
                        <th>Nama Pegawai</th>
                        <th>Bulan</th>
                        <th>Tahun</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allData->where('status', 'menungguttd') as $item)
                        <tr>
                            <td>{{ $item->user->name ?? 'User' }}</td>
                            <td>{{ $item->bulan }}</td>
                            <td>{{ $item->tahun }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d-m-Y') }}</td>
                            <td><span class="badge-status badge-ttd">Menunggu TTD</span></td>
                            <td class="text-end">
                                <a class="btn btn-outline-success btn-sm" href="{{ route('skp.show', $item->id) }}">
                                    <i class="bi bi-qr-code"></i> Tanda Tangani
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4">Tidak ada data menunggu TTD</td></tr>
                    @endforelse
                </tbody>
            </table>

            <table class="table align-middle status-table rm-table d-none" data-status="selesai">
                <thead>
                    <tr>
                        <th>Nama Pegawai</th>
                        <th>Bulan</th>
                        <th>Tahun</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allData->where('status', 'selesai') as $item)
                        <tr>
                            <td>{{ $item->user->name ?? 'User' }}</td>
                            <td>{{ $item->bulan }}</td>
                            <td>{{ $item->tahun }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d-m-Y') }}</td>
                            <td><span class="badge-status badge-selesai">Selesai</span></td>
                            <td class="text-end">
                                <a class="btn btn-outline-success btn-sm" href="{{ url('/skp/review' . $item->id ) }}">
                                    <i class="bi bi-qr-code"></i> Tanda Tangani
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4">Tidak ada data selesai</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.status-card');
    const tables = document.querySelectorAll('.status-table');

    cards.forEach(card => {
        card.addEventListener('click', function() {
            cards.forEach(c => c.classList.remove('active'));
            this.classList.add('active');

            const target = this.getAttribute('data-status');
            tables.forEach(table => {
                table.classList.toggle('d-none', table.getAttribute('data-status') !== target);
            });
        });
    });
});
</script>
@endsection