<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Division;

class DivisionController extends Controller
{
    public function index(){
        $divisions = Division::orderBy('id', 'asc')->get();
        foreach($divisions as $division){
            $division->roles;
            foreach($division->units as $unit){
                $unit->roles;
                foreach($unit->subunits as $subunit){
                    $subunit->roles;
                }
            }
        }
        return $divisions;
    }
}
