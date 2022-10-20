<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cluster extends Model
{
    use HasFactory;

    protected $fillables = [
        'title', 'subprogram_id'
    ];

    public function subprogram(){
        return $this->belongsTo(Subprogram::class, 'subprogram_id');
    }
    
    public function projects(){
        return $this->hasMany(Project::class);
    }
}
