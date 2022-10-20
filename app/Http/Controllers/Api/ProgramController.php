<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Program;

class ProgramController extends Controller
{
    public function index(){
        $programs = Program::with('projects', 'subprograms.projects', 'subprograms.clusters.projects')->orderBy('id', 'asc')->get();
        return $programs;
    }
}
