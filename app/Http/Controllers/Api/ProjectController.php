<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Subproject;
use DB;

class ProjectController extends Controller
{
    public function index(){
        return $this->getProjects();
    }

    public function show($id){
        return Project::findOrFail($id);
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
        $projects = Project::orderBy('id', 'asc')->get();

        foreach($projects as $project){
            $project->subprojects;

            $project->program;
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
}
