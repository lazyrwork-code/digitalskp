@extends('layouts.app')

@section('title', 'Dashboard')
@section('content')
    <div class="content">
        <!-- TITLE -->
        <h3 class="fw-bold mb-4">Detail Perbaikan Pengajuan SKP</h3>

        <!-- FORM HEADER -->
        <div class="bg-white p-4 rounded-4 mb-4">
        <div class="row g-4">
            <div class="col-md-6">
            <label class="form-label small text-muted">Tanggal Pengajuan</label>
            <input type="text" class="form-control" value="15/15/2025" disabled />
            </div>

            <div class="col-md-6">
            <label class="form-label small text-muted">Bulan SKP</label>
            <select class="form-select" disabled>
                <option selected>Desember</option>
                <option>November</option>
            </select>
            </div>

            <div class="col-md-6">
            <label class="form-label small text-muted">Nama Pegawai</label>
            <input type="text" class="form-control" value="Intansari, S.ST" disabled />
            </div>

            <div class="col-md-6">
            <label class="form-label small text-muted">Tahun SKP</label>
            <select class="form-select" disabled>
                <option selected>2025</option>
            </select>
            </div>

            <div class="col-md-6">
            <label class="form-label small text-muted">Unit</label>
            <select class="form-select" disabled>
                <option>Instalasi Rekam Medik</option>
                <option>Registrasi Vaksin</option>
                <option>Registrasi Rawat Jalan</option>
                <option>Registrasi Rawat Inap</option>
                <option>Registrasi Rawat IGD</option>
                <option selected>Pengembangan EMR</option>
                <option>Rekam Medis</option>
            </select>
            </div>
        </div>
        </div>

        <!-- DOKUMEN -->
        <div class="bg-white p-4 rounded-4">
        <h5 class="fw-bold mb-3">Dokumen SKP</h5>

        <div class="table-responsive">
            <table class="table align-middle">
            <thead>
                <tr>
                <th width="40">No</th>
                <th>Nama Dokumen</th>
                <th>Judul Laporan</th>
                <th>Link Bukti Dukung</th>
                <th class="text-end">Dokumen SKP</th>
                <th>Keterangan Koreksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                <td>1</td>
                <td>
                    Logbook SKP
                    <div class="small text-muted">Dokumen PDF</div>
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm" placeholder="Ketikkan judul Logbook" value="Logbook 2025" disabled />
                </td>
                <td class="text-center">
                    <button class="btn btn-info btn-sm text-white"><i class="bi bi-file-medical"></i></button>
                </td>
                <td class="text-center">
                    <button class="btn btn-info btn-sm text-white"><i class="bi bi-file-medical"></i></button>
                </td>
                <td>
                    <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" disabled>Seharusnya tanggal 20 tanggal merah</textarea>
                </td>
                </tr>

                <tr>
                <td>2</td>
                <td>
                    Laporan SKP 1
                    <div class="small text-muted">Dokumen PDF</div>
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm" value="Logbook 2025" disabled />
                </td>
                <td class="text-center">
                    <button class="btn btn-info btn-sm text-white"><i class="bi bi-file-medical"></i></button>
                </td>
                <td class="text-center">
                    <button class="btn btn-info btn-sm text-white"><i class="bi bi-file-medical"></i></button>
                </td>
                <td></td>
                </tr>

                <tr>
                <td>3</td>
                <td>
                    Laporan SKP 2
                    <div class="small text-muted">Dokumen PDF</div>
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm" value="Logbook 2025" disabled />
                </td>
                <td class="text-center">
                    <button class="btn btn-info btn-sm text-white"><i class="bi bi-file-medical"></i></button>
                </td>
                <td class="text-center">
                    <button class="btn btn-info btn-sm text-white"><i class="bi bi-file-medical"></i></button>
                </td>
                <td class="text-center"></td>
                </tr>

                <tr>
                <td>4</td>
                <td>
                    Laporan SKP 3
                    <div class="small text-muted">Dokumen PDF</div>
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm" value="Logbook 2025" disabled />
                </td>
                <td class="text-center">
                    <button class="btn btn-info btn-sm text-white"><i class="bi bi-file-medical"></i></button>
                </td>
                <td class="text-center">
                    <button class="btn btn-info btn-sm text-white"><i class="bi bi-file-medical"></i></button>
                </td>
                <td class="text-center"></td>
                </tr>

                <tr>
                <td>5</td>
                <td>
                    Laporan SKP 4
                    <div class="small text-muted">Dokumen PDF</div>
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm" value="Logbook 2025" disabled />
                </td>
                <td class="text-center">
                    <button class="btn btn-info btn-sm text-white"><i class="bi bi-file-medical"></i></button>
                </td>
                <td class="text-center">
                    <button class="btn btn-info btn-sm text-white"><i class="bi bi-file-medical"></i></button>
                </td>
                <td class="text-center"></td>
                </tr>
            </tbody>
            </table>
        </div>

        <!-- ACTION -->
        <div class="text-end mt-4">
            <a class="btn btn-secondary px-4" href="dashboard-admin.html"><i class="bi bi-arrow-left"></i>Kembali</a>
        </div>
        </div>
    </div>
@endsection
