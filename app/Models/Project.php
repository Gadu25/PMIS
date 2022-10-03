<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillables = [
        'num', 'title', 'status', 'program_id', 'subprogram_id', 'cluster_id', 'division_id', 'unit_id', 'subunit_id', 'description'
    ];

    public function program(){
        return $this->belongsTo(Program::class, 'program_id');
    }
    
    public function subprogram(){
        return $this->belongsTo(Subprogram::class, 'subprogram_id');
    }
    
    public function cluster(){
        return $this->belongsTo(Cluster::class, 'cluster_id');
    }
    
    public function division(){
        return $this->belongsTo(Division::class, 'division_id');
    }
    
    public function unit(){
        return $this->belongsTo(Unit::class, 'unit_id');
    }
    
    public function subunit(){
        return $this->belongsTo(Subunit::class, 'subunit_id');
    }

    public function leader(){
        return $this->hasOne(Staff::class)->where('type', 'Leader');
    }

    public function encoders(){
        return $this->hasMany(Staff::class)->where('type', 'Encoder');
    }

    public function subprojects(){
        return $this->hasMany(Subproject::class);
    }

    public function profiles(){
        return $this->hasMany(ProjectProfile::class)->orderBy('year', 'desc');
    }

    // Workshop
    public function annexones(){
        return $this->hasMany(AnnexOne::class);
    }
    
    public function annexfs(){
        return $this->belongsToMany(AnnexF::class, 'annex_f_project', 'project_id', 'annex_f_id');
    }
}
