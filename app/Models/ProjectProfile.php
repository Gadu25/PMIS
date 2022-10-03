<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectProfile extends Model
{
    use HasFactory;

    protected $fillables = [
        'title',
        'status',
        'compliance_with_law',
        'project_leader',
        'start',
        'end',
        'year',
        'project_id',
        'created_by',
        'num'
    ];

    public function project(){
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function proponents(){
        return $this->hasMany(Proponent::class);
    }

    public function proposals(){
        return $this->hasMany(ProposalContent::class);
    }

    public function libs(){
        return $this->hasMany(LineItemBudget::class);
    }

    public function activities(){
        return $this->hasMany(ScheduleOfActivity::class);
    }
}
