<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Law;

class ComplianceController extends Controller
{
    public function index(){
        return Law::all();
    }
}
