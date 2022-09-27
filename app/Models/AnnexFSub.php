<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnexFSub extends Model
{
    use HasFactory;

    protected $fillables = [
        'annex_f_id', 'subproject_id', 'remarks'
    ];

    public function annexf(){
        return $this->belongsTo(AnnexF::class, 'annex_f_id');
    }

    public function subproject(){
        return $this->belongsTo(Subproject::class, 'subproject_id');
    }

    public function activities(){
        return $this->morphMany(AnnexFActivity::class, 'activitable')->orderBy('table_key', 'asc');
    }

    public function funds(){
        return $this->morphMany(AnnexFFund::class, 'fundable')->orderBy('table_key', 'asc');
    }
}
