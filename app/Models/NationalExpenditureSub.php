<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NationalExpenditureSub extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'isAdded',
        'national_expenditure_id',
        'subproject_id'
    ];

    public function nep(){
        return $this->belongsTo(NationalExpenditure::class);
    }

    public function subproject(){
        return $this->belongsTo(Subproject::class);
    }
}
