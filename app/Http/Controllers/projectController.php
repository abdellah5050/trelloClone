<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ProjectResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class projectController extends Controller
{
    //
    public function __construct()
    {
         // unauthenticated error in the Handler
        $this->middleware('auth:api');
    }

    public function create(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        Project::create(array_merge(
            $validator->validated(),
            ['user_id' => auth()->user()->id]
        ));

        return response()->json([
            'message' => 'projected created',
        ], 201);
    }

    public function show()
    {

        $projects = Project::where('user_id', auth()->user()->id)->get();

        return ProjectResource::collection($projects);
    }


    public function edit($project)
    {
        $project = Project::where('user_id', auth()->user()->id)
            ->findOrFail($project)->paginate(10);

        return new ProjectResource($project);
    }

    public function update(Request $request, $project)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        Project::findOrFail($project)
            ->update($validator->validated());

        return response()->json([
            'message' => 'Project updated',
        ], 200);
    }
}
