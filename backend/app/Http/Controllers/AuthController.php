<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignupRequest;
use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use App\Services\ApiResponseService;
use App\Models\User;

class AuthController extends Controller {
    public function __construct(protected AuthService $authService) {}

    public function signup(SignupRequest $request){
        $data = $request->validated();

        $user = new User($data);
        $user->save();

        return ApiResponseService::success('User created successfully', $user, 201);
    }

    public function login(LoginRequest $request){
        $data = $request->validated();       
        $result = AuthService::login($data);
        return ApiResponseService::success('Login successful', $result);               
    }
}
