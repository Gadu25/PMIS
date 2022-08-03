<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Workshop;
use App\Models\Program;
use App\Models\Division;
use App\Models\Project;
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

        return ['programs' => $programs, 'divisions' => $divisions, 'projects' => $projects];
    }
}
