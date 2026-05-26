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
                <option>Kepala Instalasi Rekam Medik</option>
                <option>Registrasi</option>
                <option>Pelayanan Rekam Medik</option>
                <option>Koding dan Grouping</option>
                <option>Filing</option>
                <option selected>Pengembangan, Pelaporan dan Evaluasi</option>
            </select>
            </div>
        </div>
        </div>

        <!-- UPLOAD DOKUMEN -->
        <div class="bg-white p-4 rounded-4">
            <h5 class="fw-bold mb-3">Upload Dokumen SKP</h5>

            @php
                $dokumenUtama = $skp->dokumen->filter(fn($d) => !str_starts_with($d->nama_file, 'Aktivitas Harian eMaster'));
                $dokumenAktivitas = $skp->dokumen
                    ->filter(fn($d) => str_starts_with($d->nama_file, 'Aktivitas Harian eMaster'))
                    ->keyBy(fn($d) => trim(str_replace('Aktivitas Harian eMaster -', '', $d->nama_file)));
            @endphp

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th width="40">No</th>
                            <th>Nama Dokumen</th>
                            <th>Kegiatan Tugas Jabatan</th>
                            <th>Link Bukti Dukung</th>
                            <th>Keterangan Koreksi</th>
                            <th class="text-center">Aktivitas Harian eMaster</th>
                            <th class="text-center">Laporan Realisasi Kegiatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dokumenUtama as $i => $doc)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>
                                {{ $doc->nama_file }}
                                <div class="small text-muted">Dokumen PDF</div>
                            </td>

                            {{-- JUDUL LAPORAN --}}
                            <td>
                                <input type="text"
                                    class="form-control form-control-sm"
                                    name="judul_laporan[{{ $doc->id }}]"
                                    value="{{ $doc->nama_file }}"
                                    {{ $doc->catatan && $doc->catatan !== '-' ? '' : 'disabled' }}>
                            </td>

                            {{-- LINK BUKTI --}}
                            <td class="text-center">
                                @if($doc->catatan && $doc->catatan !== '-')
                                    <input type="text"
                                        class="form-control form-control-sm"
                                        name="link_pendukung[{{ $doc->id }}]"
                                        value="{{ $doc->link_pendukung }}">
                                @else
                                    <a href="{{ $doc->link_pendukung }}" target="_blank"
                                    class="btn btn-info btn-sm text-white">
                                        <i class="bi bi-file-medical"></i>
                                    </a>
                                @endif
                            </td>

                            {{-- KOREKSI --}}
                            <td>
                                @if($doc->catatan && $doc->catatan !== '-')
                                    <textarea class="form-control form-control-sm" rows="2" disabled>{{ $doc->catatan }}</textarea>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>

                            {{-- AKTIVITAS HARIAN --}}
                            <td class="text-center">
                                @php $aktivitas = $dokumenAktivitas[$doc->nama_file] ?? null; @endphp
                                @if($aktivitas)
                                    @if($doc->catatan && $doc->catatan !== '-')
                                        <label class="btn btn-warning btn-sm text-white me-1 mb-0">
                                            <template x-if="!dokumenList[{{ $loop->index }}].aktivitasLoading">
                                                <span><i class="bi bi-pencil"></i> Ubah</span>
                                            </template>
                                            <template x-if="dokumenList[{{ $loop->index }}].aktivitasLoading">
                                                <span><span class="spinner-border spinner-border-sm"></span> Uploading...</span>
                                            </template>
                                            <input type="file" hidden
                                                @change="handleFileUpload($event, {{ $loop->index }}, {{ $aktivitas->id }}, 'aktivitas')">
                                        </label>
                                        <input type="hidden"
                                            :name="'dokumen[{{ $aktivitas->id }}][path]'"
                                            x-model="dokumenList[{{ $loop->index }}].aktivitasPath">
                                    @endif
                                    <a :href="dokumenList[{{ $loop->index }}]?.aktivitasUploaded ? dokumenList[{{ $loop->index }}].aktivitasUrl : '{{ asset('storage/' . $aktivitas->url) }}'"
                                    target="_blank"
                                    class="btn btn-sm"
                                    :class="dokumenList[{{ $loop->index }}]?.aktivitasUploaded ? 'btn-success text-white' : 'btn-outline-primary'">
                                        <i class="bi bi-eye"></i>
                                        <span x-text="dokumenList[{{ $loop->index }}]?.aktivitasUploaded ? 'Lihat (Baru)' : 'Lihat'"></span>
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            {{-- LAPORAN REALISASI --}}
                            <td class="text-center">
                                @if($doc->catatan && $doc->catatan !== '-')
                                    <label class="btn btn-warning btn-sm text-white me-1 mb-0">
                                        <template x-if="!dokumenList[{{ $loop->index }}].isLoading">
                                            <span><i class="bi bi-pencil"></i> Ubah</span>
                                        </template>
                                        <template x-if="dokumenList[{{ $loop->index }}].isLoading">
                                            <span><span class="spinner-border spinner-border-sm"></span> Uploading...</span>
                                        </template>
                                        <input type="file" hidden
                                            @change="handleFileUpload($event, {{ $loop->index }}, {{ $doc->id }}, 'utama')">
                                    </label>
                                    <input type="hidden"
                                        :name="'dokumen[{{ $doc->id }}][path]'"
                                        x-model="dokumenList[{{ $loop->index }}].savedPath">
                                @endif
                                <a :href="dokumenList[{{ $loop->index }}]?.isUploaded ? dokumenList[{{ $loop->index }}].fileUrl : '{{ asset('storage/' . $doc->url) }}'"
                                target="_blank"
                                class="btn btn-sm"
                                :class="dokumenList[{{ $loop->index }}]?.isUploaded ? 'btn-success text-white' : 'btn-outline-primary'">
                                    <i class="bi bi-eye"></i>
                                    <span x-text="dokumenList[{{ $loop->index }}]?.isUploaded ? 'Lihat (Baru)' : 'Lihat'"></span>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Tidak ada dokumen SKP</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('redirect.role') }}" class="btn btn-secondary px-4">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary px-4 shadow" :disabled="!canSubmit()">
                    <i class="bi bi-send"></i> Kirim Pengajuan SKP
                </button>
            </div>
        </div>
    </div>
@endsection
