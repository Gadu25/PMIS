<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Law;

class LawSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $laws = [
            'Disaster and Risk Reduction and Management (DRRM)',
            'Gender and Development (GAD)',
            'Indigenous People',
            'Mandate',
            'Person With Disabilities (PWD)',
            'Senior Citizen'
        ];

        foreach($laws as $law){
            $compliancewithlaw = new Law;
            $compliancewithlaw->name = $law;
            $compliancewithlaw->save();
        }
    }
}
