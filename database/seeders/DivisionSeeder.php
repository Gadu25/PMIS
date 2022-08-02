<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Division;
use App\Models\Unit;
use App\Models\Subunit;
use App\Models\Role;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $divisions = [
            [
                'code' => 'OD',
                'name' => 'Office of the Director',
                'roles' => [
                    ['text' => "Provides overall leadership and supervision for the implementation of the Institute's programs and projects; "],
                    ['text' => "Establishes linkages locally and internationally with various sectors for programs and projects in science education and S&T human resource development;"],
                    ['text' => "Recommends policies for the improvement of science education and S&T human resource program which include faculty development, science curriculum development, improvement of laboratory facilities and other program-related activities; and "],
                    ['text' => "Exercises supervision and control over all units of the Institute in the conduct of national program and activities. "],
                ],
                'units' => [
                    [
                        'name' => 'Office of the Deputy Director (ODD)',
                        'roles' => [
                            ['text' => "Provides assistance to the Office of the Director in setting of directions, targets and operational policies of the Institute's programs and projects; "],
                            ['text' => "Assists the Office of the Director in the conceptualization, planning and implementation of programs and projects; "],
                            ['text' => "Gives assistance in establishing linkages with national and international organizations; and "],
                            ['text' => "Assists the Office of the Director in tasks related to fiscal management and other administrative concerns."],
                        ],
                        'subunits' => []
                    ],
                    [
                        'name' => 'Planning Unit',
                        'roles' => [
                            ['text' => "Organization/Coordination/Conduct of SEI Planning-Workshops/Project Review and Assessment; "],
                            ['text' => "Preparation and Submission of Budget Proposals; "],
                            ['text' => "Preparation of Budget Briefing Materials and Attendance to Technical Hearings; "],
                            ['text' => "Collaboration with the Divisions on the preparation and submission of various reports to DOST Central Office, DBM, NEDA and other stakeholders; and "],
                            ['text' => "Coordination on the Conceptualization/ Design/Printing of SEI Annual Report"],
                        ],
                        'subunits' => []
                    ],
                ]
            ],
            [
                'code' => 'STSD',
                'name' => 'Science and Technology Scholarship Division',
                'roles' => [
                    ['text' => "Administration of scholarships, grants and fellowships for the country's human resources in science, mathematics and engineering; and "],
                    ['text' => "Formulation of policies and development of systems and procedures for the effective implementation of the scholarship programs."],
                ],
                'units' => [
                    [
                        'name' => 'Scholarship Administrations and Monitoring Unit',
                        'roles' => [
                            ['text' => "Implements and manages the undergraduate and graduate scholarships in the pure and applied sciences, engineering and science and mathematics education; "],
                            ['text' => "Monitors the scholars' progress through periodic evaluation of their academic performance; "],
                            ['text' => "Processes and releases the financial assistance to the scholars; "],
                            ['text' => "Undertakes test development for use in the screening and identification of scholarship qualifiers and maintain a test item bank; and "],
                            ['text' => "Undertakes data analysis relative to the implementation/administration of the scholarship programs."],
                        ],
                        'subunits' => []
                    ],
                    [
                        'name' => 'Scholarship Technical Support and Service Unit',
                        'roles' => [
                            ['text' => "Reviews and formulates scholarship policies and procedures for the effective and efficient administration of the scholarship programs; "],
                            ['text' => "Creates and maintains the electronic database of scholars for the monitoring and tracking of the scholars; "],
                            ['text' => "Conducts the quality assurance activities; "],
                            ['text' => "Validates documents and issue clearances for NBI, DFA and Bl; "],
                            ['text' => "Conducts S&T learning support mechanisms such as orientation and enrichment programs; psycho-social activities; research mentoring; organization of students' conference on important S&T topics; "],
                            ['text' => "Conducts post scholarship activities such as job fairs, seminars on entrepreneurship; job hunting strategies, etc.; "],
                            ['text' => "Tracks the scholar-graduates' whereabouts; "],
                            ['text' => "Prepares technical reports and generate statistical data; and "],
                            ['text' => "Reviews financial reports and prepare subsequent funding releases. "],
                        ],
                        'subunits' => []
                    ],
                ]
            ],
            [
                'code' => 'SEID',
                'name' => 'Science Education and Innovations Division',
                'roles' => [
                    ['text' => "Development of innovations, strategies and modalities in strengthening of science and mathematics education; "],
                    ['text' => "Conduct of specialized training programs and alternative delivery modes of teaching and learning; "],
                    ['text' => "Development of technology-based educational resources for science and mathematics; and "],
                    ['text' => "Development of cooperative programs, both local and international, for capacity building in science and mathematics education."],
                ],
                'units' => [
                    [
                        'name' => 'Training Unit',
                        'roles' => [
                            ['text' => "Conceptualizes specialized training programs for: Science and Mathematics teachers, DOST-SEI scholars"],
                            ['text' => "Conducts specialized training programs; and "],
                            ['text' => "Monitors and evaluates training programs. "],
                        ],
                        'subunits' => []
                    ],
                    [
                        'name' => 'Program Development Unit',
                        'roles' => [
                            ['text' => "Develops cooperative programs/strategies for capacity building in science and mathematics education; "],
                            ['text' => "Establishes/strengthens local and international linkage for resource generation, exchange of information and collaboration undertaking; and "],
                            ['text' => "Provides technical support for evaluating project proposals for funding. "],
                        ],
                        'subunits' => []
                    ],
                    [
                        'name' => 'Innovations Unit',
                        'roles' => [
                            ['text' => "Develops technology-based educational resources; "],
                            ['text' => "Develops models for science and mathematics education programs; and "],
                            ['text' => "Catalyzes development efforts for strengthening science and mathematics education. "],
                        ],
                        'subunits' => []
                    ]
                ]
            ],
            [
                'code' => 'STMERPD',
                'name' => 'S&T Manpower Education Research and Promotions Division',
                'roles' => [
                    ['text' => "Conduct of policy researches/studies and evaluation of programs on S&T human resources development and science education; "],
                    ['text' => "Development and implementation of science education information, advocacy and promotions program; "],
                    ['text' => "Conduct of science, mathematics and ICT competitions and nurturing of gifted and talented students in science and mathematics including the recognition of outstanding accomplishments and achievements in science and mathematics; and "],
                    ['text' => "Development and implementation of management information systems. "],
                ],
                'units' => [
                    [
                        'name' => 'Promotions Unit',
                        'roles' => [
                            ['text' => "Plans and develops advocacy programs to promote S&T among the youth through the use of multimedia channels; "],
                            ['text' => "Conducts science and mathematics competitions to enhance the interests of the youth in S&T careers; "],
                            ['text' => "Supports and implements programs and projects to nurture the gifted in science and mathematics; and "],
                            ['text' => "Plans and implements programs to recognize outstanding accomplishments and achievements in S&T by the youth and in science education by individuals and institutions."],
                        ],
                        'subunits' => []
                    ],
                    [
                        'name' => 'Research Unit',
                        'roles' => [
                            ['text' => "Plans and undertakes policy studies on S&T education; "],
                            ['text' => "Evaluates programs and projects in S&T education and human resource development; and "],
                            ['text' => "Supports the country's participation in international assessment studies for science and mathematics education, as needed. "],
                        ],
                        'subunits' => []
                    ],
                    [
                        'name' => 'Management Information System Unit',
                        'roles' => [
                            ['text' => "Develops and maintains the Institute's Local Area Network and Internet Connectivity and coordinates with the DOST-MIS regarding compliance with network operations requirements; "],
                            ['text' => "Plans and implements the Institute's Information Systems and IT Infrastructure;"],
                            ['text' => "Develops and maintains the various websites of the Institute including web and content management; and "],
                            ['text' => "Conducts network users' training to enable the efficient use of the network and information systems of the Institute. "],
                        ],
                        'subunits' => []
                    ],
                ]
            ],
            [
                'code' => 'FAD',
                'name' => 'Finance and Administrative Division',
                'roles' => [
                    ['text' => "Provides the Institute with economical, efficient and effective support services relating to personnel, information, records, supplies and materials, equipment, collection and disbursements, budgetary estimates and requirements, and security and custodial works. "],
                ],
                'units' => [
                    [
                        'name' => 'Finance Units',
                        'roles' => [],
                        'subunits' => [
                            [
                                'name' => 'Budget',
                                'roles' => [
                                    ['text' => "Facilitates the preparation of budget estimates/proposal in coordination with Planning and various Divisions; "],
                                    ['text' => "Prepares reports/documents required by DBM;"],
                                    ['text' => "Prepares the budget execution documents; and "],
                                    ['text' => "Allocates/monitors the budget utilization of the divisions. "],
                                ]
                            ],
                            [
                                'name' => 'Accounting',
                                'roles' => [
                                    ['text' => "Prepares and certifies availability of funds; "],
                                    ['text' => "Certifies correctness of disbursements, journals and financial reports for submission to various agencies; "],
                                    ['text' => "Release and liquidation of Financial Assistance to NGAs/NGOs/POs; "],
                                    ['text' => "Release and liquidation of Cash Advances to Officers/ Employees/Others; and "],
                                ]
                            ],
                            [
                                'name' => 'Collection and Disbursement',
                                'roles' => [
                                    ['text' => "Collection and Disbursement of the Salaries/allowances and other benefits of SEI Employees through Payroll Credit System Validation (PACSVAL); "],
                                    ['text' => "Management of various collections of the institute and deposit the same to LBP; "],
                                    ['text' => "Checks/LDDAP-ADA preparation for various transactions; "],
                                    ['text' => "Preparation of various Financial Reports; and "],
                                    ['text' => "Management of local on-line systems."],
                                ]
                            ],
                        ]
                    ],
                    [
                        'name' => 'Administrative Units',
                        'roles' => [],
                        'subunits' => [
                            [
                                'name' => 'Human Resource Management',
                                'roles' => [
                                    ['text' => "Recruitment, Selection and Placement; "],
                                    ['text' => "Human Resource Development; "],
                                    ['text' => "Timekeeping and Leave Administration; "],
                                    ['text' => "Payroll Preparation for Compensation and benefits;"],
                                    ['text' => "Updating and Issuance of Personnel Records and; "],
                                    ['text' => "Maintenance of 201 Files"],
                                ]
                            ],
                            [
                                'name' => 'Records',
                                'roles' => [
                                    ['text' => "Manages the dissemination of incoming and outgoing documents in the institute. "],
                                    ['text' => "Assumes full responsibility for the custody and safekeeping of official records holdings of the agency. "],
                                    ['text' => "Coordinates with various government and private linkages of SEI of the documents needed of the institute. "],
                                ]
                            ],
                            [
                                'name' => 'General Services',
                                'roles' => [
                                    ['text' => "Procurement of supplies, materials and equipment; "],
                                    ['text' => "Maintains inventory of properties; "],
                                    ['text' => "Maintenance of vehicles, air-conditioning units and other facilities; and "],
                                    ['text' => "Monitors the upkeep of the DOST-SEI Offices on physical, janitorial and security services, including the water and electricity lines"],
                                ]
                            ],
                        ]
                    ],
                ]
            ],
        ];

        foreach($divisions as $div){
            $division = new Division;
            $division->code = $div['code'];
            $division->name = $div['name'];
            $division->save();

            foreach($div['roles'] as $rol){
                $role = new Role;
                $role->body = $rol['text'];
                $division->roles()->save($role);
            }

            foreach($div['units'] as $uni){
                $unit = new Unit;
                $unit->name = $uni['name'];
                $unit->division_id = $division->id;
                $unit->save();

                foreach($uni['roles'] as $rol){
                    $role = new Role;
                    $role->body = $rol['text'];
                    $unit->roles()->save($role);
                }

                foreach($uni['subunits'] as $sub){
                    $subunit = new Subunit;
                    $subunit->name = $sub['name'];
                    $subunit->unit_id = $unit->id;
                    $subunit->save();

                    foreach($sub['roles'] as $rol){
                        $role = new Role;
                        $role->body = $rol['text'];
                        $subunit->roles()->save($role);
                    }
                }
            }
        }
    }
}
