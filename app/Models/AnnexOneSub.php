<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnexOneSub extends Model
{
    use HasFactory;

    protected $fillables = [
        'annex_one_id'
    ];

    public function annexone(){
        return $this->belongsTo(AnnexOne::class, 'annex_one_id');
    }

    public function funds(){
        return $this->morphMany(AnnexOneFund::class, 'fundable')->orderBy('year', 'asc');
    }
}
