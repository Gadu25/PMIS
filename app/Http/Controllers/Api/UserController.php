<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Profile;
use App\Models\Title;
use App\Models\Notification;
use App\Models\SidebarItem;
use App\Models\SidebarRole;
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
        $auth = Auth::user();
        $user = $auth->with(['division', 'unit', 'subunit', 
        'activeProfile.notifications.from.user', 
        'activeProfile.notifications.to.user', 
        'activeProfile.title', 'activeProfile.leaderOf.project.subprojects'])->where('id', $auth->id)->first();
        $profile = $user->activeProfile;
        $profile->unread = $profile->notifications->where('is_read', false)->count();
        $profile->groupedroles = $profile->roles->groupBy('sidebar.name');
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

                $profile->roles()->sync($value['roles']);
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
                $newpassword = bcrypt($request['password']);
                $user->password = $newpassword;
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

                $profile->roles()->sync($value['roles']);
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
            return ['message' => 'User saved!', 'users' => $this->getUsers($id)];
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
            User::with(['profiles', 'activeProfile.title', 'activeProfile.roles', 'division', 'unit', 'subunit'])->orderBy('id', 'asc')->where('division_id', $divId)->get() : 
            User::with(['profiles', 'activeProfile.title', 'activeProfile.roles', 'division', 'unit', 'subunit'])->orderBy('id', 'asc')->get();
        foreach($users as $user){
            foreach($user->profiles as $profile){
                $profile->title;
                $profile->roles;
            }
            $user->titleSortOrder = $user->profiles[0]->title_id;
            $user->header = ($user->subunit_id) ? $user->subunit->name : (($user->unit_id) ? $user->unit->name : '');
        }
        $sorted = $users->sortBy('titleSortOrder');
        $grouped = $sorted->groupBy(['division.name', 'header']);
        return $grouped;
    }

    // Notification
    public function updateNotification($id){
        $notification = Notification::findOrFail($id);
        if(!$notification->is_read){
            $notification->is_read = true;
            $notification->save();
        }
        return ['message' => 'Saved!'];
    }

    // Roles 
    public function getSidebarItems(){
        return SidebarItem::with('roles')->get();
    }

    public function storeSidebarRole(Request $request){
        DB::beginTransaction();
        try {
            foreach($request['roles'] as $role){
                $sbrole = new SidebarRole;
                $sbrole->title = $role['title'];
                $sbrole->code = $role['code'];
                $sbrole->description = $role['description'];
                $sbrole->sidebar_item_id = $request['tab'];
                $sbrole->save();
            }
            DB::commit();
            return ['message' => 'Sidebar Roles saved!', 'roles' => $this->getSidebarItems()];
        }
        catch (\Exception $e){
            DB::rollback();
            return ['message' => 'Something went wrong', 'errors' => $e->getMessage()];
        }
    }

    public function updateSidebarRole(Request $request, $id){
        $sbrole = SidebarRole::findOrFail($id);
        $role = $request['roles'][0];

        $sbrole->title = $role['title'];
        $sbrole->code = $role['code'];
        $sbrole->description = $role['description'];
        $sbrole->sidebar_item_id = $request['tab'];
        $sbrole->save();

        return ['message' => 'Sidebar Item saved!', 'roles' => $this->getSidebarItems()];
    }

    public function destroySidebarRole($id){
        $sbrole = SidebarRole::findOrFail($id);
        $sbrole->delete();

        return ['message' => 'Sidebar Item deleted!', 'roles' => $this->getSidebarItems()];
    }

    public function deleteUser($id){
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            $user->delete();

            $profile = Profile::where("user_id", $id);
            $profile->delete();

            DB::commit();
            return ['message' => 'User deleted!'];
        }
        catch (\Exception $e){
            DB::rollback();
            return ['message' => 'Something went wrong', 'errors' => $e->getMessage()];
        }
    }

    // Staff
    public function getStaffs(){
        $titles = Title::where('name', 'Unit Head')->orWhere('name', 'Project Leader')->orWhere('name', 'Staff / Encoder')->get();
        $ids = [];
        foreach($titles as $title){
            array_push($ids, $title->id);
        }
        $user = User::with('activeProfile.title', 'division', 'unit', 'subunit')
                    ->whereHas('activeProfile.title', function($q) use($ids){
                        $q->whereIn('id', $ids);
                    })->get();
        $grouped = $user->groupBy(['activeProfile.title.name']);
        return $grouped;
    }
}
