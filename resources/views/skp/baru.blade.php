@extends('layouts.app')
@section('title', 'Ajukan SKP Baru')
@section('content')

<style>
.skp-card {
    background: #fff;
    border: 0.5px solid #e5e7eb;
    border-radius: 14px;
    padding: 1.5rem;
}
.skp-table thead th {
    font-size: 12px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #6b7280;
    border-bottom: 1px solid #f3f4f6;
    padding: 10px 12px;
    white-space: nowrap;
    background: #fafafa;
}
.skp-table tbody tr {
    border-bottom: 1px solid #f3f4f6;
    transition: background 0.15s;
}
.skp-table tbody tr:last-child {
    border-bottom: none;
}
.skp-table tbody tr:hover {
    background: #f9fafb;
}
.skp-table td {
    padding: 12px;
    vertical-align: middle;
    font-size: 13.5px;
}
.doc-name {
    font-weight: 500;
    color: #111827;
    font-size: 13.5px;
}
.doc-sub {
    font-size: 11.5px;
    color: #9ca3af;
    margin-top: 2px;
}
.no-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #f3f4f6;
    color: #6b7280;
    font-size: 12px;
    font-weight: 500;
}
.upload-slot {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
}
.upload-label-text {
    font-size: 10.5px;
    color: #9ca3af;
    text-align: center;
}
.btn-upload {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 10px;
    border-radius: 7px;
    font-size: 12px;
    font-weight: 500;
    border: 1px solid;
    cursor: pointer;
    transition: all 0.15s;
    white-space: nowrap;
}
.btn-upload-primary {
    background: #1d4ed8;
    color: #ffffff;
    border-color: #1d4ed8;
}
.btn-upload-primary:hover {
    background: #1e40af;
}
.btn-upload-green {
    background: #15803d;
    color: #ffffff;
    border-color: #15803d;
}
.btn-upload-green:hover {
    background: #166534;
}

.btn-upload-warning {
    background: #b45309;
    color: #ffffff;
    border-color: #b45309;
    padding: 4px 8px;
}
.btn-upload-warning:hover {
    background: #92400e;
}
.status-uploaded {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 8px;
    border-radius: 6px;
    background: #f0fdf4;
    color: #15803d;
    border: 1px solid #bbf7d0;
    font-size: 11.5px;
    font-weight: 500;
}
.status-dash {
    color: #d1d5db;
    font-size: 18px;
    display: block;
    text-align: center;
}
.action-row {
    display: flex;
    align-items: center;
    gap: 4px;
    justify-content: center;
}
.btn-icon-sm {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 26px;
    height: 26px;
    border-radius: 6px;
    border: 1px solid #e5e7eb;
    background: #fff;
    color: #6b7280;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.15s;
    text-decoration: none;
}
.btn-icon-sm:hover {
    background: #f3f4f6;
    color: #111827;
}
.form-label-custom {
    font-size: 12.5px;
    font-weight: 500;
    color: #374151;
    margin-bottom: 6px;
    display: block;
}
.form-control, .form-select {
    font-size: 13.5px;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    padding: 8px 12px;
    transition: border-color 0.15s, box-shadow 0.15s;
}
.form-control:focus, .form-select:focus {
    border-color: #1D9E75;
    box-shadow: 0 0 0 3px rgba(29,158,117,0.1);
    outline: none;
}
.section-title {
    font-size: 15px;
    font-weight: 600;
    color: #111827;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 8px;
}
.section-title::after {
    content: '';
    flex: 1;
    height: 1px;
    background: #f3f4f6;
}
.btn-submit {
    background: #1D9E75;
    color: #fff;
    border: none;
    padding: 10px 28px;
    border-radius: 9px;
    font-size: 14px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    cursor: pointer;
    transition: background 0.15s;
}
.btn-submit:hover:not(:disabled) {
    background: #178a65;
}
.btn-submit:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
.page-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 1.5rem;
}
.page-header-icon {
    width: 36px;
    height: 36px;
    border-radius: 9px;
    background: #e8f5f0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #1D9E75;
    font-size: 18px;
}
</style>

<div class="page-header">
    <div class="page-header-icon">
        <i class="bi bi-file-earmark-plus"></i>
    </div>
    <div>
        <h4 class="fw-bold mb-0" style="font-size:18px;">Ajukan SKP Baru</h4>
        <div style="font-size:12.5px; color:#6b7280;">Isi data dan upload dokumen yang diperlukan</div>
    </div>
</div>

<form action="{{ route('skp.store') }}" 
    method="POST" 
    enctype="multipart/form-data"
    x-data="skpUpload()"
    @submit="submitForm">

@csrf

    {{-- SECTION: INFO PENGAJUAN --}}
    <div class="skp-card mb-3">
        <div class="section-title">
            <i class="bi bi-info-circle text-muted" style="font-size:14px;"></i>
            Informasi Pengajuan
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label-custom">Tanggal Pengajuan</label>
                <input type="date" name="tanggal_pengajuan" 
                    class="form-control @error('tanggal_pengajuan') is-invalid @enderror" 
                    value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label-custom">Bulan SKP</label>
                <select name="bulan" class="form-select">
                    @php
                        $bulanList = ['Januari','Februari','Maret','April','Mei','Juni',
                                      'Juli','Agustus','September','Oktober','November','Desember'];
                        $bulanSekarang = $bulanList[(int)date('m') - 1];
                    @endphp
                    @foreach($bulanList as $m)
                        <option value="{{ $m }}" {{ $bulanSekarang == $m ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label-custom">Nama Pegawai</label>
                <input type="text" class="form-control" style="background:#f9fafb; color:#6b7280;" 
                    value="{{ auth()->user()->nama }}" readonly>
                <input type="hidden" name="user_id" value="{{ auth()->id() }}">
            </div>
            <div class="col-md-6">
                <label class="form-label-custom">Tahun SKP</label>
                <select name="tahun" class="form-select">
                    @foreach([2024, 2025, 2026, 2027] as $t)
                        <option value="{{ $t }}" {{ date('Y') == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label-custom">Unit</label>
                <select name="unit" class="form-select">
                    <option value="Kepala Instalasi Rekam Medik">Kepala Instalasi Rekam Medik</option>
                    <option value="Registrasi">Registrasi</option>
                    <option value="Pelayanan Rekam Medik">Pelayanan Rekam Medik</option>
                    <option value="Koding dan Grouping">Koding dan Grouping</option>
                    <option value="Filing">Filing</option>
                    <option value="Pengembangan Pelaporan dan Evaluasi" selected>Pengembangan, Pelaporan dan Evaluasi</option>
                </select>
            </div>
        </div>
    </div>

    {{-- SECTION: UPLOAD DOKUMEN --}}
    <div class="skp-card">
        <div class="section-title">
            <i class="bi bi-paperclip text-muted" style="font-size:14px;"></i>
            Upload Dokumen SKP
        </div>

        <div class="table-responsive">
            <table class="table skp-table mb-0">
                <thead>
                    <tr>
                        <th style="width:40px;">No</th>
                        <th style="min-width:160px;">Dokumen</th>
                        <th style="min-width:180px;">Kegiatan Tugas Jabatan</th>
                        <th style="min-width:180px;">Link Bukti Dukung</th>
                        <th style="width:160px; text-align:center;">Aktivitas Harian eMaster</th>
                        <th style="width:160px; text-align:center;">Laporan Realisasi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(doc, index) in dokumenList" :key="index">
                        <tr>
                            {{-- NO --}}
                            <td>
                                <span class="no-badge" x-text="index + 1"></span>
                            </td>

                            {{-- NAMA DOKUMEN + HIDDEN FIELDS --}}
                            <td>
                                <div class="doc-name" x-text="doc.nama"></div>
                                <div class="doc-sub">PDF · maks 2MB</div>
                                <input type="hidden" :name="'dokumen['+index+'][nama]'" :value="doc.nama">
                                <input type="hidden" :name="'dokumen['+index+'][tipe]'" :value="doc.tipe">
                                <input type="hidden" :name="'dokumen['+index+'][path]'" :value="doc.savedPath">
                                <template x-if="doc.hasAktivitas">
                                    <span>
                                        <input type="hidden" :name="'dokumen['+index+'][aktivitas_path]'" :value="doc.aktivitas.savedPath">
                                        <input type="hidden" :name="'dokumen['+index+'][aktivitas_tipe]'" :value="doc.aktivitas.tipe">
                                    </span>
                                </template>
                            </td>

                            {{-- JUDUL LAPORAN --}}
                            <td>
                                <input type="text" 
                                    :name="'dokumen['+index+'][judul_laporan]'" 
                                    class="form-control form-control-sm"
                                    style="font-size:12.5px;"
                                    x-model="doc.judul_laporan" 
                                    :placeholder="'Judul ' + doc.nama">
                            </td>

                            {{-- LINK BUKTI DUKUNG --}}
                            <td>
                                <div class="d-flex gap-1 align-items-center">
                                    <input type="text" 
                                        :name="'dokumen['+index+'][link_bukti_dukung]'" 
                                        class="form-control form-control-sm"
                                        style="font-size:12.5px;"
                                        x-model="doc.link_bukti_dukung" 
                                        placeholder="https://drive.google.com/...">
                                    <a :href="doc.link_bukti_dukung" target="_blank" 
                                        x-show="doc.link_bukti_dukung.trim() !== ''"
                                        class="btn-icon-sm" title="Buka link">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                </div>
                            </td>

                            {{-- UPLOAD: AKTIVITAS HARIAN --}}
                            <td>
                                {{-- Baris yang PUNYA aktivitas harian (SKP 1-4) --}}
                                <template x-if="doc.hasAktivitas">
                                    <div>
                                        <input type="file" accept="application/pdf" class="d-none" 
                                            :id="'file-aktivitas-' + index" 
                                            @change="handleFileUpload($event, index, 'aktivitas')">

                                        <div class="upload-slot">
                                            <template x-if="!doc.aktivitas.isUploaded">
                                                <label :for="'file-aktivitas-' + index" class="btn-upload btn-upload-green">
                                                    <template x-if="!doc.aktivitas.isLoading">
                                                        <span style="display:inline-flex;align-items:center;gap:5px;">
                                                            <i class="bi bi-upload"></i> Unggah
                                                        </span>
                                                    </template>
                                                    <template x-if="doc.aktivitas.isLoading">
                                                        <span style="display:inline-flex;align-items:center;gap:5px;">
                                                            <span class="spinner-border spinner-border-sm"></span> Proses...
                                                        </span>
                                                    </template>
                                                </label>
                                            </template>

                                            <template x-if="doc.aktivitas.isUploaded">
                                                <div class="action-row">
                                                    <span class="status-uploaded">
                                                        <i class="bi bi-check-circle-fill"></i>
                                                        <span>Terunggah</span>
                                                    </span>
                                                    <label :for="'file-aktivitas-' + index" class="btn-icon-sm btn-upload-warning" title="Ganti file" style="border-radius:6px;cursor:pointer;">
                                                        <i class="bi bi-arrow-repeat"></i>
                                                    </label>
                                                    <a :href="doc.aktivitas.fileUrl" target="_blank" class="btn-icon-sm" title="Lihat file">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                {{-- Baris yang TIDAK punya aktivitas harian (Catatan Harian Kerja) --}}
                                <template x-if="!doc.hasAktivitas">
                                    <span class="status-dash">—</span>
                                </template>
                            </td>

                            {{-- UPLOAD: LAPORAN REALISASI --}}
                            <td>
                                <input type="file" accept="application/pdf" class="d-none" 
                                    :id="'file-' + index" 
                                    @change="handleFileUpload($event, index, 'utama')">

                                <div class="upload-slot">
                                    {{-- Belum upload --}}
                                    <template x-if="!doc.isUploaded">
                                        <label :for="'file-' + index" class="btn-upload btn-upload-primary">
                                            <template x-if="!doc.isLoading">
                                                <span style="display:inline-flex;align-items:center;gap:5px;">
                                                    <i class="bi bi-upload"></i> Unggah
                                                </span>
                                            </template>
                                            <template x-if="doc.isLoading">
                                                <span style="display:inline-flex;align-items:center;gap:5px;">
                                                    <span class="spinner-border spinner-border-sm"></span> Proses...
                                                </span>
                                            </template>
                                        </label>
                                    </template>

                                    {{-- Sudah upload --}}
                                    <template x-if="doc.isUploaded">
                                        <div class="action-row">
                                            <span class="status-uploaded">
                                                <i class="bi bi-check-circle-fill"></i>
                                                <span>Terunggah</span>
                                            </span>
                                            <label :for="'file-' + index" class="btn-icon-sm btn-upload-warning" title="Ganti file" style="border-radius:6px;cursor:pointer;">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </label>
                                            <a :href="doc.fileUrl" target="_blank" class="btn-icon-sm" title="Lihat file">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    </template>
                                </div>
                            </td>    

                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- FOOTER: INFO + TOMBOL SUBMIT --}}
        <div class="d-flex align-items-center justify-content-between mt-4 pt-3" 
            style="border-top: 1px solid #f3f4f6;">
            <div style="font-size:12px; color:#9ca3af; display:flex; align-items:center; gap:6px;">
                <i class="bi bi-info-circle"></i>
                Minimal Catatan Harian Kerja harus terunggah sebelum mengirim
            </div>
            <button type="submit" class="btn-submit" :disabled="!canSubmit()">
                <template x-if="!isSubmitting">
                    <span style="display:inline-flex;align-items:center;gap:7px;">
                        <i class="bi bi-send"></i> Kirim Pengajuan
                    </span>
                </template>
                <template x-if="isSubmitting">
                    <span style="display:inline-flex;align-items:center;gap:7px;">
                        <span class="spinner-border spinner-border-sm"></span> Mengirim...
                    </span>
                </template>
            </button>
        </div>
    </div>

</form>

<script>
function skpUpload() {
    return {
        dokumenList: [],
        isSubmitting: false,

        init() {
            const savedData = localStorage.getItem('skp_form_cache');
            if (savedData) {
                this.dokumenList = JSON.parse(savedData);
                this.dokumenList.forEach(doc => {
                    doc.isLoading = false;
                    if (doc.hasAktivitas) doc.aktivitas.isLoading = false;
                });
            } else {
                this.resetList();
            }

            this.$watch('dokumenList', (value) => {
                if (!this.isSubmitting) {
                    localStorage.setItem('skp_form_cache', JSON.stringify(value));
                }
            }, { deep: true });
        },

        resetList() {
            const aktivitasEmpty = (tipe) => ({
                tipe: tipe,
                isUploaded: false,
                isLoading: false,
                savedPath: '',
                fileUrl: '',
            });

            this.dokumenList = [
                {
                    nama: 'Catatan Harian Kerja',
                    tipe: 'catatan_harian',
                    hasAktivitas: false,
                    isUploaded: false, isLoading: false,
                    savedPath: '', fileUrl: '',
                    judul_laporan: '', link_bukti_dukung: '',
                },
                {
                    nama: 'Laporan SKP 1',
                    tipe: 'laporan_skp_1',
                    hasAktivitas: true,
                    aktivitas: aktivitasEmpty('aktivitas_harian_1'),
                    isUploaded: false, isLoading: false,
                    savedPath: '', fileUrl: '',
                    judul_laporan: '', link_bukti_dukung: '',
                },
                {
                    nama: 'Laporan SKP 2',
                    tipe: 'laporan_skp_2',
                    hasAktivitas: true,
                    aktivitas: aktivitasEmpty('aktivitas_harian_2'),
                    isUploaded: false, isLoading: false,
                    savedPath: '', fileUrl: '',
                    judul_laporan: '', link_bukti_dukung: '',
                },
                {
                    nama: 'Laporan SKP 3',
                    tipe: 'laporan_skp_3',
                    hasAktivitas: true,
                    aktivitas: aktivitasEmpty('aktivitas_harian_3'),
                    isUploaded: false, isLoading: false,
                    savedPath: '', fileUrl: '',
                    judul_laporan: '', link_bukti_dukung: '',
                },
                {
                    nama: 'Laporan SKP 4',
                    tipe: 'laporan_skp_4',
                    hasAktivitas: true,
                    aktivitas: aktivitasEmpty('aktivitas_harian_4'),
                    isUploaded: false, isLoading: false,
                    savedPath: '', fileUrl: '',
                    judul_laporan: '', link_bukti_dukung: '',
                },
            ];
        },

        handleFileUpload(event, index, type) {
            const file = event.target.files[0];
            if (!file) return;

            if (type === 'utama') {
                this.dokumenList[index].isLoading = true;
            } else {
                this.dokumenList[index].aktivitas.isLoading = true;
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
                        this.dokumenList[index].savedPath  = data.file_path;
                        this.dokumenList[index].fileUrl    = data.file_url;
                    } else {
                        this.dokumenList[index].aktivitas.isUploaded = true;
                        this.dokumenList[index].aktivitas.savedPath  = data.file_path;
                        this.dokumenList[index].aktivitas.fileUrl    = data.file_url;
                    }
                } else {
                    alert('Gagal: ' + (data.message || 'Cek format/ukuran file'));
                }
            })
            .catch(() => alert('Upload gagal, periksa koneksi atau ukuran file.'))
            .finally(() => {
                if (type === 'utama') {
                    this.dokumenList[index].isLoading = false;
                } else {
                    this.dokumenList[index].aktivitas.isLoading = false;
                }
            });
        },

        submitForm(event) {
            this.isSubmitting = true;
            localStorage.removeItem('skp_form_cache');
        },

        canSubmit() {
            return !this.isSubmitting && 
                   this.dokumenList.length > 0 && 
                   this.dokumenList[0].isUploaded;
        }
    }
}
</script>

@endsection