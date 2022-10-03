<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineItemBudget extends Model
{
    use HasFactory;

    protected $fillables = [
        'status',
        'source_of_funds',
        'project_profile_id',
        'created_by'
    ];

    public function profile(){
        return $this->belongsTo(ProjectProfile::class, 'project_profile_id');
    }

    public function types(){
        return $this->hasMany(LibType::class, 'lib_id');
    }
}
