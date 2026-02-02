<?php

namespace App\Http\Controllers;

use App\Models\SkpPengajuan;
use App\Models\SkpDokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        
        // Ambil input filter atau default ke bulan/tahun sekarang
        $bulanDipilih = $request->get('bulan', $bulanIndo[(int)date('m')]); 
        $tahunDipilih = $request->get('tahun', date('Y'));

        $baseQuery = SkpPengajuan::query();
        if ($user->role === 'pegawai') {
            $baseQuery->where('user_id', $user->id);
        }

        // 1. Ambil SEMUA data yang butuh tindakan (tanpa filter bulan)
        // 2. Ambil data 'selesai' HANYA sesuai bulan terpilih
        $allData = (clone $baseQuery)->with('user')
            ->where(function($q) use ($bulanDipilih, $tahunDipilih) {
                $q->whereIn('status', ['verifikasi', 'perbaikan', 'menungguttd'])
                ->orWhere(function($sq) use ($bulanDipilih, $tahunDipilih) {
                    $sq->where('status', 'selesai')
                        ->where('bulan', $bulanDipilih)
                        ->where('tahun', $tahunDipilih);
                });
            })
            ->latest()
            ->get();

        // Hitung counts berdasarkan data yang SUDAH ditarik ($allData)
        // Supaya angka di kartu SAMA dengan jumlah data di tabel
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

public function updateStatus(Request $request, $id)
{
    $request->validate([
        'status_baru' => 'required|in:perbaikan,menungguttd',
        'koreksi' => 'nullable|array',
    ]);

    // 1. Update status utama di tabel skp_pengajuan
    $skp = SkpPengajuan::findOrFail($id);
    $skp->status = $request->status_baru;
    $skp->save();

    // 2. Jika statusnya 'perbaikan', simpan alasan ke masing-masing dokumen
    if ($request->status_baru == 'perbaikan' && $request->has('koreksi')) {
        foreach ($request->koreksi as $docId => $pesan) {
            if (!empty($pesan)) {
                $dokumen = SkpDokumen::find($docId);
                if ($dokumen) {
                    $dokumen->update([
                        'catatan' => $pesan // Sudah benar pakai 'catatan'
                    ]);
                }
            }
        }
    }

    // 3. PERBAIKAN DI SINI: Ganti 'catatan_koreksi' menjadi 'catatan'
    if ($request->status_baru == 'menungguttd') {
        SkpDokumen::where('skp_id', $id)->update(['catatan' => null]);
    }

    $pesanFlash = ($request->status_baru == 'perbaikan') 
        ? 'SKP dikembalikan. Catatan koreksi telah dikirim ke tiap dokumen.' 
        : 'SKP berhasil disetujui.';

    return redirect()->route('admin.dashboard')->with('success', $pesanFlash);
}
}