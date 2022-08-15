<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnexE extends Model
{
    use HasFactory;

    protected $fillables = [
        'workshop_id', 'project_id', 'remarks', 'status'
    ];
    
    public function project(){
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function subs(){
        return $this->hasMany(AnnexESub::class);
    }
}
