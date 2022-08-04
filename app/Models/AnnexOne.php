<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnexOne extends Model
{
    use HasFactory;
    
    protected $fillables = [
        'source_of_funds','header_type','workshop_id','project_id'
    ];

    public function subs(){
        return $this->hasMany(AnnexOneSub::class);
    }

    public function funds(){
        return $this->morphMany(AnnexOneFund::class, 'fundable')->orderBy('year', 'asc');
    }

    public function project(){
        return $this->belongsTo(Project::class, 'project_id');
    }
}
