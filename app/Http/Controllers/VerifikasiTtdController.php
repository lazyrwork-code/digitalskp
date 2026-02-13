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
            $pdf->SetAutoPageBreak(false); // ⭐ PENTING biar text gak lompat page

            $pageCount = $pdf->setSourceFile($originalPath);

            /*
            =====================
            TEMP QR
            =====================
            */
            $tempDir = storage_path('app/public/temp_skp');
            if (!file_exists($tempDir)) mkdir($tempDir, 0755, true);

            $kepalaNama = auth()->user()->nama ?? 'Pejabat';

            /*
            =====================
            LOOP HALAMAN
            =====================
            */
            for ($i = 1; $i <= $pageCount; $i++) {

                $tpl = $pdf->importPage($i);
                $size = $pdf->getTemplateSize($tpl);

                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($tpl);

                foreach ($request->positions as $index => $pos) {

                    $targetPage = $pos['page'];

                    if ($targetPage === "last") {
                        $targetPage = $pageCount;
                    }

                    if ($targetPage == $i) {

                        /*
                        =====================
                        GENERATE QR
                        =====================
                        */
                        $qrText = "ID: $docId | TTD: $kepalaNama";

                        $qr = Builder::create()
                            ->data($qrText)
                            ->size(150)
                            ->build();

                        $qrTempPath = $tempDir . "/qr_{$docId}_{$index}.png";
                        file_put_contents($qrTempPath, $qr->getString());

                        /*
                        =====================
                        KOORDINAT
                        =====================
                        */
                        $pageWidth  = $size['width'];
                        $pageHeight = $size['height'];

                        $qrWidth  = 30; // mm
                        $qrHeight = 30; // mm

                        // posisi persen → pdf coordinate
                    $posX = $pos['x'] * $pageWidth;
                        $posY = $pos['y'] * $pageHeight;

                        // batasi biar gak keluar halaman
                        $posX = max(0, min($posX, $pageWidth - $qrWidth));
                        $posY = max(0, min($posY, $pageHeight - $qrHeight));

                        /*
                        =====================
                        TARUH QR
                        =====================
                        */
                        $pdf->Image($qrTempPath, $posX, $posY, $qrWidth, $qrHeight);

                        /*
                        =====================
                        TEXT BAWAH QR
                        =====================
                        */
                        $pdf->SetFont('Helvetica', '', 9);
                        $pdf->SetTextColor(0, 0, 0);

                        $textY = $posY + $qrHeight + 3;

                        $pdf->SetXY($posX - 5, $textY);
                        $pdf->Cell($qrWidth + 10, 4, $kepalaNama, 0, 2, 'C');

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

            Storage::put(
                'public/' . $newFileName,
                $pdf->Output('S')
            );

            /*
            =====================
            UPDATE DATABASE
            =====================
            */
            $dokumen->update([
                'url_signed' => $newFileName,
                'isttd' => true
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

public function updateStatus(Request $request, $id)
{
    $request->validate([
        'status_baru' => 'required|in:perbaikan,menungguttd,selesai',
        'koreksi' => 'nullable|array',
    ]);

    $skp = SkpPengajuan::findOrFail($id);

    if ($request->status_baru == 'perbaikan') {

        $skp->update([
            'status' => 'perbaikan'
        ]);

        if ($request->has('koreksi')) {
            foreach ($request->koreksi as $docId => $pesan) {

                if (!empty($pesan)) {
                    $dokumen = SkpDokumen::find($docId);

                    if ($dokumen) {
                        $dokumen->update([
                            'catatan' => $pesan
                        ]);
                    }
                }
            }
        }

        return redirect()->route('admin.dashboard')
            ->with('success', 'SKP dikembalikan.');
    }

    if ($request->status_baru == 'menungguttd') {

        SkpDokumen::where('skp_id', $id)->update([
            'catatan' => null
        ]);

        $skp->update([
            'status' => 'menungguttd'
        ]);

        return redirect()->route('admin.dashboard')
            ->with('success', 'SKP menunggu tanda tangan.');
    }

    if ($request->status_baru == 'selesai') {

        $belumTTD = SkpDokumen::where('skp_id', $id)
            ->where(function ($q) {
                $q->where('isttd', false)
                  ->orWhereNull('isttd');
            })
            ->exists();

        if ($belumTTD) {
            return redirect()->back()
                ->with('error', 'Masih ada dokumen belum TTD.');
        }

        $skp->update([
            'status' => 'selesai'
        ]);

        return redirect()->route('kepala.dashboard')
            ->with('success', 'SKP selesai.');
    }
}


}

