@extends('layouts.app')

@section('title', 'Verifikasi SKP')

@section('content')
<div class="content">
    <h3 class="fw-bold mb-4">Verifikasi SKP</h3>

    <div class="bg-white p-4 rounded-4 mb-4">
        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label small text-muted">Tanggal Pengajuan</label>
                <input type="text" class="form-control" value="{{ $skp->created_at->format('d/m/Y') }}" disabled />
            </div>

            <div class="col-md-6">
                <label class="form-label small text-muted">Bulan SKP</label>
                <input type="text" class="form-control" value="{{ $skp->bulan }}" disabled />
            </div>

            <div class="col-md-6">
                <label class="form-label small text-muted">Nama Pegawai</label>
                <input type="text" class="form-control" value="{{ $skp->user->nama ?? 'N/A' }}" disabled />
            </div>

            <div class="col-md-6">
                <label class="form-label small text-muted">Tahun SKP</label>
                <input type="text" class="form-control" value="{{ $skp->tahun }}" disabled />
            </div>

            <div class="col-md-6">
                <label class="form-label small text-muted">Unit</label>
                <input type="text" class="form-control" value="{{ $skp->unit }}" disabled />
            </div>
        </div>
    </div>

    {{-- Form membungkus tabel dan modal agar data terkirim --}}
    <form action="{{ route('admin.skp.update-status', $skp->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="bg-white p-4 rounded-4">
            <h5 class="fw-bold mb-3">Dokumen SKP</h5>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th width="40">No</th>
                            <th>Nama Dokumen</th>
                            <th>Kegiatan Tugas Jabatan</th>
                            <th>Link Bukti Dukung</th>
                            <th>Laporan Realisasi Kegiatan</th>
                            <th>Keterangan Koreksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dokumen as $index => $doc)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $doc->kategori_dokumen ?? 'Dokumen' }}</strong>
                                <div class="small text-muted">{{ $doc->nama_file }}</div>
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm" value="{{ $doc->catatan }}" disabled />
                            </td>
                            <td class="text-center">
                                @if($doc->link_pendukung)
                                    <a href="{{ $doc->link_pendukung }}" target="_blank" class="btn btn-info btn-sm text-white">
                                        <i class="bi bi-link-45deg"></i>
                                    </a>
                                @else
                                    <span class="text-muted small">N/A</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($doc->url)
                                    <a href="{{ asset('storage/' . $doc->url) }}" target="_blank" class="btn btn-info btn-sm text-white">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                @else
                                    <span class="text-danger small">No File</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="koreksi-wrapper">
                                    <button type="button" class="btn btn-warning btn-sm koreksi-btn {{ $doc->catatan ? 'd-none' : '' }}">
                                        <i class="bi bi-pencil"></i> Koreksi
                                    </button>
                                    
                                    <div class="koreksi-edit {{ $doc->catatan ? '' : 'd-none' }}">
                                        {{-- Key array menggunakan ID dokumen --}}
                                        <textarea name="koreksi[{{ $doc->id }}]" class="form-control koreksi-text" rows="2" placeholder="Tulis alasan perbaikan...">{{ $doc->catatan }}</textarea>
                                        <button type="button" class="btn btn-outline-danger btn-sm koreksi-cancel mt-2">Batal Koreksi</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted p-4">Tidak ada dokumen yang ditemukan untuk pengajuan ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white p-4 rounded-4 mt-4">
            <h5 class="fw-bold mb-4">Keputusan Verifikasi SKP</h5>
            <div class="d-flex gap-4 flex-wrap">
                <button type="button" class="btn btn-keputusan btn-perbaikan" data-bs-toggle="modal" data-bs-target="#modalKembalikan">
                    <i class="bi bi-pencil-square me-2"></i> Kembalikan untuk perbaikan
                </button>
                <button type="button" class="btn btn-keputusan btn-setujui" data-bs-toggle="modal" data-bs-target="#modalSetujui">
                    <i class="bi bi-file-earmark-check me-2"></i> Setujui & Teruskan
                </button>
            </div>
        </div>

        <input type="hidden" name="status_baru" id="status_utama_input" value="">

        {{-- Modal Kembalikan --}}
        <div class="modal fade" id="modalKembalikan" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-4 border-0 rounded-4 text-center">
                    <i class="bi bi-exclamation-triangle text-warning mb-3" style="font-size: 64px;"></i>
                    <h5 class="fw-semibold mb-4">Kembalikan pengajuan untuk perbaikan?</h5>
                    
                    <div class="d-flex flex-column gap-3">
                        {{-- Onclick ngisi ke id 'status_utama_input' --}}
                        <button type="submit" onclick="document.getElementById('status_utama_input').value='perbaikan'" class="btn btn-warning w-100">Ya, Kembalikan</button>
                        <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Batal</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Setujui --}}
        <div class="modal fade" id="modalSetujui" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-4 border-0 rounded-4 text-center">
                    <i class="bi bi-check2-circle text-success mb-3" style="font-size: 64px;"></i>
                    <h5 class="fw-semibold mb-4">Setujui dan teruskan ke Kepala RM?</h5>
                    
                    <div class="d-flex flex-column gap-3">
                        {{-- Onclick ngisi ke id 'status_utama_input' --}}
                        <button type="submit" onclick="document.getElementById('status_utama_input').value='menungguttd'" class="btn btn-teal w-100">Ya, Setujui & Teruskan</button>
                        <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Batal</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="text-end mt-4">
        <a class="btn btn-secondary px-4" href="{{ route('admin.dashboard') }}"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>
</div>

<script>
    document.querySelectorAll('.koreksi-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            this.classList.add('d-none');
            this.nextElementSibling.classList.remove('d-none');
        });
    });
    document.querySelectorAll('.koreksi-cancel').forEach(btn => {
        btn.addEventListener('click', function() {
            const wrapper = this.closest('.koreksi-edit');
            wrapper.classList.add('d-none');
            wrapper.previousElementSibling.classList.remove('d-none');
            wrapper.querySelector('textarea').value = '';
        });
    });
</script>
@endsection