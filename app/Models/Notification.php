<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notification extends Model
{
    use HasFactory;

    protected $fillables = [
        'profile_to', 'profile_from', 'title', 'body'
    ];

    protected function serializeDate(\DateTimeInterface $dates)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $dates)->diffForHumans();
        // OR
        //return Carbon::createFromFormat('Y-m-d H:i:s', $dates)->format('Y-m-d');
    }

    public function from(){
        return $this->hasOne(Profile::class, 'id', 'profile_from');
    }
    
    public function to(){
        return $this->hasOne(Profile::class, 'id', 'profile_to');
    }
}
