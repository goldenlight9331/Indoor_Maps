<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Logs;
use App\Models\MiscSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use App\Models\setting;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class ApiController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            "name" => "required",
            "email" => "required|email|unique:users",
            "password" => "required",
        ]);

        if (User::where('email', $request->email)->first()) {
            return response([
                'message' => 'Email already exists',
                'status' => 'failed'
            ], 200);
        }

        // Automatically set role to 'User' during registration
        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password),
            "role" => 'User', // Automatically set to 'User'
        ]);

        $token = $user->createToken($request->email)->plainTextToken;
        return response([
            'token' => $token,
            'message' => 'Registration Success',
            'status' => 'success'
        ], 201);
    }

    public function login(Request $request)
    {

        $setting = Setting::first();  

        if ($setting && $setting->et) {
            // Decrypt the expiry date
            $expiryDate = Crypt::decrypt($setting->et);

            // Compare the expiry date with the current date
            $currentDate = Carbon::now();

            // Check if the expiry date has passed
            if ($currentDate->greaterThan(Carbon::parse($expiryDate))) {
                return response()->json(['message' => 'License Expired']);
            }
        }


        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();
        if ($user && Hash::check($request->password, $user->password)) {
            $token = $user->createToken($request->email)->plainTextToken;
            return response([
                'token' => $token,
                'message' => 'Login Success',
                'status' => 'success'
            ], 200);
        }
        return response([
            'message' => 'The Provided Credentials are incorrect',
            'status' => 'failed'
        ], 401);
    }

    public function profile()
    {
        // Add profile retrieval logic here if needed
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response([
            'message' => 'Logout Success',
            'status' => 'success'
        ], 200);
    }

    public function Company()
    {
        $setting = Setting::first();  // Assuming your expiry date is in the 'et' column of 'settings' table
        $filePath = public_path('indoor_data.db');

        if ($setting && $setting->et) {
            // Decrypt the expiry date
            $expiryDate = Crypt::decrypt($setting->et);

            // Compare the expiry date with the current date
            $currentDate = Carbon::now();

            // Check if the expiry date has passed
            if ($currentDate->greaterThan(Carbon::parse($expiryDate))) {
                return response()->json(['message' => 'License Expired']);
            }
        }

        if (File::exists($filePath)) {
            $headers = [
                'Content-Type' => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="indoor_navigation.db"', // Adjust content type as per your file type
            ];
        }
        return Response::download($filePath, 'indoor_navigation.db', $headers);
    }

    public function History()
    {
        $filePath = public_path('history.json');
        if (File::exists($filePath)) {
            $headers = [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="history.json"',
            ];
            return response()->download($filePath, 'history.json', $headers);
        } else {
            return response()->json(['error' => 'File not found'], 404);
        }
    }

    public function getVideo($filename)
    {
        $path = storage_path('app/public/vids/' . $filename); // Adjust the path accordingly

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }

    public function logInterval()
    {
        try {
            $miscSettings = MiscSetting::first();

            return response([
                'log_interval' => $miscSettings->log_interval,
                'status' => 'success'
            ], 200);
        }
        catch (\Exception $e) {
            \Log::error('Error storing logs: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Error getting log interval ' . $e->getMessage()], 500);
        }
    }


    public function storeLogs(Request $request)
    {
        try {
            // Log the incoming request data for debugging
            \Log::info('Incoming Log Data: ', $request->all());
    
            // Validate the incoming request
            $request->validate([
                'suggestions' => 'required|array',
                'suggestions.*.kiosk_id' => 'required|integer',
                'suggestions.*.store_id' => 'required|integer',
                'suggestions.*.timestamp' => 'required|date',
            ]);
    
            // Log validation passed
            \Log::info('Validation passed for incoming log data.');
    
            // Loop through each suggestion and create a log entry for each
            foreach ($request->input('suggestions') as $logEntry) {
                \Log::info('Processing log entry: ', $logEntry); // Log each entry
    
                Logs::create([
                    'kiosk_id' => $logEntry['kiosk_id'],
                    'store_id' => $logEntry['store_id'],
                    'timestamp' => $logEntry['timestamp'], // You can format it if needed
                ]);
            }
    
            // Log successful completion
            \Log::info('Logs stored successfully.');
    
            return response()->json(['success' => true, 'message' => 'Logs stored successfully'], 200);
    
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error storing logs: ' . $e->getMessage());
    
            return response()->json(['success' => false, 'error' => 'Error storing logs: ' . $e->getMessage()], 500);
        }
    }
    
}
