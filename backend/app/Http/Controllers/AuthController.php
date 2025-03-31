<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignupRequest;
use Illuminate\Support\Facades\Auth;
use App\Services\ApiResponseService;
use App\Models\User;
use App\Http\Requests\LoginRequest;

class AuthController extends Controller {
    public function signup(SignupRequest $request) {
        $credentials = $request->validated();

        $user = new User($credentials);
        $user->save();
        return ApiResponseService::success('User created successfully', $user, 201);
    }
    public function login (LoginRequest $request) {
        $credentials = $request->validated();

        if (!Auth::attempt($credentials)) {
            return ApiResponseService::error('Invalid credentials', null, 401);
        }
        $user = Auth::user();
        $token = $user->createToken('auth_token')->accessToken;
        return ApiResponseService::success('Login successful', ['user' => $user,'token' => $token]);
    }
}
