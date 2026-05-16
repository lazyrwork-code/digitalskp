<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
class SkpVerifikasiController extends Controller
{
    public function verifikasi($id)
    {
        $skp = SkpPengajuan::findOrFail($id);

        $statusLama = $skp->status;
        $skp->update(['status' => 'menungguttd']);

        SkpLog::create([
            'skp_id' => $id,
            'dari_status' => $statusLama,
            'ke_status' => 'menungguttd',
            'dibuat_oleh' => auth()->id(),
            'keterangan' => 'Diverifikasi admin'
        ]);

        return back();
    }

    public function perbaikan(Request $request, $id)
    {
        $skp = SkpPengajuan::findOrFail($id);

        $statusLama = $skp->status;
        $skp->update([
            'status' => 'perbaikan',
            'catatan_perbaikan' => $request->catatan
        ]);

        SkpLog::create([
            'skp_id' => $id,
            'dari_status' => $statusLama,
            'ke_status' => 'perbaikan',
            'dibuat_oleh' => auth()->id(),
            'keterangan' => $request->catatan
        ]);

        return back();
    }
}
