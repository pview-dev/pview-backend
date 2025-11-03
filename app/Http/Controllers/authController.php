<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Socialite;

use function Pest\Laravel\json;

class authController extends Controller
{
    public function login(Request $request){
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        if(!Auth::attempt($validated,true)){
            return response()->json(['response'=>'unable to login'],401);
        }

        return response()->json(['message' => 'Login successful'], 200);
    }
    public function register(Request $request){
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::firstOrCreate([
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user,true);
        return response()->json(['message' => 'Registration successful'], 201);
    }

    public function viaGithubCallback(Request $request){
        $githubUser = Socialite::driver('github')->user();

        $user = User::updateOrCreate(
            ['email' => $githubUser->email], // find user using email
            [
                'name' => $githubUser->name,
                'github_id' => $githubUser->id,
                'github_token' => $githubUser->token,
                'github_refresh_token' => $githubUser->refreshToken,
            ]
        );
        $user->image()->updateOrCreate([
            'path' => $githubUser->avatar,
            'alt' => '',
        ]);

        Auth::login($user,true);
        return redirect(env('FRONTEND_URL') . '/');
    }
}
