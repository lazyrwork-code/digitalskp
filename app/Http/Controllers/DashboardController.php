<?php

namespace App\Http\Controllers;

use App\Models\SkpPengajuan;
use App\Models\SkpDokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Setasign\Fpdi\Fpdi;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $bulanIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        $bulanDipilih = $request->get('bulan', $bulanIndo[(int)date('m')]); 
        $tahunDipilih = $request->get('tahun', date('Y'));

        $baseQuery = SkpPengajuan::query();
        if ($user->role === 'pegawai') {
            $baseQuery->where('user_id', $user->id);
        }

        $allData = (clone $baseQuery)->with('user')
            ->whereIn('status', ['verifikasi', 'perbaikan', 'menungguttd', 'selesai'])
            ->where('tahun', $tahunDipilih)
            ->whereRaw('TRIM(bulan) = ?', [$bulanDipilih])
            ->when($user->role === 'pegawai', fn($q) => $q->latest(), fn($q) => $q->oldest())
            ->get();
        
        $counts = [
            'verifikasi' => $allData->where('status', 'verifikasi')->count(),
            'perbaikan'  => $allData->where('status', 'perbaikan')->count(),
            'ttd'        => $allData->where('status', 'menungguttd')->count(),
            'selesai'    => $allData->where('status', 'selesai')->count(),
        ];

        $view = $user->role === 'admin' ? 'admin.dashboard' : ($user->role === 'kepala' ? 'kepala.dashboard' : 'dashboard');

        return view($view, compact('counts', 'allData', 'bulanDipilih', 'tahunDipilih', 'bulanIndo'));
    }

    public function show($id)
    {
        $skp = SkpPengajuan::with('user')->findOrFail($id);
        $dokumen = SkpDokumen::where('skp_id', $id)->get();
        return view('admin.verifikasi-skp', compact('skp', 'dokumen'));
    }

    public function showfinish($id)
    {
        $skp = SkpPengajuan::with('user')->findOrFail($id);
        $dokumen = SkpDokumen::where('skp_id', $id)->get();
        return view('skp.showfinal', compact('skp', 'dokumen'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_baru' => 'required|in:perbaikan,menungguttd',
            'koreksi'     => 'nullable|array',
        ]);

        $skp = SkpPengajuan::findOrFail($id);
        $skp->status = $request->status_baru;
        $skp->save();

        if ($request->status_baru == 'perbaikan' && $request->has('koreksi')) {
            foreach ($request->koreksi as $docId => $pesan) {
                if (!empty($pesan)) {
                    $dokumen = SkpDokumen::find($docId);
                    if ($dokumen) {
                        // Simpan judul asli ke catatan sebelum ditimpa koreksi
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

        if ($request->status_baru == 'menungguttd') {
            SkpDokumen::where('skp_id', $id)
                ->get()
                ->each(function($dok) {
                    if (str_starts_with($dok->nama_file ?? '', '[KOREKSI]')) {
                        $dok->update([
                            'nama_file' => $dok->catatan, // restore judul asli
                            'catatan'   => 'utama',
                        ]);
                    }
                });
        }

        $pesanFlash = ($request->status_baru == 'perbaikan')
            ? 'SKP dikembalikan. Catatan koreksi telah dikirim.'
            : 'SKP berhasil disetujui.';

        return redirect()->route('admin.dashboard')->with('success', $pesanFlash);
    }

    public function exportPdf(Request $request)
    {
        $bulan  = $request->query('bulan', now()->locale('id')->monthName);
        $tahun  = $request->query('tahun', date('Y'));
        $role   = strtolower(auth()->user()->role);

        $data = SkpPengajuan::with('user')
            ->where('status', 'selesai')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->latest()
            ->get();

        // $html = View::make('exports.skp-selesai-pdf', compact('data', 'bulan', 'tahun'))->render();

        $pdf = new \setasign\Fpdi\Fpdi();
        $pdf->AddPage('L');
        $pdf->SetFont('Helvetica', 'B', 14);
        $pdf->SetFillColor(29, 158, 117);

        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 12, 'REKAP SKP SELESAI - RSUD DR. SOETOMO', 0, 1, 'C', true);
        $pdf->SetFont('Helvetica', '', 10);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(0, 7, 'Periode: ' . $bulan . ' ' . $tahun, 0, 1, 'C');
        $pdf->Ln(4);

        $pdf->SetFont('Helvetica', 'B', 9);
        $pdf->SetFillColor(243, 244, 246);
        $pdf->SetTextColor(55, 65, 81);
        $pdf->SetDrawColor(229, 231, 235);
        $pdf->SetLineWidth(0.3);

        $pdf->Cell(10,  9, 'No',              1, 0, 'C', true);
        $pdf->Cell(65,  9, 'Nama Pegawai',    1, 0, 'C', true);
        $pdf->Cell(60,  9, 'Unit',            1, 0, 'C', true);
        $pdf->Cell(30,  9, 'Bulan',           1, 0, 'C', true);
        $pdf->Cell(20,  9, 'Tahun',           1, 0, 'C', true);
        $pdf->Cell(45,  9, 'Tanggal Selesai', 1, 0, 'C', true);
        $pdf->Cell(37,  9, 'Status',          1, 1, 'C', true);

        $pdf->SetFont('Helvetica', '', 9);
        $pdf->SetTextColor(17, 24, 39);
        $no = 1;
        foreach ($data as $item) {
            $fill = ($no % 2 === 0);
            $pdf->SetFillColor(249, 250, 251);

            $pdf->Cell(10,  8, $no,                                                              1, 0, 'C', $fill);
            $pdf->Cell(65,  8, $item->user->nama ?? '-',                                         1, 0, 'L', $fill);
            $pdf->Cell(60,  8, $item->unit ?? '-',                                               1, 0, 'L', $fill);
            $pdf->Cell(30,  8, $item->bulan,                                                     1, 0, 'C', $fill);
            $pdf->Cell(20,  8, $item->tahun,                                                     1, 0, 'C', $fill);
            $pdf->Cell(45,  8, \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d-m-Y'), 1, 0, 'C', $fill);
            $pdf->Cell(37,  8, 'Selesai',                                                        1, 1, 'C', $fill);
            $no++;
        }

        $pdf->Ln(6);
        $pdf->SetFont('Helvetica', 'I', 8);
        $pdf->SetTextColor(156, 163, 175);
        $pdf->Cell(0, 6, 'Dicetak oleh: ' . auth()->user()->nama . ' | ' . now()->format('d-m-Y H:i'), 0, 0, 'R');

        $filename = 'rekap-skp-selesai-' . $bulan . '-' . $tahun . '.pdf';
        $pdf->Output('D', $filename);
        exit;
    }
}