<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use App\Models\UserTask;
use App\Models\UserProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\TaskResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\taskAllResource;
use App\Http\Resources\taskUserResources;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\taskProjectResources;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class taskController extends Controller
{
    //

    public function __construct()
    {
        // unauthenticated error in the Handler
        $this->middleware('auth:api');
    }

    public function create(Request $request, Project $project)
    {


        $validator = Validator::make($request->task, [
            'title' => 'required',
            'description' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $taskData = $validator->validated();
        $taskData['project_id'] = $project->id;
        $taskData['status_id'] = $request->status['id'];

        $task = Task::create($taskData);


        if ($request->has('users')) {

            //mapping the users with tasks
            $userTasks = Task::mapUserTask($request->users, $task);

            $userTasks = $userTasks->filter();
            UserTask::insert($userTasks->toArray());
        }

        return response()->json([
            'message' => 'task created',
        ], 201);
    }

    public function show(Request $request)
    {
        $tasks = DB::table('tasks');

        $keywords = trim($request->keywords);

        if (!empty($keywords)) {
            $tasks->where('title', 'LIKE', '%' . $keywords . '%');
        }

        $sortBy = $request->sortBy;
        if (!in_array($sortBy, ['id', 'created_at', 'status_id'])) {
            $sortBy = 'id';
        }

        $sortOrder = $request->sortOrder;
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        return TaskResource::collection($tasks->orderBy($sortBy, $sortOrder)->paginate(50));
    }

    public function update(Request $request,  $id)
    {
        $validator = Validator::make($request->task, [
            'title' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $taskData = $validator->validated();
        $taskData['status_id'] = $request->status['id'];

        $task = Task::findOrFail($id);
        $task->updated($taskData);

        if ($request->has('users')) {

            //mapping the users with tasks
            $userTasks = Task::mapUserTask($request->users, $task);

            UserTask::where('task_id', $task->id)->delete();
            UserTask::insert($userTasks->toArray());
        } else {
            UserTask::where('task_id', $task->id)->delete();
        }

        return response()->json(['message' => 'Task updated'], 200);
    }

    public function edit(Task $task)
    {

        $task = Task::where('id', $task->id)->with('users')->get();

        return taskUserResources::collection($task);
    }
    public function showtaskProject($id)
    {
        $listTask = Task::where('project_id', $id)->paginate(15);

        return TaskResource::collection($listTask);
    }
}
