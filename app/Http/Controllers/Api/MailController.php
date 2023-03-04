<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProjectUpdate;
use App\Mail\OneTimePin;

class MailController extends Controller
{
    public function sendMail(){
        logger("TESTING SEND MAIL");

        $title = "Workshop - Annex E";
        $messageString = '<p class="m-0">Action: Indicator - Update Indicator Details</p><p class="m-0">Status: Draft <small><i>`Unchanged</small>`</i></p>';
        $userName = "John Doe";

        if($this->isOnline()){
            Mail::to('audag25@gmail.com')->send(new ProjectUpdate($title, $messageString, $userName));
            return ['message' => 'Mail sent!'];
        }else{
            return "No Internet Connection";
        }
    }

    public function sendOtp(Request $request){
        $user = $request->to;
        $digits = $request->otp;

        if($this->isOnline()){
            Mail::to($user)->send(new OneTimePin($digits));
            return ['message' => 'OTP Sent!'];
        }else{
            return "No Internet Connection";
        }
    }

    public function isOnline($site = "https://youtube.com/"){
        if(@fopen($site, "r")){
            return true;
        }else{
            return false;
        }
    }
}
