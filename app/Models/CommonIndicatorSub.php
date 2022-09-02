<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommonIndicatorSub extends Model
{
    use HasFactory;

    protected $fillables = [
        'description','common_indicator_id'
    ];

    public function tags(){
        return $this->morphToMany(Tag::class, 'taggable', 'taggables');
    }

    public function details(){
        return $this->morphOne(IndicatorDetail::class, 'indicatorable');
    }

    public function commonindicator(){
        return $this->belongsTo(CommonIndicator::class, 'common_indicator_id');
    }
}
