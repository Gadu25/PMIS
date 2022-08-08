<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tag;
use DB;

class TagController extends Controller
{
    public function index(){
        return Tag::all();
    }

    public function store(Request $request){
        DB::beginTransaction();
        try {
            $tag = new Tag;
            $tag->name = $request['name'];
            $tag->program_id = $request['program_id'];
            $tag->type = $request['type'];
            $tag->save();

            DB::commit();
            return ['message' => 'Successfully added!', 'tags' => $this->getTagsByType($request['type'])];
        }
        catch (\Exception $e){
            DB::rollback();
            return ['message' => 'Something went wrong', 'errors' => $e->getMessage()];
        }
    }

    public function update(Request $request, $id){
        DB::beginTransaction();
        try {
            $tag = Tag::findOrFail($id);
            $tag->name = $request['name'];
            $tag->program_id = $request['program_id'];
            $tag->type = $request['type'];
            $tag->save();
            
            DB::commit();
            return ['message' => 'Successfully saved!', 'tags' => $this->getTagsByType($request['type'])];
        }
        catch (\Exception $e){
            DB::rollback();
            return ['message' => 'Something went wrong', 'errors' => $e->getMessage()];
        }
    }

    public function destroy($id){
        DB::beginTransaction();
        try{
            $tag = Tag::findOrFail($id);
            $tag->delete();

            DB::commit();
            return ['message' => 'Successfully deleted!', 'tags' => $this->getTagsByType($tag->type)];
        }
        catch (\Exception $e){
            DB::rollback();
            return ['message' => 'Something went wrong', 'errors' => $e->getMessage()];
        }
    }

    public function getTagsByType($type){
        $tags = Tag::where('type', $type)->orderBy('id','asc')->get();
        $grouped = $tags->groupBy(['program_id']);
        return $grouped;
    }
}
