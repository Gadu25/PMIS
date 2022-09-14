<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class History extends Model
{
    use HasFactory;

    protected $fillables = [
        'historiable_id', 'historiable_type', 'subject', 'profile_id', 'comment'
    ];

    protected $dates = ['created_at','updated_at'];

    protected function serializeDate(\DateTimeInterface $dates)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $dates)->diffForHumans();
        // OR
        //return Carbon::createFromFormat('Y-m-d H:i:s', $dates)->format('Y-m-d');
    }

    public function profile(){
        return $this->belongsTo(Profile::class, 'profile_id');
    }

    // public function historiable(){
    //     return $this->morphTo();
    // }
}
