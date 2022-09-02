<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillables = [
        'program_id', 'name', 'type'
    ];

    public function otherindicators(){
        return $this->morphedByMany(CommonIndicator::class, 'taggable', 'taggables')->whereNot('type', 'Performance');
    }

    public function otherindicatorsubs(){
        return $this->morphedByMany(CommonIndicatorSub::class, 'taggable', 'taggables');
    }

    public function performanceindicators(){
        return $this->morphedByMany(PerformanceIndicator::class, 'taggable', 'taggables');
    }
}
