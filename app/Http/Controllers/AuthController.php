<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        // unauthenticated error in the Handler
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        return response()->json([
            'message' => 'User successfuly registered',
            'user' =>  new UserResource($user)
        ], 201);
    }


    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'email' => 'required|string|email',
            'password' => 'required|string|',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 422);
        }
        if (!$token = auth('api')->attempt($validator->validated())) {
            return response()->json([
                'error' => 'unauthorized'
            ], 401);
        }
        return $this->createNewToken($token);
    }


    public function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => new UserResource(auth()->user())
        ]);
    }

    public function profile()
    {
        return new UserResource(auth()->user());
    }


    public function logout()
    {
        auth()->logout();
        return response()->json([
            'message' => 'User successfuly loggedout',

        ], 201);
    }
}
