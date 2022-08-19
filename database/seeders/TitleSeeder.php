<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Title;

class TitleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $titles = [
            ['name' => 'Director'],
            ['name' => 'Deputy Director'],
            ['name' => 'Division Chief'],
            ['name' => 'Unit Head'],
            ['name' => 'Project Leader'],
            ['name' => 'Staff / Encoder'],
            ['name' => 'Superadmin Profile']
        ];

        foreach($titles as $value){
            $title = new Title;
            $title->name = $value['name'];
            $title->save();
        }
    }
}
