@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
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
      <div class="card-status status-card" data-status="ttd" style="cursor: pointer;">
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
      @if(auth()->user()->role === 'pegawai')
      <a class="btn btn-primary rounded-pill" href="{{ route('skp.baru') }}">
        <i class="bi bi-file-earmark-plus me-1"></i> Ajukan SKP Baru
      </a>
      @endif
    </div>

    <div id="table-wrapper">
    @php
        $statusConfigs = [
            'verifikasi'  => ['class' => 'badge-verifikasi', 'label' => 'Menunggu Verifikasi'],
            'perbaikan'   => ['class' => 'badge-perbaikan',  'label' => 'Perlu Perbaikan'],
            'menungguttd' => ['class' => 'badge-ttd',        'label' => 'Menunggu TTD'],
            'selesai'     => ['class' => 'badge-selesai',    'label' => 'Pengajuan Selesai'],
        ];
    @endphp

    @foreach($statusConfigs as $key => $config)
        <table class="table align-middle status-table {{ $key == 'verifikasi' ? '' : 'd-none' }}" 
               data-status="{{ $key == 'menungguttd' ? 'ttd' : $key }}">
            <thead>
                <tr>
                    @if(auth()->user()->role !== 'pegawai') <th>Pegawai</th> @endif
                    <th>Bulan</th>
                    <th>Tahun</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($allData->where('status', $key) as $item)
                    <tr>
                        @if(auth()->user()->role !== 'pegawai') <td>{{ $item->user->name ?? 'User Terhapus' }}</td> @endif
                        <td>{{ $item->bulan }}</td>
                        <td>{{ $item->tahun }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d-m-Y') }}</td>
                        <td>
                            <span class="badge-status {{ $config['class'] }}"> 
                                {{ $config['label'] }} 
                            </span>
                        </td>
                        <td class="text-end">
                          @if($item->status === 'perbaikan')
                              <a class="btn btn-warning btn-sm rounded-pill"
                                href="{{ route('skp.edit', $item->id) }}">
                                  <i class="bi bi-edit"></i> Perbaiki SKP
                              </a>
                          @else
                              <a class="btn btn-success btn-sm rounded-pill"
                                href="{{ route('skp.show', $item->id) }}">
                                  <i class="bi bi-search"></i> Detail SKP
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
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.status-card');
        const tables = document.querySelectorAll('.status-table');

        cards.forEach(card => {
            card.addEventListener('click', function() {
                // 1. Ubah tampilan card aktif
                cards.forEach(c => c.classList.remove('active'));
                this.classList.add('active');

                // 2. Ambil target status
                const targetStatus = this.getAttribute('data-status');

                // 3. Tampilkan tabel yang sesuai
                tables.forEach(table => {
                    if (table.getAttribute('data-status') === targetStatus) {
                        table.classList.remove('d-none');
                    } else {
                        table.classList.add('d-none');
                    }
                });
            });
        });
    });
  </script>
@endsection