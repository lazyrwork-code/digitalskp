<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VerifikasiTtd;
use App\Models\SkpPengajuan;
use App\Models\SkpDokumen;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\Builder\Builder;

class VerifikasiTtdController extends Controller
{
    public function show($id)
    {
        // Pastikan relasi 'dokumen' sudah ada di Model SkpPengajuan
        $skp = SkpPengajuan::with(['dokumen','user'])->findOrFail($id);
        return view('kepala.review-skp', compact('skp'));
    }
    public function saveTTD(Request $request)
    {
        $doc = Dokumen::findOrFail($request->doc_id);

        $doc->qr_x = $request->pos_x;
        $doc->qr_y = $request->pos_y;

        $doc->save();

        return response()->json([
            'success' => true
        ]);
    }

    public function getTTD($docId)
    {
        $ttd = VerifikasiTtd::where('skp_id', $docId)
            ->where('ditandatangani_oleh', auth()->id())
            ->first();

        return response()->json($ttd);
    }
public function simpan(Request $request, $docId)
{
    $request->validate([
        'pos_x' => 'required|numeric',
        'pos_y' => 'required|numeric'
    ]);

    try {
        $dokumen = SkpDokumen::findOrFail($docId);
        $originalPath = storage_path('app/public/' . $dokumen->url);
        
        if (!file_exists($originalPath)) {
            return response()->json(['success' => false, 'message' => 'File tidak ditemukan']);
        }

        // 1. Generate QR Code
        $qrText = "ID: " . $docId . " | TTD: " . auth()->user()->nama;
        $qr = \Endroid\QrCode\Builder\Builder::create()
            ->data($qrText)
            ->size(150)
            ->build();

        $tempDir = storage_path('app/public/temp_skp');
        if (!file_exists($tempDir)) mkdir($tempDir, 0755, true);
        
        $qrTempPath = $tempDir . '/qr_' . $docId . '.png';
        file_put_contents($qrTempPath, $qr->getString());

        // 2. Proses PDF
        $pdf = new \setasign\Fpdi\Fpdi();
        $pageCount = $pdf->setSourceFile($originalPath);

        for ($i = 1; $i <= $pageCount; $i++) {
            $tpl = $pdf->importPage($i);
            $size = $pdf->getTemplateSize($tpl);
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($tpl);

            // 3. TEMPEL QR HANYA DI HALAMAN TERAKHIR
            if ($i == $pageCount) {
                // Konversi Pixel ke MM (Standard 96 DPI)
                $ratio = 25.4 / 96;

                // FIX MASALAH RANDOM: 
                // Kita harus 'mengurangi' koordinat Y jika user scroll ke bawah 
                // Tapi karena di frontend kita pakai getBoundingClientRect relatif terhadap container,
                // kita harus pastikan tingginya pas dengan halaman terakhir.
                
                $posX_mm = $request->pos_x * $ratio;
                $posY_mm = $request->pos_y * $ratio;

                // Ukuran QR di PDF (30mm x 30mm)
                $qrSize = 30;

                $pdf->Image($qrTempPath, $posX_mm, $posY_mm, $qrSize, $qrSize);
            }
        }

        // 4. Simpan
        $newFileName = 'ttd/skp_doc_' . $docId . '_signed.pdf';
        \Illuminate\Support\Facades\Storage::put('public/' . $newFileName, $pdf->Output('S'));

        // 5. Update Database
        $dokumen->update(['url' => $newFileName]);

        if (file_exists($qrTempPath)) unlink($qrTempPath);

        return response()->json([
            'success' => true,
            'new_url' => asset('storage/' . $newFileName) . '?v=' . time()
        ]);

    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}


}

