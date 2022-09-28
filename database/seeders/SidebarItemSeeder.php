<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SidebarItem;
use App\Models\SidebarRole;

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
            [
                'name'  => 'Divisions and Units',
                'roles' => [
                    [
                        'title'       => 'Access Divisions and Units',
                        'code'        => 'dau_access',
                        'description' => 'Access Divisions and Units'
                    ],
                ]
            ],
            [
                'name'  => 'Programs and Projects',
                'roles' => [
                    [
                        'title'       => 'Access Programs and Projects',
                        'code'        => 'pap_access',
                        'description' => 'Access Programs and Projects'
                    ],
                ]
            ],
            [
                'name'  => 'Events Management',
                'roles' => [
                    [
                        'title'       => 'Access Events Management',
                        'code'        => 'em_access',
                        'description' => 'Access Events Management'
                    ],
                ]
            ],
            [
                'name'  => 'Budget Executive Documents',
                'roles' => [
                    [
                        'title'       => 'Access Budget Executive Documents',
                        'code'        => 'bed_access',
                        'description' => 'Access Budget Executive Documents'
                    ],
                    [
                        'title'       => 'Add Workshop',
                        'code'        => 'workshop_add',
                        'description' => 'Add new operational planning workshop'
                    ],
                    [
                        'title'       => 'Edit Workshop',
                        'code'        => 'workshop_edit',
                        'description' => 'Edit existing operational planning workshop'
                    ],
                    [
                        'title'       => 'Delete Workshop',
                        'code'        => 'workshop_delete',
                        'description' => 'Delete existing operational planning workshop'
                    ],
                    [
                        'title'       => 'Sync Annex E',
                        'code'        => 'annex_e_sync',
                        'description' => 'Syncing of Annex E records based on selected filters'
                    ],
                    [
                        'title'       => 'Edit Annex E Indicators',
                        'code'        => 'annex_e_edit_indicators',
                        'description' => 'Edit performance indicators of Annex E items'
                    ],
                    [
                        'title'       => 'Edit Annex E Details',
                        'code'        => 'annex_e_edit_details',
                        'description' => 'Edit performance indicator details of Annex E items'
                    ],
                    [
                        'title'       => 'Edit Annex E Other Indicators',
                        'code'        => 'annex_e_edit_other_indicators',
                        'description' => 'Edit outcome and output indicators on Annex E'
                    ],
                    [
                        'title'       => 'Sync Annex F',
                        'code'        => 'annex_f_sync',
                        'description' => 'Syncing of Annex F records based on selected filters'
                    ],
                    [
                        'title'       => 'Edit Annex F Details',
                        'code'        => 'annex_f_edit_details',
                        'description' => 'Edit details of Annex F items'
                    ],
                    [
                        'title'       => 'Publish Projects',
                        'code'        => 'annex_one_publish_projects',
                        'description' => 'Publish presented projects during operational planning workshop to Annexes E & F'
                    ],
                    [
                        'title'       => 'Add Annex One',
                        'code'        => 'annex_one_add',
                        'description' => 'Add project presented during the operational planning workshop'
                    ],
                    [
                        'title'       => 'Edit Annex One',
                        'code'        => 'annex_one_edit',
                        'description' => 'Edit existing projects of annex one'
                    ],
                    [
                        'title'       => 'Delete Annex One',
                        'code'        => 'annex_one_delete',
                        'description' => 'Delete existing projects of annex one'
                    ],
                    [
                        'title'       => 'Add Common Indicator',
                        'code'        => 'common_indicator_add',
                        'description' => 'Add common performance, outcome and output indicators that will appear on program selected'
                    ],
                    [
                        'title'       => 'Edit Common Indicator',
                        'code'        => 'common_indicator_edit',
                        'description' => 'Edit of common performance, outcome and output indicators'
                    ],
                    [
                        'title'       => 'Delete Common Indicator',
                        'code'        => 'common_indicator_delete',
                        'description' => 'Delete of common performance, outcome and output indicators'
                    ],
                    [
                        'title'       => 'Add Workshop Tag',
                        'code'        => 'workshop_tag_add',
                        'description' => 'Add workshop tag'
                    ],
                    [
                        'title'       => 'Edit Workshop Tag',
                        'code'        => 'workshop_tag_edit',
                        'description' => 'Edit workshop tag'
                    ],
                    [
                        'title'       => 'Delete Workshop Tag',
                        'code'        => 'workshop_tag_delete',
                        'description' => 'Delete workshop tag'
                    ],
                    [
                        'title'       => 'Export Annex E',
                        'code'        => 'annex_e_export',
                        'description' => 'Exporting of Annex E Report'
                    ],
                    [
                        'title'       => 'Export Annex F',
                        'code'        => 'annex_f_export',
                        'description' => 'Exporting of Annex F Report'
                    ],
                ]
            ],
            [
                'name'  => 'Resources and Publications',
                'roles' => [
                    [
                        'title'       => 'Access Resources and Publications',
                        'code'        => 'rap_access',
                        'description' => 'Access Resources and Publications'
                    ],
                ]
            ],
            [
                'name'  => 'Reports',
                'roles' => [
                    [
                        'title'       => 'Access Reports',
                        'code'        => 'rep_access',
                        'description' => 'Access Reports'
                    ],
                ]
            ],
            [
                'name'  => 'Strategic Planning',
                'roles' => [
                    [
                        'title'       => 'Access Strategic Planning',
                        'code'        => 'sp_access',
                        'description' => 'Access Strategic Planning'
                    ],
                ]
            ],
            [
                'name'  => 'User Management',
                'roles' => [
                    [
                        'title'       => 'Access User Management',
                        'code'        => 'um_access',
                        'description' => 'Access User Management'
                    ],
                ]
            ]
        ];

        foreach($items as $item){
            $sidebaritem = new SidebarItem;
            $sidebaritem->name = $item['name'];
            $sidebaritem->save();
            foreach($item['roles'] as $role){
                $sidebarrole = new SidebarRole;
                $sidebarrole->title           = $role['title'];
                $sidebarrole->code            = $role['code'];
                $sidebarrole->description     = $role['description'];
                $sidebarrole->sidebar_item_id = $sidebaritem->id;
                $sidebarrole->save();
            }
        }
    }
}
