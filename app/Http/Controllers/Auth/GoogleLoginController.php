<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Client;

class GoogleLoginController extends Controller
{
    public function redirectToGoogle(){
    return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
    try {
        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');
        $redirectUri = config('services.google.redirect');
        Socialite::extend('google', function ($app) use ($clientId, $clientSecret, $redirectUri) {
            return Socialite::buildProvider(\Laravel\Socialite\Two\GoogleProvider::class, [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect' => $redirectUri,
            ]);
        });
        $googleUser = Socialite::driver('google')->user();
        $existingUser = User::where('email', $googleUser->email)->first();
        if (!$existingUser) {
            $newUser = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'password' => null, 
            ]);

            $token = JWTAuth::fromUser($newUser);

            return response()->json(['user' => $newUser, 'token' => $token]);
        }
        $token = JWTAuth::fromUser($existingUser);

        return response()->json(['user' => $existingUser, 'token' => $token]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

}
