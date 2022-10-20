<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $fillables = [
        'num', 'title'
    ];

    public function subprograms(){
        return $this->hasMany(Subprogram::class);
    }
    
    public function projects(){
        return $this->hasMany(Project::class);
    }
}
