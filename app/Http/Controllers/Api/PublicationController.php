<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Publication;
use App\Models\PublicationFile;

use DB;

class PublicationController extends Controller
{
    public function index(){
        return Publication::with('files')->get();
    }

    public function store(Request $request){

    }

    public function update(Request $request, $id){

    }

    public function destroy($id){

    }
}
