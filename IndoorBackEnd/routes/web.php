<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\Auth\RegisterController;


Route::middleware(['auth', 'superadmin', 'mfa'])->group(function () {
    Route::get('/all-users', [DashboardController::class, 'allUser'])->name('allUser');
    Route::get('/create-users', [DashboardController::class, 'createUsers'])->name('createUsers');
    Route::post('/create-users', [DashboardController::class, 'saveUsers'])->name('saveUsers');
    Route::get('/edit-users/{id}', [DashboardController::class, 'editUsers'])->name('editUsers');
    Route::post('/edit-users/{id}', [DashboardController::class, 'updateUsers'])->name('updateUsers');
    Route::get('/view/{id}', [DashboardController::class, 'viewUser'])->name('viewUser');
    Route::get('/delete-users/{id}', [DashboardController::class, 'deleteUser'])->name('deleteUser');
});

Route::middleware(['auth', 'adminOrSuperAdmin', 'mfa'])->group(function () {
    Route::get('/all-companies', [DashboardController::class, 'allCompanies'])->name('allCompanies');
    Route::get('/edit-companies/{id}', [DashboardController::class, 'editCompanies'])->name('editCompanies');
    Route::post('/edit-companies/{id}', [DashboardController::class, 'updateCompanies'])->name('updateCompanies');
    Route::get('/view-companies/{id}', [DashboardController::class, 'viewCompanies'])->name('viewCompanies');
    Route::get('/kiosk', [DashboardController::class, 'kiosk'])->name('kiosk');
    Route::post('/kiosk', [DashboardController::class, 'Addkiosk'])->name('Addkiosk');
    Route::get('/edit-kiosk/{id}', [DashboardController::class, 'EditKiosk'])->name('EditKiosk');
    Route::post('/update-kiosk/{id}', [DashboardController::class, 'UpdateKiosk'])->name('UpdateKiosk');
    Route::get('/view-kiosk/{id}', [DashboardController::class, 'ViewKiosk'])->name('ViewKiosk');
    Route::get('/selectPlaza', [DashboardController::class, 'selectPlaza'])->name('selectPlaza');
    Route::get('/setting', [DashboardController::class, 'setting'])->name('setting');
    Route::post('/setting', [DashboardController::class, 'settingUpdate'])->name('settingUpdate');

    Route::get('/logs', [DashboardController::class, 'viewLogs'])->name('viewLogs');
    Route::get('/statistics', [DashboardController::class, 'viewLogStatistics'])->name('viewLogStatistics');
    Route::get('/statisticsChart', [DashboardController::class, 'viewLogStatisticsChart'])->name('viewLogStatisticsChart');

});


Route::middleware(['auth', 'mfa'])->group(function () {
    Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::get('/change-users-password/{id}', [DashboardController::class, 'changePasswordUser'])->name('changePasswordUser');
    Route::post('/change-users-password/{id}', [DashboardController::class, 'UpdatePasswordUser'])->name('UpdatePasswordUser');

    Route::get('/adss', [DashboardController::class, 'adsAndPromotions'])->name('ads');
    Route::post('/adss', [DashboardController::class, 'uploadAds'])->name('uploadAds');
    Route::post('/promotions', [DashboardController::class, 'uploadPromotions'])->name('uploadPromotions');

    Route::get('/banners', [DashboardController::class, 'banners'])->name('banners');
    Route::post('/banners', [DashboardController::class, 'uploadBanners'])->name('uploadBanners');

    Route::get('/theme', [DashboardController::class, 'themes'])->name('themes.index');
    Route::post('/theme', [DashboardController::class, 'store'])->name('themes.store');
    
    Route::get('/videos', [DashboardController::class, 'videos'])->name('videos'); // Display video upload form
    Route::post('/videos', [DashboardController::class, 'uploadVideos'])->name('uploadVideos'); // Handle video upload

    Route::get('/front-end', [DashboardController::class, 'forntSetting'])->name('forntSetting');
    Route::post('/front-end', [DashboardController::class, 'UpdateforntSetting'])->name('UpdateforntSetting');

    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
    Route::post('/profile', [DashboardController::class, 'updateProfile'])->name('updateProfile');

    Route::get('/misc', [DashboardController::class, 'misc'])->name('misc');
    Route::post('/misc', [DashboardController::class, 'updateMisc'])->name('misc.update');


    Route::get('/export', [DashboardController::class, 'ConvertData'])->name('sqlite');
});

Route::controller('App\Http\Controllers\Auth\LoginController')->group(function () {
    Route::get('/login', 'login')->name('login');
    Route::post('/admin/login', 'adminLogin')->name('admin.login');
    Route::post('/logout', 'logout')->name('logout');
    Route::get('/mfa/verify', 'showMfaForm')->name('mfa.verify');
    Route::post('/mfa/resend', 'resendMfaToken')->name('mfa.resend');
    Route::post('/mfa/verify', 'verifyMfaToken')->name('mfa.verify.token');
});


//commented since Registration is not required
//Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
//Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');


// Password Reset Routes
Route::get('password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');