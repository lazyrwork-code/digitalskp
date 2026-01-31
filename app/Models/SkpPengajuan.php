<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkpPengajuan extends Model
{
    protected $table = 'skp_pengajuan';

    protected $fillable = [
        'user_id','unit','bulan','tahun','tanggal_pengajuan',
        'status','judul_laporan','link_bukti_dukung',
        'pdf_file','pdf_ttdfinal'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function logs()
    {
        return $this->hasMany(SkpLog::class, 'skp_id');
    }

    public function dokumen()
    {
        return $this->hasMany(SkpDokumen::class, 'skp_id');
    }

    public function ttd()
    {
        return $this->hasOne(VerifikasiTtd::class, 'skp_id');
    }
}
