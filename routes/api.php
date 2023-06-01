<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\VerificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// User registration route
Route::post('/register', [RegisterController::class, 'register']);

// User login route
Route::post('/login', [LoginController::class, 'login']);

// Verification route (requires authentication)
Route::middleware('auth:sanctum')->post('/verify', [VerificationController::class, 'verify']);


// Route::post('/verify', [VerificationController::class, 'verify']);

