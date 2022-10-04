<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Subproject;
use App\Models\Staff;
use App\Models\Program;
use App\Models\Subprogram;
use App\Models\Cluster;

// Project Profile
use App\Models\ProjectProfile;
use App\Models\Proponent;
use App\Models\ProposalContent;
use App\Models\LineItemBudget;
use App\Models\LibType;
use App\Models\LibItem;
use App\Models\ScheduleOfActivity;
use App\Models\SoaMonth;

use Auth;
use DB;

class ProjectController extends Controller
{
    public function index(){
        return $this->getProjects();
    }

    public function show($id){
        return $this->getProject($id);
    }
    
    private function getProject($id){
        return Project::with(
            'profiles.proponents', 
            'profiles.proposals', 
            'profiles.milestones.months', 
            'profiles.libs.types.items', 
            'profiles.activities.months', 
            'leader.profile.user', 
            'encoders.profile.user')
            ->where('id', $id)->first();
    }

    public function store(Request $request){

    }

    public function update(Request $request, $id){
        DB::beginTransaction();
        try {
            $proj = $request['projects'][0];
            $project = Project::findOrFail($id);
            $project->num = $proj['num'];
            $project->status = $proj['status'];
            $project->title = $proj['title'];
            $project->description = $proj['description'];
            $this->saveIds($project, $request);
            $project->save();

            $tempIds = [];
            foreach($proj['subprojects'] as $subp){
                $subproject = ($subp['id'] == '') ? new Subproject : Subproject::findOrFail($subp['id']);
                $subproject->title = $subp['title'];
                $subproject->project_id = $project->id;
                $subproject->save();
                if($subp['id'] != ''){
                    array_push($tempIds, $subp['id']);
                }
            }

            $forDelete = array_diff($request['tempIds'], $tempIds);
            foreach($forDelete as $id){
                $subproject = Subproject::findOrFail($id);
                $subproject->delete();
            }

            $tempIds = [];
            $forDelete = [];
            
            if($project->leader){
                array_push($forDelete, $project->leader->id);
                foreach($project->encoders as $encoder){
                    array_push($forDelete, $encoder->id);
                }
            }
            foreach($proj['staffs'] as $value){
                $staff = $value['id'] ? Staff::findOrFail($value['id']) : new Staff;
                $staff->project_id = $project->id;
                $staff->profile_id = $value['profile_id'];
                $staff->type = $value['type'];
                $staff->save();

                if($value['id'] != ''){
                    array_push($tempIds, $value['id']);
                }
            }

            foreach($forDelete as $id){
                if(!in_array($id, $tempIds)){
                    $staff = Staff::findOrFail($id)->delete();
                }
            }

            DB::commit();
            return ['message' => 'Successfully saved!', 'projects' => $this->getProjects()];
        }
        catch (\Exception $e){
            DB::rollback();
            return ['message' => 'Something went wrong', 'errors' => $e->getMessage()];
        }
    }

    public function destroy($id){

    }

    public function storeMultiple(Request $request){
        DB::beginTransaction();
        try {
            foreach($request['projects'] as $proj){
                $project = new Project;
                $project->num = $proj['num'];
                $project->status = $proj['status'];
                $project->title = $proj['title'];
                $this->saveIds($project, $request);
                $project->save();

                foreach($proj['subprojects'] as $subp){
                    $subproject = new Subproject;
                    $subproject->title = $subp['title'];
                    $subproject->project_id = $project->id;
                    $subproject->save();
                }
                
                foreach($proj['staffs'] as $value){
                    $staff = $value['id'] ? Staff::findOrFail($value['id']) : new Staff;
                    $staff->project_id = $project->id;
                    $staff->profile_id = $value['profile_id'];
                    $staff->type = $value['type'];
                    $staff->save();
                }
            }
            DB::commit();
            return ['message' => 'Successfully added!', 'projects' => $this->getProjects()];
        }
        catch (\Exception $e){
            DB::rollback();
            return ['message' => 'Something went wrong', 'errors' => $e->getMessage()];
        }
    }

    private function getProjects(){
        $projects = Project::with(['leader', 'encoders'])->orderBy('id', 'asc')->get();

        foreach($projects as $project){
            $project->subprojects;
            $project->program;
            $project->project_leader = '';
            if($project->leader){
                $user = $project->leader->profile->user;
                $project->project_leader = $user->firstname.' '.$user->lastname;
            }

            if($project->subprogram_id){ $project->subprogram; }
            if($project->cluster_id){ $project->cluster; }

            $project->division;
            if($project->unit_id){ $project->unit->name; }
            if($project->subunit_id){ $project->subunit; }
        }

        $divSort = $projects->sortBy('division_id');
        $divGroup = $divSort->groupBy(['division.name', 'unit.name', 'subunit.name']);

        $progSort = $projects->sortBy('program_id');
        $progGroup = $progSort->groupBy(['program.title', 'subprogram.title', 'cluster.title']);

        return ['program_group' => $progGroup, 'division_group' => $divGroup];
    }

    private function saveIds($project, $request){
        $project->program_id = $request['program_id'];
        $project->subprogram_id = ($request['subprogram_id'] == 0) ? null : $request['subprogram_id'];
        $project->cluster_id = ($request['cluster_id'] == 0) ? null : $request['cluster_id'];
        $project->division_id = $request['division_id'];
        $project->unit_id = ($request['unit_id'] == 0) ? null : $request['unit_id'];
        $project->subunit_id = ($request['subunit_id'] == 0) ? null : $request['subunit_id'];
    }

    public function showSortedProjects($selected){
        $program = Program::where('title', $selected)->get();
        $subprogram = Subprogram::where('title', $selected)->get();
        $cluster = Cluster::where('title', $selected)->get();
        $projects = [];
        $query = Project::query();
        $query = $query->with(['program', 'subprogram', 'cluster', 'subprojects', 'leader.profile.user']);
        if(sizeof($program) > 0){
            $query = $query->where('program_id', $program[0]->id);
        }
        if(sizeof($subprogram) > 0){
            $query = $query->where('subprogram_id', $subprogram[0]->id);
        }
        if(sizeof($cluster) > 0){
            $query = $query->where('cluster_id', $cluster[0]->id);
        }
        $projects = $query->get();
        return $projects;
    }

    public function updatePortfolio(Request $request, $id){
        $project = Project::findOrFail($id);
        $project->description = $request['description'];
        $project->save();

        $leader = $request['leader'];
        if($project->leader->profile->id != $leader['profile_id']){
            $project->leader()->delete();
            $newLeader = new Staff;
            $newLeader->profile_id = $leader['profile_id'];
            $newLeader->type = 'Leader';
            $newLeader->project_id = $project->id;
            $newLeader->save();
        }

        $encoders = $request['encoders'];
        $initialEncoders = $project->encoders;
        $ids = [];

        foreach($encoders as $enc){
            $encoder = $enc['id'] ? Staff::findOrFail($enc['id']) : new Staff;
            $encoder->type = 'Encoder';
            $encoder->profile_id = $enc['profile_id'];
            $encoder->project_id = $project->id;
            $encoder->save();

            array_push($ids, $encoder->id);
        }

        foreach($initialEncoders as $encoder){
            if(!in_array($encoder->id, $ids)){
                $encoder->delete();
            }
        }


        return $this->getProject($id);
    }

    // Project Profile Functions
    public function getProfiles(){

    }

    public function storeProfile(Request $request){
        DB::beginTransaction();
        try {
            $project = Project::findOrFail($request['project_id']);
            foreach($project->profiles as $profile){
                if($profile->year == $request['year']){
                    return ['message' => 'Project Profile for year '.$profile->year.' already existed.', 'errors' => 'Profile Year Exists'];
                }
            }

            $leader = $project->leader->profile->user;
            $projectleader = $leader->firstname.' '.$leader->lastname;

            $auth = Auth::user();
            $authProfileId = $auth->activeProfile->id;

            $profile = new ProjectProfile;
            $this->saveProfile($profile, $request, $projectleader);
            $this->saveProponents($request['proponents'], $profile->id);
            $this->saveProposalContents($request['proposalcontent'], $profile->id);
            $this->saveActivities($request['activities'], $profile->id);

            $lib = new LineItemBudget;
            $lib->status = 'Draft';
            $lib->source_of_funds = $request['source'];
            $lib->project_profile_id = $profile->id;
            $lib->created_by = $authProfileId;
            $lib->save();
            foreach($request['budgets'] as $budget){
                if(sizeof($budget['items']) > 0){
                    $libtype = new LibType;
                    $libtype->name = $budget['name'];
                    $libtype->lib_id = $lib->id;
                    $libtype->save();

                    foreach($budget['items'] as $budgetitem){
                        $item = new LibItem;
                        $item->name = $budgetitem['name'];
                        $item->amount = $this->formatAmount($budgetitem['amount']);
                        $item->lib_type_id = $libtype->id;
                        $item->save();
                    }
                }
            }

            DB::commit();
            return ['message' => 'Profile added!', 'project' => $this->getProject($project->id)];
        }
        catch (\Exception $e){
            DB::rollback();
            return ['message' => 'Something went wrong', 'errors' => $e->getMessage()];
        }
    }

    public function showProfile($id){
        return ProjectProfile::with('project', 'proponents', 'proposals', 'libs.types.items', 'activities.months')->where('id',$id)->first();
    }

    public function updateProfile(Request $request, $id){
        DB::beginTransaction();
        try {
            $project = Project::findOrFail($request['project_id']);
            $profile = ProjectProfile::findOrFail($id);

            foreach($project->profiles as $prof){
                if($prof->year == $request['year'] && $profile->id != $id){
                    return ['message' => 'Project Profile for year '.$profile->year.' already existed.', 'errors' => 'Profile Year Exists'];
                }
            }

            $this->saveProfile($profile, $request, $profile->project_leader);
            $this->saveProponents($request['proponents'], $profile->id, $profile->proponents);
            $this->saveProposalContents($request['proposalcontent'], $profile->id, $profile->proposals);
            $this->saveActivities($request['activities'], $profile->id, $profile->activities);

            DB::commit();
            return ['message' => 'Profile added!', 'profile' => $this->showProfile($id)];
        }
        catch (\Exception $e){
            DB::rollback();
            return ['message' => 'Something went wrong', 'errors' => $e->getMessage()];
        }
    }

    public function updateLib(Request $request){
        // pending, need further clarifications
        DB::beginTransaction();
        try {
            $id = $request['id'];
            $lib = LineItemBudget::findOrFail($id);
            $lib->source_of_funds = $request['source'];
            $lib->status = $request['status'];
            $lib->save();

            foreach($request['types'] as $type){
                if(in_array($type['name'], $request['selectedudget'])){
                    // proceed saving
                }
                else{
                    // find items to delete
                }
            }
        }
        catch (\Exception $e){
            DB::rollback();
            return ['message' => 'Something went wrong', 'errors' => $e->getMessage()];
        }
    }

    public function destroyProfile($id){

    }

    private function saveProfile($profile, $request, $projectleader){
        $profile->title               = $request['title'];
        $profile->status              = $request['status'];
        $profile->compliance_with_law = $request['comp'];
        $profile->project_leader      = $projectleader;
        $profile->start               = $request['start'];
        $profile->end                 = $request['end'];
        $profile->year                = $request['year'];
        $profile->project_id          = $request['project_id'];
        if(!$request['id']){
            $profile->created_by      = $authProfileId;
        }
        $profile->save();
    }

    private function saveProponents($proponents, $profileId, $initialProponents = []){
        $ids = [];
        foreach($proponents as $prop){
            $proponent = $prop['id'] ? Proponent::findOrFail($prop['id']) : new Proponent;
            $proponent->name = $prop['name'];
            $proponent->project_profile_id = $profileId;
            $proponent->save();
            array_push($ids, $proponent->id);
        }
        foreach($initialProponents as $proponent){
            if(!in_array($proponent->id, $ids)){
                $proponent->delete();
            }
        }
    }

    private function saveProposalContents($contents, $profileId, $initialContents = []){
        $ids = [];
        foreach($contents as $propcon){
            $content = $propcon['id'] ? ProposalContent::findOrFail($propcon['id']) : new ProposalContent;
            $content->title = $propcon['title'];
            $content->text = $propcon['text'];
            $content->project_profile_id = $profileId;
            $content->save();
            array_push($ids, $content->id);
        }
        foreach($initialContents as $content){
            if(!in_array($content->id, $ids)){
                $content->delete();
            }
        }
    }

    private function saveActivities($activities, $profileId, $initialActivities = []){
        $ids = []; 
        foreach($activities as $activity){
            $soa = $activity['id'] ? ScheduleOfActivity::findOrFail($activity['id']) : new ScheduleOfActivity;
            $initialMonths = $soa->months; $monthIds = [];
            $soa->title = $activity['title'];
            $soa->project_profile_id = $profileId;
            $soa->save();
            foreach($activity['months'] as $month){
                if($month['type'] != ''){
                    $soamonth = $month['id'] ? SoaMonth::findOrFail($month['id']) : new SoaMonth;
                    $soamonth->type = $month['type'];
                    $soamonth->start = $month['start'];
                    $soamonth->end = $month['end'];
                    $soamonth->month = $month['month'];
                    $soamonth->soa_id = $soa->id;
                    $soamonth->save();
                    array_push($monthIds, $soamonth->id);
                }
            }
            foreach($initialMonths as $month){
                if(!in_array($month->id, $monthIds)){
                    $month->delete();
                }
            }
            array_push($ids, $soa->id);
        }
        foreach($initialActivities as $activity){
            if(!in_array($activity->id, $ids)){
                $activity->months()->delete();
                $activity->delete();
            }
        }
    }

    private function formatAmount($amount){
        $newAmount = str_replace(',', '', $amount);
        return $amount ? abs((float)$newAmount) : 0;
    }

    // Project Profile Events

    public function getEvents($year, $start, $end){
        $profiles = ProjectProfile::with('project','milestones.dates')->where('year', $year)->get();
        $results = [];
        foreach($profiles as $profile){
            $milestones = [];
            foreach($profile->milestones as $milestone){
                $newdates = [];
                foreach($milestone->dates as $date){
                    if($date->month >= $start && $date->month <= $end){
                        array_push($newdates, $date);
                    }
                }
                $milestone->dates = [];
                $milestone->newdates = $newdates;
                if(sizeof($newdates) > 0){
                    array_push($milestones, $milestone);
                }
            }
            $profile->sorted = $milestones;
            if(sizeof($milestones) > 0){
                array_push($results, $profile);
            }
        }
        return $results;
    }
}
