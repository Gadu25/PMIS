<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnexF extends Model
{
    use HasFactory;

    protected $fillables = [ 
        'workshop_id', 'remarks', 'year'
    ];

    public function projects(){
        return $this->belongsToMany(Project::class, 'annex_f_project', 'annex_f_id', 'project_id');
    }

    public function subs(){
        return $this->hasMany(AnnexFSub::class);
    }

    public function activities(){
        return $this->morphMany(AnnexFActivity::class, 'activitable')->orderBy('table_key', 'asc');
    }

    public function funds(){
        return $this->morphMany(AnnexFFund::class, 'fundable')->orderBy('table_key', 'asc');
    }
    
    public function histories(){
        return $this->morphMany(History::class, 'historiable')->orderBy('created_at', 'desc');
    }
}
