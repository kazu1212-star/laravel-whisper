<?php

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
use App\Http\Controllers\ChatGptController;

Route::get('/chat', [ChatGptController::class, 'index'])->name('chat_gpt-index');
Route::post('/chat', [ChatGptController::class, 'chat'])->name('chat_gpt-chat');

Route::get('/', function () {
    return view('welcome');
});
