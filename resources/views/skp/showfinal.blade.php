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

    <div class="table-responsive">
        <table class="table align-middle">
        <thead>
            <tr>
            <th width="40">No</th>
            <th>Nama Dokumen</th>
            <th>Judul Laporan</th>
            <th>Link Bukti Dukung</th>
            <th>Upload Dokumen SKP</th>
            <th>Dokumen SKP Selesai TTD</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($skp->dokumen as $i => $dok)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>
                    {{ $dok->nama_file }}
                    <div class="small text-muted">Dokumen PDF</div>
                </td>

                <td>
                    <input type="text"
                        class="form-control form-control-sm"
                        value="{{ $dok->nama_file }}"
                        disabled />
                </td>

                <td class="text-center">
                    @if ($dok->link_pendukung)
                        <a href="{{ $dok->link_pendukung }}"
                        target="_blank"
                        class="btn btn-info btn-sm text-white">
                            <i class="bi bi-file-medical"></i>
                        </a>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>

                <td class="text-center">
                    @if ($dok->url)
                        <a href="{{ asset('storage/'.$dok->url) }}"
                        target="_blank"
                        class="btn btn-info btn-sm text-white">
                            <i class="bi bi-file-earmark-pdf"></i>
                        </a>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td class="text-center">
                    @if ($dok->url_signed)
                        <a href="{{ asset('storage/'.$dok->url_signed) }}"
                        target="_blank"
                        class="btn btn-primary btn-sm text-white">
                            <i class="bi bi-file-earmark-pdf"></i>
                        </a>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center text-muted">
                    Belum ada dokumen diunggah
                </td>
            </tr>
            @endforelse
            </tbody>

        </table>
    </div>

    <!-- ACTION -->
    <div class="text-end mt-4">
        <a class="btn btn-secondary px-4" href="{{ route('redirect.role') }}"><i class="bi bi-arrow-left"></i>Kembali</a>
    </div>
    </div>
</div>
@endsection