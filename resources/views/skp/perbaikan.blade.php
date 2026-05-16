@extends('layouts.app')

@section('title', 'Detail Perbaikan Pengajuan SKP')

@section('content')
    <!-- TITLE -->
    <h3 class="fw-bold mb-4">Detail Perbaikan Pengajuan SKP</h3>

    <!-- FORM HEADER -->
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
                <select class="form-select" >
                    <option selected>{{ $skp->bulan }}</option>
                </select>
                </div>

                <div class="col-md-6">
                <label class="form-label small text-muted">Nama Pegawai</label>
                <input type="text" class="form-control" value="{{ $skp->user->nama }}" disabled />
                </div>

                <div class="col-md-6">
                <label class="form-label small text-muted">Tahun SKP</label>
                <select class="form-select" >
                    <option selected>{{ $skp->tahun }}</option>
                </select>
                </div>

                <div class="col-md-6">
                <label class="form-label small text-muted">Unit</label>
                <select class="form-select" >
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
                            <th>Kegiatan Tugas Jabatan</th>
                            <th>Link Bukti Dukung</th>
                            <th>Keterangan Koreksi</th>
                            <th class="text-end">Laporan Realisasi Kegiatan</th>
                        </tr>
                    </thead>
                    <tbody>
                            @forelse($skp->dokumen as $i => $doc)
                                <tr>
                                    <td>{{ $i + 1 }}</td>

                                    <td>
                                        {{ $doc->nama_dokumen }}
                                        <div class="small text-muted">Dokumen PDF</div>
                                    </td>

                                    {{-- JUDUL LAPORAN --}}
                                    <td >
                                        <input type="text"
                                            class="form-control form-control-sm"
                                            name="judul_laporan[{{ $doc->id }}]"
                                            value="{{ $doc->nama_file }}"
                                            {{ $doc->catatan !== null ? '' : 'disabled' }}>
                                    </td>

                                    {{-- LINK BUKTI --}}
                                    <td class="text-center">
                                        @if($doc->catatan !== null)
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
                                        @if($doc->catatan !== null)
                                            <textarea class="form-control form-control-sm"
                                                rows="2"
                                                disabled>{{ $doc->catatan ?? '-' }}</textarea>
                                        @else
                                        @endif
                                    </td>

                                    {{-- AKSI --}}
                                    <td class="text-end">

                                        @if(!empty($doc->catatan))
                                            <label class="btn btn-warning btn-sm text-white me-1 mb-0">
                                               <template x-if="!dokumenList[{{ $i }}].isLoading">
                                                    <span><i class="bi bi-pencil"></i> Ubah</span>
                                                </template>

                                                <template x-if="dokumenList[{{ $i }}].isLoading">
                                                    <span><span class="spinner-border spinner-border-sm"></span> Uploading...</span>
                                                </template>

                                            <input type="file"
                                                    hidden
                                                    @change="handleFileUpload($event, {{ $i }}, {{ $doc->id }})">

                                            </label>
                                            <input type="hidden" 
                                                :name="'dokumen[' + {{ $doc->id }} + '][path]'" 
                                                x-model="dokumenList[{{ $i }}].savedPath">

                                        @endif

                                       @if($doc->url || !empty($doc->catatan)) {{-- Tetap muncul jika ada file lama ATAU ada ruang untuk upload baru --}}
                                            <a :href="dokumenList[{{ $i }}]?.isUploaded ? dokumenList[{{ $i }}].fileUrl : '{{ asset('storage/' . $doc->url) }}'" 
                                            target="_blank" 
                                            class="btn btn-sm"
                                            :class="dokumenList[{{ $i }}]?.isUploaded ? 'btn-success text-white' : 'btn-outline-primary'"
                                            x-show="dokumenList[{{ $i }}]?.isUploaded || '{{ $doc->url }}'"
                                            @click="if(!$el.getAttribute('href') || $el.getAttribute('href') == '#') $event.preventDefault()">
                                                <i class="bi bi-eye"></i> 
                                                <span x-text="dokumenList[{{ $i }}]?.isUploaded ? 'Lihat (Baru)' : 'Lihat'"></span>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Tidak ada dokumen SKP
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>

            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('redirect.role') }}" class="btn btn-secondary px-4"> <i class="bi bi-arrow-left"></i> Kembali </a>

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
            @foreach($skp->dokumen as $i => $doc)
                this.dokumenList[{{ $i }}] = { 
                    isUploaded: false, 
                    isLoading: false, 
                    savedPath: '',
                    fileUrl: '' // <-- Tambahkan ini agar tidak undefined
                };
            @endforeach
        },

        handleFileUpload(event, index, docId) {
            const file = event.target.files[0];
            if (!file) return;

            this.dokumenList[index].isLoading = true;

            let formData = new FormData();
            formData.append('file', file);
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('doc_id', docId);

            fetch("{{ route('skp.uploadTemp') }}", {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.dokumenList[index].isUploaded = true;
                    this.dokumenList[index].savedPath = data.file_path;
                    this.dokumenList[index].fileUrl = data.file_url; // <-- WAJIB: Ambil URL dari response Controller
                    console.log('Upload berhasil:', data.file_path);
                } else {
                    alert('Gagal upload: ' + (data.message || 'Cek file'));
                }
            })
            .catch(err => {
                console.error(err);
                alert('Terjadi kesalahan sistem.');
            })
            .finally(() => {
                this.dokumenList[index].isLoading = false;
            });
        },

        canSubmit() {
            return true; 
        }
    }
}
</script>
