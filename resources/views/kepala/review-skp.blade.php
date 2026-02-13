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
        <form action="{{ route('kepala.skp.update-status', $skp->id) }}" method="POST">
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
                                <button type="button". class="btn btn-warning btn-sm koreksi-btn"><i class="bi bi-pencil"></i> Koreksi</button>
                                <div class="koreksi-edit d-none">
                                    <textarea class="form-control koreksi-text" rows="2" name="koreksi[{{ $doc->id }}]">{{ $doc->catatan_koreksi }}</textarea>
                                    <button type="button" class="btn btn-outline-danger btn-sm koreksi-cancel mt-2">Batal</button>
                                </div>
                            </div>
                        </td>
                        <td class="text-end">
                            @if(!$doc->isttd)
                                <button 
                                    type="button"
                                    class="btn btn-prima btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalTTD"
                                    data-file="{{ asset('storage/'.$doc->url) }}"
                                    data-doc="{{ $doc->id }}">
                                    Tanda Tangani
                                </button>
                            @else
                                <a 
                                    href="{{ asset('storage/'.$doc->url_signed) }}" 
                                    target="_blank"
                                    class="btn btn-success btn-sm">
                                    Lihat Dokumen
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
              </table>
            </div>
          </div>
          <br />

          <div class="bg-white p-4 rounded-4">
            <h5 class="fw-bold mb-4">Keputusan Verifikasi SKP</h5>

            <div class="d-flex gap-4 flex-wrap">
              <button type="button" class="btn btn-keputusan btn-perbaikan" data-bs-toggle="modal" data-bs-target="#modalKembalikan">
                <i class="bi bi-pencil-square me-2"></i>
                Kembalikan ke pegawai untuk perbaikan File
              </button>

              <button type="button" class="btn btn-keputusan btn-setujui" data-bs-toggle="modal" data-bs-target="#modalSetujui">
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
                        <h5 class="fw-semibold mb-4">Apakah anda yakin Setujui dan teruskan berkas pengajuan SKP ke Kepala Rekam Medis ?</h5>
                        
                        <div class="d-flex flex-column gap-3">
                            {{-- Onclick ngisi ke id 'status_utama_input' --}}
                            <button type="submit" onclick="document.getElementById('status_utama_input').value='selesai'" class="btn btn-teal w-100">Ya, Setujui & Teruskan</button>
                            <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Batal</button>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Modal TTD --}}
            <div class="modal fade" id="modalTTD" tabindex="-1">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title">Tanda Tangan Dokumen</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                    <div class="modal-body">
                            <div id="pdfViewer"
                                style="position:relative;width:100%;height:600px;border:1px solid #ccc;overflow:auto;">
                                <div id="pdfPages"></div>
                            </div>
                            <div id="qrDrag"
                                style="position:absolute;top:100px;left:100px;cursor:move;text-align:center;background:white;padding:6px;border-radius:6px;z-index:10;">
                                <div id="qrCanvas"></div>
                                <div style="font-size:12px;font-weight:bold;margin-top:4px;">
                                    {{ auth()->user()->nama }}
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
        </form>
@endsection
<style>
#pdfViewer {
    height: 600px;
    overflow-y: auto;
    position: relative;
}

#pdfPages {
    position: relative;
}

.pdf-page {
    position: relative;
    margin: 0 auto 20px;
}

#qrDrag {
    position: absolute;
    cursor: move;
    user-select: none;
    touch-action: none;
    z-index: 9999;
}
</style>
<script>
document.addEventListener("DOMContentLoaded", function () {

    let currentDocId = null;
    let currentFile = null;
    let kepalaNama = @json(auth()->user()->nama);

    const modalTTD = document.getElementById('modalTTD');
    const qrDrag = document.getElementById("qrDrag");
    const pdfPages = document.getElementById("pdfPages");

    pdfjsLib.GlobalWorkerOptions.workerSrc = "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js";

    modalTTD.addEventListener('show.bs.modal', function (event) {

        let button = event.relatedTarget;
        currentFile = button.getAttribute("data-file");
        currentDocId = button.getAttribute("data-doc");

    });

    modalTTD.addEventListener('shown.bs.modal', function () {

        loadPDF(currentFile);

        setTimeout(() => {
            generateQR();
            placeQRToLastPage();
        }, 300);

    });

    async function loadPDF(url) {

        pdfPages.innerHTML = "";

        const pdf = await pdfjsLib.getDocument(url).promise;

        for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {

            const page = await pdf.getPage(pageNum);

            let scale = 1.3;
            let viewport = page.getViewport({ scale });

            /* wrapper halaman */
            let pageWrapper = document.createElement("div");
            pageWrapper.classList.add("pdf-page");
            pageWrapper.dataset.page = pageNum;
            pageWrapper.style.width = viewport.width + "px";

            let canvas = document.createElement("canvas");
            let ctx = canvas.getContext("2d");

            canvas.width = viewport.width;
            canvas.height = viewport.height;

            pageWrapper.appendChild(canvas);
            pdfPages.appendChild(pageWrapper);

            await page.render({
                canvasContext: ctx,
                viewport: viewport
            }).promise;

        }

    }

    function placeQRToLastPage(){

        const pages = document.querySelectorAll(".pdf-page");
        if(pages.length === 0) return;

        const lastPage = pages[pages.length - 1];

        lastPage.appendChild(qrDrag);
        qrDrag.style.left = "100px";
        qrDrag.style.top = "100px";
    }

    function generateQR(){

        let qrCanvas = document.getElementById("qrCanvas");
        qrCanvas.innerHTML = "";

        new QRCode(qrCanvas,{
            text: "Dokumen ID : "+currentDocId+" | TTD : "+kepalaNama,
            width:100,
            height:100
        });
    }

    function moveQRToPage(){

        const qrRect = qrDrag.getBoundingClientRect();
        const pages = document.querySelectorAll(".pdf-page");
        for(let page of pages){
            const rect = page.getBoundingClientRect();
            if(qrRect.top >= rect.top && qrRect.top <= rect.bottom){
                if(qrDrag.parentElement !== page){
                    page.appendChild(qrDrag);
                    qrDrag.style.left = qrRect.left - rect.left + "px";
                    qrDrag.style.top = qrRect.top - rect.top + "px";
                }
                return;
            }
        }
    }

    let offsetX = 0;
    let offsetY = 0;
    let isDragging = false;
    qrDrag.addEventListener("pointerdown",(e)=>{
        e.preventDefault();
        isDragging = true;
        qrDrag.setPointerCapture(e.pointerId);
        const rect = qrDrag.getBoundingClientRect();
        offsetX = e.clientX - rect.left;
        offsetY = e.clientY - rect.top;
    });
    qrDrag.addEventListener("pointermove",(e)=>{

        if(!isDragging || !qrDrag.hasPointerCapture(e.pointerId)) return;

        const parentRect = qrDrag.parentElement.getBoundingClientRect();

        let newX = e.clientX - parentRect.left - offsetX;
        let newY = e.clientY - parentRect.top - offsetY;

        let maxX = parentRect.width - qrDrag.clientWidth;
        let maxY = parentRect.height - qrDrag.clientHeight;

        newX = Math.max(0,Math.min(newX,maxX));
        newY = Math.max(0,Math.min(newY,maxY));

        qrDrag.style.left = newX+"px";
        qrDrag.style.top = newY+"px";

        moveQRToPage();

    });
    function stopDrag(e){
        if(qrDrag.hasPointerCapture(e.pointerId)){
            qrDrag.releasePointerCapture(e.pointerId);
        }
        isDragging = false;
    }
    qrDrag.addEventListener("pointerup",stopDrag);
    qrDrag.addEventListener("pointercancel",stopDrag);

    document.getElementById("btnSimpanTTD").addEventListener("click",function(){

        const page = qrDrag.parentElement;
        const pageNumber = parseInt(page.dataset.page);

        const posX = qrDrag.offsetLeft / page.clientWidth;
        const posY = qrDrag.offsetTop  / page.clientHeight;

        fetch(`/kepala/ttd/${currentDocId}`,{
            method:"POST",
            headers:{
                "Content-Type":"application/json",
                "X-CSRF-TOKEN":"{{ csrf_token() }}"
            },
            body: JSON.stringify({
                positions:[{
                    page: pageNumber,
                    x: posX,
                    y: posY
                }]
            })
        })
        .then(r=>r.json())
        .then(res=>{
            if(res.success){
                alert("Berhasil");
                location.reload();
            }
        });

    });
});
</script>