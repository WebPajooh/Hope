<?php

namespace App\Http\Controllers\User;

use App\Actions\User\CreateUserAction;
use App\Actions\User\LoginUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegistrationRequest;
use App\Http\Resources\UserTokenResource;

class AuthController extends Controller
{
    public function register(UserRegistrationRequest $request, CreateUserAction $createUserAction)
    {
        $createUserAction->execute($request);

        return $this->created();
    }

    public function login(UserLoginRequest $request, LoginUserAction $loginUserAction)
    {
        $token = $loginUserAction->execute($request);

        return $this->ok(
            UserTokenResource::make($token)
        );
    }
}
