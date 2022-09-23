<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Workshop;
use App\Models\Program;
use App\Models\Subprogram;
use App\Models\Division;
use App\Models\Unit;
use App\Models\Project;

use App\Models\AnnexE;
use App\Models\AnnexESub;
use App\Models\PerformanceIndicator;
use App\Models\IndicatorDetail;
use App\Models\IndicatorBreakdown;
use App\Models\Tag;

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
use App\Models\Profile;

use Auth;
use DB;

use Shuchkin\SimpleXLSXGen;

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
            $query = $query->whereHas('project', function($q) use($request){
                if($request['type'] == 'Program'){
                    if($request['program_id']    != 0){$q->where('program_id',     $request['program_id']);}
                    if($request['subprogram_id'] != 0){$q->where('subprogram_id',  $request['subprogram_id']);}
                    if($request['cluster_id']    != 0){$q->where('cluster_id',     $request['cluster_id']);}
                }
                else{
                    if($request['division_id'] != 0){$q->where('division_id', $request['division_id']);}
                    if($request['unit_id']     != 0){$q->where('unit_id',     $request['unit_id']);}
                    if($request['subunit_id']  != 0){$q->where('subunit_id',  $request['subunit_id']);}
                }
            });

            $annexes = $query->with(
                ['project.leader', 'histories.profile.user', 'subs.subproject', 
                'indicators.details', 'indicators.tags', 'indicators.breakdowns', 
                'subs.indicators.details', 'subs.indicators.tags', 'subs.indicators.breakdowns'])
                ->where('status', $request['status'])
                ->where('workshop_id', $request['workshopId'])->get();
            $grouped = $annexes->groupBy(['project.program_id', 'project.subprogram_id', 'project.cluster_id']);

            $query = CommonIndicator::query();
            if($request['type'] == 'Program'){
                if($request['program_id']    != 0){$query = $query->where('program_id',    $request['program_id']);}
                if($request['subprogram_id'] != 0){$query = $query->where('subprogram_id', $request['subprogram_id']);}
                if($request['cluster_id']    != 0){$query = $query->where('cluster_id',    $request['cluster_id']);}
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
                    $this->isLoaded($program, $subprogram->items, 'subpwithitems');
                    $this->isLoaded($program, $subprogram->commonindicators, 'subpwithci');
                    $subprogram->cluswithitems = false;
                    $subprogram->cluswithci = false;
                    if($program->id == 1){
                        // add total using items
                        $subprogram->totalitems = $this->setTotal($subprogram->items);
                    }
                    foreach($subprogram->clusters as $cluster){
                        $cluster->commonindicators = $this->setItems($cigrouped, $program->id, $subprogram->id, $cluster->id);
                        $cluster->items = $this->setItems($grouped, $program->id, $subprogram->id, $cluster->id);
                        $this->isLoaded($subprogram, $cluster->items, 'cluswithitems', $program, 'subpwithitems');
                        $this->isLoaded($subprogram, $cluster->commonindicators, 'cluswithci', $program, 'subpwithci');
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

    public function updateAnnexE(Request $request, $id){
        DB::beginTransaction();
        try {
            $annexe = AnnexE::findOrFail($id);
            $columns = ['actual', 'estimate', 'physical_targets', 'first', 'second', 'third', 'fourth'];
            $status = $request['status'];
            $statchange = ($annexe->status != $status);
            $initialIndicators = $annexe->indicators;
            $initialStatus = $annexe->status;
            $subject = '';
            $action = $this->saveIndicators($request, $annexe, $initialIndicators);

            foreach($request['subs'] as $sub){
                $annexesub = AnnexESub::findOrFail($sub['id']);
                $initialIndicators = $annexesub->indicators;
                $this->saveIndicators($request, $annexesub, $initialIndicators, $sub['indicators'], true);
            }

            $subject = $subject.(($status == 'New' || $status == 'Draft' || $status == 'For Review' || $status == 'same') ? $action : '<p class="m-0">Action: Change Status </p>');
            $subject = $subject.((!$statchange || $status == 'same') ? '<p class="m-0">Status: Unchanged</p>' : '');

            if($statchange && $status != 'same'){
                $prevstatus = $annexe->status;
                $annexe->status = $status;
                $annexe->save();
                $subject = $subject.'<p class="m-0">Status: '.$prevstatus.' => '.$annexe->status.'</p>';
            }

            // $subject = $request['comment'] != '' ? $subject.'<p class="m-0">Comment: <i>'.$request['comment'].'</i></p>' : $subject;

            if($statchange){
                if($initialStatus != 'New' || $status == 'For Review'){
                    $link = '/budget-executive-documents/annex-e/'.$annexe->workshop_id.'?id='.$annexe->id;
                    $message = $this->sendNotification($annexe->project, $status, 'Annex E', $link);
                    $subject = $subject.$message;
                }
            }
            

            $this->createHistory($annexe, $subject, $request['comment']);

            DB::commit();
            return ['message' => 'Successfully saved!', 'status' => $annexe->status];
        }
        catch(\Exception $e){
            DB::rollback();
            return ['message' => 'Something went wrong', 'errors' => $e->getMessage(), 'trace' => $e->getTrace()];
        }
    }

    public function showAnnexE($id){
        $annexe = AnnexE::with(
            ['project.leader', 'histories.profile.user', 'subs.subproject', 
            'indicators.details', 'indicators.tags', 'indicators.breakdowns', 
            'subs.indicators.details', 'subs.indicators.tags', 'subs.indicators.breakdowns'])->where('id', $id)->first();
        return $annexe;
    }

    private function setTotal($items){
        $total = new AnnexE;
        $total->title = 'Total';
        $total->indicators; $total->subs;
        foreach($items as $item){
            $this->setIndicators($total, $item->indicators);
            foreach($item->subs as $sub){
                if(!$this->inSubs($total->subs, $sub)){
                    $annexesub = new AnnexESub;
                    $annexesub->temp_title = $sub->temp_title;
                    $annexesub->subproject_id = $sub->subproject_id;
                    $annexesub->subproject; $annexesub->indicators;
                    $this->setIndicators($annexesub, $sub->indicators);
                    $total->subs->push($annexesub);
                }
                else{
                    $annexesub = $this->getSub($total->subs, $sub);
                    $this->setIndicators($annexesub, $sub->indicators);
                }
            }
        }
        return $total;
    }

    private function setIndicators($parent, $indicators){
        $columns = ['actual', 'estimate', 'physical_targets', 'first', 'second', 'third', 'fourth'];
        foreach($indicators as $indicator){
            if(!$this->inIndicators($parent->indicators, $indicator->description)){
                $performanceindicator = new PerformanceIndicator;
                $performanceindicator->description = $indicator->description;
                $details = new IndicatorDetail;
                foreach($columns as $column){
                    $details[$column] = $indicator->details !== null ? $indicator->details[$column] : 0;
                }
                $performanceindicator->details = $details;
                $parent->indicators->push($performanceindicator);
            }
            else{
                $performanceindicator = $this->getIndicator($parent->indicators, $indicator->description);
                foreach($columns as $column){
                    $performanceindicator->details[$column] = $performanceindicator->details[$column] + ($indicator->details !== null ? $indicator->details[$column] : 0);
                }
            }
        }
    }

    private function inSubs($subs, $subitem){
        foreach($subs as $sub){
            if($sub->subproject_id == $subitem->subproject_id && $sub->temp_title == $subitem->temp_title){
                return true;
            }
        }
        return false;
    }

    private function getSub($subs, $subitem){
        foreach($subs as $sub){
            if($sub->subproject_id == $subitem->subproject_id && $sub->temp_title == $subitem->temp_title){
                return $sub;
            }
        }
    }

    private function inIndicators($indicators, $description){
        foreach($indicators as $indicator){
            if($indicator->description == $description){
                return true;
            }
        }
        return false;
    }

    private function getIndicator($indicators, $description){
        foreach($indicators as $indicator){
            if($indicator->description == $description){
                return $indicator;
            }
        }
    }

    // Other Indicator = Outcome or Output Indicators
    public function updateOtherIndicatorDetails(Request $request, $id){
        DB::beginTransaction();
        try {
            $commonindicator = CommonIndicator::findOrFail($id);
            $this->saveIndicatorDetails($commonindicator, $request);
            foreach($request['subs'] as $sub){
                $commonindicatorsub = CommonIndicatorSub::findOrFail($sub['id']);
                $this->saveIndicatorDetails($commonindicatorsub, $sub);
            }
            DB::commit();
            return ['message' => 'Indicator saved!'];
        }
        catch(\Exception $e){
            DB::rollback();
            return ['message' => 'Something went wrong', 'errors' => $e->getMessage(), 'trace' => $e->getTrace()];
        }
    }

    private function saveIndicatorDetails($indicator, $form){
        $columns = ['actual', 'estimate', 'physical_targets', 'first', 'second', 'third', 'fourth'];
        $tag = $form['tag'];
        $withDetails = $this->indicatorHaveDetails($form);
        if($withDetails){
            $details = $indicator->details ? IndicatorDetail::findOrFail($indicator->details->id) : new IndicatorDetail;
            if($tag){
                $this->updateCommonIndicatorWithTag($tag['id'], $indicator->item, true, $details, $form); 
            }
            foreach($columns as $column){
                $details[$column] = $this->formatAmount($form[$column]);
            }
            $indicator->details()->save($details);
        }
        if(!$withDetails && $indicator->details){
            $indicator->details()->delete();
        }
    }
    
    private function updateCommonIndicatorWithTag($tagId, $annexe, $withDetails, $initialDetails, $form){
        $columns = ['actual', 'estimate', 'physical_targets', 'first', 'second', 'third', 'fourth'];

        $commonindcators = CommonIndicator::with('tags')
            ->whereHas('tags', function($query) use($tagId){
                $query->where('id', $tagId);
            })
            ->where('workshop_id', $annexe->workshop_id)
            ->whereNot('type', 'Performance')->get();

        $commonindicatorsubs = CommonIndicatorSub::with('tags', 'commonindicator')
            ->whereHas('tags', function($query) use($tagId){
                $query->where('id', $tagId);
            })
            ->whereHas('commonindicator', function($query) use($annexe){
                $query->where('workshop_id', $annexe->workshop_id);
            })->get();

        foreach($commonindcators as $commonindicator){
            $details = $commonindicator->details ? IndicatorDetail::findOrFail($commonindicator->details->id) : new IndicatorDetail;
            foreach($columns as $column){
                $details[$column] = $this->formatAmount($details[$column]) + $this->formatAmount($form[$column]) - $this->formatAmount($initialDetails[$column]);
                
                if(!$withDetails){
                    $details[$column] = $this->formatAmount($details[$column]) - $this->formatAmount($initialDetails[$column]);
                }
            }
            $commonindicator->details()->save($details);
            if(!$this->indicatorHaveDetails($details)){
                $commonindicator->details()->delete();
            }
        }

        foreach($commonindicatorsubs as $commonindicator){
            $details = $commonindicator->details ? IndicatorDetail::findOrFail($commonindicator->details->id) : new IndicatorDetail;
            foreach($columns as $column){
                $details[$column] = $this->formatAmount($details[$column]) + $this->formatAmount($form[$column]) - $this->formatAmount($initialDetails[$column]);
                
                if(!$withDetails){
                    $details[$column] = $this->formatAmount($details[$column]) - $this->formatAmount($initialDetails[$column]);
                }
            }
            $commonindicator->details()->save($details);
            if(!$this->indicatorHaveDetails($details)){
                $commonindicator->details()->delete();
            }
        }
    }

    private function saveIndicators($request, $parent, $initialIndicators, $indicators = [], $isSub = false){
        $indicators = ($isSub === false) ? $request['indicators'] : $indicators;
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
                $this->saveIndicatorDetails($performanceindicator, $indicator);
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

        $item = $isSub ? 'Sub Indicator - ' : 'Indicator - ';
        $action = (sizeof($indicators) > sizeof($initialIndicators) ? 'Added new indicators' : (sizeof($indicators) < sizeof($initialIndicators) ? 'Removed indicators' : (sizeof($indicators) == 0 && sizeof($initialIndicators) > 0 ? 'Removed all indicators' : (($request['formtype'] == 'details') ? 'Update Indicator Details' : 'No Changes'))));
        $action = $item.$action;
        return '<p class="m-0">Action: '.$action.'</p>';
    }

    private function indicatorHaveDetails($details){
        $state = false;
        $columns = ['actual', 'estimate', 'physical_targets', 'first', 'second', 'third', 'fourth'];
        foreach($columns as $column){
            if(!$state){
                $state = ($this->formatAmount($details[$column]) > 0);
            }
        }
        return $state;
    }

    // Annex F 
    public function getAnnexF(Request $request){
        DB::beginTransaction();
        try {
            $query = AnnexF::query();
            $query = $query->whereHas('projects', function($q) use($request){
                if($request['type'] == 'Program'){
                    if($request['program_id'])   { $q->where('program_id',    $request['program_id']);    }
                    if($request['subprogram_id']){ $q->where('subprogram_id', $request['subprogram_id']); }
                    if($request['clustser_id'])  { $q->where('clustser_id',   $request['clustser_id']);   }
                }
                else{
                    if($request['division_id']){ $q->where('division_id', $request['division_id']); }
                    if($request['unit_id'])    { $q->where('unit_id',     $request['unit_id']);     }
                    if($request['subunit_id']) { $q->where('subunit_id',  $request['subunit_id']);  }
                }
            });
            $annexfs = $query->with(['projects.subprogram', 'projects.leader', 'histories.profile.user', 'funds', 'activities', 'subs.activities', 'subs.funds', 'subs.subproject'])
                ->where('workshop_id', $request['workshopId'])
                ->where('status', $request['status'])->get();
            
            $grouped = $annexfs->groupBy(['projects.0.program_id', 'projects.0.subprogram_id', 'projects.0.cluster_id']);

            $programs = Program::with('subprograms.clusters')->get();
            foreach($programs as $program){
                $program->show = false;
                $program->items = $this->setItems($grouped, $program->id);
                foreach($program->subprograms as $subprogram){
                    $subprogram->show = false;
                    $subprogram->items = $this->setItems($grouped, $program->id, $subprogram->id);
                    $this->isLoaded($program, $subprogram->items, 'show');
                    foreach($subprogram->clusters as $cluster){
                        $cluster->items = $this->setItems($grouped, $program->id, $subprogram->id, $cluster->id);
                        $this->isLoaded($subprogram, $cluster->items, 'show');
                        $this->isLoaded($program, $cluster->items, 'show');
                    }
                }
            }

            DB::commit();

            return $programs;
        }
        catch(\Exception $e){
            DB::rollback();
            return ['message' => 'Something went wrong!', 'errors' => $e->getMessage()];
        }
    }

    public function updateAnnexF(Request $request, $id){
        DB::beginTransaction();
        try {
            $annexf = AnnexF::findOrFail($id);
            $annexf->remarks = $request['remarks'];
            $initialStatus = $annexf->status;
            $status = $request['status'];
            $subject = '';
            $statchange = $initialStatus != $status;

            $this->saveAnnnexFActivities($annexf, $request['activities']);
            $this->saveAnnnexFFunds($annexf, $request['funds']);

            foreach($request['subs'] as $sub){
                $annexfsub = AnnexFSub::findOrFail($sub['id']);
                $this->saveAnnnexFActivities($annexfsub, $sub['activities']);
                $this->saveAnnnexFFunds($annexfsub, $sub['funds']);
            }

            $subject = $subject.(($status == 'New' || $status == 'Draft' || $status == 'For Review' || $status == 'same') ? '<p class="m-0">Action: Update details </p>' : '<p class="m-0">Action: Change Status </p>');
            $subject = $subject.((!$statchange || $status == 'same') ? '<p class="m-0">Status: Unchanged</p>' : '');

            if($statchange && $status != 'same'){
                $annexf->status = $status;
                $subject = $subject.'<p class="m-0">Status: '.$initialStatus.' => '.$annexf->status.'</p>';
            }

            if($statchange){
                if($initialStatus != 'New' || $status == 'For Review'){
                    // $linkstatus = $status == 'For Review' ? 'For%20Review' : ($status == 'For Approval' ? 'For%20Approval' : ($status == 'Approved' ? 'Approved' : 'Submitted'));
                    $link = '/budget-executive-documents/annex-f/'.$annexf->workshop_id.'?id='.$annexf->id;
                    $message = $this->sendNotification($annexf->projects, $status, 'Annex F', $link);
                    $subject = $subject.$message;
                }
            }
            
            $annexf->save();

            $this->createHistory($annexf, $subject, $request['comment']);

            DB::commit();
            return ['message' => 'Saved!', 'status' => $annexf->status];
        }
        catch (\Exception $e){
            DB::rollback();
            return ['message' => 'Something went wrong', 'errors' => $e->getMessage()];
        }
    }

    public function showAnnexF($id){
        $annexf = AnnexF::with(['projects.subprogram', 'projects.leader', 'histories.profile.user',
        'funds', 'activities', 'subs.activities', 
        'subs.funds', 'subs.subproject'])->where('id', $id)->first();
        return $annexf;
    }

    private function saveAnnnexFActivities($parent, $array){
        $ids = []; 
        $initialActivities = $parent->activities;
        foreach($array as $key => $activityArray){
            foreach($activityArray as $act){
                if($act['description'] != ''){
                    $activity = $act['id'] ? AnnexFActivity::findOrFail($act['id']) : new AnnexFActivity;
                    $activity->description = $act['description'];
                    $activity->table_key = $key;
                    $parent->activities()->save($activity);
                    array_push($ids, $activity->id);
                }
                if($act['description'] == '' && $act['id']){
                    $activity = AnnexFActivity::findOrFail($act['id']);
                    $activity->delete();
                }
            }
        }
        foreach($initialActivities as $activity){ // compare item original activities from request activities
            if(!in_array($activity->id, $ids)){
                $activity->delete();
            }
        }
    }

    private function saveAnnnexFFunds($parent, $array){
        foreach($array as $key => $value){
            $amount = $this->formatAmount($value['amount']);
            if($amount > 0){
                $fund = $value['id'] ? AnnexFFund::findOrFail($value['id']) : new AnnexFFund;
                $fund->amount = $amount;
                $fund->table_key = $key;
                $parent->funds()->save($fund);
            }
            else if($value['id']){
                $fund = AnnexFFund::findOrFail($value['id']);
                $fund->delete();
            }
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
            $annexones = AnnexOne::with('project')->where('workshop_id', $workshopId)->get();
            $commonindicators = CommonIndicator::with(['tags'])
                ->where('workshop_id', $workshopId)
                ->where('type', 'Performance')->get();
            $ctr = 0;
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

                // check if undergraduate
                $undergrad = Subprogram::where('title_short', 'Undergraduate')->first();
                if($annexone->project->subprogram_id === $undergrad->id){
                    if($ctr === 0){
                        $annexf = new AnnexF;
                        $annexf->workshop_id = $workshopId;
                        $annexf->status = 'New';
                        $annexf->save();
                        $tempIds = [];
                        foreach($undergrad->projects as $project){
                            array_push($tempIds, $project->id);
                        }
                        $annexf->projects()->sync($tempIds);
                        $ctr = 1;
                    }
                }
                else{
                    $annexf = new AnnexF;
                    $annexf->workshop_id = $workshopId;
                    $annexf->status = 'New';
                    $annexf->save();
                    $annexf->projects()->sync([$annexone->project_id]);
                }


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
        $programs = Program::with(['subprograms', 'subprograms.clusters'])->orderBy('id', 'asc')->get();

        $divisions = Division::with(['units.subunits'])->orderBy('id', 'asc')->get();
        
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
        return $amount ? (float)$newAmount : 0;
    }

    private function getWorkshopYear($id){
        $workshop = Workshop::findOrFail($id);
        $str = explode('-', $workshop->start);
        return (int)$str[0];
    }

    private function sendNotification($project, $status, $type, $link = ''){
        $sender = Auth::user(); $notified = 0;
        $projects = []; $length = 1;
        if($type == 'Annex F'){
            $length = sizeof($project);
            $project = $project[0];
        }
        $projectleader = $project->leader->profile;
        $message = $this->setMessage($project, $status, $type, $length);
        $results = '<p class="m-0">Notified: <br>';
        // notify project leader unless status is for review
        if($status != 'For Review' && $projectleader->id != $sender->activeProfile->id){
            $this->sendMessage($sender, $projectleader->id, $message['title'], $message['body'], $link);
            $results = $results.'<li>'.$projectleader->user->firstname.' '.$projectleader->user->lastname.'</li>';
            $notified = $notified+1;
        }
        $query = Profile::query();
        $query = $query->with('user')->where('active', true);
        // notify unit head if status is for review, approved or back to draft
        if($status == 'For Review' || $status == 'Approved' || $status == 'Draft'){
            $query = $query->where('title_id', 4)
                    ->whereHas('user', function($q) use($project){
                        $q->where('division_id', $project->division_id)
                        ->where('unit_id', $project->unit_id)
                        ->where('subunit_id', $project->subunit_id);
                    });
        }
        // notify division chief if status is for approval
        if($status == 'For Approval'){
            $query = $query->where('title_id', 3)
                    ->whereHas('user', function($q) use($project){
                        $q->where('division_id', $project->division_id);
                    });
        }
        // notify planning unit head if status is submitted
        if($status == 'Submitted'){
            $division = Division::where('code', 'OD')->first();
            $unit     = Unit::where('name', 'Planning Unit')->first();
            $ids      = ['division_id' => $division->id, 'unit_id' => $unit->id];
            $query = $query->where('title_id', 4)
                        ->whereHas('user', function($q) use($ids){
                            $q->where('division_id', $ids['division_id'])
                            ->where('unit_id', $ids['unit_id']);
                        });
        }

        $recipient = $query->first();
        if($recipient->id != $sender->activeProfile->id && $recipient->title_id != 7){
            $this->sendMessage($sender, $recipient->id, $message['title'], $message['body'], $link);
            $results = $results.'<li>'.$recipient->user->firstname.' '.$recipient->user->lastname.'</li>';
            $notified = $notified+1;
        }
        
        return $notified > 0 ? $results.'</p>' : '';

        // Titles
        // 3 = Division Chief
        // 4 = Unit Head
        // 5 = Project Leader
    }

    private function sendMessage($sender, $recipientId, $title, $body, $link){
        $notification = new Notification;
        $notification->profile_to = $recipientId; 
        $notification->profile_from = $sender->activeProfile->id;
        $notification->title = $title;
        $notification->body = $body;
        $notification->link = $link;
        $notification->save();
    }

    private function setMessage($project, $status, $type, $length){
        $body = '<p class="m-0">';
        $projectTitle = $length > 1 ? $project->subprogram->title : $project->title;
        $title = '<strong> Workshop - '.$type.'</strong>';
        if($status == 'Draft'){
            $body = $body.$projectTitle.' was sent back to <b>Drafts</b>. <br><small><i>Click here to view project logs and see what happen</i></small>';
        }
        if($status == 'For Review' || $status == 'For Approval'){
            $body = $body.$projectTitle.' was sent '.$status;
        }
        else if($status == 'Approved'){
            $body = $body.$projectTitle.' approved! ';
        }
        else if($status == 'Submitted'){
            $body = $body.$projectTitle.' submitted! ';
        }
        
        return ['title' => $title, 'body' => $body.'</p>'];
    }

    private function createHistory($item, $subject, $comment = ''){
        $user = Auth::user();

        $history = new History;
        $history->subject = $subject;
        $history->comment = $comment;
        $history->profile_id = $user->activeProfile->id;

        $item->histories()->save($history);
    }
    
    // Annex E & F Item Setter
    private function isLoaded($array, $comparator, $col, $array2 = null, $col2 = null){
        if(!$array[$col]){
            $array[$col] = (sizeof($comparator) > 0);
            if($array2){
                if(!$array2[$col2]){
                    $array2[$col2] = (sizeof($comparator) > 0);
                }
            }
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

    // Export
    public function exportxlsx($workshopId, $annex, $status, $filter, $id1, $id2, $id3){ // $annex = E or F
        $workshop = Workshop::findOrFail($workshopId);
        $this->formatWorkshop($workshop);
        $year = (int) $workshop['year'];


        $data = [];
        $xlsxheader = $this->xlsxheader($annex, $year);
        foreach($xlsxheader['header'] as $head){
            array_push($data, $head);
        }

        $xlsxbody = $this->xlsxbody($annex, $workshopId, $status, $filter, $id1, $id2, $id3);

        foreach($xlsxbody['body'] as $body){
            array_push($data, $body);
        }
        
        $filename = 'CY_'.$year.'_'.$annex.'_'.$status.'.xlsx';
        $xlsx = SimpleXLSXGen::fromArray( $data, $filename );
        foreach($xlsxheader['mergedcells'] as $merge){
            $xlsx->mergeCells($merge);
        }
        foreach($xlsxbody['mergedcells'] as $merge){
            $xlsx->mergeCells($merge);
        }
        $length = ($annex == 'annex-e') ? 8 : 19;
        for ($x = 0; $x <= $length; $x++) {
            if($annex == 'annex-e'){
                $num = $x+1;
                $width = $x > 1 ? 20 : 40;
                $xlsx->setColWidth($num, $width);
            }
        }
        // return $xlsxbody['body'];\
        $xlsx->downloadAs($filename); 
    }

    private function xlsxheader($annex, $year){
        if($annex == 'annex-e'){
            $header = [
                [null, null, null, null, null, null, null, null, '<b>Annex E</b>'],
                ['<middle><center><b>CY '.$year.' PHYSICAL PLAN</b></center></middle>'],
                [null],
                ['<middle><center><b>Program/Project</b></center></middle>', 
                 '<middle><center><b>Performance Indicators</b></center></middle>', 
                 '<middle><center><b>Previous Year Accomplishments<br> CY '.$year.'</b></center></middle>', null , 
                 '<middle><center><b><wraptext>CY '.($year+1).'<br> Physical Targets</wraptext></b></center></middle>',
                 '<middle><center><b>CY '.($year+1).' Quarterly Physical Targets</b></center></middle>'],
                [null, null,
                '<middle><center><b>Actual</b></center></middle>',
                '<middle><center><b>Estimate</b></center></middle>', null,
                '<middle><center><b>1st</b></center></middle>',
                '<middle><center><b>2nd</b></center></middle>',
                '<middle><center><b>3rd</b></center></middle>',
                '<middle><center><b>4th</b></center></middle>'],
                [null, null, '<middle><center><b>Jan 1 - Sep 30</b></center></middle>', '<middle><center><b>Oct 1 - Dec 30</b></center></middle>']
            ];
            $mergedcells = [
                'A2:I2',
                'A4:A6',
                'B4:B6',
                'E4:E6',
                'F5:F6',
                'G5:G6',
                'H5:H6',
                'I5:I6',
                'C4:D4',
                'F4:I4',
            ];
            return ['header' => $header, 'mergedcells' => $mergedcells];
        }
        else{

        }
    }

    private function xlsxbody($annex, $workshopId, $status, $filter, $id1, $id2, $id3){
        $body = []; $mergedcells = [];
        $programs = Program::with('subprograms.clusters')->get();
        $query = $annex == 'annex-e' ? AnnexE::query() : AnnexF::query();

        $query = $annex == 'annex-e' ? 
            $query->whereHas('project', function($q) use($filter, $id1, $id2, $id3){
                $this->filterquery($q, $filter, $id1, $id2, $id3);
            }) : 
            $query->whereHas('projects', function($q) use($filter, $id1, $id2, $id3){
                $this->filterquery($q, $filter, $id1, $id2, $id3);
            });

        $query = $annex == 'annex-e' ? 
            $query->with(['project.leader', 'histories.profile.user', 'subs.subproject', 
                'indicators.details', 'indicators.tags', 'indicators.breakdowns', 
                'subs.indicators.details', 'subs.indicators.tags', 'subs.indicators.breakdowns']) :
            $query->with(['projects.subprogram', 'projects.leader',
                'histories.profile.user', 'funds', 'activities', 
                'subs.activities', 'subs.funds', 'subs.subproject']);

        $items = $query->where('workshop_id', $workshopId)->where('status', $status)->get();
        $grouped = $annex == 'annex-e' ? 
            $items->groupBy(['project.program_id', 'project.subprogram_id', 'project.cluster_id']) :
            $items->groupBy(['projects.0.program_id', 'projects.0.subprogram_id', 'projects.0.cluster_id']);

        $cigrouped = [];
        if($annex == 'annex-e'){
            // get outcome and output
            $query = CommonIndicator::query();
            if($filter == 'Program'){
                if($id1 != 0){$query = $query->where('program_id',    $id1);}
                if($id2 != 0){$query = $query->where('subprogram_id', $id2);}
                if($id3 != 0){$query = $query->where('cluster_id',    $id3);}
            }
            $commonindicators = $query->with(['details', 'tags', 'subindicators.details'])
                ->whereNot('type', 'Performance')
                ->where('workshop_id', $workshopId)
                ->get();
            $cigrouped = $commonindicators->groupBy(['program_id', 'subprogram_id', 'cluster_id']);
        }


        $ctr = 0; $defaultrow = 6;
        foreach($programs as $program){
            $items = $this->setItems($grouped, $program->id);
            if(sizeof($items) > 0 || $this->childWithItem($program, $grouped, 'program')){
                $programTitle = $this->appendBgColor('<b>'.$program->title.'</b>', '#92D050');
                array_push($body, [$programTitle]); $ctr = $ctr+1;
                array_push($mergedcells, $this->setRowMerge($defaultrow, $ctr, 'A', 'I'));
            }

            if($annex == 'annex-e'){
                $otheritems = $this->setItems($cigrouped, $program->id);
                $xlsxotheritems = $this->xlsxOtherItems($otheritems);
                // return ['body' => $xlsxotheritems, 'mergedcells' => []];
                foreach($xlsxotheritems as $item){
                    array_push($body, $item); $ctr = $ctr+1;
                    array_push($mergedcells, $this->setRowMerge($defaultrow, $ctr, 'A', 'B'));
                }
            }
            $xlsxitems = $this->xlsxItems($items, $annex);
            foreach($xlsxitems as $item){
                array_push($body, $item);
                $ctr = $ctr+1;
            }
            foreach($program->subprograms as $subprogram){
                $items = $this->setItems($grouped, $program->id, $subprogram->id);
                if(sizeof($items) > 0 || $this->childWithItem($subprogram, $grouped, $program->id)){
                    array_push($body, ['<b>'.$subprogram->title.'</b>']); $ctr = $ctr+1;
                    array_push($mergedcells, $this->setRowMerge($defaultrow, $ctr, 'A', 'I'));
                }
                $xlsxitems = $this->xlsxItems($items, $annex);
                foreach($xlsxitems as $item){
                    array_push($body, $item);
                    $ctr = $ctr+1;
                }
                if($program->id == 1 && $annex == 'annex-e'){
                    $totalitems = $this->setTotal($items);
                    $xlsxtotemitems = $this->xlsxTotalItems($totalitems);
                    foreach($xlsxtotemitems as $item){
                        array_push($body, $item);
                        $ctr = $ctr+1;
                    }
                }
                foreach($subprogram->clusters as $cluster){
                    $items = $this->setItems($grouped, $program->id, $subprogram->id, $cluster->id);
                    if(sizeof($items) > 0){
                        $clusterTitle = $this->appendBgColor('<b>'.$cluster->title.'</b>', '#BDD7EE');
                        array_push($body, [$clusterTitle]); $ctr = $ctr+1;
                        array_push($mergedcells, $this->setRowMerge($defaultrow, $ctr, 'A', 'I'));
                    }

                    $xlsxitems = $this->xlsxItems($items, $annex);
                    foreach($xlsxitems as $item){
                        array_push($body, $item);
                        $ctr = $ctr+1;
                    }
                }
            }
        }
            
        return ['body' => $body, 'mergedcells' => $mergedcells];
    }

    private function xlsxItems($items, $annex){
        $results = [];
        if($annex == 'annex-e'){
            foreach($items as $item){
                $withindicator = false; $length = sizeof($item->indicators);
                foreach($item->indicators as $key => $indicator){
                    $title = !$withindicator ? $item->project->title : null;
                    $array = [
                        '<wraptext>'.$title.'</wraptext>',
                    ];
                    if($indicator->details || !$indicator->common){
                        array_push($array, '<wraptext>'.$indicator->description.'</wraptext>');
                    }
                    $details = $this->xlsxDetails($indicator->details);
                    foreach($details as $detail){
                        array_push($array, $detail);
                    }
                    if($indicator->details || ($title && $withindicator) || $length == ($key+1) || !$indicator->common){
                        array_push($results, $array);
                        $withindicator = true;
                    }
                }
                foreach($item->subs as $sub){
                    $withindicator = false; $length = sizeof($item->indicators);
                    foreach($sub->indicators as $key => $indicator){
                        $title = $withindicator ? null : ($sub->temp_title == null ? $sub->subproject->title : $this->formatTempTitle($sub->temp_title));
                        $title = $title != null ? '-   '.$title : null;
                        $array = [
                            '<wraptext><i>'.$title.'</i></wraptext>',
                        ];
                        if($indicator->details){
                            array_push($array, '<wraptext>'.$indicator->description.'</wraptext>');
                        }
                        $details = $this->xlsxDetails($indicator->details);
                        foreach($details as $detail){
                            array_push($array, $detail);
                        }
                        if($indicator->details || ($title && $withindicator) || $length == ($key+1) || !$indicator->common){
                            array_push($results, $array);
                            $withindicator = true;
                        }
                    }
                    if(sizeof($sub->indicators) == 0){
                        $title = $sub->temp_title == null ? $sub->subproject->title : $this->formatTempTitle($sub->temp_title);
                        $title = '-   '.$title;
                        $array = [
                            '<wraptext><i>'.$title.'</i></wraptext>',
                        ];
                        array_push($results, $array);
                    }
                }
            }
        }
        return $results;
    }

    private function xlsxDetails($details, $bold = false, $blue = false){
        $columns = ['actual', 'estimate', 'physical_targets', 'first', 'second', 'third', 'fourth'];
        $array = [];
        foreach($columns as $column){
            $num = null;
            if($details !== null){
                $num = $details[$column] == 0 ? null : number_format($details[$column], 0, ".", ",");
            }
            $num = $bold ? '<b>'.$num.'</b>' : $num;
            $num = $blue ? '<style color="#0000FF">'.$num.'</style>' : $num;
            array_push($array, $num);
        }
        return $array;
    }

    private function xlsxTotalItems($item){
        $results = [];
        foreach($item->indicators as $key => $indicator){
            $title = $key == 0 ? $item->title : null;
            $array = [
                '<center><b>'.$title.'</b></center>',
                '<b><wraptext>'.$indicator->description.'</wraptext></b>'
            ];
            $details = $this->xlsxDetails($indicator->details, true);
            foreach($details as $detail){
                array_push($array, $detail);
            }
            array_push($results, $array);
        }
        foreach($item->subs as $sub){
            foreach($sub->indicators as $key => $indicator){
                $title = $key != 0 ? null : ($sub->temp_title == null ? $sub->subproject->title : $this->formatTempTitle($sub->temp_title));
                $array = [
                    '<b><wraptext>'.$title.'</wraptext></b>',
                    '<b><wraptext>'.$indicator->description.'</wraptext></b>'
                ];
                $details = $this->xlsxDetails($indicator->details, true);
                foreach($details as $detail){
                    array_push($array, $detail);
                }
                array_push($results, $array);
            }
            if(sizeof($sub->indicators) == 0){
                $title = $sub->temp_title == null ? $sub->subproject->title : $this->formatTempTitle($sub->temp_title);
                $array = [
                    '<b><wraptext>'.$title.'</wraptext></b>',
                ];
                array_push($results, $array);
            }
        }
        return $results;
    }

    private function xlsxOtherItems($indicators){
        $results = [];
        foreach($indicators as $key=>$indicator){
            $title = '<style color="#0000FF"><b><wraptext>'.$indicator->description.'</wraptext></b></style>';
            $array = [
                $title, null
            ];
            $details = $this->xlsxDetails($indicator->details, true, true);
            foreach($details as $detail){
                array_push($array, $detail);
            }
            array_push($results, $array);
            foreach($indicator->subindicators as $sub){
                $title = '<style color="#0000FF"><wraptext>'.$sub->description.'</wraptext></style>';
                $array = [
                    $title, null
                ];
                $details = $this->xlsxDetails($sub->details, false, true);
                foreach($details as $detail){
                    array_push($array, $detail);
                }
                array_push($results, $array);
            }
        }
        return $results;
    }

    private function childWithItem($parent, $grouped, $type){
        if($type == 'program'){
            foreach($parent->subprograms as $subprogram){
                $items = $this->setItems($grouped, $parent->id, $subprogram->id);
                if(sizeof($items) > 0){
                    return true;
                }
                foreach($subprogram->clusters as $cluster){
                    $items = $this->setItems($grouped, $parent->id, $subprogram->id, $cluster->id);
                    if(sizeof($items) > 0){
                        return true;
                    }
                }
            }
        }
        else{
            // Tried to replace $type with $id = '' for conditions, but its not working
            foreach($parent->clusters as $cluster){
                $items = $this->setItems($grouped, $type, $parent->id, $cluster->id);
                if(sizeof($items) > 0){
                    return true;
                }
            }
        }
        return false;
    }

    private function formatTempTitle($title){
        return $title == 'ms' ? 'MS' : ($title == 'phd' ? 'PhD' : $title);
    }

    private function filterquery($q, $filter, $id1, $id2, $id3){
        if($filter == 'Program'){
            if($id1 != 0){$q->where('program_id',     $id1);}
            if($id2 != 0){$q->where('subprogram_id',  $id2);}
            if($id3 != 0){$q->where('cluster_id',     $id3);}
        }
        else{
            if($id1 != 0){$q->where('division_id', $id1);}
            if($id2 != 0){$q->where('unit_id',     $id2);}
            if($id3 != 0){$q->where('subunit_id',  $id3);}
        }
    }

    private function appendBgColor($cell, $color){
        return '<style bgcolor="'.$color.'">'.$cell.'</style>';
    }

    private function setRowMerge($defaultrow, $ctr, $col1, $col2){
        $row = $defaultrow+$ctr;
        return $col1.$row.':'.$col2.$row;
    }

    
}