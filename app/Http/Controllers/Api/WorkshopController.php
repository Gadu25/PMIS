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
use App\Models\PerformanceIndicator;
use App\Models\IndicatorDetail;
use App\Models\IndicatorBreakdown;

use App\Models\AnnexF;
use App\Models\AnnexFSub;
use App\Models\AnnexFActivity;
use App\Models\AnnexFFund;

use App\Models\AnnexOne;
use App\Models\AnnexOneSub;
use App\Models\AnnexOneFund;

use App\Models\CommonIndicator;
use App\Models\CommonIndicatorSub;

use App\Models\History;
use App\Models\Notification;

use Auth;
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
            $query = AnnexE::query();
            $query = $query->with(['project', 'histories.profile.user', 'indicators.details', 'indicators.breakdowns', 'subs.subproject', 'subs.indicators.details', 'subs.indicators.breakdowns'])
                        ->where('status', $request['status'])
                        ->where('workshop_id', $request['workshopId']);
            
            $query = $query->whereHas('project', function($q) use($request){
                if($request['type'] == 'Program'){
                    if($request['program_id'] != 0){$q->where('program_id', $request['program_id']);}
                    if($request['subprogram_id'] != 0){$q->where('subprogram_id', $request['subprogram_id']);}
                    if($request['cluster_id'] != 0){$q->where('cluster_id', $request['cluster_id']);}
                }
                else{
                    if($request['division_id'] != 0){$q->where('division_id', $request['division_id']);}
                    if($request['unit_id'] != 0){$q->where('unit_id', $request['unit_id']);}
                    if($request['subunit_id'] != 0){$q->where('subunit_id', $request['subunit_id']);}
                }
            });

            $annexes = $query->get();
            $grouped = $annexes->groupBy(['project.program_id', 'project.subprogram_id', 'project.cluster_id']);

            $query = CommonIndicator::query();
            if($request['type'] == 'Program'){
                if($request['program_id'] != 0){$query = $query->where('program_id', $request['program_id']);}
                if($request['subprogram_id'] != 0){$query = $query->where('subprogram_id', $request['subprogram_id']);}
                if($request['cluster_id'] != 0){$query = $query->where('cluster_id', $request['cluster_id']);}
            }

            $commonindicators = $query->with(['details', 'tags', 'subindicators.details'])
                ->whereNot('type', 'Performance')
                ->where('workshop_id', $request['workshopId'])
                ->get();

            $cigrouped = $commonindicators->groupBy(['program_id', 'subprogram_id', 'cluster_id']);

            $programs = Program::with(['subprograms.clusters'])->get();
            foreach($programs as $program){
                $program->subpwithitems = false;
                $program->subpwithci = false;
                $program->commonindicators = $this->setItems($cigrouped, $program->id);
                $program->items = $this->setItems($grouped, $program->id);
                foreach($program->subprograms as $subprogram){
                    $subprogram->commonindicators = $this->setItems($cigrouped, $program->id, $subprogram->id);
                    $subprogram->items = $this->setItems($grouped, $program->id, $subprogram->id);
                    if(!$program->subpwithitems){
                        $program->subpwithitems = (sizeof($subprogram->items) > 0);
                    }
                    if(!$program->subpwithci){
                        $program->subpwithci = (sizeof($subprogram->commonindicators) > 0);
                    }
                    $subprogram->cluswithitems = false;
                    $subprogram->cluswithci = false;
                    foreach($subprogram->clusters as $cluster){
                        $cluster->commonindicators = $this->setItems($cigrouped, $program->id, $subprogram->id, $cluster->id);
                        $cluster->items = $this->setItems($grouped, $program->id, $subprogram->id, $cluster->id);
                        if(!$subprogram->cluswithitems){
                            $subprogram->cluswithitems = (sizeof($cluster->items) > 0);
                            if(!$program->subpwithitems){
                                $program->subpwithitems = (sizeof($cluster->items) > 0);
                            }
                        }
                        if(!$subprogram->cluswithci){
                            $subprogram->cluswithci = (sizeof($cluster->commonindicators) > 0);
                            if(!$program->subpwithci){
                                $program->subpwithci = (sizeof($cluster->commonindicators) > 0);
                            }
                        }
                    }
                }
            }

            return $programs;
        }
        catch(\Exception $e){
            DB::rollback();
            return ['message' => 'Something went wrong!', 'errors' => $e->getMessage()];
        }
    }

    private function setItems($grouped, $progId, $subpId = '', $clusId = ''){
        $items = [];
        if($grouped->has($progId)){
            if($grouped[$progId]->has($subpId)){
                if($grouped[$progId][$subpId]->has($clusId)){
                    $items = $grouped[$progId][$subpId][$clusId];
                }
            }
        }
        return $items;
    }

    public function storeAnnexE(Request $request){
        DB::beginTransaction();
        try {
            
        }
        catch(\Exception $e){
            DB::rollback();
            return ['message' => 'Something went wrong', 'errors' => $e->getMessage()];
        }
    }
    
    public function updateAnnexE(Request $request, $id){
        DB::beginTransaction();
        try {
            $annexe = AnnexE::findOrFail($id);
            $columns = ['actual', 'estimate', 'physical_targets', 'first', 'second', 'third', 'fourth'];
            $statchange = ($annexe->status != $request['status']);
            $initialIndicators = $annexe->indicators;
            $subject = '';
            $action = $this->saveIndicatorDetails($request, $annexe, $initialIndicators, $statchange);

            foreach($request['subs'] as $sub){
                $annexesub = AnnexESub::findOrFail($sub['id']);
                $initialIndicators = $annexesub->indicators;
                $this->saveIndicatorDetails($request, $annexesub, $initialIndicators, $statchange, $sub['indicators'], true);
            }

            $subject = $subject.(($request['status'] == 'New' || $request['status'] == 'Draft' || $request['status'] == 'For Review' || $request['status'] == 'same') ? $action : '<p class="m-0">Action: Change Status </p>');
            $subject = $subject.((!$statchange || $request['status'] == 'same') ? '<p class="m-0">Status: Unchanged</p>' : '');

            if($statchange && $request['status'] != 'same'){
                $prevstatus = $annexe->status;
                $annexe->status = $request['status'];
                $annexe->remarks = ($request['remarks'] != '') ? $request['remarks'] : null;
                $annexe->save();
                $subject = $subject.'<p class="m-0">Status: '.$prevstatus.' => '.$annexe->status.'</p>';
                $remarks = ($request['remarks'] != '') ? '<p>Remarks: '.$request['remarks'].'</p>' : '';
                $subject = $subject.$remarks;
            }

            // need to clarify how notification will work
            // titles, 1 = director, 2 = deputy, 3 = div chief, 4 unit head, 5 proj leader, 6 staff, 7 superadmin
            // if($request['status'] != 'New' && ($request['status'] == 'Draft' && $annexe->status != 'Draft')){
            //     $stat = $request['status'];
            //     $title_id = ($stat == 'For Review') ? 4 : $stat == 'For Approval'
            //     $this->getRecipient()
            // }
            

            $this->createHistory($annexe, $subject);

            DB::commit();
            return ['message' => 'Successfully saved!', 'status' => $annexe->status];
        }
        catch(\Exception $e){
            DB::rollback();
            return ['message' => 'Something went wrong', 'errors' => $e->getMessage(), 'trace' => $e->getTrace()];
        }
    }
    
    public function destroyAnnexE($id){

    }

    private function saveIndicatorDetails($request, $parent, $initialIndicators, $statchange, $indicators = [], $isSub = false){
        $columns = ['actual', 'estimate', 'physical_targets', 'first', 'second', 'third', 'fourth'];
        $indicators = ($isSub === false) ? $request['indicators'] : $indicators;
        // $indicators = (sizeof($indicators) > 0) ? $indicators : $request['indicators'];
        $tempIndicatorIds = [];
        foreach($indicators as $indicator){
            $existingIndicator = (is_int($indicator['id']));
            $performanceindicator = $existingIndicator ? PerformanceIndicator::findOrFail($indicator['id']) : new PerformanceIndicator;
            if(is_int($indicator['id'])){
                array_push($tempIndicatorIds, $indicator['id']);
            }
            if($request['formtype'] == 'indicator'){
                $performanceindicator->description = $indicator['description'];
                $parent->indicators()->save($performanceindicator);
                array_push($tempIndicatorIds, $performanceindicator->id);
            }
            if($request['formtype'] == 'details'){
                if($indicator['description'] == 'Sub-Total' && !is_int($indicator['id'])){
                    $performanceindicator->description = $indicator['description'];
                    $parent->indicators()->save($performanceindicator);
                }
                if($this->indicatorHaveDetails($indicator)){
                    $details = ($performanceindicator->details != null) ? IndicatorDetail::findOrFail($performanceindicator->details->id) : new IndicatorDetail ;
                    foreach($columns as $column){
                        $details[$column] = $this->formatAmount($indicator[$column]);
                    }
                    $performanceindicator->details()->save($details);
                }
                else{
                    if($existingIndicator){
                        $performanceindicator->details()->delete();
                    }
                }
                if($request['program_id'] != 1){
                    $nums = [1,2,3];
                    foreach($indicator['breakdowns'] as $bd){
                        foreach($nums as $num){
                            $numId  = ($num == 1) ? 'fid'   : (($num == 2) ? 'sid'    : 'tid');
                            $numKey = ($num == 1) ? 'first' : (($num == 2) ? 'second' : 'third');
                            $existingBreakdown = ($bd[$numId]);
                            $breakdown = ($existingBreakdown) ? IndicatorBreakdown::findOrFail($bd[$numId]) : new IndicatorBreakdown; 
                            if($this->formatAmount($bd[$numKey]) > 0){
                                $breakdown->quarter = $bd['quarter'];
                                $breakdown->month = $num;
                                $breakdown->number = $bd[$numKey];
                                $performanceindicator->breakdowns()->save($breakdown);
                            }
                            else{
                                if($existingBreakdown){
                                    $breakdown->delete();
                                }
                            }
                        }
                    }
                }
            }
        }

        foreach($initialIndicators as $indicator){
            if(!$indicator->common && !in_array($indicator->id, $tempIndicatorIds)){
                $indicator->details()->delete();
                $indicator->breakdowns()->delete();
                $indicator->delete();
            }
        }

        // if(!$statchange){
        $item = $isSub ? 'Sub Indicator - ' : 'Indicator - ';
        $action = (sizeof($indicators) > sizeof($initialIndicators) ? 'Added new indicators' : (sizeof($indicators) < sizeof($initialIndicators) ? 'Removed indicators' : (sizeof($indicators) == 0 && sizeof($initialIndicators) > 0 ? 'Removed all indicators' : (($request['formtype'] == 'details') ? 'Update Indicator Details' : 'No Changes'))));
        $action = $item.$action;
        return '<p class="m-0">Action: '.$action.'</p>';
        // }
        // else{
        //     return ((!$isSub) ? '<p>Updated Indicator Status </p>' : '');
        // }
        
    }

    private function indicatorHaveDetails($indicator){
        $state = false;
        $columns = ['actual', 'estimate', 'physical_targets', 'first', 'second', 'third', 'fourth'];
        foreach($columns as $column){
            if(!$state){
                $state = ($this->formatAmount($indicator[$column]) > 0);
            }
            else{
                return $state;
            }
        }
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
            $commonindicators = CommonIndicator::with(['tags'])
                ->where('workshop_id', $workshopId)
                ->where('type', 'Performance')->get();
            foreach($annexones as $annexone){
                $annexe = new AnnexE;
                $annexe->workshop_id = $workshopId;
                $annexe->project_id = $annexone->project_id;
                $annexe->status = 'New';
                $annexe->save();

                $project = $annexe->project;

                foreach($commonindicators as $commonindicator){
                    $coprogram_id    = $commonindicator->program_id;
                    $cosubprogram_id = $commonindicator->subprogram_id;
                    $cocluster_id    = $commonindicator->cluster_id;
                    // common indicator - for projects under program
                    if($coprogram_id == $project->program_id && $cosubprogram_id === null && $cocluster_id === null){
                        $this->saveAnnexECommonIndicator($annexe, $commonindicator);
                    }
                    else 
                    // common indicator - for projects under cluster
                    if($coprogram_id == $project->program_id && $cosubprogram_id !== null && $cocluster_id !== null){
                        if($cocluster_id == $project->cluster_id){
                            $this->saveAnnexECommonIndicator($annexe, $commonindicator);
                        }
                    }
                    else
                    // common indicator - for projects under subprogram
                    if($coprogram_id == $project->program_id && $cosubprogram_id == $project->subprogram_id){
                        $this->saveAnnexECommonIndicator($annexe, $commonindicator);
                    }
                }

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

    private function saveAnnexECommonIndicator($annexe, $commonindicator){
        $performanceindicator = new PerformanceIndicator;
        $performanceindicator->description = $commonindicator->description;
        $performanceindicator->common = true;
        $annexe->indicators()->save($performanceindicator);
        $performanceindicator->tags()->sync([$commonindicator->tags[0]->id]);

        $tempTags = [];
        foreach($commonindicator->subindicators as $sub){
            $tag = $sub->tags[0];
            if(!in_array($tag->name, $tempTags)){
                $annexesub = new AnnexESub;
                $annexesub->annex_e_id = $annexe->id;
                $annexesub->temp_title = $tag->name;
                $annexesub->save();

                array_push($tempTags, $tag->name);
            }

            $performanceindicator = new PerformanceIndicator;
            $performanceindicator->description = $sub->description;
            $performanceindicator->common = true;
            $annexesub->indicators()->save($performanceindicator);
            $performanceindicator->tags()->sync([$tag->id]);
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

    private function sendNotification(){
        // $recipientId = $this->getRecipient($)
    }

    private function getRecipient($titleId, $divId, $unitId = null, $subId = null){
        $user = User::with(['activeProfile'])
                    ->whereHas('profile', function($q) use($titleId) {
                        $q->where('title_id', $titleId);
                        $q->where('active', true);
                    })
                    ->where('division_id', $divId)
                    ->where('unit_id', $unitId)
                    ->where('subunit_id', $subuId)
                    ->first();
        return $user;
    }

    private function createHistory($item, $subject){
        $user = Auth::user();

        $history = new History;
        $history->subject = $subject;
        $history->profile_id = $user->activeProfile->id;

        $item->histories()->save($history);
    }
}
