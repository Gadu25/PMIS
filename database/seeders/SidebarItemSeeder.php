<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SidebarItem;

class SidebarItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            ['name' => 'Divisions and Units'],
            ['name' => 'Programs and Projects'],
            ['name' => 'Events Management'],
            ['name' => 'Budget Executive Documents'],
            ['name' => 'Resources and Publications'],
            ['name' => 'Reports'],
            ['name' => 'User Management'],
        ];

        foreach($items as $item){
            $sidebaritem = new SidebarItem;
            $sidebaritem->name = $item['name'];
            $sidebaritem->save();
        }
    }
}
