<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NationalExpenditure extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'isAdded',
        'project_id',
        'workshop_id'
    ];

    public function project(){
        return $this->belongsTo(Project::class);
    }

    public function workshop(){
        return $this->belongsTo(Workshop::class);
    }

    public function subs(){
        return $this->hasMany(NationalExpenditureSub::class);
    }
}
