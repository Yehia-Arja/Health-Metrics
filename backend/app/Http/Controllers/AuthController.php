<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignupRequest;
use App\Http\Requests\LoginRequest;
use App\Services\ApiResponseService;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller {
    public function __construct() {}

    public function signup(SignupRequest $request){
        $data = $request->validated();

        $user = new User($data);
        $user->save();

        return ApiResponseService::success('User created successfully');
    }

    public function login(LoginRequest $request){
        $data = $request->validated();       
        if (!Auth::attempt(['email'=>$data['email'], 'password'=>$data['password']])) {
            return ApiResponseService::error('Invalid credentials', null, 401);
        }
        $user = Auth::user();
        $token = $user->createToken('Personal Access Token')->accessToken;
        return ApiResponseService::success('Login successful', ['user'=>$user,'token'=>$token]);               
    }
}
