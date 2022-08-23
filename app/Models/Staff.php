<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staffs';

    protected $fillables = [
        'project_id', 'profile_id', 'type'
    ];

    public function project(){
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function profile(){
        return $this->belongsTo(Profile::class, 'profile_id');
    }
}
