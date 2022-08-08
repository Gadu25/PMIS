<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommonIndicator extends Model
{
    use HasFactory;

    protected $fillables = [
        'type', 'description', 'workshop_id', 'program_id', 'subprogram_id', 'cluster_id' 
    ];

    public function tags(){
        return $this->morphToMany(Tag::class, 'taggable', 'taggables');
    }

    public function subindicators(){
        return $this->hasMany(CommonIndicatorSub::class);
    }
}
