@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
          <!-- TITLE -->
          <h3 class="fw-bold mb-4">Verifikasi SKP</h3>

          <!-- FORM HEADER -->
          <div class="bg-white p-4 rounded-4 mb-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label small text-muted">Tanggal Pengajuan</label>
                    <input type="text" class="form-control" value="{{ $skp->tanggal_pengajuan->format('d-m-Y') }}" disabled />
                </div>

                <div class="col-md-6">
                    <label class="form-label small text-muted">Bulan SKP</label>
                    <input type="text" class="form-control" value="{{ $skp->bulan }}" disabled />
                </div>

                <div class="col-md-6">
                    <label class="form-label small text-muted">Nama Pegawai</label>
                    <input type="text" class="form-control" value="{{ $skp->user->nama }}" disabled />
                </div>

                <div class="col-md-6">
                    <label class="form-label small text-muted">Tahun SKP</label>
                    <input type="text" class="form-control" value="{{ $skp->tahun }}" disabled />
                </div>

                <div class="col-md-6">
                    <label class="form-label small text-muted">Unit</label>
                    <input type="text" class="form-control" value="{{ $skp->unit ?? 'N/A' }}" disabled />
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
                    <th>Dokumen SKP</th>
                    <th>Keterangan Koreksi</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                    @foreach($skp->dokumen as $index => $doc)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            {{ $doc->nama_file }}
                            <div class="small text-muted">Dokumen PDF</div>
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm" value="{{ $doc->nama_file }}" disabled />
                        </td>
                        <td class="text-center">
                            @if ($doc->link_pendukung)
                                <a href="{{ $doc->link_pendukung }}"
                                target="_blank"
                                class="btn btn-info btn-sm text-white">
                                    <i class="bi bi-file-medical"></i>
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ asset('storage/'.$doc->url) }}" target="_blank" class="btn btn-info btn-sm text-white">
                                <i class="bi bi-file-earmark-pdf"></i>
                            </a>
                        </td>
                        <td class="text-start">
                            <div class="koreksi-wrapper">
                                <button class="btn btn-warning btn-sm koreksi-btn"><i class="bi bi-pencil"></i> Koreksi</button>
                                <div class="koreksi-edit d-none">
                                    <textarea class="form-control koreksi-text" rows="2" name="koreksi[{{ $doc->id }}]">{{ $doc->catatan_koreksi }}</textarea>
                                    <button type="button" class="btn btn-outline-danger btn-sm koreksi-cancel mt-2">Batal</button>
                                </div>
                            </div>
                        </td>
                        <td class="text-end">
                           <button 
                                class="btn btn-prima btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#modalTTD"
                                data-file="{{ asset('storage/'.$doc->url) }}"
                                data-doc="{{ $doc->id }}">
                                Tanda Tangani
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
              </table>
            </div>
          </div>
          <br />

          <!-- Keputusan Verifikasi -->
          <div class="bg-white p-4 rounded-4">
            <h5 class="fw-bold mb-4">Keputusan Verifikasi SKP</h5>

            <div class="d-flex gap-4 flex-wrap">
              <!-- BUTTON PERBAIKAN FILE -->
              <button class="btn btn-keputusan btn-perbaikan" data-bs-toggle="modal" data-bs-target="#modalKembalikan">
                <i class="bi bi-pencil-square me-2"></i>
                Kembalikan ke pegawai untuk perbaikan File
              </button>

              <!-- BUTTON SETUJUI -->
              <button class="btn btn-keputusan btn-setujui" data-bs-toggle="modal" data-bs-target="#modalSetujui">
                <i class="bi bi-file-earmark-check me-2"></i>
                Tanda Tangan SKP Digital Selesai
              </button>
            </div>
          </div>
          <br />
          <!-- ACTION -->
          <div class="text-end mt-4">
            <a class="btn btn-secondary px-4" href="dashboard-kepalarm.html"><i class="bi bi-arrow-left"></i>Kembali</a>
          </div>
<div class="modal fade" id="modalTTD" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Tanda Tangan Dokumen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div id="pdfContainer"
                     style="position:relative;width:100%;height:600px;border:1px solid #ccc">

                    <!-- PDF VIEW -->
                    <iframe id="pdfView"
                            style="width:100%;height:100%;border:none;">
                    </iframe>

                    <!-- QR DRAG -->
                    <div id="qrDrag"
                        style="position:absolute;top:100px;left:100px;cursor:move;text-align:center;background:white;padding:6px;border-radius:6px;">

                        <div id="qrCanvas"></div>

                        <div style="font-size:12px;font-weight:bold;margin-top:4px;">
                            {{ auth()->user()->nama }}
                        </div>

                        <div style="font-size:11px;">
                            {{ auth()->user()->role }}
                        </div>

                    </div>

                </div>

            </div>

            <div class="modal-footer">
                <button id="btnSimpanTTD" class="btn btn-success">
                    Simpan TTD
                </button>
            </div>

        </div>
    </div>
</div>

@endsection
<script>
document.addEventListener("DOMContentLoaded", function () {

let currentDocId = null;
let currentFile = null;
let kepalaNama = @json(auth()->user()->nama);

const modalTTD = document.getElementById('modalTTD');
const qrDrag = document.getElementById("qrDrag");
const iframe = document.getElementById("pdfView");

if (!modalTTD) return;


/* =============================
   BUKA MODAL
============================= */
modalTTD.addEventListener('show.bs.modal', function (event) {

    let button = event.relatedTarget;

    currentFile = button.getAttribute("data-file");
    currentDocId = button.getAttribute("data-doc");

});


/* =============================
   MODAL TERBUKA
============================= */
modalTTD.addEventListener('shown.bs.modal', function () {

    iframe.src = "";
    iframe.src = currentFile + "?v=" + Date.now();

    setTimeout(() => {
        generateQR();
    }, 200);
});


/* =============================
   GENERATE QR
============================= */
function generateQR() {

    let qrCanvas = document.getElementById("qrCanvas");
    qrCanvas.innerHTML = "";

    let qrText = "Dokumen ID : " + currentDocId + " | TTD : " + kepalaNama;

    new QRCode(qrCanvas, {
        text: qrText,
        width: 100,
        height: 100
    });
}


/* =============================
   DRAG QR
============================= */
let isDragging = false;
let startX, startY;
let initialLeft, initialTop;

qrDrag.addEventListener("mousedown", function(e){

    isDragging = true;

    startX = e.clientX;
    startY = e.clientY;

    initialLeft = qrDrag.offsetLeft;
    initialTop = qrDrag.offsetTop;

});

document.addEventListener("mousemove", function(e){

    if(!isDragging) return;

    let dx = e.clientX - startX;
    let dy = e.clientY - startY;

    qrDrag.style.left = (initialLeft + dx) + "px";
    qrDrag.style.top = (initialTop + dy) + "px";

});

document.addEventListener("mouseup", function(){
    isDragging = false;
});

document.getElementById("btnSimpanTTD").addEventListener("click", function() {
    const btn = this;
    btn.disabled = true;

    const container = document.getElementById("pdfContainer");
    const qr = document.getElementById("qrDrag");

    const containerRect = container.getBoundingClientRect();
    const qrRect = qr.getBoundingClientRect();

    // Posisi X relatif terhadap lebar kontainer
    let posX = qrRect.left - containerRect.left;
    
    // POSISI Y RELATIF (PENTING!)
    // Kita ambil posisi Y relatif terhadap kotak preview yang terlihat di layar (viewport modal)
    // Bukan relatif terhadap seluruh isi PDF yang di-scroll.
    let posY = qrRect.top - containerRect.top;

    // Tambahan proteksi: pastikan tidak negatif
    posX = Math.max(0, posX);
    posY = Math.max(0, posY);

    fetch(`/kepala/ttd/${currentDocId}`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            pos_x: posX,
            pos_y: posY
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert("Berhasil!");
            location.reload();
        } else {
            alert("Gagal: " + data.message);
            btn.disabled = false;
        }
    });
});

});
</script>
