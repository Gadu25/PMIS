<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibItem extends Model
{
    use HasFactory;

    protected $fillables = [
        'item',
        'amount',
        'type',
        'lib_id'
    ];

    public function lib(){
        return $this->belongsTo(LineItemBudget::class, 'lib_id');
    }
}
