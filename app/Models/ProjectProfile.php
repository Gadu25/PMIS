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
        // 'compliance_with_law',
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
        return $this->hasMany(ProposalContent::class)->orderBy('id', 'asc');
    }

    public function libs(){
        return $this->hasMany(LineItemBudget::class);
    }

    public function activities(){
        return $this->hasMany(ScheduleOfActivity::class);
    }

    public function milestones($start = 1, $end = 12){
        return $this->hasMany(ScheduleOfActivity::class)
            ->whereHas('months', function($q) use($start, $end){
                // $q->whereBetween('month', [$start,$end]);
                $q->where('type', 'Milestone');
            });
    }

    public function laws(){
        return $this->belongsToMany(Law::class, 'project_profile_law', 'project_profile_id', 'law_id');
    }

    // future improvements

    // auto fill project profile status (new, ongoing, completed, terminated)
    // new - if project does not have any profile before
    // ongoing - if project previously have profile 
    // completed - auto update on end of year (if project is not terminated)
    // terminated - create a button for project leaders to terminate a project profile

    // project leaders - include division chiefs on select options

    // project - restrictions
}
