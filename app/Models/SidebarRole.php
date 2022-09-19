<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SidebarRole extends Model
{
    use HasFactory;

    protected $fillables = [
        'title', 'code', 'description', 'sidebar_item_id'
    ];

    public function sidebar(){
        return $this->belongsTo(SidebarItem::class, 'sidebar_item_id');
    }

    public function profiles(){
        return $this->belongsToMany(Profile::class, 'profile_role', 'sidebar_role_id');
    }
}
