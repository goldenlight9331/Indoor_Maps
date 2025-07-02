<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ads;
use App\Models\Promotion;
use App\Models\Banner;
use App\Models\Theme;
use App\Models\Video;
use App\Models\forntEnd;
use App\Models\Kiosk;
use App\Models\setting;
use App\Models\User;
use App\Models\Logs;
use App\Models\MiscSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;


class DashboardController extends Controller
{
    public function dashboard()
    {
        $companies = DB::table('companies')->get()->count();
        $lots = DB::table('lots')->get()->count();
        $users = User::all()->count();
        $kioks = Kiosk::all()->count();
    
        $usersAll = User::paginate(5);
        $kioksData = Kiosk::paginate(5);
        $companiesData = DB::table('companies')->paginate(5);
    
        // Fetch logs ordered by latest timestamp first
        $paginatedLogs = Logs::orderBy('timestamp', 'desc')->paginate(5)->map(function ($log) {
            // Fetch kiosk name using Kiosk model
            $kiosk = Kiosk::find($log->kiosk_id);
    
            // Fetch company name using DB facade (without a Company model)
            $company = DB::table('companies')->where('id', $log->store_id)->first();
    
            return (object) [
                'kiosk_id' => $log->kiosk_id,
                'kiosk_name' => $kiosk ? $kiosk->name : 'Unknown Kiosk',
                'store_id' => $log->store_id,
                'company_name' => $company ? $company->storename : 'Unknown Company',
                'timestamp' => $log->timestamp,
                'created_at' => $log->created_at
            ];
        });
    
        return view('Admin.dashboard', compact([
            'companies', 'users', 'lots', 'kioks', 'usersAll', 'kioksData', 'companiesData', 'paginatedLogs'
        ]));
    }
    
    public function selectPlaza()
    {
        return view('Admin.selectPlaza.selectPlaza');
    }

    public function allCompanies()
    {
        $companies = DB::table('companies')
            ->get();

            return view("Admin.companies.allCompanies", compact('companies'));
    }
    public function editCompanies($id)
    {
        $company = DB::table('companies')->where('id', $id)->first();

        // if ($company) {
        //     if ($company->image_data) {
        //         $company->image_data = stream_get_contents($company->image_data);
        //     } else {
        //         $company->image_data = null;
        //     }
        // dd($company);
        return view('Admin.companies.editCompany', compact('company'));

        // }
    }
    public function updateCompanies(Request $request, $id)
    {
        $request->validate([
            'image' => 'nullable|image'
        ]);

        $updateData = [
            'storename' => $request->input('storename'),
            'category' => $request->input('category'),
            'phone' => $request->input('phonenumber'),
            'day' => $request->input('day'),
            'time' => $request->input('time'),
            'day_1' => $request->input('day_1'),
            'time_1' => $request->input('time_1'),
            'website' => $request->input('website'),
            'sub_cat' => $request->input('sub_cat'),
        ];
        // if ($request->hasFile('image') && $request->file('image')->isValid()) {
        //     $imageFile = $request->file('image');
        //     $imageName = uniqid() . '.' . $imageFile->getClientOriginalExtension();
        //     $imagePath = 'companies/' . $imageName;
        //     $imageFile->move(public_path('companies'), $imageName);
        //     if ($request->hasFile('images') && file_exists(public_path($updateData['images']))) {
        //         unlink(public_path($updateData['images']));
        //     }

        //     $updateData['images'] = $imagePath;
        // }
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $imageData = file_get_contents($imageFile->getPathname());
            $base64ImageData = base64_encode($imageData);
            // $setting->image = $base64ImageData;
            $updateData['image_data'] = $base64ImageData;
        }


        DB::table('companies')->where('id', $id)->update($updateData);
        return redirect()->route('allCompanies')->with("message", "Company update successfully");
    }
    public function viewCompanies($id)
    {
        $companies = DB::table('companies')
            ->select(['*', DB::raw('st_x(geom) as longitude'), DB::raw('st_y(geom) as latitude')])
            ->orderBy('id', 'ASC')
            ->where("id", $id)
            ->get();
        $companies->transform(function ($company) {
            $company->image_data = $company->image_data ? stream_get_contents($company->image_data) : null;
            return $company;
        });
        $point = [];
        foreach ($companies as $kiosk) {
            $levelid = $kiosk->levelid;
            $markKiosks = DB::table('kiosks')
                ->select(DB::raw('ST_X(geom) as latitude'), DB::raw('ST_Y(geom) as longitude'))
                ->where('levelid', $levelid)
                ->get();
            foreach ($markKiosks as $markKiosk) {
                $point[] = $markKiosk;
            }

            $results = DB::table('lots')
                ->selectRaw('ST_AsGeoJSON(lots.geom) as geojson, lots.levelcode, lots.category, com.STORENAME as storename')
                ->leftJoin(DB::raw("(SELECT STORENAME, geom, levelid FROM companies WHERE levelid = '$levelid') as com"), function ($join) {
                    $join->on(DB::raw('ST_Within(com.geom, lots.geom)'), '=', DB::raw('true'));
                })
                ->where('lots.levelid', $levelid)
                ->get();
        }


        return view("Admin.companies.viewCompanies", compact(["companies", "point", "results"]));
    }

    public function createUsers()
    {
        return view("Admin.users.create");
    }

    public function saveUsers(Request $request)
    {
        // Validate the request input
        $request->validate([
            'name' => 'required|string|max:255|not_in:NAVISupport',
            'email' => 'required|email|unique:users,email', // Ensure email is unique
            'password' => 'required|min:8|confirmed', // 'confirmed' checks password_confirmation automatically
        ]);

        if ($request->name === 'NAVISupport') {
            return redirect()->back()->with('message', "You cannot add a user with this name!");
        }

    
        try {
            // Create new user
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = \Hash::make($request->password); // Hash the password
            $user->save();
    
            // Redirect with success message
            return redirect()->back()->with('message', 'Successfully Added User');
    
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('User creation failed: '.$e->getMessage());
    
            // Redirect back with an error message
            return redirect()->back()->withErrors(['error' => 'Something went wrong while adding the user. Please try again.']);
        }
    }
    

    public function allUser()
    {
        $users = User::all();
        return view("Admin.users.users", compact("users"));
    }

    public function editUsers($id)
    {
        $user = User::findOrFail($id);
        return view("Admin.users.update", compact("user"));
    }

    public function updateUsers(Request $request, $id)
    {
        $user = User::findOrFail($id);
    
        // Prevent updates for NAVISupport
        if ($user->name === 'NAVISupport') {
            return redirect()->back()->with('message', "You cannot change details of NAVISupport.");
        }
    
        $this->validate($request, [
            'name' => 'required|string|max:255|not_in:NAVISupport',
            'email' => 'required|email|max:255',
            'role' => 'required|in:User,Admin,Super Admin',
        ]);
    
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->save();
    
        return redirect()->route("allUser")->with("message", "Successfully Edited User");
    }
        

    public function changePasswordUser($id)
    {
        $user = User::findOrFail($id);
        return view("Admin.users.changePassword", compact("user"));
    }

    public function UpdatePasswordUser(Request $request, $id)
    {
        // Find the user by ID
        $user = User::findOrFail($id);
    
        // Check if the logged-in user is trying to change their own password
        if ($request->user()->id !== $user->id && !$request->user()->isSuperAdmin()) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to change this user\'s password.');
        }
    
        // Validate the request
        $this->validate($request, [
            'current_password' => 'required',
            'new_password' => 'required|min:8',
            'password_confirmation' => 'required|same:new_password',
        ]);
    
        // Check if the current password matches
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('message', "Old Password Doesn't match!");
        }
    
        // Update the new password
        $user->password = Hash::make($request->new_password);
        $user->save();
    
        return redirect()->route("allUser")->with('message', "Password Changed Successfully");
    }
    

    public function viewUser($id)
    {
        $user = User::findOrFail($id);
        return view("Admin.users.viewUser", compact("user"));
    }

    public function deleteUser($id)
    {
        $userToDelete = User::findOrFail($id);
        $loggedInUser = auth()->user();
    
        // Check if the logged-in user is trying to delete themselves
        if ($loggedInUser->id === $userToDelete->id) {
            return redirect()->back()->with("DeleteMessage", "You cannot delete your own account.");
        }
    
        // Check if the user to delete is a Super Admin or is the 'NAVISupport' user
        if ($userToDelete->isSuperAdmin() || $userToDelete->name === 'NAVISupport') {
            return redirect()->back()->with("DeleteMessage", "You cannot delete this user.");
        }
    
        // Proceed with deletion
        $userToDelete->delete();
        return redirect()->back()->with("DeleteMessage", "Successfully deleted user.");
    }    
    
    public function kiosk()
    {
        // $kiosks = Kiosk::select(['*', DB::raw('ST_X(geom) as longitude'), DB::raw('ST_Y(geom) as latitude')])
        //     ->orderBy('id', 'ASC')
        //     ->get();
        $kiosks = kiosk::all();

        $distinctLevelIds = DB::table('lots')
            ->select('levelid')
            ->distinct()
            ->orderBy('levelid')
            ->pluck('levelid');
        return view("Admin.kiosk.createkiosk", compact(["distinctLevelIds", "kiosks"]));
    }

    public function Addkiosk(Request $request)
    {
        $request->validate([
            'kioksname' => 'required',
            'latitude' => "required|numeric",
            'longitude' => 'required|numeric',
            'levelID' => 'required',
        ]);
        // $geom = DB::raw("ST_GeomFromText('POINT($request->longitude $request->latitude)', 4326)");
        $Kisok = new Kiosk();
        $Kisok->name = $request->kioksname;
        $Kisok->Longitude = $request->longitude;
        $Kisok->Latitude = $request->latitude;
        // $Kisok->geom = $request->longitude;
        $Kisok->levelid = $request->levelID;
        $Kisok->save();

        return redirect()->back()->with("message", "Kisok added Succesfully");
    }

    public function editKiosk($id)
    {
        $editKiosk = $kiosk = Kiosk::where('id', $id)
            ->orderBy('id', 'ASC')
            ->first();
        $kiosks = Kiosk::orderBy('id', 'ASC')
            ->get();
        $distinctLevelIds = DB::table('lots')
            ->select('levelid')
            ->distinct()
            ->orderBy('levelid')
            ->pluck('levelid');
        return view("Admin.kiosk.editkiosk", compact(['editKiosk', 'distinctLevelIds', 'kiosks']));
    }
    public function updateKiosk(Request $request, $id)
    {
        $request->validate([
            'kioksname' => 'required',
            'latitude' => "required|numeric",
            'longitude' => 'required|numeric',
            'levelID' => 'required',
        ]);
        // $geom = DB::raw("ST_GeomFromText('POINT($request->longitude $request->latitude)', 4326)");
        $Kisok = Kiosk::findOrFail($id);
        $Kisok->name = $request->kioksname;
        // $Kisok->geom = $geom;
        $Kisok->Longitude = $request->longitude;
        $Kisok->Latitude = $request->latitude;
        $Kisok->levelid = $request->levelID;
        $Kisok->update();
        $distinctLevelIds = DB::table('lots')
            ->select('levelid')
            ->distinct()
            ->orderBy('levelid')
            ->pluck('levelid');
        // return redirect()->back()->with("message", "Kisok Update Succesfully");
        // return view("Admin.kiosk.createkiosk", compact('distinctLevelIds'));
        return redirect()->route('kiosk');
    }

    public function ViewKiosk($id)
    {
        $kiosks = Kiosk::select(['*', DB::raw('st_x(geom) as longitude'), DB::raw('st_y(geom) as latitude')])
            ->orderBy('id', 'ASC')
            ->where("id", $id)
            ->get();

        $point = [];
        foreach ($kiosks as $kiosk) {
            $levelid = $kiosk->levelid;
            $markKiosks = DB::table('kiosks')
                ->select(DB::raw('ST_X(geom) as latitude'), DB::raw('ST_Y(geom) as longitude'))
                ->where('levelid', $levelid)
                ->get();
            foreach ($markKiosks as $markKiosk) {
                $point[] = $markKiosk;
            }
            $results = DB::table('lots')
                ->selectRaw('ST_AsGeoJSON(lots.geom) as geojson, lots.levelcode, lots.category, com.STORENAME as storename')
                ->leftJoin(DB::raw("(SELECT STORENAME, geom, levelid FROM companies WHERE levelid = '$levelid') as com"), function ($join) {
                    $join->on(DB::raw('ST_Within(com.geom, lots.geom)'), '=', DB::raw('true'));
                })
                ->where('lots.levelid', $levelid)
                ->get();
        }

        return view("Admin.kiosk.viewkiosk", compact("kiosks", "point", "results"));
    }


    public function setting()
    {
        $setting = Setting::findOrFail(1);
        
        if ($setting && $setting->et) {
            // Decrypt the expiry date if it's not empty
            $setting->et = Crypt::decrypt($setting->et);
        }
        
        return view("Admin.setting.setting", compact('setting'));
    }
    public function settingUpdate(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'image' => 'nullable|image',
            'et' => 'required|date',
        ]);

        $setting = Setting::findOrFail(1);
        // $setting = Setting::firstOrNew();
        $setting->name = $request->name;
        $setting->nor_ang = $request->nor_ang;
        $setting->et = Crypt::encrypt($request->et);

        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $imageData = file_get_contents($imageFile->getPathname());
            $base64ImageData = base64_encode($imageData);
            $setting->image = $base64ImageData;
        }
        // dd($setting);
        $setting->save();
        return redirect()->back()->with('message', 'Settings updated successfully');
    }


    public function adsAndPromotions()
    {
        $ads = Ads::findOrFail(1); // Ensure this is the correct model and record
        $promotion = Promotion::find(1); // Ensure this is the correct model and record

        return view('Admin.ads.ads_and_promotions', compact('ads', 'promotion'));
    }



    public function uploadAds(Request $request)
    {
        $request->validate([
            'image' => 'required|image',
        ]);

        $ads = ads::findOrFail(1);
        // if ($request->hasFile('image') && $request->file('image')->isValid()) {
        //     $imageFile = $request->file('image');
        //     $imageName = uniqid() . '.' . $imageFile->getClientOriginalExtension();
        //     $imagePath = 'public/ads/' . $imageName;
        //     $imageFile->move(public_path('ads'), $imageName);
        //     if ($ads->image && file_exists(public_path($ads->image))) {
        //         unlink(public_path($ads->home));
        //     }
        //     $ads->image = $imagePath;
        //     $ads->save();
        // }
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $imageData = file_get_contents($imageFile->getPathname());
            $base64ImageData = base64_encode($imageData);
            // $setting->image = $base64ImageData;
            $ads->image = $base64ImageData;
        }
        // dd($ads);
        $ads->save();
        return redirect()->back()->with('message', 'Ads updated successfully');
    }



    public function uploadPromotions(Request $request)
    {
        $request->validate([
            'image' => 'required|image',
        ]);

        $promotion = Promotion::find(1); // Fetch a record; adjust as needed

        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $imageData = file_get_contents($imageFile->getPathname());
            $base64ImageData = base64_encode($imageData);
            $promotion->image = $base64ImageData;
        }

        $promotion->save();

        return redirect()->back()->with('message', 'Promotion updated successfully');
    }
    

    public function banners()
    {
        $banners = Banner::find(1);

        // Log::info('Banner Model:', ['banner' => $banners]);

        return view("Admin.banners.banners", compact('banners'));
    }

    public function uploadBanners(Request $request)
    {
        $request->validate([
            'image' => 'required|image',
        ]);

        $banners = Banner::find(1);
        
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $imageData = file_get_contents($imageFile->getPathname());
            $base64ImageData = base64_encode($imageData);
            $banners->image = $base64ImageData;
        }

        $banners->save();
        
        return redirect()->back()->with('message', 'Banners updated successfully');
    }

    public function themes()
    {
        $theme = Theme::first();

        return view('Admin.themes.themes', compact('theme'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'color' => 'required|string',
            'button_color' => 'required|string',
            'options_ribbon_color' => 'required|string',
            'browse_venue_text_color' => 'required|string',
            'browse_venue_ribbon_color' => 'required|string',
            'categories_background_color' => 'required|string',
            'map_container_color' => 'required|string', // Add this line
            'lots_color' => 'required|string', // Add this line
        ]);
    
        // Find existing theme or create a new one
        $theme = Theme::first() ?? new Theme();
        $theme->color = $request->input('color');
        $theme->button_color = $request->input('button_color');
        $theme->options_ribbon_color = $request->input('options_ribbon_color');
        $theme->browse_venue_text_color = $request->input('browse_venue_text_color');
        $theme->browse_venue_ribbon_color = $request->input('browse_venue_ribbon_color');
        $theme->categories_background_color = $request->input('categories_background_color');
        $theme->map_container_color = $request->input('map_container_color'); // Add this line
        $theme->lots_color = $request->input('lots_color'); // Add this line
        $theme->save();
    
        return redirect()->route('themes.index')->with('message', 'Theme settings updated successfully');
    }
    

    public function videos()
    {
        return view('Admin.videos.upload'); // Create this view file
    }

    public function uploadVideos(Request $request)
    {
        \Log::info('Video upload request received.');
    
        // Validate the request
        $request->validate([
            'video' => 'required|file|mimes:mp4,avi,mkv|max:10240', // 200MB max size
            'video_type' => 'required|in:landscape,portrait',
        ], [
            'video.required' => 'Please select a video to upload.',
            'video.mimes' => 'Only mp4, avi, and mkv video formats are allowed.',
            'video.max' => 'The video size cannot exceed 200MB.',
            'video_type.in' => 'Invalid video type selected.',
        ]);
    
        // Proceed to handle the video upload
        if ($request->hasFile('video')) {
            $videoFile = $request->file('video');
            $videoType = $videoFile->getClientOriginalExtension(); // Get video extension
            $originalName = pathinfo($videoFile->getClientOriginalName(), PATHINFO_FILENAME);
            $videoName = $originalName . '_' . time() . '.' . $videoType;
            $videoPath = $videoFile->storeAs('vids', $videoName, 'public');
    
            \Log::info('Uploaded video details: ', [
                'original_name' => $originalName,
                'size' => $videoFile->getSize(),
                'path' => $videoPath,
                'type' => $videoType,
                'video_type' => $request->video_type,
            ]);
    
            // Remove any existing video records of the same video_type
            $existingVideo = Video::where('video_type', $request->video_type)->first();
            if ($existingVideo) {
                Storage::disk('public')->delete($existingVideo->path);
                $existingVideo->delete();
            }
    
            // Save the new video details to the database
            Video::create([
                'title' => $originalName,
                'path'  => $videoPath,
                'type'  => $videoType,
                'video_type' => $request->video_type,
            ]);
    
            return redirect()->back()->with('message', ucfirst($request->video_type) . ' video uploaded successfully.');
        } else {
            // Customize error messages for each video type
            if ($request->video_type === 'landscape') {
                $errorMessage = 'No landscape video file found. Please upload a valid file.';
            } else {
                $errorMessage = 'No portrait video file found. Please upload a valid file.';
            }
    
            \Log::error($errorMessage);
            return redirect()->back()->with('error', $errorMessage);
        }
    }


    public function misc()
    {
        // Fetch the first record of misc settings, or create a new one if none exist
        $miscSettings = MiscSetting::firstOrCreate(
            [],
            [
                'screensaver_time' => 120,
                'city' => 'Default City',
                'state' => 'Default State',
                'log_interval' => 3600,
                'day_image' => null,
                'night_image' => null
            ]
        );
    
        // Determine MIME types if images exist
        $dayImageMime = $miscSettings->day_image ? 'image/jpeg' : null;
        $nightImageMime = $miscSettings->night_image ? 'image/jpeg' : null;
    
        return view('Admin.misc.misc', compact('miscSettings', 'dayImageMime', 'nightImageMime'));
    }
    
    
    public function updateMisc(Request $request)
    {
        $validated = $request->validate([
            'screensaver_time' => 'required|integer|min:30',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'log_interval' => 'required|integer|min:1',
            'day_image' => 'nullable|image',
            'night_image' => 'nullable|image',
        ]);
    
        $miscSettings = MiscSetting::first() ?? new MiscSetting();
    
        // Update text fields
        $miscSettings->screensaver_time = $validated['screensaver_time'];
        $miscSettings->city = $validated['city'];
        $miscSettings->state = $validated['state'];
        $miscSettings->log_interval = $validated['log_interval'];
    
        // Handle day image upload and base64 encode it
        if ($request->hasFile('day_image')) {
            $imageFile = $request->file('day_image');
            $imageData = file_get_contents($imageFile->getRealPath());
            $base64ImageData = base64_encode($imageData);
            $miscSettings->day_image = $base64ImageData;
        }
    
        // Handle night image upload and base64 encode it
        if ($request->hasFile('night_image')) {
            $imageFile = $request->file('night_image');
            $imageData = file_get_contents($imageFile->getRealPath());
            $base64ImageData = base64_encode($imageData);
            $miscSettings->night_image = $base64ImageData;
        }
    
        $miscSettings->save();
    
        return redirect()->route('misc')->with('success', 'Settings updated successfully!');
    }
        
    

    public function forntSetting()
    {
        $fornt = forntEnd::findOrFail(1);
        return view('Admin.front.front', compact('fornt'));
    }

    public function UpdateforntSetting(Request $request)
    {
        $request->validate([
            'home' => 'required|image',
            'navigation' => 'nullable|image',
            'more' => 'nullable|image',
        ]);

        try {
            $frontEnd = forntEnd::findOrFail(1);
            
            if ($request->hasFile('home')) {
                $imageFile = $request->file('home');
                $imageData = file_get_contents($imageFile->getPathname());
                $base64ImageData = base64_encode($imageData);
                // $setting->image = $base64ImageData;
                $frontEnd->home = $base64ImageData;
                $frontEnd->save();
            }

            if ($request->hasFile('navigation')) {
                $imageFile = $request->file('navigation');
                $imageData = file_get_contents($imageFile->getPathname());
                $base64ImageData = base64_encode($imageData);
                // $setting->image = $base64ImageData;
                $frontEnd->navigation = $base64ImageData;
                $frontEnd->save();
            }

            if ($request->hasFile('more')) {
                $imageFile = $request->file('more');
                $imageData = file_get_contents($imageFile->getPathname());
                $base64ImageData = base64_encode($imageData);
                // $setting->image = $base64ImageData;
                $frontEnd->more = $base64ImageData;
                $frontEnd->save();
            }

            $frontEnd->save();
            
            return redirect()->back()->with('message', 'Front-End Settings updated successfully');
        }
        catch (\Exception $e) {
            Log::error('Error saving FrontEnd settings: ' . $e->getMessage());
            return redirect()->back()->with('message', 'An error occurred while updating Front-End Settings' . $e->getMessage());
        }
    }




    public function profile()
    {
        $users = Auth()->user();
        // if ($users->image) {
        //     $users->image = $users->image ? stream_get_contents($users->image) : null;
        // }
        return view("Admin.profile.profile", compact('users'));
    }
    public function updateProfile(Request $request)
    {
        $user = Auth()->user();
        
        $request->validate([
            'image' => 'nullable|image',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);
    
        // If the user is not NAVISupport, validate and update the name field
        if ($user->name !== 'NAVISupport') {
            $request->validate(['name' => 'required']);
            $user->name = $request->name;
        }
    
        // Update other fields
        $user->email = $request->email;
    
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $imageData = file_get_contents($imageFile->getPathname());
            $base64ImageData = base64_encode($imageData);
            $user->image = $base64ImageData;
        }
    
        $user->save();
    
        return redirect()->back()->with('message', 'Profile updated successfully');
    }
    

    public function ConvertData()
    {

        $response = Http::get('http://localhost:10000/update-database');
        if ($response->successful()) {
            return redirect()->back()->with('success', 'Data transfer and further processing initiated.');
        } else {
            // API call failed
            return response()->json(['success' => false, 'message' => 'Failed to call API'], 500);
        }

    }

    public function viewLogs(Request $request)
    {
        // Validate that if one date is selected, the other must also be provided
        $request->validate([
            'start_date' => 'required_with:end_date',
            'end_date' => 'required_with:start_date',
        ], [
            'start_date.required_with' => 'Both Start Date and End Date must be selected.',
            'end_date.required_with' => 'Both Start Date and End Date must be selected.'
        ]);
    
        $query = Logs::query();
    
        // Apply filtering by kiosk_id only if it's present
        if ($request->has('kiosk_id') && $request->input('kiosk_id') !== null && $request->input('kiosk_id') !== '') {
            $query->where('kiosk_id', $request->input('kiosk_id'));
        }
    
        // Check if a date range is provided by the user
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
            
        // If no date range is provided, default to the last 7 days up to tomorrow's start
        if (!$startDate && !$endDate) {
            $startDate = Carbon::now()->subDays(6)->startOfDay()->format('Y-m-d H:i:s'); // Start 6 days ago
            $endDate = Carbon::now()->addDay()->startOfDay()->format('Y-m-d H:i:s'); // Start of tomorrow
        } else {
            $startDate = Carbon::parse($startDate)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::parse($endDate)->addDay()->startOfDay()->format('Y-m-d H:i:s');
        }
    
        // Apply date filtering
        if ($startDate) {
            $query->where('timestamp', '>=', $startDate);
        }
    
        if ($endDate) {
            $query->where('timestamp', '<=', $endDate);
        }
    
        // Fetch logs with kiosk and company info
        $logs = $query->orderBy('timestamp', 'desc')->get()->map(function ($log) {
            $kiosk = Kiosk::find($log->kiosk_id);
            $company = DB::table('companies')->where('id', $log->store_id)->first();
    
            return (object) [
                'kiosk_id' => $log->kiosk_id,
                'kiosk_name' => $kiosk ? $kiosk->name : 'Unknown Kiosk',
                'store_id' => $log->store_id,
                'company_name' => $company ? $company->storename : 'Unknown Company',
                'timestamp' => $log->timestamp,
                'created_at' => $log->created_at
            ];
        });
    
        // Check if logs are empty, fetch the latest 100 logs
        if ($logs->isEmpty()) {
            $logs = Logs::orderBy('timestamp', 'desc')->limit(100)->get()->map(function ($log) {
                $kiosk = Kiosk::find($log->kiosk_id);
                $company = DB::table('companies')->where('id', $log->store_id)->first();
    
                return (object) [
                    'kiosk_id' => $log->kiosk_id,
                    'kiosk_name' => $kiosk ? $kiosk->name : 'Unknown Kiosk',
                    'store_id' => $log->store_id,
                    'company_name' => $company ? $company->storename : 'Unknown Company',
                    'timestamp' => $log->timestamp,
                    'created_at' => $log->created_at
                ];
            });
        }
    
        // Fetch all kiosks for dropdown
        $kiosks = Kiosk::all();
    
        return view('Admin.logs.logs', compact('logs', 'kiosks', 'startDate', 'endDate'));
    }
        
    
    public function viewLogStatistics(Request $request)
    {
        $kioskId = $request->get('kiosk_id');
        $storeId = $request->get('store_id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        // Get all kiosks and companies for dropdowns
        $kiosks = Kiosk::all();
        $companies = DB::table('companies')->select('id', 'storename')->get(); 
        
        // Initialize base query for logs with joins
        $baseQuery = DB::table('logs')
            ->join('kiosks', 'logs.kiosk_id', '=', 'kiosks.id')
            ->join('companies', 'logs.store_id', '=', 'companies.id')
            ->select(
                'logs.kiosk_id', 
                'kiosks.name as kiosk_name',
                'logs.store_id', 
                'companies.storename as company_name',
                'logs.timestamp'
            );
        
        // Apply filters for kiosk, store, and date range
        if ($kioskId) {
            $baseQuery->where('logs.kiosk_id', $kioskId);
        }
    
        if ($storeId) {
            $baseQuery->where('logs.store_id', $storeId);
        }
    
        // Require both start and end date if either is provided
        if ($startDate || $endDate) {
            if ($startDate && $endDate) {
                $baseQuery->whereBetween('logs.timestamp', [$startDate, $endDate]);
            } else {
                return redirect()->back()->withErrors(['Both start and end dates must be provided']);
            }
        } else {
            // Default: filter logs from the last week
            $baseQuery->where('logs.timestamp', '>=', now()->subWeek());
        }
    
        // Fetch filtered logs
        $filteredLogs = $baseQuery->get();
    
        // If no logs are found, fetch the latest 100 logs
        if ($filteredLogs->isEmpty()) {
            $filteredLogs = DB::table('logs')
                ->join('kiosks', 'logs.kiosk_id', '=', 'kiosks.id')
                ->join('companies', 'logs.store_id', '=', 'companies.id')
                ->select(
                    'logs.kiosk_id', 
                    'kiosks.name as kiosk_name',
                    'logs.store_id', 
                    'companies.storename as company_name',
                    'logs.timestamp'
                )
                ->orderBy('logs.timestamp', 'desc')
                ->limit(100)
                ->get();
        }
    
        // Total logs with applied filters
        $totalLogs = $filteredLogs->count();
    
        // Logs per kiosk with applied filters
        $logsPerKiosk = $filteredLogs->groupBy('kiosk_id')->map(function ($group, $kioskId) {
            return (object) [
                'kiosk_id' => $kioskId,
                'kiosk_name' => $group->first()->kiosk_name ?? 'Unknown Kiosk',
                'total' => $group->count(),
            ];
        })->values();
    
        // Logs per company with applied filters
        $logsPerCompany = $filteredLogs->groupBy('store_id')->map(function ($group, $storeId) {
            return (object) [
                'store_id' => $storeId,
                'company_name' => $group->first()->company_name ?? 'Unknown Company',
                'total' => $group->count(),
            ];
        })->values();
    
        // Logs in the last 24 hours (ignores other filters)
        $logsLast24Hours = DB::table('logs')
            ->where('timestamp', '>=', now()->subDay())
            ->count();
    
        return view('Admin.logs.statistics', compact('totalLogs', 'logsPerKiosk', 'logsPerCompany', 'logsLast24Hours', 'kiosks', 'companies'));
    }
                

    
    public function viewLogStatisticsChart(Request $request)
    {
        $kioskId = $request->get('kiosk_id');
        $storeId = $request->get('store_id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        // Get all kiosks and companies for dropdowns
        $kiosks = Kiosk::all();
        $companies = DB::table('companies')->select('id', 'storename')->get(); 
        
        // Initialize base query for logs with joins
        $baseQuery = DB::table('logs')
            ->join('kiosks', 'logs.kiosk_id', '=', 'kiosks.id')
            ->join('companies', 'logs.store_id', '=', 'companies.id')
            ->select(
                'logs.kiosk_id', 
                'kiosks.name as kiosk_name',
                'logs.store_id', 
                'companies.storename as company_name',
                'logs.timestamp'
            );
        
        // Apply filters for kiosk, store, and date range
        if ($kioskId) {
            $baseQuery->where('logs.kiosk_id', $kioskId);
        }
    
        if ($storeId) {
            $baseQuery->where('logs.store_id', $storeId);  // New: Filter by store
        }
    
        // Require both start and end date if either is provided
        if ($startDate || $endDate) {
            if ($startDate && $endDate) {
                $baseQuery->whereBetween('logs.timestamp', [$startDate, $endDate]);
            } else {
                return redirect()->back()->withErrors(['Both start and end dates must be provided']);
            }
        } else {
            // Default: filter logs from the last week
            $baseQuery->where('logs.timestamp', '>=', now()->subWeek());
        }
        
        // Total logs with applied filters
        $totalLogs = (clone $baseQuery)->count();
    
        // Logs per kiosk with applied filters
        $logsPerKiosk = (clone $baseQuery)
            ->select('logs.kiosk_id', 'kiosks.name as kiosk_name', DB::raw('count(*) as total'))
            ->groupBy('logs.kiosk_id', 'kiosks.name')
            ->get();
    
        // Logs per company with applied filters
        $logsPerCompany = (clone $baseQuery)
            ->select('logs.store_id', 'companies.storename as company_name', DB::raw('count(*) as total'))
            ->groupBy('logs.store_id', 'companies.storename')
            ->orderBy('total', 'desc')
            ->get();
    
        // Logs in the last 24 hours (ignores other filters)
        $logsLast24Hours = DB::table('logs')
            ->where('timestamp', '>=', now()->subDay())
            ->count();
    
        return view('Admin.logs.logStatisticsChart', compact('totalLogs', 'logsPerKiosk', 'logsPerCompany', 'logsLast24Hours', 'kiosks', 'companies'));
    }   
            
}