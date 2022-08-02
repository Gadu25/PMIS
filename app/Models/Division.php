<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    protected $fillables = [
        'code', 'name'
    ];

    public function units(){
        return $this->hasMany(Unit::class);
    }

    public function roles(){
        return $this->morphMany(Role::class, 'roleable');
    }
}
