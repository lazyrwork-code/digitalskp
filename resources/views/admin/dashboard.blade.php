@extends('layouts.app')
@section('title', 'Dashboard Admin')
@section('content')
<style>
.card-status.active {
    border-color: #1D9E75;
    box-shadow: 0 0 0 2px #1D9E75;
    background: #f0fdf4;
}
</style>
  <h3 class="fw-bold mb-4">Dashboard</h3>

  <form action="{{ route('admin.dashboard') }}" method="GET" class="d-flex gap-3 mb-4">
    <select name="bulan" class="form-select w-auto" onchange="this.form.submit()">
        @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $m)
            <option value="{{ $m }}" {{ $bulanDipilih == $m ? 'selected' : '' }}>{{ $m }}</option>
        @endforeach
    </select>
    <select name="tahun" class="form-select w-auto" onchange="this.form.submit()">
        @for($t = date('Y'); $t >= 2024; $t--)
            <option value="{{ $t }}" {{ $tahunDipilih == $t ? 'selected' : '' }}>{{ $t }}</option>
        @endfor
    </select>
  </form>

  <div class="row g-4 mb-5">
    <div class="col-md-3">
      <div class="card-status status-card active" data-status="verifikasi" style="cursor: pointer;">
        <div>
          <div class="text-muted">Menunggu Verifikasi</div>
          <h2 class="fw-bold mt-2">{{ $counts['verifikasi'] }}</h2>
        </div>
        <div class="icon-circle text-warning">
          <i class="bi bi-clock-history"></i>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card-status status-card" data-status="perbaikan" style="cursor: pointer;">
        <div>
          <div class="text-muted">Perlu Perbaikan</div>
          <h2 class="fw-bold mt-2">{{ $counts['perbaikan'] }}</h2>
        </div>
        <div class="icon-circle icon-orange">
          <i class="bi bi-pencil-square"></i>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card-status status-card" data-status="menungguttd" style="cursor: pointer;">
        <div>
          <div class="text-muted">Menunggu TTD</div>
          <h2 class="fw-bold mt-2">{{ $counts['ttd'] }}</h2>
        </div>
        <div class="icon-circle icon-purple">
          <i class="bi bi-pen"></i>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card-status status-card" data-status="selesai" style="cursor: pointer;">
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
      <h5 class="fw-bold mb-0">Riwayat Pengajuan SKP ({{ $bulanDipilih }} {{ $tahunDipilih }})</h5>
      <a id="btn-export-pdf"
          href="{{ route('admin.export.pdf', ['bulan' => $bulanDipilih, 'tahun' => $tahunDipilih]) }}"
          class="btn btn-success btn-sm rounded-pill d-none">
          <i class="bi bi-file-earmark-arrow-down me-1"></i> Export PDF
      </a>
    </div>

    @php
        $statusConfigs = [
            'verifikasi'  => ['class' => 'badge-verifikasi', 'label' => 'Menunggu Verifikasi'],
            'perbaikan'   => ['class' => 'badge-perbaikan',  'label' => 'Perlu Perbaikan'],
            'menungguttd' => ['class' => 'badge-ttd',        'label' => 'Menunggu TTD'],
            'selesai'     => ['class' => 'badge-selesai',    'label' => 'Pengajuan Selesai'],
        ];
    @endphp

    @foreach($statusConfigs as $key => $config)
        <table class="table align-middle status-table {{ $key === 'verifikasi' ? '' : 'd-none' }}"
               data-status="{{ $key }}">
            <thead>
                <tr>
                    <th>Pegawai</th>
                    <th>Bulan</th>
                    <th>Tahun</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($allData->filter(fn($item) => trim(strtolower($item->status)) === strtolower($key)) as $skp)
                    <tr>
                        <td>{{ $skp->user->nama ?? 'User Terhapus' }}</td>
                        <td>{{ $skp->bulan }}</td>
                        <td>{{ $skp->tahun }}</td>
                        <td>{{ \Carbon\Carbon::parse($skp->tanggal_pengajuan)->format('d-m-Y') }}</td>
                        <td>
                            <span class="badge-status {{ $config['class'] }}">
                                {{ $config['label'] }}
                            </span>
                        </td>
                        <td class="text-end">
                            @if($key === 'verifikasi')
                                <a href="{{ url('admin/skp/'.$skp->id) }}" 
                                   class="btn btn-success btn-sm rounded-pill">
                                    <i class="bi bi-clipboard-check"></i> Verifikasi
                                </a>
                            @elseif($key === 'selesai')
                                <a href="{{ url('show/done/'.$skp->id) }}" 
                                   class="btn btn-success btn-sm rounded-pill">
                                    <i class="bi bi-file-earmark-pdf"></i> Lihat Final
                                </a>
                            @else
                                <a href="{{ url('skp/show/'.$skp->id) }}" 
                                   class="btn btn-success btn-sm rounded-pill">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            Tidak ada data pengajuan dengan status {{ $config['label'] }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endforeach
  </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const cards     = document.querySelectorAll('.status-card');
    const tables    = document.querySelectorAll('.status-table');
    const btnExport = document.getElementById('btn-export-pdf');

    cards.forEach(card => {
        card.addEventListener('click', function () {
            cards.forEach(c => c.classList.remove('active'));
            this.classList.add('active');

            const status = this.dataset.status;

            tables.forEach(t => {
                t.getAttribute('data-status') === status
                    ? t.classList.remove('d-none')
                    : t.classList.add('d-none');
            });

            // Tombol export hanya muncul di tab selesai
            if (btnExport) {
                status === 'selesai'
                    ? btnExport.classList.remove('d-none')
                    : btnExport.classList.add('d-none');
            }
        });
    });
});
</script>

@endsection