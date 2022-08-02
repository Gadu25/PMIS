<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Program;
use App\Models\Subprogram;
use App\Models\Cluster;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $programs = [
            [
                'num' => 'Program 1',
                'title' => 'Scholarship Program',
                'desc' => 'S&T Scholarship Program',
                'subprograms' => [
                    [
                        'title' => 'Graduate',
                        'desc' => 'Development and Administration of S&T Scholarship Programs, Awards and Grants for Graduate Level',
                        'clusters' => []
                    ],
                    [
                        'title' => 'Undergraduate',
                        'desc' => 'Development and Administration of S&T Scholarship Programs, Awards and Grants for Undergraduate Level',
                        'clusters' => []
                    ],
                ]
            ],
            [
                'num' => 'Program 2',
                'title' => 'Educ Dev Program',
                'desc' => 'S&T Educational Development Program',
                'subprograms' => [
                    [
                        'title' => 'RPD STET',
                        'desc' => 'Research, Promotion and Development of S&T Education and Training',
                        'clusters' => [
                            ['title' => 'STEM Promotions'],
                            ['title' => 'STEM Trainings'],
                            ['title' => 'Innovations in Science Education'],
                            ['title' => 'Researches / Studies in STHR and Science Education'],
                        ]
                    ],
                    [
                        'title' => 'PD 997',
                        'desc' => 'Support to the Presidential Committee Implementing PD 997',
                        'clusters' => []
                    ],
                ]
            ],
            [
                'num' => 'Program 3',
                'title' => 'Inst. Services',
                'desc' => 'Institutional Services',
                'subprograms' => [
                    [
                        'title' => 'MISU',
                        'desc' => 'Management Information System Unit',
                        'clusters' => []
                    ],
                    [
                        'title' => 'HRMU',
                        'desc' => 'Human Resource Management Unit',
                        'clusters' => []
                    ],
                    [
                        'title' => 'GSU - BMS',
                        'desc' => 'General Service Unit - Building Maintenance Section',
                        'clusters' => []
                    ],
                    [
                        'title' => 'Planning',
                        'desc' => 'Planning Unit',
                        'clusters' => []
                    ],
                    [
                        'title' => 'GAD',
                        'desc' => 'Gender and Development',
                        'clusters' => []
                    ],
                    [
                        'title' => 'SEI-EA',
                        'desc' => 'SEI - Employee Association',
                        'clusters' => []
                    ],
                ]
            ],
        ];

        foreach($programs as $prog){
            $program = new Program;
            $program->num = $prog['num'];
            $program->title = $prog['desc'];
            $program->save();
            foreach($prog['subprograms'] as $subp){
                $subprogram = new Subprogram;
                $subprogram->title = $subp['desc'];
                $subprogram->title_short = $subp['title'];
                $subprogram->program_id = $program->id;
                $subprogram->save();
                foreach($subp['clusters'] as $clus){
                    $cluster = new Cluster;
                    $cluster->title = $clus['title'];
                    $cluster->subprogram_id = $subprogram->id;
                    $cluster->save();
                }
            }
        }
    }
}
