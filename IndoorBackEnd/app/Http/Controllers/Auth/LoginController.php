<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\setting;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Show the login form or redirect if already authenticated.
     */
    public function login()
    {
        //if the user is authenticated and MFA is not verified, redirect to the MFA verification page
        if (Auth::check() && !session()->has('mfa_verified')) {
            return redirect()->route('mfa.verify');
        }
    
        //if the user is authenticated and MFA is verified, redirect to the dashboard
        if (Auth::check() && session()->has('mfa_verified')) {
            return redirect()->route('dashboard');
        }

        $setting = Setting::findOrFail(1);
    
        //if the user is not authenticated, show the login form
        return view('auth.login', compact('setting'));
    }
    
    /**
     * Handle ALL login requests.
     */
    public function adminLogin(Request $request)
    {
        // Validate the request input
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        // Credentials
        $credentials = $request->only('email', 'password');
    
        // Attempt to log in the user
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
    
            // Generate a random OTP/token
            $token = rand(100000, 999999);
    
            session(['mfa_token' => $token]);
    
            \Mail::to($user->email)->send(new \App\Mail\MFATokenMail($token, $user));
    
            // Redirect to MFA verification page
            return redirect()->route('mfa.verify');
        }
        else {
            return redirect()->back()->withErrors([
                'email' => 'These credentials do not match our records.',
            ]);
        }
    }

    /**
     * Log the user out of the application.
     */
    public function logout()
    {
        // Clear MFA-related session variables
        session()->forget(['mfa_token', 'mfa_verified']);
        Auth::logout();
        return redirect()->route('login');
    }

    /**
     * Show the MFA form for verification.
     */
    public function showMfaForm()
    {
        // If the user is logged in and MFA is already verified, redirect to the homepage
        if (Auth::check() && session()->has('mfa_verified')) {
            return redirect()->route('dashboard');
        }
    
        // Otherwise, show the MFA verification form
        return view('auth.mfa_verify');
    }

    public function resendMfaToken(Request $request)
    {
        $user = Auth::user(); // Get the authenticated user

        // Generate a new random OTP/token
        $newToken = rand(100000, 999999); // Secure token generation can be used as per your preference

        // Save the new token in the session
        session(['mfa_token' => $newToken]);

        // Resend the token via email
        \Mail::to($user->email)->send(new \App\Mail\MFATokenMail($newToken, $user));

        // Redirect back to the MFA verification page with a success message
        return redirect()->route('mfa.verify')->with('status', 'A new MFA token has been sent to your email.');
    }
    

    /**
     * Verify the MFA token entered by the user.
     */
    public function verifyMfaToken(Request $request)
    {
        $request->validate([
            'token' => 'required',
        ]);
    
        // Check if the token matches the one stored in the session
        if ($request->token == session('mfa_token')) {
            // Token is correct, clear the session and grant access
            session()->forget('mfa_token');
            session(['mfa_verified' => true]); // Set session variable for MFA verification
            return redirect()->route('dashboard'); // or admin dashboard
        }
    
        // Token was incorrect
        return redirect()->back()->withErrors(['token' => 'Invalid MFA token.']);
    }
}
