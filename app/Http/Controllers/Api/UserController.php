<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class UserController extends Controller
{
    public function index(){
        return User::all();
    }

    public function show($id){

    }

    public function authUser(){
        $user = Auth::user();
        $user->activeProfile;
        return $user;
    }
}
