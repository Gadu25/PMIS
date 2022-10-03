<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProposalContent extends Model
{
    use HasFactory;

    protected $fillables = [
        'title',
        'text',
        'img_filename',
        'img_position',
        'project_profile_id'
    ];

    public function profile(){
        return $this->belongsTo(ProjectProfile::class, 'project_profile_id');
    }
}
