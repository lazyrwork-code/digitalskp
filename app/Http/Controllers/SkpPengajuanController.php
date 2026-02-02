<?php

namespace App\Http\Controllers;

use App\Models\SkpPengajuan;
use App\Models\SkpDokumen; // WAJIB IMPORT INI
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;      // WAJIB IMPORT INI
use Illuminate\Support\Facades\Storage; // WAJIB IMPORT INI

class SkpPengajuanController extends Controller
{
    // LIST (pegawai lihat punya sendiri, admin/kepala lihat semua)
    public function index(Request $request)
    {
        // Ambil tahun dari tombol yang diklik, default ke tahun saat ini
        $tahunDipilih = $request->query('tahun', date('Y'));

        $query = SkpPengajuan::with('user')
                    ->where('tahun', $tahunDipilih)
                    ->where('status', 'selesai');

        if (auth()->user()->role === 'pegawai') {
            $query->where('user_id', auth()->id());
        }

        $data = $query->latest()->get();

        // Kirim data dan tahunDipilih ke view
        return view('skp.riwayat', compact('data', 'tahunDipilih'));
    }

    // FORM BARU
    public function create()
    {
        return view('skp.baru');
    }

    // SIMPAN PENGAJUAN SEMENTARA (AJAX)
    public function uploadTemp(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf|max:2048', 
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            // Simpan di storage/app/public/temp_skp
            $path = $file->store('temp_skp', 'public');
            
            return response()->json([
                'success' => true,
                'file_path' => $path,
                'file_url' => asset('storage/' . $path)
            ]);
        }

        return response()->json(['success' => false], 400);
    }

    // SIMPAN FINAL KE DATABASE
    public function store(Request $request)
    {
        // Hapus dd() jika ada
        
        $request->validate([
            'tanggal_pengajuan' => 'required|date',
            'bulan'             => 'required',
            'tahun'             => 'required',
            'unit'              => 'required',
            'dokumen'           => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            // AMBIL DATA DARI DOKUMEN PERTAMA UNTUK HEADER
            // Kita pastikan ada isinya, kalau kosong kasih string "Tanpa Judul"
            $judulHeader = $request->dokumen[0]['judul_laporan'] ?? 'Tanpa Judul';
            $linkHeader  = $request->dokumen[0]['link_bukti_dukung'] ?? '-';
            $pdfUtama    = isset($request->dokumen[0]['path']) ? 'skp_files/' . basename($request->dokumen[0]['path']) : null;

            // GUNAKAN METODE MANUAL (TIDAK PAKAI CREATE) UNTUK BYPASS FILLABLE JIKA PERLU
            $skp = new \App\Models\SkpPengajuan();
            $skp->user_id = auth()->id();
            $skp->unit = $request->unit;
            $skp->bulan = $request->bulan;
            $skp->tahun = $request->tahun;
            $skp->tanggal_pengajuan = $request->tanggal_pengajuan;
            $skp->status = 'verifikasi';
            $skp->judul_laporan = $judulHeader; // PASTIKAN NAMA KOLOM DI DB SAMA
            $skp->link_bukti_dukung = $linkHeader;
            $skp->pdf_file = $pdfUtama;
            
            // Simpan header dulu
            $skp->save();

            // LOOPING DOKUMEN DETAIL
            foreach ($request->dokumen as $item) {
                if (!empty($item['path'])) {
                    $oldPath = $item['path'];
                    $newPath = 'skp_files/' . basename($oldPath);
                    
                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->makeDirectory('skp_files');
                        Storage::disk('public')->move($oldPath, $newPath);

                        // Simpan ke detail
                        $dokumen = new \App\Models\SkpDokumen();
                        $dokumen->skp_id = $skp->id;
                        $dokumen->nama_file = $item['nama'];
                        $dokumen->tipe = 'pdf';
                        $dokumen->url = $newPath;
                        $dokumen->save();
                    }
                }
            }

            DB::commit();
            return redirect()->route('skp.riwayat')->with('success', 'Berhasil!');

        } catch (\Exception $e) {
            DB::rollBack();
            // Pakai dd() di sini buat liat error aslinya kalau gagal lagi
            dd("Gagal simpan: " . $e->getMessage());
        }
    }

    // DETAIL
    public function show($id)
    {
        // Pastikan relasi 'dokumen' sudah ada di Model SkpPengajuan
        $skp = SkpPengajuan::with(['dokumen','user'])->findOrFail($id);
        return view('skp.show', compact('skp'));
    }
}