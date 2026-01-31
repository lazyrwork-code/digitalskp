<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepositorySkp extends Model
{
    use HasFactory;
    protected $table = 'repository_skp';

    protected $fillable = [
        'skp_id',
        'user_id',
        'tahun',
        'bulan',
        'kategori',
        'file_pdf'
    ];

    public function skp()
    {
        return $this->belongsTo(SkpPengajuan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
}
