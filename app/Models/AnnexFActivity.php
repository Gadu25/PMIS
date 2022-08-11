<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnexFActivity extends Model
{
    use HasFactory;

    protected $fillables = [
        'activitable_id', 'activitable_type', 'table_key', 'description'
    ];

    public function activitable(){
        return $this->morphTo();
    }
}
