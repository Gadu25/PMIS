<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Workshop;
use App\Models\Program;
use App\Models\Division;
use App\Models\Project;

use App\Models\AnnexE;
use App\Models\AnnexESub;

use App\Models\AnnexF;
use App\Models\AnnexFSub;
use App\Models\AnnexFActivity;
use App\Models\AnnexFFund;

use App\Models\AnnexOne;
use App\Models\AnnexOneSub;
use App\Models\AnnexOneFund;

use App\Models\CommonIndicator;
use App\Models\CommonIndicatorSub;

use DB;

class WorkshopController extends Controller
{
    // Workshop
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
    
        $tester = AnnexF::where('workshop_id', $workshop->id)->first();
        $workshop->published = ($tester !== null);
    }

    // Annex E
    public function getAnnexE(Request $request){
        DB::beginTransaction();
        try{
            // if()
            $annexes = AnnexE::with(['project', 'subs'])
                ->whereHas('project', function($q) use($request){
                    if($request['type'] == 'Program'){
                        $q->where('program_id', $request['program_id']);
                        if($request['subprogram_id'] != 0){$q->where('subprogram_id', $request['subprogram_id']);}
                        if($request['cluster_id'] != 0){$q->where('cluster_id', $request['cluster_id']);}
                    }
                    else{
                        $q->where('division_id', $request['division_id']);
                        if($request['unit_id'] != 0){$q->where('unit_id', $request['unit_id']);}
                        if($request['subunit_id'] != 0){$q->where('subunit_id', $request['subunit_id']);}
                    }
                })
                ->where('status', 'Submitted')->get();

            foreach($annexes as $annexe){
                foreach($annexe->subs as $sub){
                    $sub->subproject;
                }
            }
            return $annexes;
        }
        catch(\Exception $e){
            DB::rollback();
            return ['message' => 'Something went wrong!', 'errors' => $e->getMessage()];
        }
        // $annexes = AnnexE::where('workshop_id', $workshopId)->orderBy('id', 'asc')->get();
        // foreach($annexes as $annexe){
        //     $project = $annexe->project;
        //     $annexe->program_header = ($project->cluster_id !== null) ? $project->cluster->title : (($project->subprogram_id !== null) ? $project->subprogram->title_short : '');
        //     $annexe->division_header = ($project->subunit_id !== null) ? $project->subunit->name : (($project->unit_id !== null) ? $project->unit->name : '');
        //     foreach($annexe->subs as $annexesub){
        //         $annexesub->subproject;
        //     }
        // }
        // $result = [];
        // $result['divisions'] = $annexes->groupBy(['project.division_id', 'division_header']);
        // $result['programs'] = $annexes->groupBy(['project.program_id', 'program_header']);
        // return $result;
    }

    public function storeAnnexE(Request $request){
        
    }
    
    public function updateAnnexE(Request $request, $id){
        
    }
    
    public function destroyAnnexE($id){

    }

    // Annex F 
    public function getAnnexF($workshopId){
        $annexfs = AnnexF::where('workshop_id', $workshopId)->orderBy('id', 'asc')->get();
        foreach($annexfs as $annexf){
            $annexf->activities;
            $annexf->funds;
            $project = $annexf->projects[0];
            $annexf->title = (sizeof($annexf->projects) > 1) ? $project->subprogram->title : $project->title;
            // $subpTitleType = $project->program->title == 'S&T Scholarship Program' ? 'title_short' : 'title';
            // $headerAppend = (($project->cluster_id) ? ' - '.$project->cluster->title : (($project->subprogram_id) ? ' - '.$project->subprogram[$subpTitleType] : ''));
            $annexf->program = $project->program->title;
            $annexf->subprogram = ($project->subprogram_id) ? $project->subprogram->title_short : '';
            $annexf->cluster = ($project->cluster_id) ? $project->cluster->title : '';
            foreach($annexf->subs as $sub){
                $sub->subproject;
                $sub->activities;
                $sub->funds;
            }
        }
        $grouped = $annexfs->groupBy(['program', 'subprogram', 'cluster']);
        return $grouped;
    }

    public function storeAnnexF(Request $request){
        DB::beginTransaction();
        try {
            foreach($request['projects'] as $project){
                $annexf = new AnnexF;
                $annexf->workshop_id = $request['workshop_id'];
                $annexf->save();

                $projects = ($project['multiple']) ? $project['project_ids'] : [$project['project_id']];
                $annexf->projects()->sync($projects);

                foreach($project['subprojects'] as $subproject){
                    if($subproject['state']){
                        $annexfsub = new AnnexFSub;
                        $annexfsub->annex_f_id = $annexf->id;
                        $annexfsub->subproject_id = $subproject['subproject_id'];
                        $annexfsub->save();
                    }
                }
            }

            DB::commit();
            return ['message' => 'Successfully added!', 'annexfs' => $this->getAnnexF($request['workshop_id'])];
        }
        catch(\Exception $e){
            DB::rollback();
            return ['message' => 'Something went wrong', 'errors' => $e->getMessage()];
        }
    }
    
    public function updateAnnexF(Request $request, $id){
        DB::beginTransaction();
        try {
            $project = $request['projects'][0];
            $annexf = AnnexF::findOrFail($id);
            $annexf->remarks = $request['remarks'];
            $annexf->save();

            $projects = ($project['multiple']) ? $project['project_ids'] : [$project['project_id']];
            $annexf->projects()->sync($projects);

            foreach($request['activities'] as $key => $activityArray){
                foreach($activityArray as $act){
                    if($act['description'] != ''){
                        $activity = $act['id'] ? AnnexFActivity::findOrFail($act['id']) : new AnnexFActivity;
                        $activity->description = $act['description'];
                        $activity->table_key = $key;
                        $annexf->activities()->save($activity);
                    }
                    if($act['description'] == '' && $act['id']){
                        $activity = AnnexFActivity::findOrFail($act['id']);
                        $activity->delete();
                    }
                }
            }

            foreach($request['funds'] as $key => $value){
                $amount = $this->formatAmount($value['amount']);
                if($amount > 0){
                    $fund = $value['id'] ? AnnexFFund::findOrFail($value['id']) : new AnnexFFund;
                    $fund->amount = $amount;
                    $fund->table_key = $key;
                    $annexf->funds()->save($fund);
                }
                else if($value['id']){
                    $fund = AnnexFFund::findOrFail($value['id']);
                    $fund->delete();
                }
            }
            
            foreach($project['subprojects'] as $subproject){
                $annexfsub = $subproject['id'] ? AnnexFSub::findOrFail($subproject['id']) : new AnnexFSub;
                if($subproject['state']){
                    $annexfsub->annex_f_id = $annexf->id;
                    $annexfsub->subproject_id = $subproject['subproject_id'];
                    $annexfsub->remarks = $subproject['remarks'];
                    $annexfsub->save();

                    foreach($subproject['activities'] as $key => $activityArray){
                        foreach($activityArray as $act){
                            if($act['description'] != ''){
                                $activity = $act['id'] ? AnnexFActivity::findOrFail($act['id']) : new AnnexFActivity;
                                $activity->description = $act['description'];
                                $activity->table_key = $key;
                                $annexfsub->activities()->save($activity);
                            }
                            else if($act['id']){
                                $activity = AnnexFActivity::findOrFail($act['id']);
                                $activity->delete();
                            }
                        }
                    }

                    foreach($subproject['funds'] as $key => $value){
                        $amount = $this->formatAmount($value['amount']);
                        if($amount > 0){
                            $fund = $value['id'] ? AnnexFFund::findOrFail($value['id']) : new AnnexFFund;
                            $fund->amount = $amount;
                            $fund->table_key = $key;
                            $annexfsub->funds()->save($fund);
                        }
                        else if($value['id']){
                            $fund = AnnexFFund::findOrFail($value['id']);
                            $fund->delete();
                        }
                    }
                }
                else{
                    $annexfsub->activities()->delete();
                    $annexfsub->funds()->delete();
                    $annexfsub->delete();
                }
            }

            AnnexFActivity::whereIn('id', $request['activityIds'])->delete();

            DB::commit();
            return ['message' => 'Successfully saved!', 'annexfs' => $this->getAnnexF($request['workshop_id'])];
        }
        catch(\Exception $e){
            DB::rollback();
            return ['message' => 'Something went wrong', 'errors' => $e->getMessage(), 'test' => $request['activityIds']];
        }
    }
    
    public function destroyAnnexF($id){
        DB::beginTransaction();
        try {
            $annexf = AnnexF::findOrFail($id);
            foreach($annexf->subs as $sub){
                $sub->activities()->delete();
                $sub->funds()->delete();
                $sub->delete();
            }
            $annexf->activities()->delete();
            $annexf->funds()->delete();
            $annexf->delete();

            DB::commit();
            return ['message' => 'Successfully removed!', 'annexfs' => $this->getAnnexF($annexf->workshop_id)];
        }
        catch(\Exception $e){
            DB::rollback();
            return ['message' => 'Something went wrong', 'errors' => $e->getMessage(), 'test' => $request['activityIds']];
        }
    }
    
    // Annex One
    public function getAnnexOne($workshopId){
        $workshopYear = $this->getWorkshopYear($workshopId);
        $divisions = Division::select('id','code')->get();
        $annexones = AnnexOne::where('workshop_id', $workshopId)->orderBy('id','asc')->get();

        $headers = [];
        foreach($annexones as $annexone){
            $annexone->funds;
            $project = $annexone->project;
            $annexone->header = ($annexone->header_type == 'Subprogram') ? $annexone->project->subprogram->title_short : 
                                (($annexone->header_type == 'Unit') ? ($annexone->project->subunit_id) ? $annexone->project->subunit->name : $annexone->project->unit->name : 'None');
            foreach($annexone->subs as $sub){
                $sub->funds;
                $sub->subproject;
            }

            $annexone->division = $project->division->code.(($project->subunit_id !== null) ? ' - '.$project->subunit->name : (($project->unit_id !== null) ? ' - '.$project->unit->name : ''));
            $annexone->program = $project->program->title.(($project->cluster_id !== null) ? ' - '.$project->cluster->title : (($project->subprogram_id !== null) ? ' - '.$project->subprogram->title_short : ''));
        }

        $grouped = $annexones->groupBy(['project.division.code','source_of_funds','header']);
        // $array = $grouped-;

        return $grouped;
    }

    public function storeAnnexOne(Request $request){
        DB::beginTransaction();
        try {
            $year = $request['workshop_year'];
            foreach($request['projects'] as $project){
                $annexone = new AnnexOne;
                $annexone->source_of_funds = $request['source'];
                $annexone->header_type     = $request['header'];
                $annexone->workshop_id     = $request['workshop_id'];
                $annexone->project_id      = $project['project_id'];
                $annexone->save();
                $this->saveFunds($annexone, $project, $year);

                foreach($project['subprojects'] as $subproject){
                    if($subproject['state']){
                        $annexonesub = new AnnexOneSub;
                        $annexonesub->annex_one_id = $annexone->id;
                        $annexonesub->subproject_id = $subproject['subproject_id'];
                        $annexonesub->save();
                        $this->saveFunds($annexonesub, $subproject, $year);
                    }
                }
            }

            DB::commit();
            return ['message' => 'Successfully added!', 'annexones' => $this->getAnnexOne($request['workshop_id'])];
        }
        catch (\Exception $e){
            DB::rollback();
            return ['message' => 'Something went wrong', 'errors' => $e->getMessage()];
        }
    }

    public function updateAnnexOne(Request $request, $id){
        DB::beginTransaction();
        try {
            $year = $request['workshop_year'];
            $project = $request['projects'][0];
            $annexone = AnnexOne::findOrFail($id);
            $annexone->source_of_funds = $request['source'];
            $annexone->header_type     = $request['header'];
            $this->saveFunds($annexone, $project, $year, true, false);
            
            foreach($project['subprojects'] as $subproject){
                $subId = $subproject['id'];
                if($subproject['state']){
                    $annexonesub = ($subId) ? AnnexOneSub::findOrFail($subId) : new AnnexOneSub;
                    $annexonesub->annex_one_id = $annexone->id;
                    $annexonesub->subproject_id = $subproject['subproject_id'];
                    $annexonesub->save();
                    $this->saveFunds($annexonesub, $subproject, $year, true, true);
                }
                else{
                    if($subId){
                        $annexonesub = AnnexOneSub::findOrFail($subId);
                        $annexonesub->funds()->delete();
                        $annexonesub->delete();
                    }
                }
            }

            DB::commit();
            return ['message' => 'Successfully updated!', 'annexones' => $this->getAnnexOne($request['workshop_id'])];
        }
        catch (\Exception $e){
            DB::rollback();
            return ['message' => 'Something went wrong', 'errors' => $e->getMessage()];
        }
    }

    public function destroyAnnexOne($id){
        DB::beginTransaction();
        try {
            $annexone = AnnexOne::findOrFail($id);
            foreach($annexone->subs as $annexonesub){
                $annexonesub->funds()->delete();
            }
            $annexone->funds()->delete();
            $annexone->delete();

            DB::commit();
            return ['message' => 'Successfully deleted!', 'annexones' => $this->getAnnexOne($annexone->workshop_id)];
        }
        catch (\Exception $e){
            DB::rollback();
            return ['message' => 'Something went wrong', 'errors' => $e->getMessage()];
        }
    }

    public function publishAnnexOneProjects($workshopId){
        DB::beginTransaction();
        try {
            $annexones = AnnexOne::where('workshop_id', $workshopId)->get();
            foreach($annexones as $annexone){
                $annexe = new AnnexE;
                $annexe->workshop_id = $workshopId;
                $annexe->project_id = $annexone->project_id;
                $annexe->status = 'New';
                $annexe->save();

                $annexf = new AnnexF;
                $annexf->workshop_id = $workshopId;
                $annexf->status = 'New';
                $annexf->save();
                $annexf->projects()->sync([$annexone->project_id]);

                foreach($annexone->subs as $annexonesub){
                    $annexesub = new AnnexESub;
                    $annexesub->annex_e_id = $annexe->id;
                    $annexesub->subproject_id = $annexonesub->subproject_id;
                    $annexesub->save();

                    $annexfsub = new AnnexFSub;
                    $annexfsub->annex_f_id = $annexf->id;
                    $annexfsub->subproject_id = $annexonesub->subproject_id;
                    $annexfsub->save();
                }
            }

            DB::commit();
            return ['message' => 'Projects Published!'];
        }
        catch(\Exception $e){
            DB::rollback();
            return ['message' => 'Something went wrong!', 'errors' => $e->getMessage()];
        }
    }

    private function saveFunds($parent, $data, $year, $update = false, $isSub = false){
        $cols = ['col1', 'col2', 'col3', 'col4', 'col5', 'col6', 'col7'];
        $amounts = [];
        $funds = [];
        foreach($cols as $key => $col){
            $col_year = $year + (int)$key;
            $amount = $this->formatAmount($data[$col]);
            if(!$update){
                if($amount != 0){
                    $this->basicSave($parent, $col_year, $amount);
                }
            }
            else{
                $model = ($isSub) ? 'App\Models\AnnexOneSub' : 'App\Models\AnnexOne';
                $fund = AnnexOneFund::where('year', $col_year)->where('fundable_type', $model)->where('fundable_id', $parent->id)->first();
                if($amount != 0){
                    if($fund){
                        $fund->amount = $amount;
                        $fund->save();
                    }
                    else{
                        $this->basicSave($parent, $col_year, $amount);
                    }
                }
                else{
                    if($fund){
                        $fund->delete();
                    }
                }
            }
        }
    }

    private function basicSave($parent, $year, $amount){
        $fund = new AnnexOneFund;
        $fund->year = $year;
        $fund->amount = $amount;
        $parent->funds()->save($fund);
    }

    // Common Indicators
    public function getCommonIndicator($workshopId){
        $commonindicators = CommonIndicator::where('workshop_id', $workshopId)->orderBy('id', 'asc')->get();
        foreach($commonindicators as $indicator){
            $indicator->tags;
            $indicator->header = (($indicator->cluster_id) ? $indicator->cluster->title : (($indicator->subprogram_id) ? $indicator->subprogram->title : ''));
            $indicator->display_type = ($indicator->type == 'Performance') ? $indicator->type : 'Other';
            if($indicator->program_id == 1){
                $indicator->header = ($indicator->subprogram_id) ? $indicator->subprogram->title_short : '';
            }
            foreach($indicator->subindicators as $subindicator){
                $subindicator->tags;
            }
        }
        $grouped = $commonindicators->groupBy(['program_id', 'display_type', 'header']);
        return $grouped;
    }

    public function storeCommonIndicator(Request $request){
        DB::beginTransaction();
        try {
            foreach($request['indicators'] as $indicator){
                $commonindicator = new CommonIndicator;
                $commonindicator->type = $request['type'];
                $commonindicator->description = $indicator['description'];
                $commonindicator->program_id = $request['program_id'];
                $commonindicator->subprogram_id = ($request['subprogram_id'] == 0) ? null : $request['subprogram_id'];
                $commonindicator->cluster_id = ($request['cluster_id'] == 0) ? null : $request['cluster_id'];
                $commonindicator->workshop_id = $request['workshop_id'];
                $commonindicator->save();
                
                $tags = ($request['type'] == 'Performance') ? [$indicator['tag']] : $indicator['tags'];
                $commonindicator->tags()->sync($tags);
                foreach($indicator['subs'] as $sub){
                    $commonindicatorsub = new CommonIndicatorSub;
                    $commonindicatorsub->description = $sub['description'];
                    $commonindicatorsub->common_indicator_id = $commonindicator->id;
                    $commonindicatorsub->save();

                    $tags = ($request['type'] == 'Performance') ? [$sub['tag']] : $sub['tags'];
                    $commonindicatorsub->tags()->sync($tags);
                }
            }

            DB::commit();
            return ['message' => 'Successfully added!', 'commonindicators' => $this->getCommonIndicator($request['workshop_id'])];
        }
        catch (\Exception $e){
            DB::rollback();
            return ['message' => 'Something went wrong', 'errors' => $e->getMessage()];
        }
    }

    public function updateCommonIndicator(Request $request, $id){
        DB::beginTransaction();
        try {
            foreach($request['indicators'] as $indicator){
                $commonindicator = CommonIndicator::findOrFail($id);
                $commonindicator->type = $request['type'];
                $commonindicator->description = $indicator['description'];
                $commonindicator->subprogram_id = ($request['subprogram_id'] == 0) ? null : $request['subprogram_id'];
                $commonindicator->cluster_id = ($request['cluster_id'] == 0) ? null : $request['cluster_id'];
                $commonindicator->save();

                $tags = ($request['type'] == 'Performance') ? [$indicator['tag']] : $indicator['tags'];
                $commonindicator->tags()->sync($tags);

                $tempIds = [];
                foreach($indicator['subs'] as $sub){
                    $commonindicatorsub = ($sub['id']) ? CommonIndicatorSub::findOrFail($sub['id']) : new CommonIndicatorSub;
                    $commonindicatorsub->description = $sub['description'];
                    $commonindicatorsub->common_indicator_id = $commonindicator->id;
                    $commonindicatorsub->save();

                    $tags = ($request['type'] == 'Performance') ? [$sub['tag']] : $sub['tags'];
                    $commonindicatorsub->tags()->sync($tags);

                    if($sub['id']){
                        array_push($tempIds, $sub['id']);
                    }
                }

                $forDelete = array_diff($request['subIds'], $tempIds);
                foreach($forDelete as $id){
                    $commonindicatorsub = CommonIndicatorSub::findOrFail($id);
                    $commonindicatorsub->tags()->detach();
                    $commonindicatorsub->delete();
                }
            }
            DB::commit();
            return ['message' => 'Successfully saved!', 'commonindicators' => $this->getCommonIndicator($request['workshop_id'])];
        }
        catch (\Exception $e){
            DB::rollback();
            return ['message' => 'Something went wrong', 'errors' => $e->getMessage()];
        }
    }

    public function destroyCommonIndicator($id){
        DB::beginTransaction();
        try{
            $commonindicator = CommonIndicator::findOrFail($id);
            $commonindicator->tags()->detach();
            foreach($commonindicator->subindicators as $sub){
                $sub->tags()->detach();
            }
            $commonindicator->delete();

            DB::commit();
            return ['message' => 'Successfully deleted!', 'commonindicators' => $this->getCommonIndicator($commonindicator->workshop_id)];
        }
        catch (\Exception $e){
            DB::rollback();
            return ['message' => 'Something went wrong', 'errors' => $e->getMessage()];
        }
    }

    // Common Functions

    public function getOptions($workshopId, $annex){
        // Fetch Programs and Divisions for select options
        // Projects of Annex E and F depends on Annex One
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
        
        $projects = [];
        $ids = []; $projectsIds = [];

        $annexones = AnnexOne::select('project_id')->where('workshop_id', $workshopId)->get();
        foreach($annexones as $annexone){
            array_push($ids, $annexone->project_id);
            array_push($projectsIds, $annexone->project_id);
        }
        if($annex == 'one'){
            $projects = Project::orderBy('id', 'asc')->whereNotIn('id', $ids)->get();
            foreach($projects as $project){
                $project->subprojects;
            }
        }
        else{
            if($annex == 'f'){
                $annexfs = AnnexF::where('workshop_id', $workshopId)->get();
                foreach($annexfs as $annexf){
                    foreach($annexf->projects as $project){
                        array_push($ids, $project->id);
                    }
                }
            }
            if($annex == 'e'){
                $annexfs = AnnexE::where('workshop_id', $workshopId)->get();
                foreach($annexfs as $annexf){
                    array_push($ids, $annexf->project_id);
                }
            }
    
            $projects = Project::orderBy('id', 'asc')->whereNotIn('id', $ids)->whereIn('id', $projectsIds)->get();
            foreach($projects as $project){
                $project->subprojects;
            }
        }
    
        $usedProjects = Project::orderBy('id', 'asc')->whereIn('id', $ids)->get();
        foreach($usedProjects as $project){
            $project->subprojects;
        }

        return ['programs' => $programs, 'divisions' => $divisions, 'projects' => $projects, 'used_projects' => $usedProjects];
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
