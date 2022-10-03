<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SidebarRole;

class SuperadminRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superadmin = User::where('email', 'like', '%superadmin%')->first();
        $profile = $superadmin->activeProfile;
        
        $dbroles = SidebarRole::all();
        $rolesArray = [];
        foreach($dbroles as $role){
            array_push($rolesArray, $role->id);
        }
        $profile->roles()->sync($rolesArray);
    }
}
