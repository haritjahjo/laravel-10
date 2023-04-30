<?php

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Profile\Avatar\AvatarController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/avatar', [AvatarController::class, 'update'])->name('profile.avatar');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('profile/avatar/ai', [AvatarController::class, 'generate'])->name('profile.avatar.ai');
});

require __DIR__.'/auth.php';

// Route::get('openai', function(){

//     $result = OpenAI::completions()->create([
//     'model' => 'text-davinci-003',
//     'prompt' => 'PHP is',
// ]);

// echo $result['choices'][0]['text']; // an open-source, widely-used, server-side scripting language.
// });

// Route::get('/auth/redirect', function () {
//     return Socialite::driver('github')->redirect();
// });

Route::post('/auth/redirect', function () {
    return Socialite::driver('github')->redirect();
})->name('login.github');
 
Route::get('/auth/callback', function () {
    $user = Socialite::driver('github')->user();

    $user = User::firstOrCreate(['email' => $user->email], [
        'name' => $user->name,
        'password' => bcrypt('password'),
    ]);

    Auth::login($user);
    return redirect('/dashboard');
 
    //dd($user);
    // $user->token
});