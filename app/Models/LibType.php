<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibType extends Model
{
    use HasFactory;

    protected $fillables = [
        'name', 'lib_id'
    ];

    public function lib(){
        return $this->belongsTo(LinItemBudget::class, 'lib_id');
    }

    public function items(){
        return $this->hasMany(LibItem::class, 'lib_type_id');
    }
}
