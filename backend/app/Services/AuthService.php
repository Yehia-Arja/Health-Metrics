<?php
namespace App\Services;

use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use Exception;
class AuthService
{
    public static function login($credentials)
    {
        if (!Auth::attempt($credentials)) {
            throw new Exception('Invalid credentials', 401);
        }
        $user = Auth::user();
        $http = new Client();

        try {

            $response = $http->post(config('services.passport.login_endpoint'), [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => config('services.passport.client_id'),
                    'client_secret' => config('services.passport.client_secret'),
                    'username' => $credentials['email'],
                    'password' => $credentials['password'],
                    'scope' => '',
                ],
            ]);

        } catch (Exception $e) {
            throw new Exception('Error connecting to the authentication server', 500, $e);
        }
        
        $data = json_decode((string) $response->getBody(), true);
        return [
            'user'          => $user,
            'access_token'  => $data['access_token'],
            'refresh_token' => $data['refresh_token'] ?? null,
            'expires_in'    => $data['expires_in'],
            'token_type'    => $data['token_type'] ?? 'Bearer',
        ];
    }
}
