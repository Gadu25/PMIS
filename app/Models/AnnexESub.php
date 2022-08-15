<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnexESub extends Model
{
    use HasFactory;

    protected $fillables = [
        'annex_e_id', 'subproject_id'
    ];
    
    public function annexe(){
        return $this->belongsTo(AnnexE::class, 'annex_f_id');
    }

    public function subproject(){
        return $this->belongsTo(Subproject::class, 'subproject_id');
    }
}
