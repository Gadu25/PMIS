<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibItem extends Model
{
    use HasFactory;

    protected $fillables = [
        'name',
        'amount',
        'lib_type_id'
    ];

    public function type(){
        return $this->belongsTo(LibType::class, 'lib_type_id');
    }
}
