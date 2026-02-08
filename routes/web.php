<?php

use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Livewire\Admin\Users;
use App\Livewire\Admin\Cotisations;
use App\Livewire\Admin\Emprunts;
use App\Model\Cotisation;
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
})->middleware('auth');

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

Route::middleware(['auth'])->group(function () {
    Route::get('/rapports', [RapportController::class, 'index'])->name('rapports.index');
    Route::post('/rapports/pdf', [RapportController::class, 'generatePdf'])->name('rapports.pdf');
});
