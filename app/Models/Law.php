<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Law extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public function profiles(){
        return $this->belongsToMany(ProjectProfile::class, 'project_profile_law', 'law_id', 'project_profile_id');
    }
    
    // projects can also be fetched use manyThrough relationship - see laravel 9 docs for more info.
}
