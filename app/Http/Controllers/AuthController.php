<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MOP_U_USERS; // Model for authentication
// use App\Models\user; // Model for authentication
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Show login form
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    // Handle login request
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Check if user exists
        $Cekuser = MOP_U_USERS::where('username', $request->username)->first();

        if ($Cekuser) {
            // Try to login
            if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
                $request->session()->regenerate();

                // Check if default password is used
                // if ($request->password === 'ptdh2025') {
                //     return redirect()->route('password.change')->with('warning', 'Please change your default password.');
                // }

                // Proceed to dashboard
                return redirect()->route('dashboard');
            }

            return back()->withErrors(['username' => 'Invalid Credentials.'])->withInput();
        }

        return back()->withErrors(['username' => 'User Not Found.'])->withInput();
    }

    public function showChangePasswordForm()
    {
        return view('auth.change-password'); // Create this Blade view
    }

    public function changePassword(Request $request)
    {
        // $request->validate([
        //     'current_password' => 'required',
        //     'new_password' => 'required|min:8|confirmed',
        // ]);

        $user = Auth::user();

        // if (!Hash::check($request->current_password, $user->password)) {
        //     return back()->withErrors(['current_password' => 'Current password is incorrect']);
        // }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Password changed successfully.');
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        return redirect()->route('login')->with('success', 'Logged out successfully!');
    }

    public function apiLogin(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = MOP_U_USERS::where('username', $request->username)->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found.',
            ], 404);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 401);
        }

        // Create token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }
}