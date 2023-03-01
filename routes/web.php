<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

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


Route::controller(AuthController::class)->middleware('cors')->group(function () {
    Route::post('signin', 'signIn');
    Route::post('signup', 'signUp');
    Route::get('info', 'info');
    Route::put('info', 'update');
    Route::get('latency', 'latency');
    Route::delete('token', 'delete');
});
