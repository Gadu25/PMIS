<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleOfActivity extends Model
{
    use HasFactory;

    protected $fillables = [
        'title',
        'project_profile_id'
    ];

    public function profile(){
        return $this->belongsTo(ProjectProfile::class, 'project_profile_id');
    }

    public function months(){
        return $this->hasMany(SoaMonth::class, 'soa_id');
    }
}
