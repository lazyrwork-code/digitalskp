<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkpLog extends Model
{
    protected $table = 'skp_log';

    protected $fillable = [
        'skp_id','dari_status','ke_status','keterangan','dibuat_oleh'
    ];

    public function skp()
    {
        return $this->belongsTo(SkpPengajuan::class);
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }
}

