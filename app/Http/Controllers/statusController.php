<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Status;
use Illuminate\Http\Request;
use App\Http\Middleware\Authenticate;
use App\Http\Resources\statustResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class statusController extends Controller
{
    //
    protected $user;
    public function __construct()
    {
        // unauthenticated error in the Handler
        $this->middleware('auth:api');

    }

    public function show()
    {
        $status = Status::all();

        return new statustResource($status);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        Status::create($validator->validated());

        return response()->json([
            'message' => 'status created',
        ], 201);
    }

    public function edit($id)
    {
        $status = Status::findOrFail($id)->paginate(10);
        return new statustResource($status);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        Status::findOrFail($id)
            ->update($validator->validated());

        return response()->json([
            'message' => 'status updated',
        ], 201);
    }
}
