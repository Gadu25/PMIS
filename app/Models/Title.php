<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Title extends Model
{
    use HasFactory;

    protected $fillables = [
        'name', 'description'
    ];

    public function profiles(){
        return $this->hasMany(Profile::class);
    }
}
