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

    // public function taggable(){
    //     return $this->morphedByMany(CommonIndicator::class, 'taggable', 'taggables');
    // }
}
