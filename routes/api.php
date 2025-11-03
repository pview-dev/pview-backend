<?php
use App\Http\Controllers\authController;
use App\Http\Middleware\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Laravel\Socialite\Facades\Socialite;


// authenticate
Route::prefix('auth')->middleware([Guest::class])->group(function(){
    Route::post('login',[authController::class,'login']);
    Route::post('register',[authController::class,'register']);
    Route::get('logout',function(){
        Auth::login();
    });
});

Route::prefix('auth/github')->middleware(['web',Guest::class])->group(function(){
    Route::get('redirect', function(){
        return Socialite::driver('github')->redirect();
    });
    Route::get('callback', [authController::class,'viaGithubCallback']);
});

