<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndicatorBreakdown extends Model
{
    use HasFactory;

    protected $fillables = [
        'performance_indicator_id', 'quarter', 'month', 'number'
    ];

    public function indicator(){
        return $this->belongsTo(PerformanceIndicator::class, 'performance_indicator_id');
    }
}
