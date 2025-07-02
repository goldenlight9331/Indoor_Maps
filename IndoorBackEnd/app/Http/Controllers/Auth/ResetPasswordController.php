<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use App\Models\User; // Make sure to import the User model
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB; // Import the DB facade
use Illuminate\Support\Facades\Hash; // Import the Hash facade

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    protected function reset(Request $request)
    {
        $this->validate($request, $this->rules(), $this->validationErrorMessages());

        $user = User::where('email', $request->email)->first();

        // Log the result of the query
        \Log::info('User found: ' . ($user ? 'Yes' : 'No'));
        \Log::info('Incoming token: ' . $request->token);

        // Fetch the token from the password_reset_tokens table
        $storedToken = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->value('token');

        \Log::info('Stored token: ' . ($storedToken ?? 'null'));

        // Check if user exists and the token is valid
        if (!$user || !Hash::check($request->token, $storedToken)) {
            \Log::info('Is token valid: ' . (Hash::check($request->token, $storedToken) ? 'Yes' : 'No'));
            return back()->withErrors(['email' => trans('passwords.user')]);
        }

        // Update the user's password
        $user->password = bcrypt($request->password);
        $user->setRememberToken(Str::random(60));
        $user->save();

        // Optionally: You can log the user out (if they were logged in)
        auth()->logout();

        // Redirect to the login page
        return redirect()->route('login')->with('status', 'Your password has been reset. You can now log in.');
    }
}
