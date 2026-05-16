@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="content">
    <h3 class="fw-bold mb-4">Dashboard</h3>

<form action="{{ url()->current() }}" method="GET" class="d-flex gap-3 mb-4">

    <input type="hidden" name="status" id="statusInput" value="{{ request('status', 'ttd') }}">

    <select name="bulan" class="form-select w-auto" onchange="this.form.submit()">
        @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $m)
            <option value="{{ $m }}" {{ $bulanDipilih == $m ? 'selected' : '' }}>
                {{ $m }}
            </option>
        @endforeach
    </select>

    <select name="tahun" class="form-select w-auto" onchange="this.form.submit()">
        @for($y = date('Y'); $y >= 2024; $y--)
            <option value="{{ $y }}" {{ $tahunDipilih == $y ? 'selected' : '' }}>
                {{ $y }}
            </option>
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
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Riwayat Pengajuan SKP</h5>
            <div class="input-group" style="width: 280px">
                {{-- <input type="text" id="searchInput" class="form-control" placeholder="Cari nama pegawai..."> --}}
                {{-- <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span> --}}
            </div>
        </div>

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
                            <td>{{ $item->user->nama ?? 'User' }}</td>
                            <td>{{ $item->bulan }}</td>
                            <td>{{ $item->tahun }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d-m-Y') }}</td>
                            <td><span class="badge-status badge-ttd">Menunggu TTD</span></td>
                            <td class="text-end">
                                <a class="btn btn-outline-success btn-sm" href="{{ route('skp.show.detail', $item->id) }}">
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
                            <td>{{ $item->user->nama ?? 'User' }}</td>
                            <td>{{ $item->bulan }}</td>
                            <td>{{ $item->tahun }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d-m-Y') }}</td>
                            <td><span class="badge-status badge-selesai">Selesai</span></td>
                            <td class="text-end">
                              <a class="btn btn-success btn-sm rounded-pill"
                                href="{{ route('skp.showskpdone', $item->id) }}">
                                <i class="bi bi-file-earmark-text"></i>Lihat SKP Final
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

    function activateStatus(status) {

        // reset semua card
        cards.forEach(c => c.classList.remove('active'));

        // reset semua table
        tables.forEach(t => t.classList.add('d-none'));

        // aktifkan yg dipilih
        document.querySelector(`.status-card[data-status="${status}"]`)
            ?.classList.add('active');

        document.querySelector(`.status-table[data-status="${status}"]`)
            ?.classList.remove('d-none');
    }

    // 🔥 default pertama kali load
    activateStatus('ttd');

    // klik event
    cards.forEach(card => {
        card.addEventListener('click', function() {
            activateStatus(this.dataset.status);
        });
    });
});
document.getElementById('searchInput').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    document.querySelectorAll('.status-table:not(.d-none) tbody tr').forEach(function(row) {
        row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
    });
});
</script>@endsection