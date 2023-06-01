<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        try {
            // Validate user login data
            $validatedData = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            // Attempt to authenticate the user
            if (!Auth::attempt($validatedData)) {
                throw ValidationException::withMessages([
                    'email' => 'Invalid credentials',
                ]);
            }

            // Generate an authentication token
            $token = $request->user()->createToken('auth-token')->plainTextToken;

            // Return the token as a response
            return response()->json(['token' => $token]);
        } catch (ValidationException $e) {
            // Handle validation errors
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Handle any other exceptions
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
