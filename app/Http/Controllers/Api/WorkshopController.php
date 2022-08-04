<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Workshop;
use App\Models\Program;
use App\Models\Division;
use App\Models\Project;
use App\Models\AnnexOne;
use App\Models\AnnexOneSub;
use App\Models\AnnexOneFund;

use DB;

class WorkshopController extends Controller
{
    public function index(){
        return $this->getWorkshops();
    }

    private function getWorkshops(){
        $workshops = Workshop::orderBy('start', 'desc')->get();
        foreach($workshops as $workshop){
            $this->formatWorkshop($workshop);
        }
        return $workshops;
    }

    public function show($id){
        $workshop = Workshop::findOrFail($id);
        $this->formatWorkshop($workshop);
        return $workshop;
    }

    public function store(Request $request){
        DB::beginTransaction();
        try{
            $workshop = new Workshop;
            $workshop->start = $request['start'];
            $workshop->end = $request['end'];
            $workshop->save();

            DB::commit();
            return ['message' => 'Workshop added!', 'workshops' => $this->getWorkshops()];

        }
        catch(\Exception $e){
            DB::rollback();
            return ['message' => 'Something went wrong', 'errors' => $e->getMessage()];
        }
    }

    public function update(Request $request, $id){
        DB::beginTransaction();
        try {
            $workshop = Workshop::findOrFail($id);
            $workshop->start = $request['start'];
            $workshop->end = $request['end'];
            $workshop->save();
            
            DB::commit();
            return ['message' => 'Workshop updated!', 'workshops' => $this->getWorkshops()];
        }
        catch(\Exception $e){
            DB::rollback();
            return ['message' => 'Something went wrong', 'errors' => $e->getMessage()];
        }
    }

    public function destroy($id){
        DB::beginTransaction();
        try {
            $workshop = Workshop::findOrFail($id);
            $workshop->delete();

            DB::commit();
            return ['message' => 'Workshop deleted!', 'workshops' => $this->getWorkshops()];
        }
        catch(\Exception $e){
            DB::rollback();
            return ['message' => 'Something went wrong', 'errors' => $e->getMessage()];
        }
    }

    private function formatWorkshop($workshop){
        $startDate = explode('-', $workshop->start);
        $startMonth = date('F', mktime(0, 0, 0, (int)$startDate[1], 10));
        $startDay = (int)$startDate[2]; 

        $endDate = explode('-', $workshop->end);
        $endMonth = date('F', mktime(0, 0, 0, (int)$endDate[1], 10));
        $endDay = (int)$endDate[2]; 

        $workshop->date = ($startMonth == $endMonth) ? $date = $startMonth.' '.$startDay.'-'.$endDay.', '.$startDate[0] : $date = $startMonth.' '.$startDay.' - '.$endMonth.' '.$endDay.', '.$startDate[0];
        
        $workshop->year = $startDate[0];
    }
    
    // Annex One
    public function getAnnexOne($workshopId){
        $workshopYear = $this->getWorkshopYear($workshopId);
        $divisions = Division::select('id','code')->get();
        $annexones = AnnexOne::where('workshop_id', $workshopId)->orderBy('id','asc')->get();

        $temp = [];
        foreach($annexones as $annexone){
            $header = ($annexone->header_type == 'Subprogram') ? $annexone->project->subprogram->title_short : 
                    (($annexone->header_type == 'Unit') ? ($annexone->project->subunit_id) ? $annexone->project->subunit->name : $annexone->project->unit->name : 'None');
            $annexone->funds;
            foreach($annexone->subs as $sub){
                $sub->funds;
            }
            $divId = $annexone->project->division_id;
            if(!array_key_exists($divId, $temp)){
                $temp[$divId]['headers'] = [];
                $temp[$divId]['funds'] = [];
            }
            if(empty($temp[$divId]['headers'])){
                array_push($temp[$divId]['headers'], [ 'name' => $header, 'funds' => [], 'items' => [] ] );
            }
            if(!in_array($header, $temp[$divId]['headers'])){
                array_push($temp[$divId]['headers'], [ 'name' => $header, 'funds' => [], 'items' => [] ] );
                // $temp[$divId]['headers'][$header]['items'] = [];
                // $temp[$divId]['headers'][$header]['funds'] = [];
                // array_push($temp[$divId]['headers'][$header]['name'], );
            }

            // array_push($temp[$annexone->project->division_id]['headers']['items'], $annexone);

        }
        // $grouped = $annexones->groupBy(['project.division_id', 'header']);
        // $array = $grouped->toArray();
        // foreach($array as $value){
        //     foreach($value as $header){
                
        //     }
        // }

        // foreach($divisions as $division){
            // if(array_key_exists($division->id, $array)){
            //     // $division->items = $grouped[$division->id];
            // }
            // else{
            //     $division->items = [];
            //     $division->funds = [];
            // }
        // }
        return $temp;
        // return ['div' => $divisions, 'grouped' => $grouped, 'array' => $array];
    }

    public function storeAnnexOne(Request $request){
        DB::beginTransaction();
        try {
            foreach($request['projects'] as $project){
                $annexone = new AnnexOne;
                $annexone->source_of_funds = $request['source'];
                $annexone->header_type     = $request['header'];
                $annexone->workshop_id     = $request['workshop_id'];
                $annexone->project_id      = $project['project_id'];
                $annexone->save();

                $this->saveFunds($annexone, $project, $request['workshop_year']);

                foreach($project['subprojects'] as $subproject){
                    if($subproject['state']){
                        $annexonesub = new AnnexOneSub;
                        $annexonesub->subproject_id = $subproject['subproject_id'];
                        $annexonesub->save();

                        $this->saveFunds($annexonesub, $subproject, $request['workshop_year']);
                    }
                }
            }

            DB::commit();
            return ['message' => 'Successfully added!', 'annexones' => []];
        }
        catch (\Exception $e){
            DB::rollback();
            return ['message' => 'Something went wrong', 'errors' => $e->getMessage()];
        }
    }

    private function saveFunds($parent, $data, $year){
        $cols = ['col1', 'col2', 'col3', 'col4', 'col5', 'col6', 'col7'];
        foreach($cols as $key => $col){
            $amount = $this->formatAmount($data[$col]);
            if($amount != 0){
                $fund = new AnnexOneFund;
                $fund->year   = $year + (int)$key;
                $fund->amount = $amount;
                $parent->funds()->save($fund);
            }
        }
    }


    // Common

    public function getOptions(){
        $programs = Program::orderBy('id', 'asc')->get();
        foreach($programs as $program){
            foreach($program->subprograms as $subprogram){
                $subprogram->clusters;
            }
        }

        $divisions = Division::orderBy('id', 'asc')->get();
        foreach($divisions as $division){
            foreach($division->units as $unit){
                $unit->subunits;
            }
        }

        $projects = Project::orderBy('id', 'asc')->get();
        foreach($projects as $project){
            $project->subprojects;
        }

        return ['programs' => $programs, 'divisions' => $divisions, 'projects' => $projects];
    }
    
    private function formatAmount($amount){
        $newAmount = str_replace(',', '', $amount);
        return (float)$newAmount;
    }

    private function getWorkshopYear($id){
        $workshop = Workshop::findOrFail($id);
        $str = explode('-', $workshop->start);
        return (int)$str[0];
    }
}
