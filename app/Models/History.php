<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;

    protected $fillables = [
        'historiable_id','historiable_type','subject','profile_id'
    ];

    // public function historiable(){
    //     return $this->morphTo();
    // }
}
