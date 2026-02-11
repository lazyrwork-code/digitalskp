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
        'positions' => 'required|array'
    ]);

    try {

        $dokumen = SkpDokumen::findOrFail($docId);

        $originalPath = storage_path('app/public/' . $dokumen->url);

        if (!file_exists($originalPath)) {
            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan'
            ]);
        }

        /*
        =====================
        LOAD PDF
        =====================
        */
        $pdf = new \setasign\Fpdi\Fpdi();
        $pageCount = $pdf->setSourceFile($originalPath);

        /*
        =====================
        SIAPKAN TEMP QR
        =====================
        */
        $tempDir = storage_path('app/public/temp_skp');
        if (!file_exists($tempDir)) mkdir($tempDir, 0755, true);

        /*
        =====================
        LOOP HALAMAN PDF
        =====================
        */
        for ($i = 1; $i <= $pageCount; $i++) {

            $tpl = $pdf->importPage($i);
            $size = $pdf->getTemplateSize($tpl);

            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($tpl);

            /*
            =====================
            CEK SEMUA POSISI QR
            =====================
            */
            foreach ($request->positions as $index => $pos) {

                $targetPage = $pos['page'];

                // Support "last"
                if ($targetPage === "last") {
                    $targetPage = $pageCount;
                }

                if ($targetPage == $i) {

                    /*
                    =====================
                    GENERATE QR
                    =====================
                    */
                    $qrText = "ID: " . $docId . " | TTD: " . auth()->user()->nama;

                    $qr = \Endroid\QrCode\Builder\Builder::create()
                        ->data($qrText)
                        ->size(150)
                        ->build();

                    $qrTempPath = $tempDir . "/qr_{$docId}_{$index}.png";

                    file_put_contents($qrTempPath, $qr->getString());

                    /*
                    =====================
                    KONVERSI PERSEN KE PDF COORDINATE
                    =====================
                    */
                    $pageWidth = $size['width'];
                    $pageHeight = $size['height'];

                    $qrWidth = 30; // mm
                    $qrHeight = 30; // mm

                    $posX = $pos['x'] * $pageWidth;
                    $posY = $pos['y'] * $pageHeight;

                    /*
                    =====================
                    TARUH QR
                    =====================
                    */
                    $pdf->Image($qrTempPath, $posX, $posY, $qrWidth, $qrHeight);

                    if (file_exists($qrTempPath)) unlink($qrTempPath);
                }
            }
        }

        /*
        =====================
        SIMPAN FILE SIGNED
        =====================
        */
        $newFileName = 'ttd/skp_doc_' . $docId . '_signed.pdf';

        \Storage::put(
            'public/' . $newFileName,
            $pdf->Output('S')
        );

        /*
        =====================
        UPDATE DATABASE
        =====================
        */
        $dokumen->update([
            'signed_url' => $newFileName
        ]);

        return response()->json([
            'success' => true,
            'new_url' => asset('storage/' . $newFileName)
        ]);

    } catch (\Exception $e) {

        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}


}

