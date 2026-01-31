<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VerifikasiTtdController extends Controller
{
    public function store($skpId)
    {
        $skp = SkpPengajuan::findOrFail($skpId);

        VerifikasiTtd::create([
            'skp_id' => $skp->id,
            'qr_text' => route('skp.verifikasi', $skp->id),
            'qr_x' => 450,
            'qr_y' => 700,
            'qr_size' => 120,
            'ditandatangani_oleh' => auth()->id(),
        ]);

        $statusLama = $skp->status;
        $skp->update(['status' => 'selesai']);

        SkpLog::create([
            'skp_id' => $skp->id,
            'dari_status' => $statusLama,
            'ke_status' => 'selesai',
            'dibuat_oleh' => auth()->id(),
            'keterangan' => 'Dokumen ditandatangani'
        ]);

        return back();
    }
}

