<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LinkUndangan extends Model
{
    //
 use HasFactory;
    
    protected $table = 'link_undangan';
    protected $fillable = [
        'link_undangan',
        'nama_pasangan_1',
        'nama_pasangan_2',
        'tanggal_pernikahan',
        'lokasi_pernikahan',
        'tamu_undangan',
    ];
}
