<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndicatorDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'detailable_id',
        'detailable_type',
        'actual',
        'estimate',
        'physical_targets',
        'first',
        'second',
        'third',
        'fourth'
    ];

    public function detailable(){
        return $this->morphTo();
    }
}
