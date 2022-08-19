<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Profile;
use App\Models\Division;
use App\Models\Unit;
use App\Models\Title;

class SuperadminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // To dynamically get OD - Planning Unit
        $division = Division::where('code', 'OD')->first();
        $unit = Unit::where('name', 'ilike' ,'%Planning%')->first();

        $user = new User;
        $user->firstname = 'Super';
        $user->lastname = 'Admin';
        $user->email = 'superadmin@pmis.com';
        $user->password = bcrypt('superadmin');
        $user->division_id = $division->id;
        $user->unit_id = $unit->id;
        $user->save();

        $title = Title::where('name', 'Superadmin Profile')->first();

        $profile = new Profile;
        $profile->user_id = $user->id;
        $profile->title_id = $title->id;
        $profile->access_level = 'Admin';
        $profile->save();
    }
}
