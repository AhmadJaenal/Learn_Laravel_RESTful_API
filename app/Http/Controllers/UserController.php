<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\Mailer\Exception\HttpTransportException;

class UserController extends Controller
{
    public function register(UserRegisterRequest $request): JsonResponse {
        $data = $request->validate([ 
            'username' => 'required',
            'password' => 'required',
            'name' => 'required',
        ]);

        if(User::where("username", $data['username'])->count() == 1) {
            throw new HttpResponseException(response([
                "errors"=> [
                    "username" => [
                        "username already registered"
                    ]
                ]
            ], 400));
        }

        $user = new User($data);
        $user->password = Hash::make($data['password']);

        $user->save();

        return (new UserResource($user))->response()->setStatusCode(201);
    } 

    public function login(UserLoginRequest $request): UserResource {
        $data = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('username', $data['username'])->first();
        if(!$user || !Hash::check($data['password'], $user->password)) {
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => [
                        'username or password wrong'
                    ]
                ]
            ], 401));
        }

        $user->token = Str::uuid()->toString();
        $user->save();

        return new UserResource($user);

    }

}
