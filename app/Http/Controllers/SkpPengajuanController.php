<?php

namespace App\Http\Controllers;

use App\Models\SkpPengajuan;
use App\Models\SkpDokumen; // WAJIB IMPORT INI
use App\Models\User; // WAJIB IMPORT INI
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

    public function indexDetail(Request $request, $id_user)
    {
        $tahunDipilih = $request->query('tahun', date('Y'));

        $data = SkpPengajuan::with('user')
                    ->where('tahun', $tahunDipilih)
                    ->where('status', 'selesai')
                    ->where('user_id', $id_user)
                    ->latest()
                    ->get();

                     $user = User::findOrFail($id_user);
        return view('kepala.riwayat', compact('user','data','tahunDipilih','id_user'));
    }

    public function indexKepala(Request $request)
    {
        $tahunDipilih = $request->query('tahun', date('Y'));

        $data = SkpPengajuan::with('user')
                    ->where('tahun', $tahunDipilih)
                    ->where('status', 'selesai')
                    ->latest()
                    ->get();

        return view('kepala.riwayatbyKepala', compact('data','tahunDipilih'));
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
        $request->validate([
            'tanggal_pengajuan' => 'required|date',
            'bulan'             => 'required',
            'tahun'             => 'required',
            'unit'              => 'required',
            'dokumen'           => 'required|array',
        ]);

        DB::beginTransaction();
        try {

            // ===== HEADER AMBIL DARI DOKUMEN PERTAMA =====
            $dokumenPertama = $request->dokumen[0] ?? [];

            $judulHeader = $dokumenPertama['judul_laporan'] ?? 'Tanpa Judul';
            $pdfUtama    = isset($dokumenPertama['path'])
                ? 'skp_files/' . basename($dokumenPertama['path'])
                : null;

            $skp = new \App\Models\SkpPengajuan();
            $skp->user_id = auth()->id();
            $skp->unit = $request->unit;
            $skp->bulan = $request->bulan;
            $skp->tahun = $request->tahun;
            $skp->tanggal_pengajuan = $request->tanggal_pengajuan;
            $skp->status = 'verifikasi';
            $link_bukti_dukung  = $request->dokumen[0]['link_bukti_dukung'] ?? '-';
            $skp->judul_laporan = $judulHeader;
            $skp->pdf_file = $pdfUtama;
            $skp->save();

            // ===== DETAIL DOKUMEN =====
            foreach ($request->dokumen as $item) {

                if (!empty($item['path'])) {

                    $oldPath = $item['path'];
                    $newPath = 'skp_files/' . basename($oldPath);

                    if (Storage::disk('public')->exists($oldPath)) {

                        Storage::disk('public')->makeDirectory('skp_files');
                        Storage::disk('public')->move($oldPath, $newPath);

                        $dokumen = new \App\Models\SkpDokumen();
                        $dokumen->skp_id = $skp->id;
                        $dokumen->nama_file = $item['nama'] ?? 'Tanpa Nama';
                        $dokumen->link_pendukung = $item['link_bukti_dukung'] ?? null;
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

    public function edit($id){
        $skp = SkpPengajuan::with(['dokumen','user'])->findOrFail($id);
        return view('skp.perbaikan', compact('skp'));
    }

    public function update(Request $request, $id)
    {
        $skp = SkpPengajuan::with('dokumen')->findOrFail($id);
        
        $request->validate([
            'judul_laporan.*' => 'nullable|string|max:255',
            'link_pendukung.*' => 'nullable|string|max:255',
            'dokumen'          => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            foreach ($skp->dokumen as $doc) {
                // Update metadata
                
                if (isset($request->judul_laporan[$doc->id])) {
                    $doc->nama_file = $request->judul_laporan[$doc->id];
                }
                if (isset($request->link_pendukung[$doc->id])) {
                    $doc->link_pendukung = $request->link_pendukung[$doc->id];
                }

                if (isset($request->dokumen[$doc->id]['path']) && !empty($request->dokumen[$doc->id]['path'])) {
                    $tempPath = $request->dokumen[$doc->id]['path'];
                    $fileName = time() . '_' . basename($tempPath);
                    $newPath  = 'skp_files/' . $fileName;

                    if (Storage::disk('public')->exists($tempPath)) {
                        if ($doc->url && Storage::disk('public')->exists($doc->url)) {
                            Storage::disk('public')->delete($doc->url);
                        }
                        Storage::disk('public')->move($tempPath, $newPath);
                        $doc->url = $newPath;
                        // Kalo dibutuhin
                        // $doc->catatan = null;
                    }
                }
                $doc->save();
            }
            $skp->status = 'verifikasi'; 
            $skp->save();
            DB::commit();
            return redirect()
                ->route('redirect.role')
                ->with('success', 'Perbaikan SKP berhasil dikirim, status: Menunggu TTD');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }
}