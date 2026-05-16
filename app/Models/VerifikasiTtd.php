<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerifikasiTtd extends Model
{
    protected $table = 'verifikasi_ttd';

    protected $fillable = [
        'skp_id',
        'qr_text',
        'qr_x',
        'qr_y',
        'qr_size',
        'ditandatangani_oleh'
    ];

    public function skp()
    {
        return $this->belongsTo(SkpPengajuan::class, 'skp_id');
    }

    public function penandatangan()
    {
        return $this->belongsTo(User::class, 'ditandatangani_oleh');
    }
}
