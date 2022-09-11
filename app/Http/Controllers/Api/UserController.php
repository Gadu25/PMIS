<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Profile;
use App\Models\Title;
use App\Models\Notification;
use Auth;
use DB;

class UserController extends Controller
{
    public function index(){
        return $this->getUsers();
    }

    public function getByDivision($id){
        return $this->getUsers($id);
    }

    public function getTitles(){
        return Title::orderBy('id', 'asc')->get();
    }

    public function authUser(){
        $user = Auth::user();
        $user->division;
        $user->unit;
        $user->subunit;
        $profile = $user->activeProfile;
        $profile->title;
        foreach($profile->notifications as $notification){
            $notification->from->user;
            $notification->to->user;
        }
        // $profile->notifications->to;
        // $user->activeProfile->notifications = Notification::where('profile_to', $profile->id)->get();
        return $user;
    }

    public function store(Request $request){
        DB::beginTransaction();
        try {
            $user = new User;
            $user->firstname = $request['firstname'];
            $user->lastname = $request['lastname'];
            $user->email = $request['email'];
            $user->password = bcrypt($request['password']);
            $user->division_id = $request['division_id'];
            $user->unit_id = ($request['unit_id'] == 0) ? null : $request['unit_id'];
            $user->subunit_id = ($request['subunit_id'] == 0) ? null : $request['subunit_id'];
            $user->save();

            foreach($request['profiles'] as $value){
                $profile = new Profile;
                $profile->user_id = $user->id;
                $profile->title_id = $value['title_id'];
                $profile->access_level = $value['access_level'];
                $profile->isOIC = $value['isOIC'];
                $profile->active = $value['active'];
                $profile->save();
            }

            $id = ($request['isSynced']) ? $user->division_id : null;

            DB::commit();
            return ['message' => 'User added!', 'users' => $this->getUsers($id)];
        }
        catch(\Exception $e){
            DB::rollback();
            return ['message' => 'Something went wrong', 'errors' => $e->getMessage()];
        }
    }

    public function update(Request $request, $id){
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            $user->firstname = $request['firstname'];
            $user->lastname = $request['lastname'];
            $user->email = $request['email'];
            if($request['password'] != ''){
                $user->password = bcrypt($request['password']);
            }
            $user->division_id = $request['division_id'];
            $user->unit_id = ($request['unit_id'] == 0) ? null : $request['unit_id'];
            $user->subunit_id = ($request['subunit_id'] == 0) ? null : $request['subunit_id'];
            $user->save();

            $initialprofiles = $user->profiles;
            $tempIds = [];
            foreach($request['profiles'] as $value){
                $profile = ($value['id']) ? Profile::findOrFail($value['id']) : new Profile;
                $profile->user_id = $user->id;
                $profile->title_id = $value['title_id'];
                $profile->access_level = $value['access_level'];
                $profile->isOIC = $value['isOIC'];
                $profile->active = $value['active'];
                $profile->save();
                array_push($tempIds, $profile->id);
            }

            foreach($initialprofiles as $profile){
                if(!in_array($profile->id, $tempIds)){
                    $profile->delete();
                    // include detach of roles later on
                }
            }

            $id = ($request['isSynced']) ? $user->division_id : null;
            
            DB::commit();
            return ['message' => 'User added!', 'users' => $this->getUsers($id)];
        }
        catch (\Exception $e){
            DB::rollback();
            return ['message' => 'Something went wrong', 'errors' => $e->getMessage()];
        }
    }

    public function show($id){

    }

    private function getUsers($divId = null){
        $users = $divId ? 
            User::with(['profiles', 'division', 'unit', 'subunit'])->orderBy('id', 'asc')->where('division_id', $divId)->get() : 
            User::with(['profiles', 'division', 'unit', 'subunit'])->orderBy('id', 'asc')->get();
        foreach($users as $user){
            foreach($user->profiles as $profile){
                $profile->title;
            }
            $user->titleSortOrder = $user->profiles[0]->title_id;
            $user->header = ($user->subunit_id) ? $user->subunit->name : (($user->unit_id) ? $user->unit->name : '');
        }
        $sorted = $users->sortBy('titleSortOrder');
        $grouped = $sorted->groupBy(['division.name', 'header']);
        return $grouped;
    }

}
