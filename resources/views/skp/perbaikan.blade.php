@extends('layouts.app')

@section('title', 'Detail Perbaikan Pengajuan SKP')

@section('content')
    <h3 class="fw-bold mb-4">Detail Perbaikan Pengajuan SKP</h3>

    <form action="{{ route('skp.update', $skp->id) }}"
        method="POST"
        enctype="multipart/form-data"
        x-data="skpUpload()"
        x-init="init()">

        @csrf
        @method('PUT')

        <div class="bg-white p-4 rounded-4 mb-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label small text-muted">Tanggal Pengajuan</label>
                    <input type="text" class="form-control" value="{{ $skp->tanggal_pengajuan->format('d/m/Y') }}" disabled />
                </div>
                <div class="col-md-6">
                    <label class="form-label small text-muted">Bulan SKP</label>
                    <select class="form-select">
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

        <div class="bg-white p-4 rounded-4">
            <h5 class="fw-bold mb-3">Upload Dokumen SKP</h5>

            @php
                $namaFix = [
                    0 => 'Catatan Harian Kerja',
                    1 => 'Laporan SKP 1',
                    2 => 'Laporan SKP 2',
                    3 => 'Laporan SKP 3',
                    4 => 'Laporan SKP 4',
                ];

                $dokumenUtama = $skp->dokumen
                    ->filter(fn($d) => !str_starts_with($d->nama_file, 'Aktivitas Harian eMaster'))
                    ->values();

                $dokumenAktivitas = $skp->dokumen
                    ->filter(fn($d) => str_starts_with($d->nama_file, 'Aktivitas Harian eMaster'))
                    ->values();
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
                        @php
                            $isKoreksi = str_starts_with($doc->nama_file ?? '', '[KOREKSI]');
                            $judulKegiatan = $isKoreksi ? $doc->catatan : $doc->nama_file;
                            $pesanKoreksi = $isKoreksi ? str_replace('[KOREKSI] ', '', $doc->nama_file) : '';
                            $aktivitas = $i > 0 ? ($dokumenAktivitas[$i - 1] ?? null) : null;
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            {{-- Nama Dokumen hardcode --}}
                            <td>
                                <strong>{{ $namaFix[$i] ?? 'Dokumen' }}</strong>
                                <div class="small text-muted">Dokumen PDF</div>
                            </td>

                            {{-- Kegiatan Tugas Jabatan --}}
                            <td>
                                @if($isKoreksi)
                                    <input type="text"
                                        class="form-control form-control-sm"
                                        name="judul_laporan[{{ $doc->id }}]"
                                        value="{{ $judulKegiatan }}">
                                @else
                                    <input type="text"
                                        class="form-control form-control-sm"
                                        value="{{ $judulKegiatan }}"
                                        disabled>
                                @endif
                            </td>

                            {{-- Link Bukti Dukung --}}
                            <td class="text-center">
                                @if($isKoreksi)
                                    <input type="text"
                                        class="form-control form-control-sm"
                                        name="link_pendukung[{{ $doc->id }}]"
                                        value="{{ $doc->link_pendukung }}">
                                @else
                                    @if($doc->link_pendukung && $doc->link_pendukung !== '-')
                                        <a href="{{ $doc->link_pendukung }}" target="_blank"
                                           class="btn btn-info btn-sm text-white">
                                            <i class="bi bi-file-medical"></i>
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                @endif
                            </td>

                            {{-- Keterangan Koreksi --}}
                            <td>
                                @if($isKoreksi)
                                    <textarea class="form-control form-control-sm"
                                        rows="2" disabled>{{ $pesanKoreksi }}</textarea>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>

                            {{-- Aktivitas Harian --}}
                            <td class="text-center">
                                @if($aktivitas)
                                    @if($isKoreksi)
                                        <label class="btn btn-warning btn-sm text-white me-1 mb-0">
                                            <template x-if="!dokumenList[{{ $i }}].aktivitasLoading">
                                                <span><i class="bi bi-pencil"></i> Ubah</span>
                                            </template>
                                            <template x-if="dokumenList[{{ $i }}].aktivitasLoading">
                                                <span><span class="spinner-border spinner-border-sm"></span> Uploading...</span>
                                            </template>
                                            <input type="file" hidden
                                                @change="handleFileUpload($event, {{ $i }}, {{ $aktivitas->id }}, 'aktivitas')">
                                        </label>
                                        <input type="hidden"
                                            :name="'dokumen[{{ $aktivitas->id }}][path]'"
                                            x-model="dokumenList[{{ $i }}].aktivitasPath">
                                    @endif
                                    <a :href="dokumenList[{{ $i }}]?.aktivitasUploaded ? dokumenList[{{ $i }}].aktivitasUrl : '{{ asset('storage/' . $aktivitas->url) }}'"
                                       target="_blank"
                                       class="btn btn-sm"
                                       :class="dokumenList[{{ $i }}]?.aktivitasUploaded ? 'btn-success text-white' : 'btn-outline-primary'">
                                        <i class="bi bi-eye"></i>
                                        <span x-text="dokumenList[{{ $i }}]?.aktivitasUploaded ? 'Lihat (Baru)' : 'Lihat'"></span>
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            {{-- Laporan Realisasi --}}
                            <td class="text-center">
                                @if($isKoreksi)
                                    <label class="btn btn-warning btn-sm text-white me-1 mb-0">
                                        <template x-if="!dokumenList[{{ $i }}].isLoading">
                                            <span><i class="bi bi-pencil"></i> Ubah</span>
                                        </template>
                                        <template x-if="dokumenList[{{ $i }}].isLoading">
                                            <span><span class="spinner-border spinner-border-sm"></span> Uploading...</span>
                                        </template>
                                        <input type="file" hidden
                                            @change="handleFileUpload($event, {{ $i }}, {{ $doc->id }}, 'utama')">
                                    </label>
                                    <input type="hidden"
                                        :name="'dokumen[{{ $doc->id }}][path]'"
                                        x-model="dokumenList[{{ $i }}].savedPath">
                                @endif
                                <a :href="dokumenList[{{ $i }}]?.isUploaded ? dokumenList[{{ $i }}].fileUrl : '{{ asset('storage/' . $doc->url) }}'"
                                   target="_blank"
                                   class="btn btn-sm"
                                   :class="dokumenList[{{ $i }}]?.isUploaded ? 'btn-success text-white' : 'btn-outline-primary'">
                                    <i class="bi bi-eye"></i>
                                    <span x-text="dokumenList[{{ $i }}]?.isUploaded ? 'Lihat (Baru)' : 'Lihat'"></span>
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
    </form>

@endsection
<script>
function skpUpload() {
    return {
        dokumenList: [],
        isSubmitting: false,

        init() {
            @foreach($skp->dokumen->filter(fn($d) => !str_starts_with($d->nama_file, 'Aktivitas Harian eMaster')) as $i => $doc)
                this.dokumenList[{{ $loop->index }}] = {
                    isUploaded: false,
                    isLoading: false,
                    savedPath: '',
                    fileUrl: '',
                    aktivitasUploaded: false,
                    aktivitasLoading: false,
                    aktivitasPath: '',
                    aktivitasUrl: '',
                };
            @endforeach
        },

        handleFileUpload(event, index, docId, type) {
            const file = event.target.files[0];
            if (!file) return;

            if (type === 'utama') {
                this.dokumenList[index].isLoading = true;
            } else {
                this.dokumenList[index].aktivitasLoading = true;
            }

            let formData = new FormData();
            formData.append('file', file);
            formData.append('_token', '{{ csrf_token() }}');

            fetch("{{ route('skp.uploadTemp') }}", {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (type === 'utama') {
                        this.dokumenList[index].isUploaded = true;
                        this.dokumenList[index].savedPath = data.file_path;
                        this.dokumenList[index].fileUrl = data.file_url;
                    } else {
                        this.dokumenList[index].aktivitasUploaded = true;
                        this.dokumenList[index].aktivitasPath = data.file_path;
                        this.dokumenList[index].aktivitasUrl = data.file_url;
                    }
                } else {
                    alert('Gagal upload: ' + (data.message || 'Cek file'));
                }
            })
            .catch(err => {
                console.error(err);
                alert('Terjadi kesalahan sistem.');
            })
            .finally(() => {
                if (type === 'utama') {
                    this.dokumenList[index].isLoading = false;
                } else {
                    this.dokumenList[index].aktivitasLoading = false;
                }
            });
        },

        canSubmit() {
            return true;
        }
    }
}
</script>