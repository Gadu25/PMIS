<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnexOneFund extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'amount', 'year', 'fundable_id', 'fundable_type', 'type'
    ];

    public function fundable(){
        return $this->morphTo();
    }
}
