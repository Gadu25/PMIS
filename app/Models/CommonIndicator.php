<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommonIndicator extends Model
{
    use HasFactory;

    protected $fillables = [
        'type', 'description', 'workshop_id', 'program_id', 'subprogram_id', 'cluster_id', 'year'
    ];

    public function tags(){
        return $this->morphToMany(Tag::class, 'taggable', 'taggables');
    }

    public function subindicators(){
        return $this->hasMany(CommonIndicatorSub::class);
    }

    public function program(){
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function subprogram(){
        return $this->belongsTo(Subprogram::class, 'subprogram_id');
    }

    public function cluster(){
        return $this->belongsTo(Cluster::class, 'cluster_id');
    }

    public function details(){
        return $this->morphOne(IndicatorDetail::class, 'indicatorable');
    }
}
