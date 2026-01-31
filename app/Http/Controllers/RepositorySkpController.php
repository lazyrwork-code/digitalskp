<?php

namespace App\Http\Controllers;

use App\Models\RepositorySkp;
use App\Models\SkpPengajuan;
use Illuminate\Http\Request;

class RepositorySkpController extends Controller
{
    public function index()
    {
        $query = RepositorySkp::with('user');

        if (auth()->user()->role === 'pegawai') {
            $query->where('user_id', auth()->id());
        }

        $data = $query->latest()->get();
        return view('repository.index', compact('data'));
    }

    public function store($skpId)
    {
        $skp = SkpPengajuan::findOrFail($skpId);

        RepositorySkp::firstOrCreate([
            'skp_id' => $skp->id
        ], [
            'user_id' => $skp->user_id,
            'tahun' => $skp->tahun,
            'bulan' => $skp->bulan,
            'kategori' => 'SKP',
            'file_pdf' => $skp->pdf_ttdfinal
        ]);

        return back();
    }
}

