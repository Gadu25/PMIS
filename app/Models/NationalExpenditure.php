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
        return $this->hasOne(Project::class, 'project_id');
    }

    public function workshop(){
        return $this->hasOne(Workshop::class, 'workshop_id');
    }
}
