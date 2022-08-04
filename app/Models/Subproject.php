<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subproject extends Model
{
    use HasFactory;

    protected $fillables = [
        'project_id', 'title'
    ];

    public function project(){
        return $this->belongsTo(Project::class, 'project_id');
    }
}
