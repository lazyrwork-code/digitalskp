@extends('layouts.app')

@section('title', 'Detail Pengajuan SKP')

@section('content')
<div class="content">
    <!-- TITLE -->
    <h3 class="fw-bold mb-4">Detail Pengajuan SKP</h3>

    <!-- FORM HEADER -->
    <div class="bg-white p-4 rounded-4 mb-4">
    <div class="row g-4">
        <div class="col-md-6">
        <label class="form-label small text-muted">Tanggal Pengajuan</label>
        <input type="text" class="form-control" value="{{ $skp->tanggal_pengajuan->format('d/m/Y') }}" disabled />
        </div>

        <div class="col-md-6">
        <label class="form-label small text-muted">Bulan SKP</label>
        <select class="form-select" disabled>
            <option selected>{{ $skp->bulan }}</option>
        </select>
        </div>

        <div class="col-md-6">
        <label class="form-label small text-muted">Nama Pegawai</label>
        <input type="text" class="form-control" value="{{ $skp->user->nama }}" disabled />
        </div>

        <div class="col-md-6">
        <label class="form-label small text-muted">Tahun SKP</label>
        <select class="form-select" disabled>
            <option selected>{{ $skp->tahun }}</option>
        </select>
        </div>

        <div class="col-md-6">
        <label class="form-label small text-muted">Unit</label>
        <select class="form-select" disabled>
            <option selected>{{ $skp->unit }}</option>
        </select>
        </div>
    </div>
    </div>

    <!-- UPLOAD DOKUMEN -->
    <div class="bg-white p-4 rounded-4">
    <h5 class="fw-bold mb-3">Upload Dokumen SKP</h5>

    @php
        $dokumenUtama = $skp->dokumen->filter(fn($d) => !str_starts_with($d->nama_file, 'Aktivitas Harian eMaster'));

        // KeyBy pakai id dokumen utama berdasarkan urutan created_at
        $dokumenAktivitas = $skp->dokumen
            ->filter(fn($d) => str_starts_with($d->nama_file, 'Aktivitas Harian eMaster'))
            ->values();

        $dokumenUtamaList = $dokumenUtama->values();
    @endphp

    <div class="table-responsive">
        <table class="table align-middle">
        <thead>
            <tr>
            <th width="40">No</th>
            <th>Nama Dokumen</th>
            <th>Kegiatan Tugas Jabatan</th>
            <th>Link Bukti Dukung</th>
            <th class="text-center">Aktivitas Harian eMaster</th>
            <th class="text-center">Laporan Realisasi Kegiatan</th>
            <th class="text-center">Dokumen SKP Selesai TTD</th>
            </tr>
        </thead>
        <tbody>
            @php
                $namaFix = [
                    0 => 'Catatan Harian Kerja',
                    1 => 'Laporan SKP 1',
                    2 => 'Laporan SKP 2',
                    3 => 'Laporan SKP 3',
                    4 => 'Laporan SKP 4',
                ];
            @endphp
            @forelse($dokumenUtamaList as $i => $dok)
            <tr>
                <td>{{ $loop->iteration }}</td>
                {{-- Nama Dokumen --}}
                <td>
                    {{ $namaFix[$loop->index] ?? $dok->nama_file }}
                    <div class="small text-muted">Dokumen PDF</div>
                </td>

                {{-- Kegiatan Tugas Jabatan --}}
                <td>
                    @php
                        $isKoreksi = str_starts_with($dok->catatan ?? '', '[KOREKSI]');
                        $kegiatan = $isKoreksi ? '-' : ($dok->nama_file ?? '-');
                    @endphp
                    <input type="text" class="form-control form-control-sm"
                        value="{{ $dok->nama_file }}" disabled />
                </td>
                <td class="text-center">
                    @if($dok->link_pendukung && $dok->link_pendukung !== '-')
                        <a href="{{ $dok->link_pendukung }}" target="_blank" class="btn btn-info btn-sm text-white">
                            <i class="bi bi-file-medical"></i>
                        </a>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>

                {{-- Aktivitas Harian — ambil berdasarkan index, bukan nama file --}}
                <td class="text-center">
                    @php
                        // Catatan Harian Kerja (index 0) tidak punya aktivitas
                        // Laporan SKP 1-4 (index 1-4) punya aktivitas di index 0-3
                        $aktivitas = ($i > 0) ? ($dokumenAktivitas[$i - 1] ?? null) : null;
                    @endphp
                    @if($aktivitas)
                        <a href="{{ asset('storage/'.$aktivitas->url) }}" target="_blank"
                        class="btn btn-info btn-sm text-white">
                            <i class="bi bi-file-earmark-pdf"></i>
                        </a>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>

                <td class="text-center">
                    @if($dok->url)
                        <a href="{{ asset('storage/'.$dok->url) }}" target="_blank"
                        class="btn btn-info btn-sm text-white">
                            <i class="bi bi-file-earmark-pdf"></i>
                        </a>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                {{-- Dokumen TTD --}}
                <td class="text-center">
                    {{-- Aktivitas Harian yang sudah TTD --}}
                    @if($aktivitas && $aktivitas->url_signed && $aktivitas->url_signed !== '-')
                        <a href="{{ asset('storage/'.$aktivitas->url_signed) }}" target="_blank"
                        class="btn btn-success btn-sm text-white d-flex align-items-center gap-1">
                            <i class="bi bi-file-earmark-pdf"></i>
                            <span style="font-size:11px;">Aktivitas</span>
                        </a>
                    @elseif($aktivitas)
                        <span class="text-muted d-block" style="font-size:11px;">Aktivitas: -</span>
                    @endif
                    {{-- Laporan Realisasi yang sudah TTD --}}
                    @if($dok->url_signed && $dok->url_signed !== '-')
                        <a href="{{ asset('storage/'.$dok->url_signed) }}" target="_blank"
                        class="btn btn-primary btn-sm text-white mb-1 d-flex align-items-center gap-1">
                            <i class="bi bi-file-earmark-pdf"></i>
                            <span style="font-size:11px;">Laporan</span>
                        </a>
                    @else
                        <span class="text-muted d-block mb-1" style="font-size:11px;">Laporan: -</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center text-muted">Belum ada dokumen diunggah</td>
            </tr>
            @endforelse
        </tbody>
        </table>
    </div>

    <!-- ACTION -->
    <div class="text-end mt-4">
        <a class="btn btn-secondary px-4" href="{{ url()->previous() }}">
            <i class="bi bi-arrow-left"></i>Kembali
        </a>
    </div>
    </div>
</div>
@endsection