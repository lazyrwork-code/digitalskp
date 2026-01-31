@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <!-- TITLE -->
    <h3 class="fw-bold mb-4">Ajukan SKP Baru</h3>

    <!-- FORM HEADER -->
    <form action="{{ route('skp.store') }}" method="POST" enctype="multipart/form-data" @submit="submitForm($event)">
    @csrf
        <div class="bg-white p-4 rounded-4 mb-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label small text-muted">Tanggal Pengajuan</label>
                    <input type="date" name="tanggal_pengajuan" 
                        class="form-control @error('tanggal_pengajuan') is-invalid @enderror" 
                        value="{{ date('Y-m-d') }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label small text-muted">Bulan SKP</label>
                    <select name="bulan" class="form-select">
                        @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $m)
                            <option value="{{ $m }}" {{ date('F') == $m ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label small text-muted">Nama Pegawai</label>
                    <input type="text" class="form-control bg-light" value="{{ auth()->user()->nama }}" readonly>
                    <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label small text-muted">Tahun SKP</label>
                    <select name="tahun" class="form-select">
                        <option value="2026" {{ date('Y') == 2026 ? 'selected' : '' }}>2026</option>
                        <option value="2025" {{ date('Y') == 2025 ? 'selected' : '' }}>2025</option>
                        <option value="2024" {{ date('Y') == 2024 ? 'selected' : '' }}>2024</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label small text-muted">Unit</label>
                    <select name="unit" class="form-select">
                        <option value="Instalasi Rekam Medik">Instalasi Rekam Medik</option>
                        <option value="Registrasi Vaksin">Registrasi Vaksin</option>
                        <option value="Registrasi Rawat Jalan">Registrasi Rawat Jalan</option>
                        <option value="Registrasi Rawat Inap">Registrasi Rawat Inap</option>
                        <option value="Registrasi Rawat IGD">Registrasi Rawat IGD</option>
                        <option value="Pengembangan EMR" selected>Pengembangan EMR</option>
                        <option value="Rekam Medis">Rekam Medis</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- UPLOAD DOKUMEN -->
        <div class="bg-white p-4 rounded-4" x-data="skpUpload()">
            <h5 class="fw-bold mb-3">Upload Dokumen SKP</h5>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th width="40">No</th>
                            <th>Nama Dokumen</th>
                            <th>Judul Laporan</th>
                            <th>Link Bukti Dukung</th>
                            <th class="text-end" width="300">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(doc, index) in dokumenList" :key="index">
                            <tr>
                                <td x-text="index + 1"></td>
        <td>
            <span x-text="doc.nama"></span>
            <div class="small text-muted">Dokumen PDF</div>
            <input type="hidden" :name="'dokumen['+index+'][nama]'" :value="doc.nama">
            <input type="hidden" :name="'dokumen['+index+'][path]'" :value="doc.savedPath">
        </td>
        <td>
            <input type="text" :name="'dokumen['+index+'][judul_laporan]'" 
                   class="form-control form-control-sm" 
                   x-model="doc.judul_laporan" 
                   :placeholder="'Judul ' + doc.nama">
        </td>
        <td>
            <input type="text" :name="'dokumen['+index+'][link_bukti_dukung]'" 
                   class="form-control form-control-sm" 
                   x-model="doc.link_bukti_dukung" 
                   placeholder="Link drive...">
        </td>
                                <td class="text-end">
                                    <input type="file" accept="application/pdf" class="d-none" 
                                        :id="'file-' + index" 
                                        @change="handleFileUpload($event, index)">

                                    <template x-if="!doc.isUploaded">
                                        <label :for="'file-' + index" class="btn btn-primary btn-sm" :disabled="doc.isLoading">
                                            <template x-if="!doc.isLoading">
                                                <span><i class="bi bi-upload"></i> Unggah Dokumen</span>
                                            </template>
                                            <template x-if="doc.isLoading">
                                                <span><span class="spinner-border spinner-border-sm"></span> Tunggu...</span>
                                            </template>
                                        </label>
                                    </template>

                                    <template x-if="doc.isUploaded">
                                        <div class="d-flex justify-content-end align-items-center">
                                            <span class="text-success small me-2"><i class="bi bi-check-circle-fill"></i> Terkirim</span>
                                            <label :for="'file-' + index" class="btn btn-warning btn-sm text-white me-1">
                                                <i class="bi bi-pencil"></i> Ubah
                                            </label>
                                            <a :href="doc.fileUrl" target="_blank" class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-eye"></i> Lihat
                                            </a>
                                        </div>
                                    </template>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="text-end mt-4">
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
        isSubmitting: false, // <-- Kunci utama di sini

        init() {
            // Cek apakah ada data lama
            const savedData = localStorage.getItem('skp_form_cache');
            
            if (savedData) {
                this.dokumenList = JSON.parse(savedData);
                // Pastikan status loading mati saat halaman dimuat ulang
                this.dokumenList.forEach(doc => doc.isLoading = false);
            } else {
                this.resetList(); // Fungsi baru biar rapi
            }

            // Watcher yang pintar: Hanya simpan kalau TIDAK sedang kirim data
            this.$watch('dokumenList', (value) => {
                if (!this.isSubmitting) {
                    localStorage.setItem('skp_form_cache', JSON.stringify(value));
                }
            }, { deep: true });
        },

        resetList() {
            this.dokumenList = [
                { nama: 'Logbook SKP', isUploaded: false, isLoading: false, savedPath: '', fileUrl: '', judul_laporan: '', link_bukti_dukung: '' },
                { nama: 'Laporan SKP 1', isUploaded: false, isLoading: false, savedPath: '', fileUrl: '', judul_laporan: '', link_bukti_dukung: '' },
                { nama: 'Laporan SKP 2', isUploaded: false, isLoading: false, savedPath: '', fileUrl: '', judul_laporan: '', link_bukti_dukung: '' },
                { nama: 'Laporan SKP 3', isUploaded: false, isLoading: false, savedPath: '', fileUrl: '', judul_laporan: '', link_bukti_dukung: '' },
                { nama: 'Laporan SKP 4', isUploaded: false, isLoading: false, savedPath: '', fileUrl: '', judul_laporan: '', link_bukti_dukung: '' },
            ];
        },

        handleFileUpload(event, index) {
            const file = event.target.files[0];
            if (!file) return;

            this.dokumenList[index].isLoading = true;

            let formData = new FormData();
            formData.append('file', file);
            formData.append('_token', '{{ csrf_token() }}');

            fetch("{{ route('skp.uploadTemp') }}", {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.dokumenList[index].isUploaded = true;
                    this.dokumenList[index].savedPath = data.file_path;
                    this.dokumenList[index].fileUrl = data.file_url;
                } else {
                    alert('Gagal: ' + (data.message || 'Cek format/ukuran file'));
                }
            })
            .catch(() => alert('Upload gagal, periksa koneksi atau ukuran file.'))
            .finally(() => this.dokumenList[index].isLoading = false);
        },

        submitForm(event) {
            // 1. Kunci Watcher agar tidak menulis ke localStorage lagi
            this.isSubmitting = true; 
            
            // 2. Hapus fisik cache-nya
            localStorage.removeItem('skp_form_cache');
            
            // 3. (Opsional) Kosongkan UI agar user tahu proses sedang berjalan
            // Tapi biarkan form mengirim data asli sebelum halaman refresh
            return true; 
        },

        canSubmit() {
            // Minimal Logbook sudah terupload
            return this.dokumenList.length > 0 && this.dokumenList[0].isUploaded;
        }
    }
}
</script>
