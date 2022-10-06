<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicationFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'publication_id'
    ];

    public function publication(){
        return $this->belongsTo(Publication::class, 'publication_id');
    }
}
