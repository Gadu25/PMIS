<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoaMonth extends Model
{
    use HasFactory;

    protected $fillables = [
        'type',
        'start',
        'end',
        'month',
        'soa_id'
    ];

    public function activity(){
        return $this->belongsTo(ScheduleOfActivity::class, 'soa_id');
    }
}
