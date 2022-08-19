<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillables = [
        'user_id', 'title_id', 'access_level', 'isOIC', 'active'
    ];

    public function title(){
        return $this->belongsTo(Title::class, 'title_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
