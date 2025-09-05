<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GreetingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('greetings.index');
});

Route::get('/greetings', [GreetingController::class, 'index'])->name('greetings.index');
Route::post('/greetings', [GreetingController::class, 'store'])->name('greetings.store');
