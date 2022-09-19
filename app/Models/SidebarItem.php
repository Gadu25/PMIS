<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SidebarItem extends Model
{
    use HasFactory;

    public function roles(){
        return $this->hasMany(SidebarRole::class, 'sidebar_item_id')->orderBy('id', 'asc');
    }
}
