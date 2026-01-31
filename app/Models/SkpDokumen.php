<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkpDokumen extends Model
{
    use HasFactory;
    // Paksa Laravel menggunakan nama tabel yang benar (tanpa 's')
    protected $table = 'skp_dokumen'; 

    protected $fillable = ['skp_id', 'nama_file', 'tipe', 'url','catatan'];
}
