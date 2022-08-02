<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillables = [
        'division_id', 'name'
    ];

    public function division(){
        return $this->belongsTo(Division::class, 'division_id');
    }

    public function subunits(){
        return $this->hasMany(Subunit::class);
    }

    public function roles(){
        return $this->morphMany(Role::class, 'roleable');
    }
}
