@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="content">
    <h3 class="fw-bold mb-4">Dashboard</h3>

    <form action="{{ url()->current() }}" method="GET" class="d-flex gap-3 mb-4">
        <select name="bulan" class="form-select w-auto" onchange="this.form.submit()">
            @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $m)
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
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Riwayat Pengajuan SKP</h5>
            <a id="btn-export-pdf"
                href="{{ route('kepala.export.pdf', ['bulan' => $bulanDipilih, 'tahun' => $tahunDipilih]) }}"
                class="btn btn-sm d-none"
                style="background:#1D9E75; color:#fff; border-radius:8px; font-size:13px; padding:7px 16px; display:none; align-items:center; gap:6px;">
                <i class="bi bi-file-earmark-arrow-down"></i> Export PDF
            </a>
        </div>

        {{-- Tabel Menunggu TTD --}}
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
                            <a class="btn btn-outline-success btn-sm"
                               href="{{ route('skp.show.detail', $item->id) }}">
                                <i class="bi bi-qr-code"></i> Tanda Tangani
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Tidak ada data menunggu TTD</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Tabel Selesai --}}
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
                                <i class="bi bi-file-earmark-text"></i> Lihat SKP Final
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Tidak ada data selesai</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const cards     = document.querySelectorAll('.rm-card');
    const tables    = document.querySelectorAll('.rm-table');
    const btnExport = document.getElementById('btn-export-pdf');

    function activateStatus(status) {
        cards.forEach(c => c.classList.remove('active'));
        tables.forEach(t => t.classList.add('d-none'));

        document.querySelector(`.rm-card[data-status="${status}"]`)
            ?.classList.add('active');
        document.querySelector(`.rm-table[data-status="${status}"]`)
            ?.classList.remove('d-none');

        if (btnExport) {
            if (status === 'selesai') {
                btnExport.style.display = 'inline-flex';
                btnExport.classList.remove('d-none');
            } else {
                btnExport.style.display = 'none';
                btnExport.classList.add('d-none');
            }
        }
    }

    activateStatus('ttd');

    cards.forEach(card => {
        card.addEventListener('click', function () {
            activateStatus(this.dataset.status);
        });
    });
});
</script>

@endsection