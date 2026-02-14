  @extends('layouts.app')

  @section('title', 'Dashboard')

  @section('content')
      <h3 class="fw-bold mb-4">Dashboard</h3>

      <form action="{{ route('admin.dashboard') }}" method="GET" class="d-flex gap-3 mb-4">
          <select name="bulan" class="form-select w-auto" onchange="this.form.submit()">
              @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $m)
                  <option value="{{ $m }}" {{ $bulanDipilih == $m ? 'selected' : '' }}>{{ $m }}</option>
              @endforeach
          </select>
          <select name="tahun" class="form-select w-auto" onchange="this.form.submit()">
              @for($t = 2024; $t <= 2026; $t++)
                  <option value="{{ $t }}" {{ $tahunDipilih == $t ? 'selected' : '' }}>{{ $t }}</option>
              @endfor
          </select>
      </form>

      <div class="row g-4 mb-5">
          <div class="col-md-3">
              <div class="card-status status-card active" data-status="verifikasi">
                  <div>
                      <div class="text-muted">Menunggu Verifikasi</div>
                      <h2 class="fw-bold mt-2">{{ $counts['verifikasi'] }}</h2>
                  </div>
                  <div class="icon-circle text-warning"><i class="bi bi-clock-history"></i></div>
              </div>
          </div>

          <div class="col-md-3">
              <div class="card-status status-card" data-status="perbaikan">
                  <div>
                      <div class="text-muted">Perlu Perbaikan</div>
                      <h2 class="fw-bold mt-2">{{ $counts['perbaikan'] }}</h2>
                  </div>
                  <div class="icon-circle icon-orange"><i class="bi bi-pencil-square"></i></div>
              </div>
          </div>

          <div class="col-md-3">
              <div class="card-status status-card" data-status="menungguttd">
                  <div>
                      <div class="text-muted">Menunggu TTD</div>
                      <h2 class="fw-bold mt-2">{{ $counts['ttd'] }}</h2>
                  </div>
                  <div class="icon-circle icon-purple"><i class="bi bi-qr-code"></i></div>
              </div>
          </div>

          <div class="col-md-3">
              <div class="card-status status-card" data-status="selesai">
                  <div>
                      <div class="text-muted">Pengajuan Selesai</div>
                      <h2 class="fw-bold mt-2">{{ $counts['selesai'] }}</h2>
                  </div>
                  <div class="icon-circle icon-green"><i class="bi bi-file-earmark-check"></i></div>
              </div>
          </div>
      </div>

      <div class="bg-white p-4 rounded-4">
          <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="fw-bold mb-0">Riwayat Pengajuan SKP</h5>
              <a class="btn btn-prima" href="#">
                  <i class="bi bi-file-earmark-plus me-1"></i> Ajukan SKP Baru
              </a>
          </div>

          <div id="table-wrapper">
            @foreach(['verifikasi', 'perbaikan', 'menungguttd', 'selesai'] as $statusKey)
                <table class="table align-middle status-table {{ $statusKey === 'verifikasi' ? '' : 'd-none' }}" data-status="{{ $statusKey }}">
                    <thead>
                        <tr>
                            <th>Nama Pegawai</th>
                            <th>Unit</th>
                            <th>Bulan</th>
                            <th>Tahun</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php 
                            // Kita paksa semua jadi huruf kecil dan hapus spasi sebelum dibandingin
                            $filteredData = $allData->filter(function($item) use ($statusKey) {
                                return trim(strtolower($item->status)) === strtolower($statusKey);
                            });
                        @endphp

                        @forelse($filteredData as $skp)
                        <tr>
                            <td>{{ $skp->user->nama ?? 'User Hilang' }}</td>
                            <td>{{ $skp->unit }}</td>
                            <td>{{ $skp->bulan }}</td>
                            <td>{{ $skp->tahun }}</td>
                            <td>{{ $skp->created_at->format('d-m-Y') }}</td>
                            <td>
                                <span class="badge-status badge-{{ $statusKey === 'menungguttd' ? 'ttd' : $statusKey }}">
                                    @if($statusKey == 'verifikasi') Menunggu Verifikasi
                                    @elseif($statusKey == 'perbaikan') Perlu Perbaikan
                                    @elseif($statusKey == 'menungguttd') Menunggu TTD
                                    @else Selesai @endif
                                </span>
                            </td>
                            <td class="text-end">
            @php
                $role = auth()->user()->role; // Sesuaikan dengan cara kamu memanggil role (misal: $role = 'admin')
                $btnLabel = 'Lihat Detail'; // Default label
                $btnIcon = 'bi-eye';
                $btnClass = 'btn-outline-primary';
                $btnUrl = url('skp/show/'.$skp->id); // Default URL
            @endphp

            @if($role == 'pegawai')
                @if($statusKey == 'verifikasi')
                    @php $btnLabel = 'Lihat Detail'; $btnIcon = 'bi-eye'; @endphp
                @elseif($statusKey == 'perbaikan')
                    @php 
                        $btnLabel = 'Perbaiki Dokumen'; 
                        $btnIcon = 'bi-pencil-square'; 
                        $btnClass = 'btn-warning';
                        $btnUrl = url('pegawai/skp/edit/'.$skp->id); // Sesuaikan route edit pegawai
                    @endphp
                @elseif($statusKey == 'menungguttd')
                    @php $btnLabel = 'Lihat Detail'; $btnIcon = 'bi-eye'; @endphp
                @elseif($statusKey == 'selesai')
                    @php $btnLabel = 'Lihat SKP Final'; $btnIcon = 'bi-file-earmark-pdf'; $btnClass = 'btn-success';  @endphp
                @endif

            @elseif($role == 'admin')
                @if($statusKey == 'verifikasi')
                    @php $btnLabel = 'Verifikasi SKP'; $btnIcon = 'bi-clipboard-check'; $btnClass = 'btn-outline-success'
                    ;$btnUrl = url('admin/skp/'.$skp->id); @endphp
                    
                @elseif($statusKey == 'perbaikan')
                    @php $btnLabel = 'Lihat Detail'; $btnIcon = 'bi-eye'; @endphp
                @elseif($statusKey == 'menungguttd')
                    @php $btnLabel = 'Lihat Detail'; $btnIcon = 'bi-eye'; @endphp
                @elseif($statusKey == 'selesai')
                    @php $btnLabel = 'Lihat SKP Final'; $btnIcon = 'bi-file-earmark-pdf'; $btnClass = 'btn-success';$btnUrl = url('show/done/'.$skp->id) @endphp
                @endif

            @elseif($role == 'kepala')
                @if($statusKey == 'menungguttd')
                    @php 
                        $btnLabel = 'Tanda Tangani'; 
                        $btnIcon = 'bi-pen'; 
                        $btnClass = 'btn-primary';
                        $btnUrl = url('kepala/skp/ttd/'.$skp->id); 
                    @endphp
                @elseif($statusKey == 'selesai')
                    @php $btnLabel = 'Lihat SKP Final'; $btnIcon = 'bi-file-earmark-pdf'; $btnClass = 'btn-success'; @endphp
                @endif
            @endif

            {{-- Render Tombol --}}
            <a class="btn {{ $btnClass }} btn-sm" href="{{ $btnUrl }}">
                <i class="bi {{ $btnIcon }}"></i> {{ $btnLabel }}
            </a>
        </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted p-5">
                        <i class="bi bi-inbox d-block mb-2" style="font-size: 2rem;"></i>
                        Data <b>{{ $statusKey }}</b> tidak ditemukan di variabel $allData.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    @endforeach
</div>
      </div>
  @endsection
  <script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.status-card').forEach(card => {
        card.addEventListener('click', function () {

            document.querySelectorAll('.status-card')
                .forEach(c => c.classList.remove('active'));
            this.classList.add('active');

            const targetStatus = this.getAttribute('data-status');

            document.querySelectorAll('.status-table')
                .forEach(table => table.classList.add('d-none'));

            const targetTable = document.querySelector(
                `.status-table[data-status="${targetStatus}"]`
            );

            if (targetTable) {
                targetTable.classList.remove('d-none');
            }
        });
    });
});
</script>
