<?php

use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\ExportDataController;
use App\Http\Controllers\RapportController;

Route::get('/', function () {
    return view('welcome');
});



Route::get('/auth/redirect', function () {
    return Socialite::driver('google')->redirect();
})->name('google.login');

Route::get('/auth/callback', function () {
    $googleUser = Socialite::driver('google')->user();

    $user = User::updateOrCreate([
        'google_id' => $googleUser->id,
    ], [
        'name' => $googleUser->name,
        'email' => $googleUser->email,
        'google_token' => $googleUser->token,
        'google_refresh_token' => $googleUser->refreshToken,
    ]);

    Auth::login($user);

    return redirect('/dashboard');
});

Route::get('/dashboard', function () {

    $user = Auth::user();
    return view('dashboard', ['user' => $user]);
})->middleware('auth')->name('dashboard');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/');
})->name('logout');



/*Route::middleware(['auth', 'admin'])->group(function () {
    return view('users');
    //Route::get('/admin/users', Users::class)->name('admin.users');
});*/

Route::get('/admin/users', function () {

    // $user = Auth::user();
    $users = User::orderBy('name')->paginate(10);
    $userCount = User::count();


    return view('users', [
        'users' => $users,
        'countUsers' => $userCount
    ]);
})->middleware(['auth', 'admin'])->name('admin.users');

Route::get('/admin/cotisations', function () {

    return view('cotisations');
})->middleware(['auth', 'admin'])->name('admin.cotisations');

Route::get('/admin/emprunts', function () {

    return view('emprunts');
})->middleware(['auth', 'admin'])->name('admin.emprunts');

Route::get('/admin/cycles', function () {
    return view('cycles');
})->middleware(['auth', 'admin'])->name('admin.cycles');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/export-data', [ExportDataController::class, 'index'])
        ->name('admin.export-data');
    Route::post('/admin/export-data', [ExportDataController::class, 'export'])
        ->name('admin.export-data.download');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/rapports', [RapportController::class, 'index'])->name('rapports.index');
    Route::post('/rapports/pdf', [RapportController::class, 'generatePdf'])->name('rapports.pdf');
});
