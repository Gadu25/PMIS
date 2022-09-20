<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subprogram extends Model
{
    use HasFactory;

    protected $fillables = [
        'title', 'title_short', 'program_id'
    ];

    public function program(){
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function clusters(){
        return $this->hasMany(Cluster::class);
    }

    public function projects(){
        return $this->hasMany(Project::class);
    }
}
