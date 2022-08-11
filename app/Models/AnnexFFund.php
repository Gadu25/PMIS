<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnexFFund extends Model
{
    use HasFactory;

    protected $fillables = [
        'fundable_id', 'fundable_type', 'table_key', 'amount'
    ];

    public function fundable(){
        return $this->morphTo();
    }
}
