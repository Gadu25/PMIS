<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillables = [
        'num', 'title', 'status', 'program_id', 'subprogram_id', 'cluster_id', 'division_id', 'unit_id', 'subunit_id'
    ];
}
