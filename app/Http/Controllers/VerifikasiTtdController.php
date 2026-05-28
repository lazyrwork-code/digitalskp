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
        $skp = SkpPengajuan::with(['dokumen', 'user'])->findOrFail($id);
        return view('kepala.review-skp', compact('skp'));
    }

    public function saveTTD(Request $request)
    {
        $doc = Dokumen::findOrFail($request->doc_id);
        $doc->qr_x = $request->pos_x;
        $doc->qr_y = $request->pos_y;
        $doc->save();

        return response()->json(['success' => true]);
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

            $pdf = new \setasign\Fpdi\Fpdi();
            $pdf->SetAutoPageBreak(false);
            $pageCount = $pdf->setSourceFile($originalPath);

            $tempDir = storage_path('app/public/temp_skp');
            if (!file_exists($tempDir)) mkdir($tempDir, 0755, true);

            $kepala     = auth()->user();
            $kepalaNama = $kepala->nama ?? 'Pejabat';
            $kepalaNip  = $kepala->nip  ?? '197501012000011003';

            for ($i = 1; $i <= $pageCount; $i++) {
                $tpl  = $pdf->importPage($i);
                $size = $pdf->getTemplateSize($tpl);

                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($tpl);

                foreach ($request->positions as $index => $pos) {
                    $targetPage = $pos['page'];
                    if ($targetPage === "last") {
                        $targetPage = $pageCount;
                    }
                    if ($targetPage != $i) continue;

                    $verifyId  = $request->verification_id ?? ('VER' . $docId);
                    $timestamp = now()->setTimezone('Asia/Jakarta')->locale('id')->translatedFormat('d F Y H:i');
                    $qrText    = "ID: {$verifyId} | Tgl: {$timestamp} | TTD: {$kepalaNama}";

                    $qr = Builder::create()
                        ->data($qrText)
                        ->size(150)
                        ->build();

                    $qrTempPath = $tempDir . "/qr_{$docId}_{$index}.png";
                    file_put_contents($qrTempPath, $qr->getString());

                    $stampWidth  = 45;
                    $qrSize      = 25;
                    $lineHeight  = 4;
                    $pageWidth   = $size['width'];
                    $pageHeight  = $size['height'];

                    $posX = $pos['x'] * $pageWidth;
                    $posY = $pos['y'] * $pageHeight;

                    $stampHeight = 10 + $qrSize + $lineHeight + $lineHeight;
                    $posX = max(0, min($posX, $pageWidth  - $stampWidth));
                    $posY = max(0, min($posY, $pageHeight - $stampHeight));

                    $pdf->SetFont('Helvetica', '', 7);
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->SetXY($posX, $posY);
                    $pdf->Cell($stampWidth, $lineHeight, 'Mengetahui', 0, 2, 'C');

                    $pdf->SetFont('Helvetica', 'B', 8);
                    $pdf->SetX($posX);
                    $pdf->Cell($stampWidth, $lineHeight, 'Atasan Langsung', 0, 2, 'C');

                    $pdf->SetFont('Helvetica', '', 7);
                    $pdf->SetX($posX);
                    $pdf->Cell($stampWidth, $lineHeight, 'Kepala Instalasi Rekam Medis', 0, 2, 'C');

                    $qrX = $posX + ($stampWidth - $qrSize) / 2;
                    $qrY = $pdf->GetY() + 1;
                    $pdf->Image($qrTempPath, $qrX, $qrY, $qrSize, $qrSize);

                    $pdf->SetFont('Helvetica', 'B', 8);
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->SetXY($posX, $qrY + $qrSize + 2);
                    $pdf->Cell($stampWidth, $lineHeight, $kepalaNama, 0, 2, 'C');

                    $pdf->SetFont('Helvetica', '', 7);
                    $pdf->SetTextColor(68, 68, 68);
                    $pdf->SetX($posX);
                    $pdf->Cell($stampWidth, $lineHeight, 'NIP. ' . $kepalaNip, 0, 2, 'C');

                    if (file_exists($qrTempPath)) unlink($qrTempPath);
                }
            }

            $newFileName = 'ttd/skp_doc_' . $docId . '_signed.pdf';
            Storage::put('public/' . $newFileName, $pdf->Output('S'));

            $dokumen->update([
                'url_signed' => $newFileName,
                'isttd'      => true
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
            'koreksi'     => 'nullable|array',
        ]);

        $skp = SkpPengajuan::findOrFail($id);

        if ($request->status_baru == 'perbaikan') {
            $skp->update(['status' => 'perbaikan']);

            if ($request->has('koreksi')) {
                foreach ($request->koreksi as $docId => $pesan) {
                    if (!empty($pesan)) {
                        $dokumen = SkpDokumen::find($docId);
                        if ($dokumen) {
                            $judulAsli = str_starts_with($dokumen->nama_file ?? '', '[KOREKSI]')
                                ? $dokumen->catatan
                                : $dokumen->nama_file;

                            $dokumen->update([
                                'nama_file' => '[KOREKSI] ' . $pesan,
                                'catatan'   => $judulAsli,
                            ]);

                            // Update aktivitas harian pasangannya
                            $allDokumen = SkpDokumen::where('skp_id', $id)->get();
                            $utamaList = $allDokumen->filter(fn($d) => $d->catatan !== 'aktivitas_harian')->values();
                            $aktivitasList = $allDokumen->filter(fn($d) => $d->catatan === 'aktivitas_harian')->values();

                            $utamaIndex = $utamaList->search(fn($d) => $d->id === $dokumen->id);
                            if ($utamaIndex > 0) {
                                $aktivitas = $aktivitasList[$utamaIndex - 1] ?? null;
                                if ($aktivitas) {
                                    $judulAsliAkt = str_starts_with($aktivitas->nama_file ?? '', '[KOREKSI]')
                                        ? $aktivitas->catatan
                                        : $aktivitas->nama_file;

                                    $aktivitas->update([
                                        'nama_file' => '[KOREKSI] ' . $pesan,
                                        'catatan'   => $judulAsliAkt,
                                    ]);
                                }
                            }
                        }
                    }
                }
            }

            return redirect()->route('kepala.dashboard')
                ->with('success', 'SKP dikembalikan untuk perbaikan.');
        }

        if ($request->status_baru == 'menungguttd') {
            SkpDokumen::where('skp_id', $id)
                ->get()
                ->each(function($dok) {
                    if (str_starts_with($dok->nama_file ?? '', '[KOREKSI]')) {
                        $dok->update([
                            'nama_file' => $dok->catatan,
                            'catatan'   => 'utama',
                        ]);
                    }
                });

            $skp->update(['status' => 'menungguttd']);

            return redirect()->route('kepala.dashboard')
                ->with('success', 'SKP menunggu tanda tangan.');
        }

        if ($request->status_baru == 'selesai') {
            $belumTTD = SkpDokumen::where('skp_id', $id)
                ->where(function($q) {
                    $q->where('isttd', false)
                      ->orWhereNull('isttd');
                })
                ->exists();

            if ($belumTTD) {
                return redirect()->back()
                    ->with('error', 'Masih ada dokumen belum TTD.');
            }

            $skp->update(['status' => 'selesai']);

            return redirect()->route('kepala.dashboard')
                ->with('success', 'SKP selesai.');
        }
    }
}