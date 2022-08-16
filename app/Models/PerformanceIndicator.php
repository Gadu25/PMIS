<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceIndicator extends Model
{
    use HasFactory;

    protected $fillables = [
        'description', 'common', 'indicatorable_id', 'indicatorable_type'
    ];

    public function item(){
        return $this->belongsTo(AnnexE::class, 'indicatorable_id');
    }

    public function subitem(){
        return $this->belongsTo(AnnexESub::class, 'indicatorable_id');
    }

    public function tags(){
        return $this->morphToMany(Tag::class, 'taggable', 'taggables');
    }

    public function details(){
        return $this->morphOne(IndicatorDetail::class, 'indicatorable');
    }
}
